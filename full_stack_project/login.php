<?php
require_once __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    redirect('/task4/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id, role_id, name, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['user_name'] = $user['name'];
            
            $_SESSION['flash_success'] = "Welcome back, " . h($user['name']) . "!";
            
            if ($user['role_id'] == 1) {
                redirect('/task4/admin/dashboard.php');
            } else {
                redirect('/task4/index.php');
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="form-container">
    <h2 class="form-title">Log In</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?php echo h($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <div style="text-align: right; margin-top: 0.25rem;">
                <a href="/task4/forgot_password.php" style="font-size: 0.875rem;">Forgot Password?</a>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
        
        <p style="text-align: center; margin-top: 1rem; font-size: 0.875rem;">
            Don't have an account? <a href="/task4/register.php">Register</a>
        </p>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
