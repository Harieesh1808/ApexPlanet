<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo->exec("UPDATE courses SET thumbnail = 'web_dev_course.jpg' WHERE title LIKE '%Web%'");
    $pdo->exec("UPDATE courses SET thumbnail = 'data_science_course.jpg' WHERE title LIKE '%Data%'");
    $pdo->exec("UPDATE courses SET thumbnail = 'design_course.jpg' WHERE title LIKE '%Design%'");
    
    echo "Thumbnails successfully linked to the courses in the database!\n";
    echo "<br><a href='/task5/courses.php'>Go back to the catalog to see them!</a>";
} catch (Exception $e) {
    echo "Error updating thumbnails: " . $e->getMessage();
}
?>
