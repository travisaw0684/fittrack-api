<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your-secret-key"; // Change this to a secure key!

function generateToken($user_id) {
    global $secret_key;
    $payload = [
        'iat' => time(),           // Issued at
        'exp' => time() + 3600,    // Expires in 1 hour
        'sub' => $user_id          // Subject (user ID)
    ];
    return JWT::encode($payload, $secret_key, 'HS256');
}

function verifyToken() {
    global $secret_key;
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);
    try {
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        return $decoded->sub; // Return user ID
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}
?>