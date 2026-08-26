<?php
header('Content-Type: application/json');

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

$emailLower = strtolower($email);

if (array_key_exists($emailLower, $users)) {
    $users[$emailLower]['password'] = $password;
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
?>
