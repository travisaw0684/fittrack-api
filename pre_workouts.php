<?php
header('Content-Type: application/json');
require 'db.php';
require 'auth.php';

// Only authenticated users can access
$user_id = verifyToken();

// Static list of pre-designed workouts (could be stored in DB in a real app)
$pre_workouts = [
    ['id' => 1, 'name' => 'Beginner Cardio', 'exercise' => 'Jogging', 'duration' => 20],
    ['id' => 2, 'name' => 'Strength Training', 'exercise' => 'Push-ups', 'duration' => 15],
    ['id' => 3, 'name' => 'Core Blast', 'exercise' => 'Plank', 'duration' => 10]
];

echo json_encode($pre_workouts);
?>