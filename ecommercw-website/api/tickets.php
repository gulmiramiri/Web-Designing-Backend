<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        requireLogin();
        handleGet($pdo);
        break;
    case 'POST':
        requireLogin();
        handleCreate($pdo);
        break;
    case 'PUT':
        requireLogin();
        handleUpdate($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function generateTicketNumber(PDO $pdo): string
{
    $prefix = 'TKT-' . date('Ymd') . '-';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE ticket_number LIKE :prefix");
    $stmt->execute(['prefix' => $prefix . '%']);
    $count = (int)$stmt->fetchColumn();
    return $prefix . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
}

function handleGet(PDO $pdo): void
{
    $userId = (int)$_SESSION['user_id'];
    $isAdmin = isAdmin();

    // Single ticket
    if (!empty($_GET['id'])) {
        $ticketId = (int)$_GET['id'];
        if ($isAdmin) {
            $stmt = $pdo->prepare(
                'SELECT t.*, u.full_name AS user_name, u.email AS user_email
                 FROM tickets t
                 JOIN users u ON u.id = t.user_id
                 WHERE t.id = :id'
            );
        } else {
            $stmt = $pdo->prepare(
                'SELECT t.*, u.full_name AS user_name
                 FROM tickets t
                 JOIN users u ON u.id = t.user_id
                 WHERE t.id = :id AND t.user_id = :user_id'
            );
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();
        $ticket = $stmt->fetch();

        if (!$ticket) {
            jsonResponse(['success' => false, 'message' => 'Ticket not found.'], 404);
        }

        // Get messages
        $stmt = $pdo->prepare(
            'SELECT tm.*, u.full_name, u.role_id, r.name AS role_name
             FROM ticket_messages tm
             JOIN users u ON u.id = tm.user_id
             JOIN roles r ON r.id = u.role_id
             WHERE tm.ticket_id = :ticket_id
             ORDER BY tm.created_at ASC'
        );
        $stmt->execute(['ticket_id' => $ticketId]);
        $messages = $stmt->fetchAll();

        // Mark messages as read for this user
        if ($isAdmin) {
            $pdo->prepare("UPDATE ticket_messages SET read_at = NOW() WHERE ticket_id = :ticket_id AND read_at IS NULL AND user_id != :user_id")
                ->execute(['ticket_id' => $ticketId, 'user_id' => $userId]);
        } else {
            $pdo->prepare("UPDATE ticket_messages SET read_at = NOW() WHERE ticket_id = :ticket_id AND read_at IS NULL AND user_id != :user_id")
                ->execute(['ticket_id' => $ticketId, 'user_id' => $userId]);
        }

        jsonResponse(['success' => true, 'ticket' => $ticket, 'messages' => $messages]);
    }

    // List tickets
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 10)));
    $offset = ($page - 1) * $perPage;
    $status = clean($_GET['status'] ?? '');
    $search = clean($_GET['search'] ?? '');
    $userIdFilter = (int)($_GET['user_id'] ?? 0);

    $where = [];
    $params = [];

    if (!$isAdmin) {
        $where[] = 't.user_id = :user_id';
        $params['user_id'] = $userId;
    }

    if ($status !== '') {
        $where[] = 't.status = :status';
        $params['status'] = $status;
    }

    if ($search !== '') {
        $where[] = '(t.subject LIKE :search OR t.ticket_number LIKE :search2)';
        $params['search'] = '%' . $search . '%';
        $params['search2'] = '%' . $search . '%';
    }

    if ($isAdmin && $userIdFilter > 0) {
        $where[] = 't.user_id = :user_id_filter';
        $params['user_id_filter'] = $userIdFilter;
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets t $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    if ($isAdmin) {
        $sql = "SELECT t.*, u.full_name AS user_name, u.email AS user_email,
                       (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id AND read_at IS NULL AND user_id != :current_user) AS unread_count
                FROM tickets t
                JOIN users u ON u.id = t.user_id
                $whereSql
                ORDER BY t.updated_at DESC
                LIMIT :limit OFFSET :offset";
    } else {
        $sql = "SELECT t.*, u.full_name AS user_name,
                       (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = t.id AND read_at IS NULL AND user_id != :current_user) AS unread_count
                FROM tickets t
                JOIN users u ON u.id = t.user_id
                $whereSql
                ORDER BY t.updated_at DESC
                LIMIT :limit OFFSET :offset";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':current_user', $userId, PDO::PARAM_INT);
    foreach ($params as $key => $val) {
        $stmt->bindValue(':' . $key, $val);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    jsonResponse([
        'success' => true,
        'tickets' => $stmt->fetchAll(),
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => (int)ceil($total / $perPage),
        ],
    ]);
}

function handleCreate(PDO $pdo): void
{
    $userId = (int)$_SESSION['user_id'];
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');
    $image = null;

    if ($subject === '' || $message === '') {
        jsonResponse(['success' => false, 'message' => 'Subject and message are required.'], 422);
    }

    if (!empty($_FILES['image'])) {
        $image = handleImageUpload($_FILES['image'], __DIR__ . '/../uploads');
    }

    $ticketNumber = generateTicketNumber($pdo);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO tickets (ticket_number, user_id, subject, status, created_at, updated_at)
             VALUES (:ticket_number, :user_id, :subject, :status, NOW(), NOW())'
        );
        $stmt->execute([
            'ticket_number' => $ticketNumber,
            'user_id' => $userId,
            'subject' => $subject,
            'status' => 'waiting_admin',
        ]);
        $ticketId = (int)$pdo->lastInsertId();

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

        // Notify admins
        $adminStmt = $pdo->prepare("SELECT id FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'admin')");
        $adminStmt->execute();
        $admins = $adminStmt->fetchAll();
        $notifStmt = $pdo->prepare(
            'INSERT INTO notifications (user_id, type, message, ticket_id, created_at)
             VALUES (:user_id, :type, :message, :ticket_id, NOW())'
        );
        foreach ($admins as $admin) {
            $notifStmt->execute([
                'user_id' => $admin['id'],
                'type' => 'new_ticket',
                'message' => "New ticket #{$ticketNumber}: {$subject}",
                'ticket_id' => $ticketId,
            ]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'Failed to create ticket.'], 500);
    }

    jsonResponse(['success' => true, 'message' => 'Ticket created.', 'ticket_id' => $ticketId]);
}

function handleUpdate(PDO $pdo): void
{
    $data = getJsonInput();
    $ticketId = (int)($data['id'] ?? 0);
    $action = clean($data['action'] ?? '');
    $userId = (int)$_SESSION['user_id'];
    $isAdmin = isAdmin();

    if ($ticketId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Ticket ID is required.'], 422);
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

    if ($action === 'close') {
        $stmt = $pdo->prepare("UPDATE tickets SET status = 'closed', updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $ticketId]);

        // Notify the other party
        if ($isAdmin) {
            $notifMsg = "Ticket #{$ticket['ticket_number']} has been closed by admin.";
            $pdo->prepare('INSERT INTO notifications (user_id, type, message, ticket_id, created_at) VALUES (:user_id, :type, :message, :ticket_id, NOW())')
                ->execute(['user_id' => $ticket['user_id'], 'type' => 'ticket_closed', 'message' => $notifMsg, 'ticket_id' => $ticketId]);
        } else {
            $notifMsg = "Ticket #{$ticket['ticket_number']} has been closed by user.";
            $adminStmt = $pdo->prepare("SELECT id FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'admin')");
            $adminStmt->execute();
            foreach ($adminStmt->fetchAll() as $admin) {
                $pdo->prepare('INSERT INTO notifications (user_id, type, message, ticket_id, created_at) VALUES (:user_id, :type, :message, :ticket_id, NOW())')
                    ->execute(['user_id' => $admin['id'], 'type' => 'ticket_closed', 'message' => $notifMsg, 'ticket_id' => $ticketId]);
            }
        }

        jsonResponse(['success' => true, 'message' => 'Ticket closed.']);
    }

    if ($action === 'reopen' && $isAdmin) {
        $stmt = $pdo->prepare("UPDATE tickets SET status = 'waiting_user', updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $ticketId]);

        $pdo->prepare('INSERT INTO notifications (user_id, type, message, ticket_id, created_at) VALUES (:user_id, :type, :message, :ticket_id, NOW())')
            ->execute([
                'user_id' => $ticket['user_id'],
                'type' => 'ticket_reopened',
                'message' => "Ticket #{$ticket['ticket_number']} has been reopened.",
                'ticket_id' => $ticketId,
            ]);

        jsonResponse(['success' => true, 'message' => 'Ticket reopened.']);
    }

    jsonResponse(['success' => false, 'message' => 'Invalid action.'], 422);
}
