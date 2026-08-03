<?php
// Konfigurasi Koneksi Database menggunakan PDO
$host = 'srv2132.hstgr.io';
$db_name = 'u684817258_DataSystemPPE';
$username = 'u684817258_SafetyMining';
$password = 'Pn.W@Y$8b5fhTNPw';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set mode error PDO ke Exception agar mudah mendeteksi error
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Jika koneksi gagal
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal: " . $e->getMessage()
    ]);
    exit();
}
?>