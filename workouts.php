<?php
header('Content-Type: application/json');
require 'db.php';
require 'auth.php';

$user_id = verifyToken();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("INSERT INTO workouts (user_id, exercise, duration, date) VALUES (?, ?, ?, CURDATE())");
    
    if (isset($data['plan_id'])) {
        // Log all exercises from a plan
        $plan_stmt = $pdo->prepare("SELECT exercise, duration FROM plan_exercises WHERE plan_id = ?");
        $plan_stmt->execute([$data['plan_id']]);
        $exercises = $plan_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($exercises as $exercise) {
            $stmt->execute([$user_id, $exercise['exercise'], $exercise['duration']]);
        }
        echo json_encode(['message' => 'Plan logged']);
    } elseif (isset($data['exercise']) && isset($data['duration'])) {
        // Log single exercise
        $stmt->execute([$user_id, $data['exercise'], $data['duration']]);
        echo json_encode(['message' => 'Workout logged']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Exercise and duration or plan_id required']);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = ? ORDER BY date DESC");
    $stmt->execute([$user_id]);
    $workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($workouts);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>