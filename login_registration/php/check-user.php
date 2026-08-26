<?php
/**
 * Dummy PHP Endpoint for checking email availability
 * Accepts a GET request with an 'email' parameter.
 * Returns JSON: { "exists": true|false }
 */

header('Content-Type: application/json');

// Get the email parameter from the request
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

// Validate input
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address provided.']);
    exit;
}

// Simulate a database check latency
usleep(500000); // 0.5 seconds

$usersFile = __DIR__ . '/users.json';
$users = [];
if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true);
}

// Check if the provided email exists in our database
$exists = array_key_exists(strtolower($email), $users);

// Return the result as JSON
echo json_encode([
    'email' => $email,
    'exists' => $exists
]);
?>
