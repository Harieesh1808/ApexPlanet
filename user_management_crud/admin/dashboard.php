<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Fetch users
$result = $conn->query("SELECT u.id, u.name, u.email, u.profile_picture, u.created_at, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");

$page_title = 'Admin Dashboard - UMS';
$base_path = '../';
include '../includes/header.php';
?>

<div class="card" style="max-width: 1000px;">
    <div class="flex justify-between align-center mb-4">
        <h2>Admin Dashboard - User Management</h2>
        <a href="add_user.php" class="btn btn-primary">Add New User</a>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pic</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <?php if (!empty($row['profile_picture'])): ?>
                            <img src="../<?php echo sanitize($row['profile_picture']); ?>" class="profile-pic-sm" alt="Pic">
                        <?php else: ?>
                            <div class="profile-pic-sm flex" style="align-items: center; justify-content: center; background: var(--secondary-color); color: var(--text-muted);">
                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo sanitize($row['name']); ?></td>
                    <td><?php echo sanitize($row['email']); ?></td>
                    <td style="text-transform: capitalize;"><?php echo sanitize($row['role_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <?php if ($row['id'] !== $_SESSION['user_id']): ?>
                            <form action="delete_user.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
