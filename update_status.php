<?php
header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/db.php'; // shared DB connection

// Ensure there's a row with id=1 (create if missing)
$mysqli->query("
  INSERT INTO arm_status (id, run_status)
  SELECT 1, 0
  FROM DUAL
  WHERE NOT EXISTS (SELECT 1 FROM arm_status WHERE id = 1)
");

// Set run_status = 0
if ($mysqli->query("UPDATE arm_status SET run_status = 0 WHERE id = 1")) {
  echo "Status updated to 0";
} else {
  http_response_code(500);
  echo "Update failed";
}

$mysqli->close();
