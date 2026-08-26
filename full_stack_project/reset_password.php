<?php
require_once __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    redirect('/task4/index.php');
}

$error = '';
$success = false;

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (empty($token) || empty($email)) {
    $error = "Invalid or missing token.";
} else {
    // Look up user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = "Invalid token.";
    } else {
        $token_hash = hash('sha256', $token);
        
        // Find valid token
        $stmt = $pdo->prepare("SELECT id FROM password_resets WHERE user_id = ? AND token_hash = ? AND expires_at > NOW() AND used_at IS NULL");
        $stmt->execute([$user['id'], $token_hash]);
        $reset_record = $stmt->fetch();
        
        if (!$reset_record) {
            $error = "Invalid or expired token.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error && !$success) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Hash and update
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user['id']]);
            
            $stmt = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
            $stmt->execute([$reset_record['id']]);
            
            $pdo->commit();
            $success = true;
            $_SESSION['flash_success'] = "Password reset successfully. You can now log in with your new password.";
            redirect('/task4/login.php');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<div class="form-container">
    <h2 class="form-title">Reset Password</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="/task4/forgot_password.php" class="btn btn-primary">Request New Link</a>
        </div>
    <?php elseif (!$success): ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            
            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
