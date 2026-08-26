<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$page_title = 'Welcome - User Management System';
$base_path = '';
include 'includes/header.php';
?>

<div class="text-center mt-4">
    <h2>Welcome to User Management System</h2>
    <p class="mt-2 text-muted">A complete PHP & MySQL authentication and user management solution.</p>
    
    <div class="mt-4 gap-2 flex" style="justify-content: center;">
        <?php if (!isLoggedIn()): ?>
            <a href="admin/login.php" class="btn btn-primary">Admin Login</a>
            <a href="login.php" class="btn btn-primary">User Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
