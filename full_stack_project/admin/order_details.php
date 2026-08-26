<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found.";
    redirect('/task4/admin/orders.php');
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

$statuses = ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $new_status = $_POST['new_status'];
    if (in_array($new_status, $statuses)) {
        $up = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $up->execute([$new_status, $order_id]);
        $_SESSION['flash_success'] = "Order status updated.";
        redirect("/task4/admin/order_details.php?id=$order_id");
    }
}
?>

<div style="display: flex; gap: 2rem;">
    <!-- Admin Sidebar -->
    <aside style="width: 250px; flex-shrink: 0;">
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); position: sticky; top: 80px;">
            <h3 style="margin-bottom: 1rem; color: var(--text-main); font-size: 1.125rem;">Admin Panel</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                <li><a href="/task4/admin/dashboard.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Dashboard</a></li>
                <li><a href="/task4/admin/users.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Users</a></li>
                <li><a href="/task4/admin/products.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Products</a></li>
                <li><a href="/task4/admin/orders.php" style="display: block; padding: 0.5rem; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); border-radius: 4px; font-weight: 500;">Orders</a></li>
                <li><a href="/task4/admin/analytics.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Analytics</a></li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div style="flex-grow: 1;">
        <a href="/task4/admin/orders.php" style="display: inline-block; margin-bottom: 1.5rem; color: var(--text-muted);">&larr; Back to Orders</a>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2rem; color: var(--text-main); margin-bottom: 0.5rem;">Order #<?php echo $order['id']; ?></h1>
                <p style="color: var(--text-muted);">Placed on <?php echo date('F j, Y, H:i', strtotime($order['created_at'])); ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <form method="POST" action="" style="display: flex; gap: 1rem; align-items: center; margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                    <input type="hidden" name="update_status" value="1">
                    <label for="new_status" class="form-label" style="margin: 0;">Status:</label>
                    <select name="new_status" id="new_status" class="form-control" style="width: 150px;">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
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
                                <td style="padding: 1rem; font-weight: 500; color: var(--text-main);"><?php echo h($item['title']); ?></td>
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
            
            <!-- Details -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                    <h3 style="margin-bottom: 1rem; font-size: 1.125rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Customer Information</h3>
                    <p style="color: var(--text-main); font-weight: 500; margin-bottom: 0.25rem;"><?php echo h($order['customer_name']); ?></p>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem;"><a href="mailto:<?php echo h($order['customer_email']); ?>"><?php echo h($order['customer_email']); ?></a></p>
                </div>
                
                <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                    <h3 style="margin-bottom: 1rem; font-size: 1.125rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Shipping Information</h3>
                    <p style="color: var(--text-main); font-weight: 500; margin-bottom: 0.25rem;"><?php echo h($order['shipping_name']); ?></p>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem;"><?php echo h($order['shipping_phone']); ?></p>
                    <p style="color: var(--text-muted); white-space: pre-wrap; margin-top: 0.5rem;"><?php echo h($order['shipping_address']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
