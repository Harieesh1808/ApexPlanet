<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and Filter
$search = trim($_GET['search'] ?? '');
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$where = ["1=1"];
$params = [];

if ($search) {
    $where[] = "(p.title LIKE ? OR p.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category;
}

$where_sql = implode(' AND ', $where);

// Count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $where_sql");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Fetch products
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE $where_sql 
        ORDER BY p.title ASC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Categories for filter
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $delete_id = (int)$_POST['delete_product_id'];
    
    try {
        // Fetch image to delete
        $img_stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $img_stmt->execute([$delete_id]);
        $image = $img_stmt->fetchColumn();
        
        // Delete product
        $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $del->execute([$delete_id]);
        
        // Delete file if exists
        if ($image && file_exists(__DIR__ . '/../uploads/products/' . $image)) {
            unlink(__DIR__ . '/../uploads/products/' . $image);
        }
        
        $_SESSION['flash_success'] = "Product deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Cannot delete product. It may be part of existing orders.";
    }
    
    redirect('/task4/admin/products.php');
}
?>

<div style="display: flex; gap: 2rem;">
    <!-- Admin Sidebar -->
    <aside style="width: 250px; flex-shrink: 0;">
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); position: sticky; top: 80px;">
            <h3 style="margin-bottom: 1rem; color: var(--text-main); font-size: 1.125rem;">Admin Panel</h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                <li><a href="/task4/admin/dashboard.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Dashboard</a></li>
                <li><a href="/task4/admin/users.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Users</a></li>
                <li><a href="/task4/admin/products.php" style="display: block; padding: 0.5rem; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color); border-radius: 4px; font-weight: 500;">Products</a></li>
                <li><a href="/task4/admin/orders.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Orders</a></li>
                <li><a href="/task4/admin/analytics.php" style="display: block; padding: 0.5rem; color: var(--text-main); border-radius: 4px;">Analytics</a></li>
            </ul>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div style="flex-grow: 1;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; color: var(--text-main);">Manage Products</h1>
            <a href="/task4/admin/add_product.php" class="btn btn-primary">Add New Product</a>
        </div>
        
        <!-- Filters -->
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
            <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="flex: 2; margin-bottom: 0;">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Title or author..." value="<?php echo h($search); ?>">
                </div>
                
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label class="form-label" for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $category === $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo h($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Filter</button>
                <a href="/task4/admin/products.php" class="btn" style="padding: 0.75rem 1.5rem; background-color: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-main);">Clear</a>
            </form>
        </div>
        
        <!-- Products Table -->
        <div style="background-color: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: var(--darker-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Image</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Title</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Category</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Price</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Stock</th>
                        <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem;">
                                    <?php if ($p['image']): ?>
                                        <img src="/task4/uploads/products/<?php echo h($p['image']); ?>" alt="Cover" style="width: 40px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 50px; background-color: var(--dark-bg); border-radius: 4px;"></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; font-weight: 500; color: var(--text-main);">
                                    <a href="/task4/product_details.php?id=<?php echo $p['id']; ?>" target="_blank"><?php echo h($p['title']); ?></a><br>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal;"><?php echo h($p['author']); ?></span>
                                </td>
                                <td style="padding: 1rem; color: var(--text-muted);"><?php echo h($p['category_name']); ?></td>
                                <td style="padding: 1rem; color: var(--text-muted);">$<?php echo number_format($p['price'], 2); ?></td>
                                <td style="padding: 1rem;">
                                    <?php if ($p['stock_quantity'] <= 0): ?>
                                        <span style="color: var(--danger); font-weight: 600;"><?php echo $p['stock_quantity']; ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-main);"><?php echo $p['stock_quantity']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center; height: 100%;">
                                    <!-- Using edit_product placeholder -->
                                    <form method="POST" action="" onsubmit="return confirm('Delete this product?');" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                                        <input type="hidden" name="delete_product_id" value="<?php echo $p['id']; ?>">
                                        <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background-color: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2);">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
