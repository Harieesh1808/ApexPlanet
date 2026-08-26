<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="/task5/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <a href="/task5/index.php">EduPortal</a>
    </div>
    <div class="navbar-nav">
        <a href="/task5/courses.php">Browse Courses</a>
        <?php if (isLoggedIn()): ?>
            <a href="/task5/dashboard.php">Dashboard</a>
            <a href="/task5/logout.php" class="btn btn-primary">Logout</a>
        <?php else: ?>
            <a href="/task5/login.php">Log In</a>
            <a href="/task5/register.php" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
