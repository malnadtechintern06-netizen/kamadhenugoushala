<?php
// api/cart.php - JSON Endpoint for Asynchronous Cart Operations

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$cartId = get_or_create_cart_id();

try {
    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        // Check product availability
        $stmtP = $pdo->prepare("SELECT id, name, stock_quantity FROM products WHERE id = ?");
        $stmtP->execute([$productId]);
        $prod = $stmtP->fetch();

        if (!$prod) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }

        if ($prod['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Requested quantity exceeds available stock (' . $prod['stock_quantity'] . ').']);
            exit;
        }

        // Check if item already in cart
        $stmtCheck = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $stmtCheck->execute([$cartId, $productId]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update->execute([$newQty, $existing['id']]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$cartId, $productId, $quantity]);
        }

        echo json_encode([
            'success' => true,
            'message' => "'" . $prod['name'] . "' added to cart!",
            'cart_count' => get_cart_count()
        ]);
        exit;

    } elseif ($action === 'count') {
        echo json_encode(['success' => true, 'cart_count' => get_cart_count()]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid API action.']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
