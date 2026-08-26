<?php
require_once __DIR__ . '/config/database.php';

try {
    // Check if lessons already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM lessons");
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "Lessons already exist in the database. If you want to reset them, you must empty the table first.\n";
        echo "<br><a href='/task5/courses.php'>Go back to Courses</a>";
        exit;
    }

    // Get all courses
    $stmt = $pdo->query("SELECT id, title FROM courses");
    $courses = $stmt->fetchAll();

    if (empty($courses)) {
        echo "No courses found. Run seed_courses.php first.\n";
        exit;
    }

    $insertStmt = $pdo->prepare("INSERT INTO lessons (course_id, title, description, content_url, position) VALUES (?, ?, ?, ?, ?)");

    foreach ($courses as $course) {
        // We will generate 4 dummy lessons per course
        $lessonsData = [
            [
                'title' => 'Introduction to the Course',
                'description' => 'A brief overview of what you will learn in this exciting course.',
                'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' // Dummy link
            ],
            [
                'title' => 'Setting Up Your Environment',
                'description' => 'Step-by-step instructions on getting all the required tools installed.',
                'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            [
                'title' => 'Core Concepts & Fundamentals',
                'description' => 'Diving deep into the fundamental concepts necessary to master the subject.',
                'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ],
            [
                'title' => 'Advanced Techniques and Conclusion',
                'description' => 'Wrapping up the course with advanced techniques and best practices.',
                'content_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
            ]
        ];

        foreach ($lessonsData as $index => $data) {
            $insertStmt->execute([
                $course['id'],
                $data['title'],
                $data['description'],
                $data['content_url'],
                $index + 1 // Position (1, 2, 3, 4)
            ]);
        }
    }
    
    echo "Successfully generated Curriculum/Lessons for all courses in the database!\n";
    echo "<br><a href='/task5/courses.php'>Go back to the catalog to view them!</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
