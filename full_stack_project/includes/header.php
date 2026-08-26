<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Generate CSRF token for forms
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/task4/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="/task4/index.php" class="nav-brand">
        📚 <span>Bookstore</span>
    </a>
    <div class="nav-links">
        <a href="/task4/products.php" class="nav-link">Books</a>
        <a href="/task4/cart.php" class="nav-link">Cart</a>
        <?php if (is_logged_in()): ?>
            <a href="/task4/orders.php" class="nav-link">My Orders</a>
            <a href="/task4/profile.php" class="nav-link">Profile</a>
            <?php if (is_admin()): ?>
                <a href="/task4/admin/dashboard.php" class="nav-link" style="color: var(--secondary-color);">Admin</a>
            <?php endif; ?>
            <a href="/task4/logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Logout</a>
        <?php else: ?>
            <a href="/task4/login.php" class="nav-link">Login</a>
            <a href="/task4/register.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?php echo h($_SESSION['flash_success']); ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-error">
            <?php echo h($_SESSION['flash_error']); ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
