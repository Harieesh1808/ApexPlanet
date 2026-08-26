<?php
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('/task4/products.php');
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    $_SESSION['flash_error'] = "Book not found.";
    redirect('/task4/products.php');
}
?>

<div style="margin-top: 2rem;">
    <a href="/task4/products.php" style="display: inline-block; margin-bottom: 1.5rem; color: var(--text-muted);">&larr; Back to Books</a>
    
    <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; md:flex-direction: row; gap: 2rem; padding: 2rem;">
        
        <div style="flex: 0 0 350px;">
            <?php if ($book['image']): ?>
                <img src="/task4/uploads/products/<?php echo h($book['image']); ?>" alt="<?php echo h($book['title']); ?>" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <?php else: ?>
                <div style="width: 100%; height: 450px; background-color: var(--dark-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.25rem;">
                    No Image Available
                </div>
            <?php endif; ?>
        </div>
        
        <div style="flex: 1;">
            <span style="font-size: 0.875rem; color: var(--secondary-color); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;"><?php echo h($book['category_name']); ?></span>
            <h1 style="font-size: 2.5rem; margin: 0.5rem 0 1rem; color: var(--text-main); line-height: 1.2;"><?php echo h($book['title']); ?></h1>
            <p style="font-size: 1.25rem; color: var(--text-muted); margin-bottom: 2rem;">by <span style="color: var(--text-main);"><?php echo h($book['author']); ?></span></p>
            
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-main); margin-bottom: 2rem;">
                $<?php echo number_format($book['price'], 2); ?>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.125rem; margin-bottom: 0.75rem; color: var(--text-main);">Description</h3>
                <p style="color: var(--text-muted); line-height: 1.7; white-space: pre-wrap;"><?php echo h($book['description']); ?></p>
            </div>
            
            <div style="background-color: var(--dark-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <?php if ($book['stock_quantity'] > 0): ?>
                    <p style="color: var(--secondary-color); font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--secondary-color); border-radius: 50%;"></span>
                        In Stock (<?php echo $book['stock_quantity']; ?> available)
                    </p>
                    
                    <form action="/task4/cart_action.php" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
                        <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
                        <input type="hidden" name="product_id" value="<?php echo $book['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group" style="margin-bottom: 0; width: 100px;">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $book['stock_quantity']; ?>" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="flex-grow: 1;">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--danger); font-weight: 600; font-size: 1.125rem;">Out of Stock</p>
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;">This item is currently unavailable.</p>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
