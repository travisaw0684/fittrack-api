<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

$username = $data['username'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
try{
    $stmt->execute([$username, $password]);
    echo json_encode(['message' => 'User registered successfully']);
} catch (Exception $e) {
    http_response_code(409);
    echo json_encode(['error' => 'Username already exists']);
}


?>