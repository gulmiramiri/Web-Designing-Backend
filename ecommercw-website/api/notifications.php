<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

requireLogin();

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'PUT':
        handleMarkRead($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function handleGet(PDO $pdo): void
{
    $userId = (int)$_SESSION['user_id'];

    // Count unread
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
    $stmt->execute(['user_id' => $userId]);
    $unreadCount = (int)$stmt->fetchColumn();

    // Get recent notifications
    $stmt = $pdo->prepare(
        'SELECT n.*, t.ticket_number, t.subject AS ticket_subject,
                ca.product_id AS ca_product_id, p.title AS ca_product_title, p.image AS ca_product_image
         FROM notifications n
         LEFT JOIN tickets t ON t.id = n.ticket_id
         LEFT JOIN cart_activity ca ON ca.id = n.cart_activity_id
         LEFT JOIN products p ON p.id = ca.product_id
         WHERE n.user_id = :user_id
         ORDER BY n.created_at DESC
         LIMIT 20'
    );
    $stmt->execute(['user_id' => $userId]);
    $notifications = $stmt->fetchAll();

    jsonResponse([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]);
}

function handleMarkRead(PDO $pdo): void
{
    $userId = (int)$_SESSION['user_id'];
    $data = getJsonInput();
    $all = !empty($data['all']);
    $notifId = (int)($data['id'] ?? 0);

    if ($all) {
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    } elseif ($notifId > 0) {
        $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $notifId, 'user_id' => $userId]);
    }

    jsonResponse(['success' => true, 'message' => 'Notifications marked as read.']);
}
