<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo    = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

requireLogin();

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handleAdd($pdo);
        break;
    case 'PUT':
        handleUpdate($pdo);
        break;
    case 'DELETE':
        handleDelete($pdo);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
}

function getCart(PDO $pdo): array
{
    $userId = (int)$_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    $cart = $stmt->fetch();

    if ($cart) {
        return $cart;
    }

    $stmt = $pdo->prepare('INSERT INTO carts (user_id, created_at) VALUES (:user_id, NOW())');
    $stmt->execute(['user_id' => $userId]);
    return ['id' => (int)$pdo->lastInsertId()];
}

function handleGet(PDO $pdo): void
{
    $cart = getCart($pdo);
    $cartId = (int)$cart['id'];

    $stmt = $pdo->prepare(
        'SELECT ci.id, ci.product_id, ci.quantity, p.title, p.price, p.image, p.stock, p.sku,
                (ci.quantity * p.price) AS subtotal
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.cart_id = :cart_id
         ORDER BY ci.created_at ASC'
    );
    $stmt->execute(['cart_id' => $cartId]);
    $items = $stmt->fetchAll();

    $total = 0;
    foreach ($items as &$item) {
        $item['subtotal'] = (float)$item['subtotal'];
        $total += $item['subtotal'];
    }
    unset($item);

    $count = 0;
    foreach ($items as $item) {
        $count += (int)$item['quantity'];
    }

    jsonResponse([
        'success' => true,
        'cart' => [
            'id' => $cartId,
            'items' => $items,
            'total' => $total,
            'count' => $count,
        ]
    ]);
}

function handleAdd(PDO $pdo): void
{
    $data      = getJsonInput();
    $productId = (int)($data['product_id'] ?? 0);
    $quantity  = max(1, (int)($data['quantity'] ?? 1));

    if ($productId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Product ID is required.'], 422);
    }

    // Check product exists and has stock
    $stmt = $pdo->prepare('SELECT id, title, stock, status FROM products WHERE id = :id');
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(['success' => false, 'message' => 'Product not found.'], 404);
    }

    if ($product['status'] !== 'active') {
        jsonResponse(['success' => false, 'message' => 'Product is not available.'], 400);
    }

    if ((int)$product['stock'] < $quantity) {
        jsonResponse(['success' => false, 'message' => 'Not enough stock available.'], 400);
    }

    $cart = getCart($pdo);
    $cartId = (int)$cart['id'];

    // Check if item already in cart
    $stmt = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id');
    $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty = (int)$existing['quantity'] + $quantity;
        if ($newQty > (int)$product['stock']) {
            jsonResponse(['success' => false, 'message' => 'Cannot add more than available stock.'], 400);
        }
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
        $stmt->execute(['quantity' => $newQty, 'id' => $existing['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity, created_at) VALUES (:cart_id, :product_id, :quantity, NOW())');
        $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId, 'quantity' => $quantity]);
    }

    // Update cart timestamp
    $pdo->prepare('UPDATE carts SET updated_at = NOW() WHERE id = :id')->execute(['id' => $cartId]);

    // Log cart activity for admin
    $stmtLog = $pdo->prepare(
        'INSERT INTO cart_activity (user_id, product_id, quantity, created_at) VALUES (:user_id, :product_id, :quantity, NOW())'
    );
    $stmtLog->execute([
        'user_id'    => (int)$_SESSION['user_id'],
        'product_id' => $productId,
        'quantity'   => $quantity,
    ]);

    jsonResponse(['success' => true, 'message' => 'Item added to cart.']);
}

function handleUpdate(PDO $pdo): void
{
    $data = getJsonInput();
    $itemId = (int)($data['item_id'] ?? 0);
    $quantity = max(1, (int)($data['quantity'] ?? 1));
    $action = clean($data['action'] ?? '');

    if ($itemId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Item ID is required.'], 422);
    }

    // Get cart item
    $stmt = $pdo->prepare(
        'SELECT ci.id, ci.cart_id, ci.product_id, ci.quantity, p.stock
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.id = :id'
    );
    $stmt->execute(['id' => $itemId]);
    $item = $stmt->fetch();

    if (!$item) {
        jsonResponse(['success' => false, 'message' => 'Cart item not found.'], 404);
    }

    // Verify cart belongs to user
    $stmt = $pdo->prepare('SELECT id FROM carts WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $item['cart_id'], 'user_id' => (int)$_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 403);
    }

    if ($action === 'increase') {
        $newQty = (int)$item['quantity'] + 1;
        if ($newQty > (int)$item['stock']) {
            jsonResponse(['success' => false, 'message' => 'Not enough stock.'], 400);
        }
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
        $stmt->execute(['quantity' => $newQty, 'id' => $itemId]);
    } elseif ($action === 'decrease') {
        $newQty = max(1, (int)$item['quantity'] - 1);
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
        $stmt->execute(['quantity' => $newQty, 'id' => $itemId]);
    } elseif ($action === 'update') {
        if ($quantity > (int)$item['stock']) {
            jsonResponse(['success' => false, 'message' => 'Not enough stock.'], 400);
        }
        $stmt = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
        $stmt->execute(['quantity' => $quantity, 'id' => $itemId]);
    }

    $pdo->prepare('UPDATE carts SET updated_at = NOW() WHERE id = :id')->execute(['id' => $item['cart_id']]);

    jsonResponse(['success' => true, 'message' => 'Cart updated.']);
}

function handleDelete(PDO $pdo): void
{
    $data = getJsonInput();
    $itemId = (int)($data['item_id'] ?? 0);
    $clearAll = !empty($data['clear_all']);

    // Get cart for user
    $stmt = $pdo->prepare('SELECT id FROM carts WHERE user_id = :user_id');
    $stmt->execute(['user_id' => (int)$_SESSION['user_id']]);
    $cart = $stmt->fetch();

    if (!$cart) {
        jsonResponse(['success' => true, 'message' => 'Cart is empty.']);
    }

    if ($clearAll) {
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id');
        $stmt->execute(['cart_id' => $cart['id']]);
        jsonResponse(['success' => true, 'message' => 'Cart cleared.']);
    }

    if ($itemId <= 0) {
        jsonResponse(['success' => false, 'message' => 'Item ID is required.'], 422);
    }

    $stmt = $pdo->prepare('DELETE ci FROM cart_items ci JOIN carts c ON c.id = ci.cart_id WHERE ci.id = :id AND c.user_id = :user_id');
    $stmt->execute(['id' => $itemId, 'user_id' => (int)$_SESSION['user_id']]);

    jsonResponse(['success' => true, 'message' => 'Item removed from cart.']);
}
