<?php
// config/database.php

$host = 'localhost';
$db_name = 'task4_bookstore';
$username = 'root'; // default XAMPP username
$password = '';     // default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch objects by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Hide real error from user, log it if logging is set up
    die("Database connection failed. Please contact the administrator.");
}
?>
