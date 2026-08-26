<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['flash_error'] = "Your cart is empty.";
    redirect('/task4/products.php');
}

$error = '';

// Calculate total and fetch products (similar to cart.php)
$placeholders = str_repeat('?,', count($cart) - 1) . '?';
$stmt = $pdo->prepare("SELECT id, title, price, stock_quantity FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_keys($cart));
$products = $stmt->fetchAll();

$cart_items = [];
$total_amount = 0;

foreach ($products as $product) {
    $qty = $cart[$product['id']];
    if ($qty > $product['stock_quantity']) {
        $error = "Not enough stock for '" . h($product['title']) . "'. Please adjust your cart.";
        $qty = $product['stock_quantity']; // Auto adjust
        $_SESSION['cart'][$product['id']] = $qty;
        if ($qty == 0) unset($_SESSION['cart'][$product['id']]);
    }
    
    $subtotal = $qty * $product['price'];
    $total_amount += $subtotal;
    
    $product['cart_quantity'] = $qty;
    $product['subtotal'] = $subtotal;
    $cart_items[] = $product;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $name = trim($_POST['shipping_name'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $phone = trim($_POST['shipping_phone'] ?? '');
    
    if (empty($name) || empty($address) || empty($phone)) {
        $error = "All shipping fields are required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1. Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_name, shipping_address, shipping_phone) VALUES (?, ?, 'Pending', ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total_amount, $name, $address, $phone]);
            $order_id = $pdo->lastInsertId();
            
            // 2. Insert order items & reduce stock
            $insert_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $update_stock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
            
            foreach ($cart_items as $item) {
                // Insert item
                $insert_item->execute([$order_id, $item['id'], $item['cart_quantity'], $item['price'], $item['subtotal']]);
                
                // Reduce stock
                $update_stock->execute([$item['cart_quantity'], $item['id'], $item['cart_quantity']]);
                if ($update_stock->rowCount() === 0) {
                    throw new Exception("Failed to update stock for product ID {$item['id']}. It may be out of stock.");
                }
            }
            
            $pdo->commit();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $_SESSION['flash_success'] = "Your order (#$order_id) has been placed successfully!";
            redirect('/task4/orders.php');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<div style="max-width: 1000px; margin: 0 auto; padding-top: 2rem;">
    <h1 style="font-size: 2rem; margin-bottom: 2rem; color: var(--text-main);">Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>
    
    <div style="display: flex; gap: 2rem; flex-direction: column; md:flex-direction: row;">
        <!-- Order Summary -->
        <div style="flex: 1; background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 2rem; align-self: flex-start; order: 2;">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">Order Summary</h3>
            
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                <?php foreach ($cart_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; font-size: 0.875rem;">
                        <div style="flex: 1; padding-right: 1rem;">
                            <p style="color: var(--text-main); font-weight: 500; margin-bottom: 0.25rem;"><?php echo h($item['title']); ?></p>
                            <p style="color: var(--text-muted);">Qty: <?php echo $item['cart_quantity']; ?> x $<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div style="font-weight: 600; color: var(--text-main);">
                            $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; font-size: 1.25rem;">
                <span style="font-weight: 500;">Total</span>
                <span style="font-weight: 700; color: var(--text-main);">$<?php echo number_format($total_amount, 2); ?></span>
            </div>
        </div>
        
        <!-- Shipping Form -->
        <div style="flex: 2; background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 2rem; order: 1;">
            <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Shipping Information</h3>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="shipping_name">Full Name</label>
                    <input type="text" id="shipping_name" name="shipping_name" class="form-control" required value="<?php echo h($_POST['shipping_name'] ?? $_SESSION['user_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="shipping_phone">Phone Number</label>
                    <input type="text" id="shipping_phone" name="shipping_phone" class="form-control" required value="<?php echo h($_POST['shipping_phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="shipping_address">Delivery Address</label>
                    <textarea id="shipping_address" name="shipping_address" class="form-control" rows="4" required><?php echo h($_POST['shipping_address'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 2rem; font-size: 1.125rem;">Place Order</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
