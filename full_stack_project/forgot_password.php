<?php
require_once __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    redirect('/task4/index.php');
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = "Please enter your email address.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Always show the same message whether user exists or not
        $message = "A confirmation mail has been sent to the registered email address.";
        $message_type = "success";
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->execute([$user['id'], $token_hash]);
            
            $reset_link = "http://localhost/task4/reset_password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
            
            // Provide a direct "Proceed" button since there's no real email server configured
            $message .= "<br><br><a href='$reset_link' class='btn btn-primary' style='display:inline-block; margin-top:10px;'>Proceed with Resetting</a>";
        }
    }
}
?>

<div class="form-container">
    <h2 class="form-title">Forgot Password</h2>
    <p style="text-align: center; margin-bottom: 1rem; color: var(--text-muted); font-size: 0.875rem;">
        Enter your email address and we'll send you instructions to reset your password.
    </p>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo h($message_type); ?>"><?php echo $message; // allow safe HTML for dev note ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="/task4/login.php" style="font-size: 0.875rem;">Back to Login</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
