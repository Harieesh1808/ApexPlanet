<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$user_id = $_SESSION['user_id'];

// Fetch orders
$stmt = $pdo->prepare("
    SELECT o.*, 
           (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) as item_count 
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div style="max-width: 1000px; margin: 0 auto; padding-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; color: var(--text-main);">My Orders</h1>
        <a href="/task4/profile.php" class="btn" style="background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main);">View Profile</a>
    </div>
    
    <?php if (empty($orders)): ?>
        <div style="background-color: var(--card-bg); padding: 3rem; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 1.125rem; margin-bottom: 1.5rem;">You haven't placed any orders yet.</p>
            <a href="/task4/products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Order ID</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Date</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Items</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Total</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Status</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 500; color: var(--text-main);">#<?php echo $order['id']; ?></td>
                            <td style="padding: 1rem; color: var(--text-muted);"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></td>
                            <td style="padding: 1rem; color: var(--text-muted);"><?php echo $order['item_count']; ?> items</td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td style="padding: 1rem;">
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
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: <?php echo $colors['bg']; ?>; color: <?php echo $colors['color']; ?>;">
                                    <?php echo h($order['status']); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <a href="/task4/order_details.php?id=<?php echo $order['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
