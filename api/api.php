<?php
session_start();

// Konfigurasi Database Hostinger
$host = "148.222.53.167";
$db_name = "u684817258_DataSystemPPE";
$username = "u684817258_SafetyMining";
$password = "Pn.W@Y$8b5fhTNPw";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Koneksi DB gagal: ' . $e->getMessage()]));
}

// Fungsi Helper untuk Response JSON
function send_json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Cek Session Timeout (15 menit = 900 detik)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    session_unset();
    session_destroy();
    send_json(['status' => 'error', 'message' => 'Session Expired'], 401);
}
 $_SESSION['LAST_ACTIVITY'] = time();

// Fungsi Audit Log
function add_log($pdo, $action) {
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action]);
    }
}

// Cek Login & Role
function check_auth($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        send_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        send_json(['status' => 'error', 'message' => 'Forbidden Access'], 403);
    }
}
?>