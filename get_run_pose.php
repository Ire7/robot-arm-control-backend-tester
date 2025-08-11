<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/db.php';

// Return the latest pose
$stmt = $mysqli->prepare(
  "SELECT id, motor1, motor2, motor3, motor4, created_at
   FROM poses
   ORDER BY id DESC
   LIMIT 1"
);
$stmt->execute();
$res = $stmt->get_result();

if ($res && ($row = $res->fetch_assoc())) {
  echo json_encode($row);
} else {
  echo json_encode(["error" => "No poses found"]);
}

$stmt->close();
$mysqli->close();
