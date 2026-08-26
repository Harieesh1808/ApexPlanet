<?php
// register.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    header("Location: /task5/dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = strtoupper(trim($_POST['captcha'] ?? ''));
    
    if (empty($name) || empty($email) || empty($password) || empty($captcha)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!isset($_SESSION['captcha']) || $captcha !== $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA code.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            // Default role is Student (id = 3)
            $role_id = 3;
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, password_hash, email_verified) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$role_id, $name, $email, $password_hash]);
                $user_id = $pdo->lastInsertId();
                
                // Set session
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role_name'] = 'Student';
                
                // Clear CAPTCHA
                unset($_SESSION['captcha']);
                
                header("Location: /task5/dashboard.php");
                exit;
            } catch (Exception $e) {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// Generate new CAPTCHA on every page load
$captcha_code = generateCaptcha();

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 400px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1.5rem;">Create an Account</h2>
    
    <?php if ($error): ?>
        <div style="background: #FEE2E2; color: var(--error-color); padding: 0.75rem; border-radius: 0.25rem; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="name">Full Name</label>
            <input type="text" id="name" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="email">Email Address</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="password">Password</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;" for="captcha">Security Code</label>
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
                <div style="background: #e2e8f0; padding: 0.75rem; font-family: monospace; font-size: 1.5rem; letter-spacing: 0.2rem; font-weight: bold; border-radius: 0.375rem; flex: 1; text-align: center;">
                    <?php echo htmlspecialchars($captcha_code); ?>
                </div>
            </div>
            <input type="text" id="captcha" name="captcha" required placeholder="Enter code above" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Sign Up</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem;">
        Already have an account? <a href="/task5/login.php" style="color: var(--primary-color); text-decoration: none;">Log In</a>
    </p>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
