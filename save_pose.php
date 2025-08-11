<?php
header('Content-Type: application/json; charset=utf-8');

// Use shared DB connection
require __DIR__ . '/db.php';

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["error" => "Method not allowed, use POST"]);
  exit;
}

// Read and validate inputs
$m1 = isset($_POST['motor1']) ? filter_var($_POST['motor1'], FILTER_VALIDATE_INT) : null;
$m2 = isset($_POST['motor2']) ? filter_var($_POST['motor2'], FILTER_VALIDATE_INT) : null;
$m3 = isset($_POST['motor3']) ? filter_var($_POST['motor3'], FILTER_VALIDATE_INT) : null;
$m4 = isset($_POST['motor4']) ? filter_var($_POST['motor4'], FILTER_VALIDATE_INT) : null;

if ($m1 === null || $m2 === null || $m3 === null || $m4 === null) {
  http_response_code(400);
  echo json_encode(["error" => "Missing or invalid parameters"]);
  exit;
}

// Optional: clamp to 0..180 range
$m1 = max(0, min(180, $m1));
$m2 = max(0, min(180, $m2));
$m3 = max(0, min(180, $m3));
$m4 = max(0, min(180, $m4));

// Insert new pose
$stmt = $mysqli->prepare(
  "INSERT INTO poses (motor1, motor2, motor3, motor4) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("iiii", $m1, $m2, $m3, $m4);

if ($stmt->execute()) {
  echo json_encode(["ok" => true, "id" => $stmt->insert_id]);
} else {
  http_response_code(500);
  echo json_encode(["error" => "Insert failed"]);
}

$stmt->close();
$mysqli->close();
