<?php
require_once __DIR__ . '/config/database.php';

try {
    $new_url = 'https://www.youtube.com/watch?v=y2kg3MOk1sY';
    $stmt = $pdo->prepare("UPDATE lessons SET content_url = ?");
    $stmt->execute([$new_url]);
    
    echo "Successfully updated all lesson video URLs to: " . htmlspecialchars($new_url) . "<br>";
    echo "You can now go back to the learning interface and watch the videos!<br>";
    echo "<a href='/task5/courses.php'>Go to Courses</a>";
} catch (Exception $e) {
    echo "Error updating videos: " . $e->getMessage();
}
?>
