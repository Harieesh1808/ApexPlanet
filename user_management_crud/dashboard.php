<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$page_title = 'Dashboard - UMS';
$base_path = '';
include 'includes/header.php';
?>

<div class="card" style="max-width: 800px;">
    <h2>Welcome to your Dashboard, <?php echo sanitize($user['name']); ?>!</h2>
    <div class="flex gap-2 mt-4" style="align-items: center;">
        <?php if (!empty($user['profile_picture'])): ?>
            <img src="<?php echo sanitize($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
        <?php else: ?>
            <div class="profile-pic flex" style="align-items: center; justify-content: center; background: var(--secondary-color); font-size: 3rem; color: var(--text-muted);">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-left: 2rem;">
            <p><strong>Name:</strong> <?php echo sanitize($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo sanitize($user['email']); ?></p>
            <p><strong>Role:</strong> <span style="text-transform: capitalize;"><?php echo sanitize($_SESSION['role_name']); ?></span></p>
            <p><strong>Member since:</strong> <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="profile.php" class="btn btn-primary">Edit Profile</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
