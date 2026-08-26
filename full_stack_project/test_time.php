<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=task4_bookstore', 'root', '');
echo 'PHP time: ' . date('Y-m-d H:i:s') . "\n";
$stmt = $pdo->query('SELECT NOW()');
echo 'MySQL time: ' . $stmt->fetchColumn() . "\n";
