<?php
// course_details.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Check if an ID was passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /task5/courses.php");
    exit;
}

$course_id = (int)$_GET['id'];

// Fetch course details
$stmt = $pdo->prepare("SELECT c.*, u.name as instructor_name, cat.name as category_name 
                       FROM courses c
                       JOIN users u ON c.instructor_id = u.id
                       JOIN categories cat ON c.category_id = cat.id
                       WHERE c.id = ? AND c.status = 'published'");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    header("Location: /task5/courses.php");
    exit;
}

// Check if user is enrolled
$is_enrolled = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    if ($stmt->fetch()) {
        $is_enrolled = true;
    }
}

// Fetch lessons
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY position ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 1000px; margin: 0 auto; background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="height: 300px; overflow: hidden; background: #e2e8f0; position: relative;">
        <img src="/task5/assets/images/courses/<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    
    <div style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="background: var(--primary-color); color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.875rem; font-weight: 500;">
                    <?php echo htmlspecialchars($course['category_name']); ?>
                </span>
                <h1 style="margin-top: 1rem; color: var(--text-main); font-size: 2rem;"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 0.5rem;">Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></p>
            </div>
            
            <div style="background: var(--background-color); padding: 1.5rem; border-radius: 0.5rem; text-align: center; min-width: 200px;">
                <p style="font-size: 2rem; font-weight: 700; color: var(--secondary-color); margin-bottom: 1rem;">
                    <?php echo $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free'; ?>
                </p>
                
                <?php if ($is_enrolled): ?>
                    <button class="btn" style="width: 100%; background: var(--secondary-color); color: white; cursor: default;">Enrolled</button>
                    <a href="/task5/learn.php?course_id=<?php echo $course_id; ?>" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Continue Learning</a>
                <?php else: ?>
                    <?php if (isLoggedIn()): ?>
                        <!-- We will build the enrollment form later -->
                        <form method="POST" action="/task5/enroll.php">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Enroll Now</button>
                        </form>
                    <?php else: ?>
                        <a href="/task5/login.php" class="btn btn-primary" style="width: 100%;">Log In to Enroll</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="margin-top: 2rem;">
            <h2 style="color: var(--primary-color); margin-bottom: 1rem;">About This Course</h2>
            <p style="line-height: 1.6; color: var(--text-main);">
                <?php echo nl2br(htmlspecialchars($course['description'])); ?>
            </p>
        </div>
        
        <div style="margin-top: 3rem;">
            <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Course Curriculum</h2>
            <?php if (empty($lessons)): ?>
                <p style="color: var(--text-muted);">No lessons have been added to this course yet.</p>
            <?php else: ?>
                <div style="border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden;">
                    <?php foreach ($lessons as $index => $lesson): ?>
                        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; <?php echo $index % 2 === 0 ? 'background: #f8fafc;' : 'background: white;'; ?>">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.875rem;">
                                    <?php echo $index + 1; ?>
                                </div>
                                <span style="font-weight: 500; color: var(--text-main);"><?php echo htmlspecialchars($lesson['title']); ?></span>
                            </div>
                            <?php if ($is_enrolled): ?>
                                <a href="/task5/learn.php?lesson_id=<?php echo $lesson['id']; ?>" class="btn" style="background: var(--background-color); color: var(--primary-color); padding: 0.25rem 0.75rem; font-size: 0.875rem;">View</a>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.875rem;">🔒 Locked</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
