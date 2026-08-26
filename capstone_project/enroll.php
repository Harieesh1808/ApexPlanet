<?php
// enroll.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin(); // User must be logged in to enroll

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken($_POST['csrf_token'] ?? '');
    
    $course_id = (int)($_POST['course_id'] ?? 0);
    $user_id = (int)$_SESSION['user_id'];
    
    if ($course_id > 0) {
        // Verify course exists and is published
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? AND status = 'published'");
        $stmt->execute([$course_id]);
        if ($stmt->fetch()) {
            
            // Check if already enrolled to prevent duplicates
            $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$user_id, $course_id]);
            if (!$stmt->fetch()) {
                // Insert new enrollment
                $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, status) VALUES (?, ?, 'active')");
                $stmt->execute([$user_id, $course_id]);
                
                // Redirect back to course details with success (could use a session flash message in a real app)
                header("Location: /task5/course_details.php?id=" . $course_id . "&enrolled=1");
                exit;
            } else {
                // Already enrolled
                header("Location: /task5/course_details.php?id=" . $course_id);
                exit;
            }
        }
    }
}

// If anything fails or GET request, redirect to courses
header("Location: /task5/courses.php");
exit;
?>
