<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

$input = getJsonInput();

$fullName        = clean($input['full_name'] ?? '');
$username        = clean($input['username'] ?? '');
$email           = clean($input['email'] ?? '');
$password        = (string)($input['password'] ?? '');
$confirmPassword = (string)($input['confirm_password'] ?? '');

$errors = [];

if ($fullName === '' || mb_strlen($fullName) < 2) {
    $errors['full_name'] = 'Full name is required.';
}
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors['username'] = 'Username must be 3-20 characters (letters, numbers, underscore).';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid email address is required.';
}
if (strlen($password) < 8) {
    $errors['password'] = 'Password must be at least 8 characters.';
}
if ($password !== $confirmPassword) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

if (!empty($errors)) {
    jsonResponse(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 422);
}

$pdo = getDBConnection();

// Duplicate email / username check
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
$stmt->execute(['email' => $email, 'username' => $username]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Email or username is already taken.'], 409);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare(
    'INSERT INTO users (full_name, username, email, password, role_id, created_at)
     VALUES (:full_name, :username, :email, :password, (SELECT id FROM roles WHERE name = "user"), NOW())'
);
$stmt->execute([
    'full_name' => $fullName,
    'username'  => $username,
    'email'     => $email,
    'password'  => $hash,
]);

jsonResponse(['success' => true, 'message' => 'Registration successful. You can now log in.']);
