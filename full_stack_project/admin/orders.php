<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Filter
$status_filter = $_GET['status'] ?? '';

$where = ["1=1"];
$params = [];

if ($status_filter) {
    $where[] = "o.status = ?";
    $params[] = $status_filter;
}

$where_sql = implode(' AND ', $where);

// Count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders o WHERE $where_sql");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Fetch orders
$sql = "SELECT o.*, u.name as customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE $where_sql 
        ORDER BY o.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statuses = ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    if (in_array($new_status, $statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $order_id])) {
            $_SESSION['flash_success'] = "Order #$order_id status updated to $new_status.";
        }
    }
    redirect('/task4/admin/orders.php');
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; color: var(--text-main);">Manage Orders</h1>
        </div>
        
        <!-- Filters -->
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
            <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label class="form-label" for="status">Filter by Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $status_filter === $s ? 'selected' : ''; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Filter</button>
                <a href="/task4/admin/orders.php" class="btn" style="padding: 0.75rem 1.5rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">Clear</a>
            </form>
        </div>
        
        <!-- Orders Table -->
        <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Order ID</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Customer</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Date</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Total</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Status</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; font-weight: 500; color: var(--text-main);">#<?php echo $order['id']; ?></td>
                                <td style="padding: 1rem; color: var(--text-main);"><?php echo h($order['customer_name']); ?></td>
                                <td style="padding: 1rem; color: var(--text-muted);"><?php echo date('M j, Y H:i', strtotime($order['created_at'])); ?></td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td style="padding: 1rem;">
                                    <form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;" onchange="this.form.submit()">
                                            <?php foreach ($statuses as $s): ?>
                                                <option value="<?php echo $s; ?>" <?php echo $order['status'] === $s ? 'selected' : ''; ?>>
                                                    <?php echo $s; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <a href="/task4/admin/order_details.php?id=<?php echo $order['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
