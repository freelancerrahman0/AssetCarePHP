<?php
// api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = 'localhost';
$db   = 'assetcare';
$user = 'root'; 
$pass = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

if ($action === 'load') {
    // Fetch all users and assets for the frontend
    $users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($assets as &$asset) {
        $asset['repairs'] = json_decode($asset['repairs_json'], true) ?: [];
        unset($asset['repairs_json']);
    }
    echo json_encode(["success" => true, "users" => $users, "assets" => $assets]);
} 
elseif ($action === 'save_user') {
    // Safely insert or update a single user without touching anyone else
    $stmt = $pdo->prepare("INSERT INTO users (username, email, passwordHash, role, status, requestDate, lastSeen) VALUES (?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE email=VALUES(email), passwordHash=VALUES(passwordHash), role=VALUES(role), status=VALUES(status), lastSeen=VALUES(lastSeen)");
    $stmt->execute([$data['username'], $data['email'], $data['passwordHash'], $data['role'], $data['status'], $data['requestDate'] ?? '', $data['lastSeen'] ?? 0]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'delete_user') {
    // Safely delete a user
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'ping') {
    // Background heartbeat to show who is online
    $stmt = $pdo->prepare("UPDATE users SET lastSeen = ? WHERE username = ?");
    $stmt->execute([time() * 1000, $_GET['username']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'save_asset') {
    // Safely insert or update a single asset
    $repairsJson = json_encode($data['repairs'] ?? []);
    $stmt = $pdo->prepare("INSERT INTO assets (tag, type, brand, model, serial, status, purchaseDate, repairCount, repairs_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE status=VALUES(status), repairCount=VALUES(repairCount), repairs_json=VALUES(repairs_json)");
    $stmt->execute([$data['tag'], $data['type'], $data['brand'], $data['model'], $data['serial'], $data['status'], $data['purchaseDate'] ?? '', $data['repairCount'] ?? 0, $repairsJson]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'delete_asset') {
    // Safely delete an asset
    $stmt = $pdo->prepare("DELETE FROM assets WHERE tag = ?");
    $stmt->execute([$data['tag']]);
    echo json_encode(["success" => true]);
}
?>