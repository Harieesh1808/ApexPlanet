<?php
require_once __DIR__ . '/../includes/header.php';
require_admin();

$error = '';
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock_quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    if (empty($title) || empty($author) || $category_id <= 0 || $price <= 0) {
        $error = "Title, Author, Category, and valid Price are required.";
    } else {
        $image_name = null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = "Image upload failed.";
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
                    $error = "Only JPG, PNG, and WebP images are allowed.";
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $image_name = 'book_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    $upload_dir = __DIR__ . '/../uploads/products/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $image_name)) {
                        $error = "Failed to save image.";
                    }
                }
            }
        }
        
        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, title, author, description, price, stock_quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$category_id, $title, $author, $description, $price, $stock, $image_name])) {
                $_SESSION['flash_success'] = "Product added successfully.";
                redirect('/task4/admin/products.php');
            } else {
                $error = "Failed to add product.";
            }
        }
    }
}
?>

<div class="form-container" style="max-width: 600px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="form-title" style="margin-bottom: 0;">Add New Product</h2>
        <a href="/task4/admin/products.php" style="color: var(--text-muted); font-size: 0.875rem;">&larr; Back</a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
        
        <div class="form-group">
            <label class="form-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" required value="<?php echo h($_POST['title'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="author">Author</label>
            <input type="text" id="author" name="author" class="form-control" required value="<?php echo h($_POST['author'] ?? ''); ?>">
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo h($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="price">Price ($)</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0.01" required value="<?php echo h($_POST['price'] ?? ''); ?>">
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="stock_quantity">Stock</label>
                <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0" required value="<?php echo h($_POST['stock_quantity'] ?? '0'); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="image">Product Image (Optional)</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="5"><?php echo h($_POST['description'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Add Product</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
