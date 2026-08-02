<?php
$host = 'localhost';
$db_name = 'u684817258_DataSystemPPE';
$username = 'u684817258_SafetyMining';
$password = 'Pn.W@Y$8b5fhTNPw';

try {
    $db = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . $exception->getMessage()
    ]);
    exit();
}
?>