<?php
if (!isset($base_path)) {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'User Management System'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container nav-container">
            <h1><a href="<?php echo $base_path; ?>index.php" class="logo">UMS</a></h1>
            <nav>
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?php echo $base_path; ?>dashboard.php">Dashboard</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="<?php echo $base_path; ?>admin/dashboard.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_path; ?>logout.php" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <?php 
                        $current_page = basename($_SERVER['PHP_SELF']);
                        if (!in_array($current_page, ['index.php', 'login.php'])): 
                        ?>
                            <li><a href="<?php echo $base_path; ?>login.php">Login</a></li>
                            <li><a href="<?php echo $base_path; ?>register.php" class="btn btn-primary">Register</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container main-content">
