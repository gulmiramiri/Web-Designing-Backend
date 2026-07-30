<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = getJsonInput();

$identifier = clean($input['identifier'] ?? ''); // username or email
$password   = (string)($input['password'] ?? '');
$remember   = !empty($input['remember']);

if ($identifier === '' || $password === '') {
    jsonResponse(['success' => false, 'message' => 'Username/email and password are required.'], 422);
}

$pdo = getDBConnection();
$stmt = $pdo->prepare(
    'SELECT u.id, u.full_name, u.username, u.email, u.password, u.avatar, r.name AS role
     FROM users u
     JOIN roles r ON r.id = u.role_id
     WHERE u.username = :username_id OR u.email = :email_id
     LIMIT 1'
);
$stmt->execute(['username_id' => $identifier, 'email_id' => $identifier]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid credentials.'], 401);
}

session_regenerate_id(true);
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['role']      = $user['role'];
$_SESSION['avatar']    = $user['avatar'];
if ($remember) {
    // Extend cookie lifetime to 30 days
    $params = session_get_cookie_params();
    setcookie(session_name(), session_id(), time() + 30 * 24 * 60 * 60, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

jsonResponse([
    'success' => true,
    'message' => 'Login successful.',
    'user' => [
        'id'        => $user['id'],
        'full_name' => $user['full_name'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'role'      => $user['role'],
        'avatar'    => $user['avatar'],
    ],
]);
