<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        requireAdmin();
        handleGet($pdo);
        break;
    case 'POST':
        requireAdmin();
        handleCreate($pdo);
        break;
    case 'PUT':
        requireLogin();
        handleUpdate($pdo);
        break;
    case 'DELETE':
        requireAdmin();
        handleDelete($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function handleGet(PDO $pdo): void
{
    $stmt = $pdo->query(
        'SELECT u.id, u.full_name, u.username, u.email, u.avatar, u.created_at, r.name AS role
         FROM users u JOIN roles r ON r.id = u.role_id
         ORDER BY u.created_at DESC'
    );
    jsonResponse(['success' => true, 'users' => $stmt->fetchAll()]);
}

function handleCreate(PDO $pdo): void
{
    $input = getJsonInput();

    $fullName = clean($input['full_name'] ?? '');
    $username = clean($input['username'] ?? '');
    $email    = clean($input['email'] ?? '');
    $password = (string)($input['password'] ?? '');
    $role     = clean($input['role'] ?? 'user');

    if ($fullName === '' || $username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        jsonResponse(['success' => false, 'message' => 'Invalid user data.'], 422);
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
    $stmt->execute(['email' => $email, 'username' => $username]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Email or username already exists.'], 409);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (full_name, username, email, password, role_id, created_at)
         VALUES (:full_name, :username, :email, :password, (SELECT id FROM roles WHERE name = :role), NOW())'
    );
    $stmt->execute([
        'full_name' => $fullName, 'username' => $username, 'email' => $email,
        'password' => $hash, 'role' => $role,
    ]);

    jsonResponse(['success' => true, 'message' => 'User created.', 'id' => $pdo->lastInsertId()]);
}

function handleUpdate(PDO $pdo): void
{
    $input = getJsonInput();
    $targetId = (int)($input['id'] ?? 0);

    // Non-admins may only update their own profile
    if (!isAdmin() && $targetId !== (int)$_SESSION['user_id']) {
        jsonResponse(['success' => false, 'message' => 'You may only edit your own profile.'], 403);
    }

    if ($targetId <= 0) {
        jsonResponse(['success' => false, 'message' => 'User id is required.'], 422);
    }

    $fields = [];
    $params = ['id' => $targetId];

    if (!empty($input['full_name'])) {
        $fields[] = 'full_name = :full_name';
        $params['full_name'] = clean($input['full_name']);
    }
    if (!empty($input['email']) && filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $fields[] = 'email = :email';
        $params['email'] = clean($input['email']);
    }
    if (isAdmin() && !empty($input['role'])) {
        $fields[] = 'role_id = (SELECT id FROM roles WHERE name = :role)';
        $params['role'] = clean($input['role']);
    }
    if (!empty($input['new_password'])) {
        if (strlen($input['new_password']) < 8) {
            jsonResponse(['success' => false, 'message' => 'New password must be at least 8 characters.'], 422);
        }
        // If a non-admin is changing their own password, verify current password
        if (!isAdmin()) {
            $check = $pdo->prepare('SELECT password FROM users WHERE id = :id');
            $check->execute(['id' => $targetId]);
            $row = $check->fetch();
            if (!$row || !password_verify((string)($input['current_password'] ?? ''), $row['password'])) {
                jsonResponse(['success' => false, 'message' => 'Current password is incorrect.'], 401);
            }
        }
        $fields[] = 'password = :password';
        $params['password'] = password_hash($input['new_password'], PASSWORD_BCRYPT);
    }

    if (empty($fields)) {
        jsonResponse(['success' => false, 'message' => 'Nothing to update.'], 422);
    }

    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ((int)$_SESSION['user_id'] === $targetId && isset($params['full_name'])) {
        $_SESSION['full_name'] = $params['full_name'];
    }

    jsonResponse(['success' => true, 'message' => 'User updated.']);
}

function handleDelete(PDO $pdo): void
{
    parse_str(file_get_contents('php://input'), $del);
    $id = (int)($del['id'] ?? ($_GET['id'] ?? 0));

    if ($id <= 0) {
        jsonResponse(['success' => false, 'message' => 'User id is required.'], 422);
    }
    if ($id === (int)$_SESSION['user_id']) {
        jsonResponse(['success' => false, 'message' => 'You cannot delete your own account.'], 400);
    }

    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);

    jsonResponse(['success' => true, 'message' => 'User deleted.']);
}
