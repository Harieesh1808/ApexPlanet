<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Verify order belongs to user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found or access denied.";
    redirect('/task4/orders.php');
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.title, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<div style="max-width: 1000px; margin: 0 auto; padding-top: 2rem;">
    <a href="/task4/orders.php" style="display: inline-block; margin-bottom: 1.5rem; color: var(--text-muted);">&larr; Back to Orders</a>
    
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; color: var(--text-main); margin-bottom: 0.5rem;">Order #<?php echo $order['id']; ?></h1>
            <p style="color: var(--text-muted);">Placed on <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
        </div>
        <div>
            <?php
            $status_colors = [
                'Pending' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'color' => '#F59E0B'],
                'Confirmed' => ['bg' => 'rgba(59, 130, 246, 0.1)', 'color' => '#3B82F6'],
                'Processing' => ['bg' => 'rgba(139, 92, 246, 0.1)', 'color' => '#8B5CF6'],
                'Shipped' => ['bg' => 'rgba(16, 185, 129, 0.1)', 'color' => '#10B981'],
                'Delivered' => ['bg' => 'rgba(5, 150, 105, 0.1)', 'color' => '#059669'],
                'Cancelled' => ['bg' => 'rgba(239, 68, 68, 0.1)', 'color' => '#EF4444'],
            ];
            $colors = $status_colors[$order['status']] ?? $status_colors['Pending'];
            ?>
            <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; background-color: <?php echo $colors['bg']; ?>; color: <?php echo $colors['color']; ?>;">
                Status: <?php echo h($order['status']); ?>
            </span>
        </div>
    </div>
    
    <div style="display: flex; gap: 2rem; flex-direction: column; md:flex-direction: row;">
        <!-- Order Items -->
        <div style="flex: 2; background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Product</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Price</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Qty</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if ($item['image']): ?>
                                        <img src="/task4/uploads/products/<?php echo h($item['image']); ?>" alt="<?php echo h($item['title']); ?>" style="width: 50px; height: 65px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 65px; background-color: var(--dark-bg); border-radius: 4px;"></div>
                                    <?php endif; ?>
                                    <a href="/task4/product_details.php?id=<?php echo $item['product_id']; ?>" style="color: var(--text-main); font-weight: 500;">
                                        <?php echo h($item['title']); ?>
                                    </a>
                                </div>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted);">$<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td style="padding: 1rem; color: var(--text-main);"><?php echo $item['quantity']; ?></td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main); text-align: right;">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="padding: 1.5rem; background-color: var(--darker-bg); display: flex; justify-content: flex-end; align-items: center; border-top: 1px solid var(--border-color);">
                <span style="color: var(--text-muted); font-size: 1.125rem; margin-right: 1rem;">Total Amount:</span>
                <span style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">$<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <!-- Shipping Details -->
        <div style="flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1.125rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Shipping Information</h3>
                <p style="color: var(--text-main); font-weight: 500; margin-bottom: 0.25rem;"><?php echo h($order['shipping_name']); ?></p>
                <p style="color: var(--text-muted); margin-bottom: 0.25rem;"><?php echo h($order['shipping_phone']); ?></p>
                <p style="color: var(--text-muted); white-space: pre-wrap; margin-top: 0.5rem;"><?php echo h($order['shipping_address']); ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1.125rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Payment Method</h3>
                <p style="color: var(--text-muted);">Cash on Delivery / Invoice</p>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">(Payment integration pending)</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
