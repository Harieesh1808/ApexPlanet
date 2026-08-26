<?php
require_once __DIR__ . '/includes/header.php';

// Fetch a few recent books for the featured section
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 6");
$featured_books = $stmt->fetchAll();
?>

<div style="text-align: center; margin: 3rem 0;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Welcome to the Online Bookstore</h1>
    <p style="color: var(--text-muted); font-size: 1.125rem; max-width: 600px; margin: 0 auto 2rem;">
        Discover your next great read. Browse our extensive catalog of books across various categories.
    </p>
    <a href="/task4/products.php" class="btn btn-primary" style="font-size: 1.125rem;">Browse All Books</a>
</div>

<?php if ($featured_books): ?>
<div style="margin-top: 4rem;">
    <h2 style="margin-bottom: 2rem; font-size: 1.75rem;">New Arrivals</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem;">
        <?php foreach ($featured_books as $book): ?>
            <div style="background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                <?php if ($book['image']): ?>
                    <img src="/task4/uploads/products/<?php echo h($book['image']); ?>" alt="<?php echo h($book['title']); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 200px; background-color: var(--dark-bg); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                        No Image Available
                    </div>
                <?php endif; ?>
                
                <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
                    <span style="font-size: 0.75rem; color: var(--secondary-color); text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;"><?php echo h($book['category_name']); ?></span>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--text-main); line-height: 1.4;"><?php echo h($book['title']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">by <?php echo h($book['author']); ?></p>
                    
                    <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.25rem; font-weight: 700; color: var(--text-main);">$<?php echo number_format($book['price'], 2); ?></span>
                        <a href="/task4/product_details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
