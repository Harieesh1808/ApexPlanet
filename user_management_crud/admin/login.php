<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (isLoggedIn() && isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'admin') {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.password_hash, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                if ($user['role_name'] === 'admin') {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role_name'] = $user['role_name'];
                    
                    redirect('dashboard.php');
                } else {
                    $error = 'Access denied. You do not have admin privileges.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$page_title = 'Admin Login - UMS';
$base_path = '../';
include '../includes/header.php';
?>

<div class="card">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo sanitize($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login as Admin</button>
    </form>
    <div class="text-center mt-4">
        <p><a href="../login.php">Back to User Login</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
