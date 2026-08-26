<?php
header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

$usersFile = __DIR__ . '/users.json';
$users = [];
if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true);
}

if (isset($users[strtolower($email)]) && $users[strtolower($email)]['password'] === $password) {
    echo json_encode([
        'success' => true, 
        'message' => 'Login successful!',
        'name' => $users[strtolower($email)]['name']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}
?>
