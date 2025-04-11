<?php
header('Content-Type: application/json');
require 'db.php';
require 'auth.php';

$user_id = verifyToken();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['name']) || !isset($data['exercises']) || !is_array($data['exercises'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and exercises array required']);
        exit;
    }

    // Insert plan
    $stmt = $pdo->prepare("INSERT INTO workout_plans (user_id, name) VALUES (?, ?)");
    $stmt->execute([$user_id, $data['name']]);
    $plan_id = $pdo->lastInsertId();

    // Insert exercises
    $stmt = $pdo->prepare("INSERT INTO plan_exercises (plan_id, exercise, duration) VALUES (?, ?, ?)");
    foreach ($data['exercises'] as $exercise) {
        if (isset($exercise['exercise']) && isset($exercise['duration'])) {
            $stmt->execute([$plan_id, $exercise['exercise'], $exercise['duration']]);
        }
    }
    echo json_encode(['message' => 'Plan saved', 'plan_id' => $plan_id]);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->prepare("SELECT wp.id, wp.name, pe.exercise, pe.duration 
                           FROM workout_plans wp 
                           LEFT JOIN plan_exercises pe ON wp.id = pe.plan_id 
                           WHERE wp.user_id = ?");
    $stmt->execute([$user_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by plan
    $plans = [];
    foreach ($results as $row) {
        if (!isset($plans[$row['id']])) {
            $plans[$row['id']] = ['id' => $row['id'], 'name' => $row['name'], 'exercises' => []];
        }
        if ($row['exercise']) {
            $plans[$row['id']]['exercises'][] = ['exercise' => $row['exercise'], 'duration' => $row['duration']];
        }
    }
    echo json_encode(array_values($plans));
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>