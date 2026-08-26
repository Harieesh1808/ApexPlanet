<?php
require_once __DIR__ . '/includes/header.php';
require_login();

$user_id = $_SESSION['user_id'];
$error = '';

$stmt = $pdo->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check for duplicate email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $error = "Email is already in use by another account.";
        } else {
            $update_sql = "UPDATE users SET name = ?, email = ? ";
            $params = [$name, $email];
            
            // Handle Password Change
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $error = "Password must be at least 6 characters.";
                } else {
                    $update_sql .= ", password_hash = ? ";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
            }
            
            // Handle File Upload
            if (!$error && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_picture'];
                
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error = "File upload failed with error code: " . $file['error'];
                } else {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                    $mime_type = mime_content_type($file['tmp_name']);
                    
                    if (!in_array($mime_type, $allowed_types)) {
                        $error = "Only JPG, PNG, and WebP images are allowed.";
                    } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB
                        $error = "File size exceeds 2MB limit.";
                    } else {
                        // Generate safe filename
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                        $upload_dir = __DIR__ . '/uploads/profile/';
                        
                        // Create directory if not exists
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                            $update_sql .= ", profile_picture = ? ";
                            $params[] = $filename;
                            
                            // Delete old picture if exists
                            if ($user['profile_picture'] && file_exists($upload_dir . $user['profile_picture'])) {
                                unlink($upload_dir . $user['profile_picture']);
                            }
                        } else {
                            $error = "Failed to move uploaded file.";
                        }
                    }
                }
            }
            
            if (!$error) {
                $update_sql .= " WHERE id = ?";
                $params[] = $user_id;
                
                $stmt = $pdo->prepare($update_sql);
                if ($stmt->execute($params)) {
                    $_SESSION['user_name'] = $name;
                    $_SESSION['flash_success'] = "Profile updated successfully.";
                    redirect('/task4/profile.php');
                } else {
                    $error = "Database update failed.";
                }
            }
        }
    }
}
?>

<div class="form-container" style="max-width: 500px;">
    <h2 class="form-title">Edit Profile</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin: 0 auto 1rem; border: 2px solid var(--border-color); background-color: var(--dark-bg);">
                <?php if ($user['profile_picture']): ?>
                    <img src="/task4/uploads/profile/<?php echo h($user['profile_picture']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--text-muted);">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <label class="form-label" for="profile_picture">Change Picture (Max 2MB)</label>
            <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required value="<?php echo h($_POST['name'] ?? $user['name']); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required value="<?php echo h($_POST['email'] ?? $user['email']); ?>">
        </div>
        
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 2rem 0;">
        
        <div class="form-group">
            <label class="form-label" for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="/task4/profile.php" class="btn" style="flex: 1; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">Cancel</a>
            <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
