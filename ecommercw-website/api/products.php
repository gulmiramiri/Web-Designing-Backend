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
    // Single product
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => (int)$_GET['id']]);
        $product = $stmt->fetch();
        if (!$product) {
            jsonResponse(['success' => false, 'message' => 'Product not found.'], 404);
        }
        jsonResponse(['success' => true, 'product' => $product]);
    }

    $page     = max(1, (int)($_GET['page'] ?? 1));
    $perPage  = min(50, max(1, (int)($_GET['per_page'] ?? 8)));
    $offset   = ($page - 1) * $perPage;
    $search   = clean($_GET['search'] ?? '');
    $category = (int)($_GET['category_id'] ?? 0);
    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;
    $status   = clean($_GET['status'] ?? '');
    $lowStock = isset($_GET['low_stock']) ? (int)$_GET['low_stock'] : 0;

    $where  = [];
    $params = [];

    if ($search !== '') {
        $where[] = '(p.title LIKE :search_title OR p.description LIKE :search_desc)';
        $params['search_title'] = '%' . $search . '%';
        $params['search_desc']  = '%' . $search . '%';
    }
    if ($category > 0) {
        $where[] = 'p.category_id = :category_id';
        $params['category_id'] = $category;
    }
    if ($featured !== null) {
        $where[] = 'p.featured = :featured';
        $params['featured'] = $featured;
    }
    if ($status !== '') {
        $where[] = 'p.status = :status';
        $params['status'] = $status;
    } else {
        // Public listing only shows active products; admin sees all
        $isApiRequest = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false;
        if (!isAdmin()) {
            $where[] = 'p.status = :status';
            $params['status'] = 'active';
        }
    }
    if ($lowStock > 0) {
        $where[] = 'p.stock <= :low_stock';
        $params['low_stock'] = $lowStock;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p $whereSql");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $sql = "SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            $whereSql
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue(':' . $key, $val);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    jsonResponse([
        'success'     => true,
        'products'    => $stmt->fetchAll(),
        'pagination'  => [
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => $total,
            'total_pages' => (int)ceil($total / $perPage),
        ],
    ]);
}

function handleCreate(PDO $pdo): void
{
    $title       = clean($_POST['title'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $categoryId  = (int)($_POST['category_id'] ?? 0);
    $featured    = !empty($_POST['featured']) ? 1 : 0;
    $stock       = (int)($_POST['stock'] ?? 0);
    $sku         = clean($_POST['sku'] ?? '');
    $status      = clean($_POST['status'] ?? 'active');
    $image       = null;

    if ($title === '' || $price <= 0 || $categoryId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Title, price and category are required.'], 422);
    }

    if (!in_array($status, ['active', 'hidden'])) {
        $status = 'active';
    }

    if (!empty($_FILES['image'])) {
        $image = handleImageUpload($_FILES['image'], __DIR__ . '/../uploads');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO products (title, description, price, category_id, image, featured, stock, sku, status, created_at)
         VALUES (:title, :description, :price, :category_id, :image, :featured, :stock, :sku, :status, NOW())'
    );
    $stmt->execute([
        'title'       => $title,
        'description' => $description,
        'price'       => $price,
        'category_id' => $categoryId,
        'image'       => $image,
        'featured'    => $featured,
        'stock'       => $stock,
        'sku'         => $sku ?: null,
        'status'      => $status,
    ]);

    jsonResponse(['success' => true, 'message' => 'Product created.', 'id' => $pdo->lastInsertId()]);
}

function handleUpdate(PDO $pdo): void
{
    parse_str(file_get_contents('php://input'), $put);

    $id          = (int)($put['id'] ?? 0);
    $title       = clean($put['title'] ?? '');
    $description = clean($put['description'] ?? '');
    $price       = (float)($put['price'] ?? 0);
    $categoryId  = (int)($put['category_id'] ?? 0);
    $featured    = !empty($put['featured']) ? 1 : 0;
    $stock       = (int)($put['stock'] ?? 0);
    $sku         = clean($put['sku'] ?? '');
    $status      = clean($put['status'] ?? 'active');
    $image       = isset($put['image']) ? clean($put['image']) : null;

    if ($id <= 0 || $title === '' || $price <= 0 || $categoryId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Invalid product data.'], 422);
    }

    if (!in_array($status, ['active', 'hidden'])) {
        $status = 'active';
    }

    if ($image !== null && $image !== '') {
        $stmt = $pdo->prepare(
            'UPDATE products SET title=:title, description=:description, price=:price,
             category_id=:category_id, featured=:featured, image=:image, stock=:stock, sku=:sku, status=:status WHERE id=:id'
        );
        $stmt->execute([
            'title' => $title, 'description' => $description, 'price' => $price,
            'category_id' => $categoryId, 'featured' => $featured, 'image' => $image,
            'stock' => $stock, 'sku' => $sku ?: null, 'status' => $status, 'id' => $id,
        ]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE products SET title=:title, description=:description, price=:price,
             category_id=:category_id, featured=:featured, stock=:stock, sku=:sku, status=:status WHERE id=:id'
        );
        $stmt->execute([
            'title' => $title, 'description' => $description, 'price' => $price,
            'category_id' => $categoryId, 'featured' => $featured,
            'stock' => $stock, 'sku' => $sku ?: null, 'status' => $status, 'id' => $id,
        ]);
    }

    jsonResponse(['success' => true, 'message' => 'Product updated.']);
}

function handleDelete(PDO $pdo): void
{
    parse_str(file_get_contents('php://input'), $del);
    $id = (int)($del['id'] ?? ($_GET['id'] ?? 0));

    if ($id <= 0) {
        jsonResponse(['success' => false, 'message' => 'Product id is required.'], 422);
    }

    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);

    jsonResponse(['success' => true, 'message' => 'Product deleted.']);
}
