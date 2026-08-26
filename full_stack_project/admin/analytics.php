<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Fetch orders by status
$status_counts = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

// Fill missing statuses with 0
$all_statuses = ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
foreach ($all_statuses as $s) {
    if (!isset($status_counts[$s])) $status_counts[$s] = 0;
}

// Fetch sales over last 7 days
$sales_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $sales_data[$date] = 0;
}
$recent_sales = $pdo->query("
    SELECT DATE(created_at) as date, SUM(total_amount) as total 
    FROM orders 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    AND status != 'Cancelled'
    GROUP BY DATE(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($recent_sales as $row) {
    if (isset($sales_data[$row['date']])) {
        $sales_data[$row['date']] = (float)$row['total'];
    }
}

// Fetch top selling books
$top_books = $pdo->query("
    SELECT p.title, SUM(oi.quantity) as sold 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    JOIN orders o ON oi.order_id = o.id 
    WHERE o.status != 'Cancelled'
    GROUP BY oi.product_id 
    ORDER BY sold DESC 
    LIMIT 5
")->fetchAll();

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
                <li><a href="/task4/admin/orders.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Orders</a></li>
                <li><a href="/task4/admin/analytics.php" style="display: block; padding: 0.5rem; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); border-radius: 4px; font-weight: 500;">Analytics</a></li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div style="flex-grow: 1;">
        <h1 style="font-size: 2rem; color: var(--text-main); margin-bottom: 2rem;">Analytics & Reports</h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Order Status Breakdown -->
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--text-main);">Order Statuses</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php 
                    $max = max($status_counts) ?: 1;
                    foreach ($status_counts as $status => $count): 
                        $width = ($count / $max) * 100;
                        $color = 'var(--primary-color)';
                        if ($status === 'Delivered') $color = 'var(--secondary-color)';
                        if ($status === 'Cancelled') $color = 'var(--danger)';
                        if ($status === 'Pending') $color = '#F59E0B';
                    ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <span><?php echo $status; ?></span>
                                <span style="font-weight: 600;"><?php echo $count; ?></span>
                            </div>
                            <div style="width: 100%; height: 8px; background-color: var(--dark-bg); border-radius: 4px; overflow: hidden;">
                                <div style="width: <?php echo $width; ?>%; height: 100%; background-color: <?php echo $color; ?>;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Top Selling Books -->
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                <h3 style="margin-bottom: 1.5rem; color: var(--text-main);">Top Selling Books</h3>
                <?php if (empty($top_books)): ?>
                    <p style="color: var(--text-muted);">No sales data available yet.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($top_books as $index => $book): ?>
                            <li style="display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                                <span style="font-size: 1.25rem; font-weight: 700; color: var(--text-muted); width: 20px; text-align: center;"><?php echo $index + 1; ?></span>
                                <div style="flex-grow: 1;">
                                    <p style="color: var(--text-main); font-weight: 500;"><?php echo h($book['title']); ?></p>
                                </div>
                                <span style="background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    <?php echo $book['sold']; ?> sold
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Last 7 Days Revenue -->
        <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--text-main);">Revenue (Last 7 Days)</h3>
            <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 0.5rem; height: 200px; padding-top: 2rem;">
                <?php 
                $max_revenue = max($sales_data) ?: 1;
                foreach ($sales_data as $date => $revenue): 
                    $height = ($revenue / $max_revenue) * 100;
                ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; height: 100%;">
                        <div style="width: 100%; max-width: 40px; height: 100%; display: flex; align-items: flex-end; justify-content: center; position: relative;">
                            <div style="width: 100%; height: <?php echo max($height, 1); ?>%; background-color: var(--secondary-color); border-radius: 4px 4px 0 0; transition: height 0.3s;" title="$<?php echo number_format($revenue, 2); ?>"></div>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-muted); transform: rotate(-45deg); margin-top: 0.5rem; white-space: nowrap;">
                            <?php echo date('M d', strtotime($date)); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
