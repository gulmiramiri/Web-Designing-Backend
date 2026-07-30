<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

requireLogin();

if ($method !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$userId = (int)$_SESSION['user_id'];
$isAdmin = isAdmin();
$ticketId = (int)($_POST['ticket_id'] ?? 0);
$message = clean($_POST['message'] ?? '');
$image = null;

if ($ticketId <= 0 || $message === '') {
    jsonResponse(['success' => false, 'message' => 'Ticket ID and message are required.'], 422);
}

// Get ticket
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = :id');
$stmt->execute(['id' => $ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    jsonResponse(['success' => false, 'message' => 'Ticket not found.'], 404);
}

if (!$isAdmin && (int)$ticket['user_id'] !== $userId) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 403);
}

if ($ticket['status'] === 'closed') {
    jsonResponse(['success' => false, 'message' => 'This ticket is closed.'], 400);
}

if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $maxSize = 5 * 1024 * 1024;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    if (in_array($mime, $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $filename = bin2hex(random_bytes(16)) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], rtrim($uploadDir, '/') . '/' . $filename)) {
                $image = $filename;
            }
        }
    }
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        'INSERT INTO ticket_messages (ticket_id, user_id, message, image, created_at)
         VALUES (:ticket_id, :user_id, :message, :image, NOW())'
    );
    $stmt->execute([
        'ticket_id' => $ticketId,
        'user_id' => $userId,
        'message' => $message,
        'image' => $image,
    ]);

    // Update ticket status and timestamp
    $newStatus = $isAdmin ? 'waiting_user' : 'waiting_admin';
    $stmt = $pdo->prepare("UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $ticketId]);

    // Notify the other party
    if ($isAdmin) {
        $notifMsg = "Admin replied to ticket #{$ticket['ticket_number']}.";
        $pdo->prepare('INSERT INTO notifications (user_id, type, message, ticket_id, created_at) VALUES (:user_id, :type, :message, :ticket_id, NOW())')
            ->execute(['user_id' => $ticket['user_id'], 'type' => 'admin_reply', 'message' => $notifMsg, 'ticket_id' => $ticketId]);
    } else {
        $notifMsg = "User replied to ticket #{$ticket['ticket_number']}.";
        $adminStmt = $pdo->prepare("SELECT id FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'admin')");
        $adminStmt->execute();
        foreach ($adminStmt->fetchAll() as $admin) {
            $pdo->prepare('INSERT INTO notifications (user_id, type, message, ticket_id, created_at) VALUES (:user_id, :type, :message, :ticket_id, NOW())')
                ->execute(['user_id' => $admin['id'], 'type' => 'user_reply', 'message' => $notifMsg, 'ticket_id' => $ticketId]);
        }
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(['success' => false, 'message' => 'Failed to send message.'], 500);
}

jsonResponse(['success' => true, 'message' => 'Message sent.']);
