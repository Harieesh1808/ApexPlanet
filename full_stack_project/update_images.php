<?php
$host = '127.0.0.1';
$db   = 'task4_bookstore';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("UPDATE products SET image = 'clean_code.jpg' WHERE id = 1");
    $pdo->exec("UPDATE products SET image = 'dune.jpg' WHERE id = 2");
    $pdo->exec("UPDATE products SET image = 'atomic_habits.jpg' WHERE id = 3");
    $pdo->exec("UPDATE products SET image = 'brief_history.jpg' WHERE id = 4");
    
    echo "Successfully updated the database images.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
