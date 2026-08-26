<?php
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total = 0;

if (!empty($cart)) {
    // Get product details for all items in cart
    $placeholders = str_repeat('?,', count($cart) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, title, price, image, stock_quantity FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        $qty = $cart[$product['id']];
        
        // Adjust if requested more than stock
        if ($qty > $product['stock_quantity']) {
            $qty = $product['stock_quantity'];
            $_SESSION['cart'][$product['id']] = $qty;
        }
        
        $subtotal = $qty * $product['price'];
        $total += $subtotal;
        
        $product['cart_quantity'] = $qty;
        $product['subtotal'] = $subtotal;
        $cart_items[] = $product;
    }
}
?>

<div style="max-width: 900px; margin: 0 auto; padding-top: 2rem;">
    <h1 style="font-size: 2rem; margin-bottom: 2rem; color: var(--text-main);">Your Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div style="background-color: var(--card-bg); padding: 3rem; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 1.125rem; margin-bottom: 1.5rem;">Your cart is currently empty.</p>
            <a href="/task4/products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Product</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Price</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Quantity</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Subtotal</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if ($item['image']): ?>
                                        <img src="/task4/uploads/products/<?php echo h($item['image']); ?>" alt="<?php echo h($item['title']); ?>" style="width: 50px; height: 65px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 65px; background-color: var(--dark-bg); border-radius: 4px;"></div>
                                    <?php endif; ?>
                                    <a href="/task4/product_details.php?id=<?php echo $item['id']; ?>" style="color: var(--text-main); font-weight: 500;">
                                        <?php echo h($item['title']); ?>
                                    </a>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted);">$<?php echo number_format($item['price'], 2); ?></td>
                            <td style="padding: 1rem;">
                                <form action="/task4/cart_action.php" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['cart_quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="form-control" style="width: 70px; padding: 0.25rem 0.5rem; text-align: center;">
                                    <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">Update</button>
                                </form>
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td style="padding: 1rem; text-align: right;">
                                <form action="/task4/cart_action.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; background-color: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); font-size: 0.75rem;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="padding: 2rem; background-color: var(--darker-bg); display: flex; justify-content: flex-end; align-items: center; gap: 2rem;">
                <div style="text-align: right;">
                    <span style="color: var(--text-muted); font-size: 1.125rem;">Total:</span>
                    <span style="font-size: 1.75rem; font-weight: 700; color: var(--text-main); margin-left: 1rem;">$<?php echo number_format($total, 2); ?></span>
                </div>
                <a href="/task4/checkout.php" class="btn btn-primary" style="font-size: 1.125rem;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
