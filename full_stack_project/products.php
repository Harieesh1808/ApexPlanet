<?php
require_once __DIR__ . '/includes/header.php';

// Pagination setup
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = trim($_GET['search'] ?? '');
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$in_stock = isset($_GET['in_stock']) ? (int)$_GET['in_stock'] : 0;

// Build query
$where_clauses = ["1=1"];
$params = [];

if ($search) {
    $where_clauses[] = "(p.title LIKE ? OR p.author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category > 0) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $category;
}
if ($in_stock) {
    $where_clauses[] = "p.stock_quantity > 0";
}

$where_sql = implode(' AND ', $where_clauses);

// Count total for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $where_sql");
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Fetch products
$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE $where_sql ORDER BY p.title ASC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$cat_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll();

// Helper to keep query string params for pagination
function get_query_string($exclude = []) {
    $params = $_GET;
    foreach ($exclude as $key) {
        unset($params[$key]);
    }
    return http_build_query($params);
}
?>

<div style="display: flex; gap: 2rem; margin-top: 2rem;">
    <!-- Sidebar Filters -->
    <aside style="width: 250px; flex-shrink: 0;">
        <div style="background-color: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); position: sticky; top: 80px;">
            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Filters</h3>
            
            <form action="" method="GET">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Title or author..." value="<?php echo h($search); ?>">
                </div>
                
                <div class="form-group">
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
                
                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="in_stock" name="in_stock" value="1" <?php echo $in_stock ? 'checked' : ''; ?>>
                    <label for="in_stock" style="font-size: 0.875rem; color: var(--text-main);">In Stock Only</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Apply Filters</button>
                <a href="/task4/products.php" class="btn btn-block" style="margin-top: 0.5rem; text-align: center; border: 1px solid var(--border-color); color: var(--text-main);">Clear</a>
            </form>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div style="flex-grow: 1;">
        <h2 style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            Books
            <span style="font-size: 1rem; font-weight: normal; color: var(--text-muted);"><?php echo $total_products; ?> results</span>
        </h2>
        
        <?php if ($products): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
                <?php foreach ($products as $book): ?>
                    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                        <?php if ($book['image']): ?>
                            <img src="/task4/uploads/products/<?php echo h($book['image']); ?>" alt="<?php echo h($book['title']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background-color: var(--dark-bg); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                No Image
                            </div>
                        <?php endif; ?>
                        
                        <div style="padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <span style="font-size: 0.75rem; color: var(--secondary-color); text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;"><?php echo h($book['category_name']); ?></span>
                            <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem; color: var(--text-main); line-height: 1.3;"><?php echo h($book['title']); ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">by <?php echo h($book['author']); ?></p>
                            
                            <?php if ($book['stock_quantity'] <= 0): ?>
                                <span style="font-size: 0.75rem; color: var(--danger); font-weight: 600; margin-bottom: 0.5rem;">Out of Stock</span>
                            <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--secondary-color); font-weight: 600; margin-bottom: 0.5rem;">In Stock</span>
                            <?php endif; ?>
                            
                            <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.125rem; font-weight: 700; color: var(--text-main);">$<?php echo number_format($book['price'], 2); ?></span>
                                <a href="/task4/product_details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.875rem;">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 3rem;">
                    <?php 
                    $qs = get_query_string(['page']);
                    $qs = $qs ? '&' . $qs : '';
                    ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $qs; ?>" class="btn" style="background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main);">Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $qs; ?>" class="btn" style="background-color: <?php echo $i === $page ? 'var(--primary-color)' : 'var(--card-bg)'; ?>; border: 1px solid <?php echo $i === $page ? 'var(--primary-color)' : 'var(--border-color)'; ?>; color: <?php echo $i === $page ? 'white' : 'var(--text-main)'; ?>;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $qs; ?>" class="btn" style="background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-main);">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div style="background-color: var(--card-bg); padding: 3rem; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
                <h3 style="margin-bottom: 1rem; color: var(--text-main);">No books found</h3>
                <p style="color: var(--text-muted);">Try adjusting your search or filters.</p>
                <a href="/task4/products.php" class="btn btn-primary" style="margin-top: 1.5rem;">View All Books</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
