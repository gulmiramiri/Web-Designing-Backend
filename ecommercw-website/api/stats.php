<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

requireAdmin();

$pdo = getDBConnection();

$stats = [
    'total_users'          => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'total_products'       => (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'total_categories'     => (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'products_out_of_stock' => (int)$pdo->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn(),
    'low_stock_products'   => (int)$pdo->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 5')->fetchColumn(),
    'total_tickets'        => (int)$pdo->query('SELECT COUNT(*) FROM tickets')->fetchColumn(),
    'open_tickets'         => (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status != 'closed'")->fetchColumn(),
    'closed_tickets'       => (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'closed'")->fetchColumn(),
    'waiting_admin'        => (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'waiting_admin'")->fetchColumn(),
    'waiting_user'         => (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'waiting_user'")->fetchColumn(),
];

jsonResponse(['success' => true, 'stats' => $stats]);
