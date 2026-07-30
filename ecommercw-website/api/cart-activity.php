<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

requireLogin();
if (!isAdmin()) {
    jsonResponse(['success' => false, 'message' => 'Admin only.'], 403);
}

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$stmt = $pdo->query('SELECT COUNT(*) FROM cart_activity');
$totalItems = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalItems / $perPage));

$stmt = $pdo->prepare(
    'SELECT ca.id, ca.quantity, ca.created_at,
            u.id AS user_id, u.full_name, u.email,
            p.id AS product_id, p.title AS product_title, p.price, p.image,
            (SELECT COUNT(*) FROM cart_activity_replies WHERE cart_activity_id = ca.id) AS reply_count,
            (SELECT message FROM cart_activity_replies WHERE cart_activity_id = ca.id ORDER BY created_at DESC LIMIT 1) AS last_reply
     FROM cart_activity ca
     JOIN users u ON u.id = ca.user_id
     JOIN products p ON p.id = ca.product_id
     ORDER BY ca.created_at DESC
     LIMIT :limit OFFSET :offset'
);
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$activities = $stmt->fetchAll();

jsonResponse([
    'success'    => true,
    'activities' => $activities,
    'pagination' => [
        'page'        => $page,
        'total_pages' => $totalPages,
        'total_items' => $totalItems,
    ],
]);
