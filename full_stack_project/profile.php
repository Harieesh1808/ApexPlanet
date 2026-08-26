<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, profile_picture, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash_error'] = "User not found.";
    redirect('/task4/logout.php');
}

// Fetch order stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
?>

<div style="max-width: 800px; margin: 2rem auto;">
    <h1 style="font-size: 2rem; color: var(--text-main); margin-bottom: 2rem;">My Profile</h1>
    
    <div style="display: flex; gap: 2rem; flex-direction: column; md:flex-direction: row;">
        <!-- Profile Card -->
        <div style="flex: 1; background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 2rem; text-align: center;">
            <div style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto 1.5rem; border: 3px solid var(--border-color); background-color: var(--dark-bg);">
                <?php if ($user['profile_picture']): ?>
                    <img src="/task4/uploads/profile/<?php echo h($user['profile_picture']); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--text-muted);">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <h2 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 0.5rem;"><?php echo h($user['name']); ?></h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;"><?php echo h($user['email']); ?></p>
            
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
            </p>
            
            <a href="/task4/edit_profile.php" class="btn btn-primary btn-block">Edit Profile</a>
        </div>
        
        <!-- Stats and Actions -->
        <div style="flex: 2; display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem; display: flex; justify-content: space-around; text-align: center;">
                <div>
                    <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Orders</p>
                    <p style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?php echo $stats['total_orders'] ?: '0'; ?></p>
                </div>
                <div>
                    <p style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Spent</p>
                    <p style="font-size: 2rem; font-weight: 700; color: var(--text-main);">$<?php echo number_format($stats['total_spent'] ?: 0, 2); ?></p>
                </div>
            </div>
            
            <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1.125rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Quick Links</h3>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="/task4/orders.php" style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">&rarr; View Order History</a></li>
                    <li><a href="/task4/products.php" style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">&rarr; Browse Catalog</a></li>
                    <li><a href="/task4/cart.php" style="color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">&rarr; View Shopping Cart</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
