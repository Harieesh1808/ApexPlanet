<?php
// dashboard.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin(); // Ensure logged in

// Fetch user data
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<div style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h1 style="color: var(--primary-color); margin-bottom: 1rem;">Dashboard</h1>
    <p style="font-size: 1.25rem;">Welcome back, <strong><?php echo htmlspecialchars($user['name']); ?></strong>!</p>
    <p style="color: var(--text-muted); margin-top: 1rem;">You are logged in as: <?php echo htmlspecialchars($_SESSION['role_name']); ?></p>
    
    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <?php if (hasRole('Student')): ?>
            <a href="/task5/courses.php" class="btn btn-primary">Browse Courses</a>
        <?php elseif (hasRole('Instructor')): ?>
            <a href="/task5/instructor/courses.php" class="btn btn-primary">Manage My Courses</a>
        <?php elseif (hasRole('Admin')): ?>
            <a href="/task5/admin/index.php" class="btn btn-primary">Admin Panel</a>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
