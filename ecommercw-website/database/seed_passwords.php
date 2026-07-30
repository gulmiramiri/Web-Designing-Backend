<?php
/**
 * One-time helper script.
 *
 * shop.sql inserts two demo accounts (admin / johndoe) with placeholder
 * password hashes. Run this script ONCE after importing shop.sql (via CLI:
 * `php database/seed_passwords.php`, or by visiting it in the browser) to
 * set their real, correctly-generated bcrypt hashes. Delete this file
 * afterwards — it should never remain accessible in production.
 *
 * Demo credentials after running this script:
 *   Admin   -> username: admin    / password: Admin@123
 *   User    -> username: johndoe  / password: User@1234
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

$accounts = [
    'admin'   => 'Admin@123',
    'johndoe' => 'User@1234',
];

foreach ($accounts as $username => $plainPassword) {
    $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE username = :username');
    $stmt->execute(['password' => $hash, 'username' => $username]);
    echo "Updated password hash for '{$username}'." . PHP_EOL;
}

echo 'Done. Please delete this file now.' . PHP_EOL;
