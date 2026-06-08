<?php
// api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// YOUR LIVE INFINITYFREE DATABASE DETAILS
$host = 'localhost';
$db   = 'assetcare'; 
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // AUTO-UPGRADER: Automatically add the delivery tracking column
    try {
        $pdo->exec("ALTER TABLE assets ADD COLUMN deliveryCount INT DEFAULT 0");
    } catch (PDOException $e) { /* Ignore if column already exists */ }

    // AUTO-UPGRADER: Automatically create the table for the new Slot Details feature
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS slots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sn VARCHAR(255),
            date_val VARCHAR(255),
            slotNo VARCHAR(255),
            slotName VARCHAR(255),
            totalAssets INT DEFAULT 0,
            returnToIT INT DEFAULT 0,
            eol INT DEFAULT 0,
            pending INT DEFAULT 0,
            remarks TEXT
        )");
    } catch (PDOException $e) { /* Ignore */ }

    // AUTO-UPGRADER: Add slotName if missing
    try {
        $pdo->exec("ALTER TABLE slots ADD COLUMN slotName VARCHAR(255) AFTER slotNo");
    } catch (PDOException $e) { /* Ignore */ }

    // AUTO-UPGRADER: Add Database Indices for mass-data performance speedups (Backend Optimization)
    try { $pdo->exec("ALTER TABLE assets ADD INDEX idx_status (status)"); } catch (PDOException $e) { /* Ignore if exists */ }
    try { $pdo->exec("ALTER TABLE assets ADD INDEX idx_tag (tag)"); } catch (PDOException $e) { /* Ignore if exists */ }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

if ($action === 'load') {
    $users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $assets = $pdo->query("SELECT * FROM assets")->fetchAll(PDO::FETCH_ASSOC);
    $slots = $pdo->query("SELECT * FROM slots ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($assets as &$asset) {
        $asset['repairs'] = json_decode($asset['repairs_json'], true) ?: [];
        unset($asset['repairs_json']);
    }
    echo json_encode(["success" => true, "users" => $users, "assets" => $assets, "slots" => $slots]);
} 
elseif ($action === 'save_user') {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, passwordHash, role, status, requestDate, lastSeen) VALUES (?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE email=VALUES(email), passwordHash=VALUES(passwordHash), role=VALUES(role), status=VALUES(status), lastSeen=VALUES(lastSeen)");
    $stmt->execute([$data['username'], $data['email'], $data['passwordHash'], $data['role'], $data['status'], $data['requestDate'] ?? '', $data['lastSeen'] ?? 0]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'delete_user') {
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'ping') {
    $stmt = $pdo->prepare("UPDATE users SET lastSeen = ? WHERE username = ?");
    $stmt->execute([time() * 1000, $_GET['username']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'save_asset') {
    $repairsJson = json_encode($data['repairs'] ?? []);
    $stmt = $pdo->prepare("INSERT INTO assets (tag, type, brand, model, serial, status, purchaseDate, repairCount, deliveryCount, repairs_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE status=VALUES(status), repairCount=VALUES(repairCount), deliveryCount=VALUES(deliveryCount), repairs_json=VALUES(repairs_json)");
    $stmt->execute([$data['tag'], $data['type'], $data['brand'], $data['model'], $data['serial'], $data['status'], $data['purchaseDate'] ?? '', $data['repairCount'] ?? 0, $data['deliveryCount'] ?? 0, $repairsJson]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'delete_asset') {
    $stmt = $pdo->prepare("DELETE FROM assets WHERE tag = ?");
    $stmt->execute([$data['tag']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'save_slot') {
    if (isset($data['id']) && $data['id']) {
        $stmt = $pdo->prepare("UPDATE slots SET sn=?, date_val=?, slotNo=?, slotName=?, totalAssets=?, returnToIT=?, eol=?, pending=?, remarks=? WHERE id=?");
        $stmt->execute([$data['sn'], $data['date_val'], $data['slotNo'], $data['slotName'], $data['totalAssets'], $data['returnToIT'], $data['eol'], $data['pending'], $data['remarks'], $data['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO slots (sn, date_val, slotNo, slotName, totalAssets, returnToIT, eol, pending, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['sn'], $data['date_val'], $data['slotNo'], $data['slotName'], $data['totalAssets'], $data['returnToIT'], $data['eol'], $data['pending'], $data['remarks']]);
    }
    echo json_encode(["success" => true]);
}
elseif ($action === 'delete_slot') {
    $stmt = $pdo->prepare("DELETE FROM slots WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode(["success" => true]);
}
?>