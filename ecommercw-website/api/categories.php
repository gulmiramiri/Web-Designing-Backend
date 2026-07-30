<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        requireAdmin();
        handleCreate($pdo);
        break;
    case 'PUT':
        requireAdmin();
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
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => (int)$_GET['id']]);
        $category = $stmt->fetch();
        if (!$category) {
            jsonResponse(['success' => false, 'message' => 'Category not found.'], 404);
        }
        jsonResponse(['success' => true, 'category' => $category]);
    }

    $stmt = $pdo->query(
        'SELECT c.*, COUNT(p.id) AS product_count
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.id
         GROUP BY c.id
         ORDER BY c.name ASC'
    );
    jsonResponse(['success' => true, 'categories' => $stmt->fetchAll()]);
}

function handleCreate(PDO $pdo): void
{
    $name  = clean($_POST['name'] ?? '');
    $image = null;

    if ($name === '') {
        jsonResponse(['success' => false, 'message' => 'Category name is required.'], 422);
    }

    if (!empty($_FILES['image'])) {
        $image = handleImageUpload($_FILES['image'], __DIR__ . '/../uploads');
    }

    $stmt = $pdo->prepare('INSERT INTO categories (name, image, created_at) VALUES (:name, :image, NOW())');
    $stmt->execute(['name' => $name, 'image' => $image]);

    jsonResponse(['success' => true, 'message' => 'Category created.', 'id' => $pdo->lastInsertId()]);
}

function handleUpdate(PDO $pdo): void
{
    $put  = getJsonInput();
    $id   = (int)($put['id'] ?? 0);
    $name = clean($put['name'] ?? '');
    $image = isset($put['image']) ? clean($put['image']) : null;

    if ($id <= 0 || $name === '') {
        jsonResponse(['success' => false, 'message' => 'Category id and name are required.'], 422);
    }

    if ($image !== null && $image !== '') {
        $stmt = $pdo->prepare('UPDATE categories SET name = :name, image = :image WHERE id = :id');
        $stmt->execute(['name' => $name, 'image' => $image, 'id' => $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE categories SET name = :name WHERE id = :id');
        $stmt->execute(['name' => $name, 'id' => $id]);
    }

    jsonResponse(['success' => true, 'message' => 'Category updated.']);
}

function handleDelete(PDO $pdo): void
{
    parse_str(file_get_contents('php://input'), $del);
    $id = (int)($del['id'] ?? ($_GET['id'] ?? 0));

    if ($id <= 0) {
        jsonResponse(['success' => false, 'message' => 'Category id is required.'], 422);
    }

    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
    $stmt->execute(['id' => $id]);

    jsonResponse(['success' => true, 'message' => 'Category deleted.']);
}
