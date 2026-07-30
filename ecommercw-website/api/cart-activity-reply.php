<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

requireLogin();

switch ($method) {
    case 'POST':
        handleCreate($pdo);
        break;
    case 'GET':
        handleGet($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function handleCreate(PDO $pdo): void
{
    requireAdmin();

    $data = getJsonInput();
    $activityId = (int)($data['cart_activity_id'] ?? 0);
    $message = clean($data['message'] ?? '');

    if ($activityId <= 0 || $message === '') {
        jsonResponse(['success' => false, 'message' => 'Cart activity ID and message are required.'], 422);
    }

    // Verify cart activity exists and get user + product info
    $stmt = $pdo->prepare(
        'SELECT ca.id, ca.user_id, p.title AS product_title
         FROM cart_activity ca
         JOIN products p ON p.id = ca.product_id
         WHERE ca.id = :id'
    );
    $stmt->execute(['id' => $activityId]);
    $activity = $stmt->fetch();

    if (!$activity) {
        jsonResponse(['success' => false, 'message' => 'Cart activity not found.'], 404);
    }

    $adminId = (int)$_SESSION['user_id'];

    // Insert reply
    $stmt = $pdo->prepare(
        'INSERT INTO cart_activity_replies (cart_activity_id, admin_id, message, created_at) VALUES (:cart_activity_id, :admin_id, :message, NOW())'
    );
    $stmt->execute([
        'cart_activity_id' => $activityId,
        'admin_id'         => $adminId,
        'message'          => $message,
    ]);

    // Create notification for the user
    $notifMessage = 'Admin replied to your cart: ' . $activity['product_title'] . ' — "' . $message . '"';
    $stmt = $pdo->prepare(
        'INSERT INTO notifications (user_id, type, message, cart_activity_id, created_at) VALUES (:user_id, :type, :message, :cart_activity_id, NOW())'
    );
    $stmt->execute([
        'user_id'          => (int)$activity['user_id'],
        'type'             => 'cart_reply',
        'message'          => $notifMessage,
        'cart_activity_id' => $activityId,
    ]);

    jsonResponse(['success' => true, 'message' => 'Reply sent.']);
}

function handleGet(PDO $pdo): void
{
    $activityId = (int)($_GET['cart_activity_id'] ?? 0);

    if ($activityId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Cart activity ID is required.'], 422);
    }

    $stmt = $pdo->prepare(
        'SELECT car.id, car.message, car.created_at,
                u.full_name AS admin_name
         FROM cart_activity_replies car
         JOIN users u ON u.id = car.admin_id
         WHERE car.cart_activity_id = :cart_activity_id
         ORDER BY car.created_at ASC'
    );
    $stmt->execute(['cart_activity_id' => $activityId]);
    $replies = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'replies' => $replies,
    ]);
}
