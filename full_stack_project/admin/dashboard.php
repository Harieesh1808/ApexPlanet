<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Fetch admin stats
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?: 0,
    'low_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 5")->fetchColumn()
];

// Fetch recent orders
$recent_orders = $pdo->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
")->fetchAll();
?>

<div style="display: flex; gap: 2rem;">
    <!-- Admin Sidebar -->
    <aside style="width: 250px; flex-shrink: 0;">
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); position: sticky; top: 80px;">
            <h3 style="margin-bottom: 1rem; color: var(--text-main); font-size: 1.125rem;">Admin Panel</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                <li><a href="/task4/admin/dashboard.php" style="display: block; padding: 0.5rem; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); border-radius: 4px; font-weight: 500;">Dashboard</a></li>
                <li><a href="/task4/admin/users.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Users</a></li>
                <li><a href="/task4/admin/products.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Products</a></li>
                <li><a href="/task4/admin/orders.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Orders</a></li>
                <li><a href="/task4/admin/analytics.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Analytics</a></li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div style="flex-grow: 1;">
        <h1 style="font-size: 2rem; color: var(--text-main); margin-bottom: 2rem;">Admin Dashboard</h1>
        
        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Total Revenue</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--secondary-color);">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Total Orders</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?php echo $stats['total_orders']; ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Pending Orders</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--danger);"><?php echo $stats['pending_orders']; ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Total Users</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?php echo $stats['total_users']; ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Products</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?php echo $stats['total_products']; ?></p>
            </div>
            
            <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; margin-bottom: 0.5rem;">Low Stock Items</p>
                <p style="font-size: 2rem; font-weight: 700; color: var(--danger);"><?php echo $stats['low_stock']; ?></p>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <h2 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 1.5rem;">Recent Orders</h2>
        
        <?php if (empty($recent_orders)): ?>
            <p style="color: var(--text-muted);">No recent orders found.</p>
        <?php else: ?>
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                        <tr>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Order ID</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Customer</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Date</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Total</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Status</th>
                            <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; font-weight: 500; color: var(--text-main);">#<?php echo $order['id']; ?></td>
                                <td style="padding: 1rem; color: var(--text-muted);"><?php echo h($order['customer_name']); ?></td>
                                <td style="padding: 1rem; color: var(--text-muted);"><?php echo date('M j, Y H:i', strtotime($order['created_at'])); ?></td>
                                <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td style="padding: 1rem;">
                                    <span style="font-size: 0.875rem; font-weight: 500;"><?php echo h($order['status']); ?></span>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <a href="/task4/admin/order_details.php?id=<?php echo $order['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 1rem; text-align: right;">
                <a href="/task4/admin/orders.php" style="color: var(--primary-color);">View all orders &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
