<?php
// Konfigurasi Database Hostinger
define('DB_HOST', 'srv2132.hstgr.io');
define('DB_NAME', 'u684817258_Systemlmippe');
define('DB_USER', 'u684817258_Systemlmippe');
define('DB_PASS', 'n.W@Y$8b5fhTNPw');

// Koneksi Database menggunakan PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

session_start();
?>