<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$res = $mysqli->query("SELECT id, motor1, motor2, motor3, motor4, created_at FROM poses ORDER BY id DESC");

$out = [];
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $out[] = $row;
  }
}

echo json_encode($out);
$mysqli->close();
