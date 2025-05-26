<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db_connection.php');
header('Content-Type: application/json');
if (!isset($_GET['meal_id'])) {
    echo json_encode([]);
    exit;
}

$meal_id = (int)$_GET['meal_id'];



// Güvenli sorgu
$stmt = $conn->prepare("
    SELECT e.user_id, e.comment, e.created_at, u.name AS username 
    FROM evaluations e 
    JOIN users u ON e.user_id = u.id 
    WHERE e.meal_id = ? 
    ORDER BY e.created_at DESC
");
$stmt->bind_param('i', $meal_id);
$stmt->execute();
$result = $stmt->get_result();

$evaluations = [];
while ($row = $result->fetch_assoc()) {
    $evaluations[] = $row;
}

echo json_encode($evaluations);
?>