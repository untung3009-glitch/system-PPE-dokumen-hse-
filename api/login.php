<?php
// 1. Panggil koneksi database
require_once 'config.php';

// 2. Set header agar merespons dalam bentuk JSON
header("Content-Type: application/json; charset=UTF-8");

// 3. Ambil data JSON yang dikirim dari JavaScript (fetch)
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// 4. Validasi jika kosong
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Username dan password wajib diisi."
    ]);
    exit();
}

try {
    // 5. Cari user di database Hostinger
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 6. Cek apakah user ditemukan dan verifikasi password
    // (Jika password di database menggunakan hash, gunakan password_verify. Jika plain text, bisa langsung dibandingkan)
    if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
        
        // Login Berhasil
        echo json_encode([
            "status" => "success",
            "nama_lengkap" => $user['nama_lengkap'] ?? $user['username'],
            "role" => $user['role'] ?? 'user'
        ]);
    } else {
        // Login Gagal
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Username atau password salah."
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Terjadi kesalahan pada server: " . $e->getMessage()
    ]);
}
?>