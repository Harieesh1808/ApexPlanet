<?php
require_once __DIR__ . '/config/database.php';

try {
    // Check if we already have an instructor
    $stmt = $pdo->query("SELECT id FROM users WHERE email = 'instructor@example.com'");
    $instructor = $stmt->fetch();
    
    if (!$instructor) {
        // Create a dummy instructor
        $password_hash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, password_hash, email_verified) VALUES (2, 'Jane Doe (Instructor)', 'instructor@example.com', ?, 1)");
        $stmt->execute([$password_hash]);
        $instructor_id = $pdo->lastInsertId();
    } else {
        $instructor_id = $instructor['id'];
    }

    // Check if courses already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insert dummy courses
        $courses = [
            [
                'instructor_id' => $instructor_id,
                'category_id' => 1, // Web Development
                'title' => 'Complete Full-Stack Web Development Bootcamp',
                'description' => 'Learn HTML, CSS, JavaScript, PHP, and MySQL from scratch.',
                'price' => 49.99,
                'status' => 'published'
            ],
            [
                'instructor_id' => $instructor_id,
                'category_id' => 2, // Data Science
                'title' => 'Python for Data Science and Machine Learning',
                'description' => 'Master data analysis, visualization, and machine learning with Python.',
                'price' => 59.99,
                'status' => 'published'
            ],
            [
                'instructor_id' => $instructor_id,
                'category_id' => 3, // Design
                'title' => 'UI/UX Design Masterclass',
                'description' => 'Learn Figma and design stunning user interfaces.',
                'price' => 29.99,
                'status' => 'published'
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO courses (instructor_id, category_id, title, description, price, status) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($courses as $course) {
            $stmt->execute([
                $course['instructor_id'],
                $course['category_id'],
                $course['title'],
                $course['description'],
                $course['price'],
                $course['status']
            ]);
        }
        echo "Successfully added dummy courses to the database!\n";
    } else {
        echo "Courses already exist in the database.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
