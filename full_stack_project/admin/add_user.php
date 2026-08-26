<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = (int)($_POST['role_id'] ?? 2);
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "An account with that email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hash, $role_id])) {
                $_SESSION['flash_success'] = "User added successfully.";
                redirect('/task4/admin/users.php');
            } else {
                $error = "Failed to add user.";
            }
        }
    }
}
?>

<div class="form-container" style="max-width: 500px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="form-title" style="margin-bottom: 0;">Add New User</h2>
        <a href="/task4/admin/users.php" style="color: var(--text-muted); font-size: 0.875rem;">&larr; Back</a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
        
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required value="<?php echo h($_POST['name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?php echo h($_POST['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="role_id">Role</label>
            <select id="role_id" name="role_id" class="form-control">
                <option value="2">User</option>
                <option value="1">Admin</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Add User</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
