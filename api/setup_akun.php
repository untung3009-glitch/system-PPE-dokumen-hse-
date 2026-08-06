<?php
require 'config.php'; // atau koneksi.php (sesuaikan dengan nama file koneksi Anda)

 $users = [
    ['Administrator', 'admin', 'admin123', 'Administrator'],
    ['Budi Safety', 'safety', 'safety123', 'Safety Coordinator'],
    ['Joko PM', 'pm', 'pm123', 'Project Manager'],
    ['Asep User', 'user', 'user123', 'Employee']
];

foreach ($users as $u) {
    // Enkripsi password menggunakan Bcrypt
    $hash = password_hash($u[2], PASSWORD_DEFAULT);
    
    // Cek apakah username sudah ada
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$u[1]]);
    
    if ($stmt->fetch()) {
        // Jika sudah ada, update passwordnya
        $upd = $pdo->prepare("UPDATE users SET name=?, password=?, role=? WHERE username=?");
        $upd->execute([$u[0], $hash, $u[3], $u[1]]);
    } else {
        // Jika belum ada, buat akun baru
        $ins = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
        $ins->execute([$u[0], $u[1], $hash, $u[3]]);
    }
}

echo "Semua akun berhasil dibuat/diperbarui! Silakan login.";
?>