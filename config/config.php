<?php
// config/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'uu684817258_Systemlmippe');
define('DB_PASS', 'n.W@Y$8b5fhTNPw');
define('DB_NAME', 'u684817258_Systemlmippe);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function audit_log($conn, $userId, $userName, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $stmt = $conn->prepare("INSERT INTO audit_trails (user_id, nama_user, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $userName, $action, $description, $ip);
    $stmt->execute();
    $stmt->close();
}
?>