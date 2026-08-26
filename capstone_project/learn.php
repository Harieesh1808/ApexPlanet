<?php
// learn.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin(); // Must be logged in

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

if ($course_id === 0 && $lesson_id === 0) {
    header("Location: /task5/courses.php");
    exit;
}

// If we only have lesson_id, fetch course_id
if ($lesson_id > 0 && $course_id === 0) {
    $stmt = $pdo->prepare("SELECT course_id FROM lessons WHERE id = ?");
    $stmt->execute([$lesson_id]);
    $result = $stmt->fetch();
    if ($result) {
        $course_id = $result['course_id'];
    }
}

// Check Enrollment (Authorization)
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
$stmt->execute([$_SESSION['user_id'], $course_id]);
if (!$stmt->fetch()) {
    // User is not enrolled - display permission denied error
    header("HTTP/1.1 403 Forbidden");
    require_once __DIR__ . '/includes/header.php';
    echo '<div style="max-width: 600px; margin: 4rem auto; background: white; padding: 2rem; border-radius: 0.5rem; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
    echo '<h1 style="color: var(--error-color); margin-bottom: 1rem;">Access Denied</h1>';
    echo '<p style="color: var(--text-muted); margin-bottom: 2rem;">You do not have permission to view this content because you are not enrolled in this course.</p>';
    echo '<a href="/task5/course_details.php?id=' . htmlspecialchars($course_id) . '" class="btn btn-primary">Go to Course Page</a>';
    echo '</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch all lessons for this course to build the sidebar
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY position ASC");
$stmt->execute([$course_id]);
$all_lessons = $stmt->fetchAll();

if (empty($all_lessons)) {
    die("This course currently has no lessons.");
}

// Determine which lesson to show
$current_lesson = null;
if ($lesson_id > 0) {
    foreach ($all_lessons as $l) {
        if ($l['id'] == $lesson_id) {
            $current_lesson = $l;
            break;
        }
    }
} else {
    // If no lesson specified, default to the first one
    $current_lesson = $all_lessons[0];
}

if (!$current_lesson) {
    die("Lesson not found.");
}

// Fetch course details for the header
$stmt = $pdo->prepare("SELECT title FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; gap: 2rem; max-width: 1200px; margin: 0 auto; align-items: flex-start;">
    
    <!-- Sidebar / Curriculum -->
    <div style="flex: 0 0 300px; background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 1.5rem; background: var(--background-color); border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($course['title']); ?></h3>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Course Curriculum</p>
        </div>
        
        <div style="display: flex; flex-direction: column;">
            <?php foreach ($all_lessons as $index => $lesson): ?>
                <?php 
                $isActive = ($lesson['id'] == $current_lesson['id']);
                $bgColor = $isActive ? '#EEF2FF' : 'white';
                $textColor = $isActive ? 'var(--primary-color)' : 'var(--text-main)';
                $borderLeft = $isActive ? '4px solid var(--primary-color)' : '4px solid transparent';
                ?>
                <a href="/task5/learn.php?lesson_id=<?php echo $lesson['id']; ?>" style="padding: 1rem; text-decoration: none; border-bottom: 1px solid var(--border-color); background: <?php echo $bgColor; ?>; border-left: <?php echo $borderLeft; ?>; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 24px; height: 24px; background: <?php echo $isActive ? 'var(--primary-color)' : 'var(--text-muted)'; ?>; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold;">
                        <?php echo $index + 1; ?>
                    </div>
                    <span style="color: <?php echo $textColor; ?>; font-weight: <?php echo $isActive ? '600' : '400'; ?>;">
                        <?php echo htmlspecialchars($lesson['title']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div style="flex: 1; background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Video Player -->
        <div style="width: 100%; height: 500px; background: #000;">
            <?php
            // Convert youtube watch URL to embed URL
            $video_url = $current_lesson['content_url'];
            $embed_url = '';
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video_url, $match)) {
                $video_id = $match[1];
                $embed_url = "https://www.youtube.com/embed/" . $video_id;
            }
            ?>
            <?php if ($embed_url): ?>
                <iframe width="100%" height="100%" src="<?php echo htmlspecialchars($embed_url); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php else: ?>
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                    <p style="color: #9CA3AF;">Invalid Video URL</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="padding: 2rem;">
            <h1 style="color: var(--text-main); margin-bottom: 1rem;"><?php echo htmlspecialchars($current_lesson['title']); ?></h1>
            <p style="color: var(--text-muted); line-height: 1.6; font-size: 1.1rem;">
                <?php echo nl2br(htmlspecialchars($current_lesson['description'])); ?>
            </p>
            
            <div style="margin-top: 3rem; display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 2rem;">
                <?php
                // Determine Previous and Next lesson IDs for navigation
                $prev_id = null;
                $next_id = null;
                for ($i = 0; $i < count($all_lessons); $i++) {
                    if ($all_lessons[$i]['id'] == $current_lesson['id']) {
                        if ($i > 0) $prev_id = $all_lessons[$i-1]['id'];
                        if ($i < count($all_lessons)-1) $next_id = $all_lessons[$i+1]['id'];
                        break;
                    }
                }
                ?>
                
                <?php if ($prev_id): ?>
                    <a href="/task5/learn.php?lesson_id=<?php echo $prev_id; ?>" class="btn" style="background: var(--background-color); color: var(--text-main);">&larr; Previous Lesson</a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <?php if ($next_id): ?>
                    <a href="/task5/learn.php?lesson_id=<?php echo $next_id; ?>" class="btn btn-primary">Next Lesson &rarr;</a>
                <?php else: ?>
                    <button class="btn" style="background: var(--secondary-color); color: white; cursor: default;">Course Completed! 🎉</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
