<?php
header('Content-Type: application/json');
require 'db.php';
require 'auth.php';

$data = json_decode(file_get_contents('php://input'), true);        // Get the JSON data sent in the request

if(!isset($data['username']) || !isset($data['password'])) {        // Check if the username and password fields are provided
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

$username = $data['username'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if($user && password_verify($data['password'], $user['password'])) {  // Check if the user exists and the password is correct
    $token = generateToken($user['id']);                              // Generate a JWT token for the user
    echo json_encode(['token' => $token]);                            // Send the token in the response
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
?>