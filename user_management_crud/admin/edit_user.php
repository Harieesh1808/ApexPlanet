<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = $_POST['role_id'] ?? 2;
    
    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email is already taken.';
        } else {
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters.';
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET role_id=?, name=?, email=?, password_hash=? WHERE id=?");
                    $stmt->bind_param("isssi", $role_id, $name, $email, $password_hash, $id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE users SET role_id=?, name=?, email=? WHERE id=?");
                $stmt->bind_param("issi", $role_id, $name, $email, $id);
            }
            
            if (!$error && $stmt->execute()) {
                $success = 'User updated successfully.';
                // If editing self, update session
                if ($id === $_SESSION['user_id']) {
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    // Also update role if changed
                    $role_stmt = $conn->query("SELECT role_name FROM roles WHERE id = " . (int)$role_id);
                    if ($role_row = $role_stmt->fetch_assoc()) {
                        $_SESSION['role_name'] = $role_row['role_name'];
                    }
                }
            } elseif (!$error) {
                $error = 'Failed to update user.';
            }
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$edit_user = $stmt->get_result()->fetch_assoc();

if (!$edit_user) {
    die("User not found.");
}

$roles = $conn->query("SELECT * FROM roles")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Edit User - Admin';
$base_path = '../';
include '../includes/header.php';
?>

<div class="card" style="max-width: 600px;">
    <h2>Edit User</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo sanitize($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo sanitize($success); ?></div>
        <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
    <?php else: ?>
    
    <form method="POST" action="edit_user.php?id=<?php echo $id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required value="<?php echo sanitize($edit_user['name']); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?php echo sanitize($edit_user['email']); ?>">
        </div>
        <div class="form-group">
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="role_id">Role</label>
            <select id="role_id" name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>" <?php echo ($edit_user['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                        <?php echo ucfirst(sanitize($role['role_name'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary mt-4">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
    </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
