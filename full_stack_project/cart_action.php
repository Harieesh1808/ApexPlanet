<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/task4/cart.php');
}

verify_csrf_token($_POST['csrf_token'] ?? '');

$action = $_POST['action'] ?? 'add';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($product_id <= 0) {
    $_SESSION['flash_error'] = "Invalid product.";
    redirect('/task4/products.php');
}

// Fetch product to verify stock
$stmt = $pdo->prepare("SELECT title, stock_quantity FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['flash_error'] = "Product not found.";
    redirect('/task4/products.php');
}

if ($action === 'add') {
    $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
    $new_qty = $current_qty + $quantity;
    
    if ($new_qty > $product['stock_quantity']) {
        $_SESSION['flash_error'] = "Not enough stock available for '" . h($product['title']) . "'. Maximum available: " . $product['stock_quantity'];
    } else {
        $_SESSION['cart'][$product_id] = $new_qty;
        $_SESSION['flash_success'] = "Added to cart.";
    }
    redirect('/task4/product_details.php?id=' . $product_id);
} 
elseif ($action === 'update') {
    if ($quantity > 0) {
        if ($quantity > $product['stock_quantity']) {
            $_SESSION['flash_error'] = "Not enough stock available. Maximum available: " . $product['stock_quantity'];
            $_SESSION['cart'][$product_id] = $product['stock_quantity'];
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
            $_SESSION['flash_success'] = "Cart updated.";
        }
    } else {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['flash_success'] = "Item removed from cart.";
    }
    redirect('/task4/cart.php');
} 
elseif ($action === 'remove') {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['flash_success'] = "Item removed from cart.";
    }
    redirect('/task4/cart.php');
}
else {
    redirect('/task4/cart.php');
}
?>
