<?php
// courses.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Fetch all categories for the filter
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Base query for published courses
$query = "SELECT c.*, u.name as instructor_name, cat.name as category_name 
          FROM courses c
          JOIN users u ON c.instructor_id = u.id
          JOIN categories cat ON c.category_id = cat.id
          WHERE c.status = 'published'";

$params = [];

// Handle Search and Filter
if (!empty($_GET['search'])) {
    $query .= " AND c.title LIKE ?";
    $params[] = '%' . $_GET['search'] . '%';
}

if (!empty($_GET['category'])) {
    $query .= " AND c.category_id = ?";
    $params[] = (int)$_GET['category'];
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div style="background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">Browse Courses</h1>
    
    <!-- Search and Filter Form -->
    <form method="GET" action="" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search courses..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
        
        <select name="category" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
            <a href="/task5/courses.php" class="btn" style="background: var(--border-color); color: var(--text-main);">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Courses Grid -->
    <?php if (empty($courses)): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No courses found matching your criteria.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($courses as $course): ?>
                <div style="border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden; display: flex; flex-direction: column;">
                    <!-- Course Thumbnail -->
                    <div style="height: 180px; overflow: hidden;">
                        <img src="/task5/assets/images/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                        <h3 style="margin-bottom: 0.5rem; font-size: 1.25rem;"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">By <?php echo htmlspecialchars($course['instructor_name']); ?></p>
                        <p style="font-weight: 600; color: var(--secondary-color); margin-bottom: 1.5rem;">
                            <?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?>
                        </p>
                        <div style="margin-top: auto;">
                            <?php if (isLoggedIn()): ?>
                                <a href="/task5/course_details.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="display: block; text-align: center;">View Course</a>
                            <?php else: ?>
                                <a href="/task5/login.php" class="btn" style="display: block; text-align: center; background: var(--border-color); color: var(--text-main);">Log In to View</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
