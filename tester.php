<?php
// ====== CONFIG ======
$BASE_URL = 'http://localhost/robot_api/'; // Change to your PC IP if testing from a phone
// ====================

$resp = null;
$err  = null;
$http = null;
$latestJson = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clamp values 0..180
    $m1 = max(0, min(180, (int)($_POST['motor1'] ?? 0)));
    $m2 = max(0, min(180, (int)($_POST['motor2'] ?? 0)));
    $m3 = max(0, min(180, (int)($_POST['motor3'] ?? 0)));
    $m4 = max(0, min(180, (int)($_POST['motor4'] ?? 0)));

    // POST to save_pose.php
    $ch = curl_init($BASE_URL . 'save_pose.php');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'motor1' => $m1,
            'motor2' => $m2,
            'motor3' => $m3,
            'motor4' => $m4,
        ]),
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    // Fetch latest pose after save
    $latestJson = @file_get_contents($BASE_URL . 'get_run_pose.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Robot Arm • PHP Tester</title>
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:640px;margin:24px auto;padding:0 16px}
        input{padding:8px;margin:4px 0;width:160px}
        button{padding:10px 16px;cursor:pointer}
        pre{background:#f6f8fa;padding:12px;border-radius:8px;overflow:auto}
        .row{display:flex;gap:12px;flex-wrap:wrap}
        small{color:#666}
        a{color:#0366d6;text-decoration:none}
        a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <h2>Save Pose (PHP tester)</h2>
    <p><small>BASE_URL: <code><?php echo htmlspecialchars($BASE_URL); ?></code></small></p>

    <form method="post">
        <div class="row">
            <div>
                <label>motor1 (0–180)</label><br>
                <input name="motor1" type="number" min="0" max="180" value="90" required>
            </div>
            <div>
                <label>motor2 (0–180)</label><br>
                <input name="motor2" type="number" min="0" max="180" value="75" required>
            </div>
            <div>
                <label>motor3 (0–180)</label><br>
                <input name="motor3" type="number" min="0" max="180" value="120" required>
            </div>
            <div>
                <label>motor4 (0–180)</label><br>
                <input name="motor4" type="number" min="0" max="180" value="60" required>
            </div>
        </div>
        <br>
        <button type="submit">Save Pose</button>
    </form>

    <?php if (!is_null($resp) || !empty($err)): ?>
        <h3>Response</h3>
        <?php if ($http !== null): ?><p><small>HTTP <?php echo (int)$http; ?></small></p><?php endif; ?>
        <pre><?php echo htmlspecialchars($resp ?: $err); ?></pre>
    <?php endif; ?>

    <?php if (!empty($latestJson)): ?>
        <h3>Latest pose (after save)</h3>
        <pre><?php echo htmlspecialchars($latestJson); ?></pre>
    <?php endif; ?>

    <hr>
    <p>
        Latest pose: <a href="get_run_pose.php" target="_blank">get_run_pose.php</a><br>
        All poses: <a href="get_all_poses.php" target="_blank">get_all_poses.php</a><br>
        Reset status: <a href="update_status.php" target="_blank">update_status.php</a>
    </p>

    <p><small>If testing from a phone on the same network, change <code>BASE_URL</code> to your PC IP (e.g. <code>http://192.168.1.10/robot_api/</code>) and allow Apache through your firewall.</small></p>
</body>
</html>
