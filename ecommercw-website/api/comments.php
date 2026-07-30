<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        requireLogin();
        handlePost($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function handleGet(PDO $pdo): void
{
    $productId = (int)($_GET['product_id'] ?? 0);
    if ($productId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Product ID is required.'], 422);
    }

    $stmt = $pdo->prepare(
        'SELECT pc.*, u.full_name, u.role_id, r.name AS role_name
         FROM product_comments pc
         JOIN users u ON u.id = pc.user_id
         JOIN roles r ON r.id = u.role_id
         WHERE pc.product_id = :product_id
         ORDER BY pc.parent_id IS NOT NULL ASC, pc.created_at ASC'
    );
    $stmt->execute(['product_id' => $productId]);
    $rows = $stmt->fetchAll();

    // Organize into parent-child structure
    $comments = [];
    foreach ($rows as $row) {
        if (!$row['parent_id']) {
            $row['replies'] = [];
            $comments[$row['id']] = $row;
        }
    }
    foreach ($rows as $row) {
        if ($row['parent_id'] && isset($comments[$row['parent_id']])) {
            $comments[$row['parent_id']]['replies'][] = $row;
        }
    }

    jsonResponse(['success' => true, 'comments' => array_values($comments)]);
}

function handlePost(PDO $pdo): void
{
    $userId  = (int)$_SESSION['user_id'];
    $isAdmin = isAdmin();

    // Parse JSON body if sent as JSON
    $rawBody  = file_get_contents('php://input');
    $jsonData = json_decode($rawBody, true);
    $input    = is_array($jsonData) ? $jsonData : $_POST;

    $productId = (int)($input['product_id'] ?? 0);
    $comment   = clean($input['comment'] ?? '');
    $parentId  = (int)($input['parent_id'] ?? 0);

    if ($productId <= 0 || $comment === '') {
        jsonResponse(['success' => false, 'message' => 'Product ID and comment are required.'], 422);
    }

    // Only admin can reply (add a comment with parent_id)
    if ($parentId > 0 && !$isAdmin) {
        jsonResponse(['success' => false, 'message' => 'Only admin can reply to comments.'], 403);
    }

    // Verify product exists
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id = :id');
    $stmt->execute(['id' => $productId]);
    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Product not found.'], 404);
    }

    // If replying, verify parent comment exists
    if ($parentId > 0) {
        $stmt = $pdo->prepare('SELECT id FROM product_comments WHERE id = :id AND product_id = :product_id');
        $stmt->execute(['id' => $parentId, 'product_id' => $productId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Parent comment not found.'], 404);
        }
    }

    $stmt = $pdo->prepare(
        'INSERT INTO product_comments (product_id, user_id, parent_id, comment, created_at)
         VALUES (:product_id, :user_id, :parent_id, :comment, NOW())'
    );
    $stmt->execute([
        'product_id' => $productId,
        'user_id'    => $userId,
        'parent_id'  => $parentId ?: null,
        'comment'    => $comment,
    ]);

    jsonResponse(['success' => true, 'message' => 'Comment added.', 'id' => $pdo->lastInsertId()]);
}
