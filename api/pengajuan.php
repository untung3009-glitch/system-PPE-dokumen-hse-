<?php
// File: api/pengajuan.php
header("Content-Type: application/json; charset=UTF-8");
include_once 'config.php';

session_start();
$method = $_SERVER['REQUEST_METHOD'];

// 1. Jika method GET: Ambil daftar pengajuan untuk tabel Approval / Dashboard
if ($method === 'GET') {
    try {
        $query = "SELECT p.*, m.nama_alat, m.satuan 
                  FROM pengajuan_apd p 
                  JOIN master_ppe m ON p.id_ppe = m.id 
                  ORDER BY p.id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["records" => $records]);
    } catch (Exception $e) {
        echo json_encode(["records" => [], "message" => "Error: " . $e->getMessage()]);
    }
} 

// 2. Jika method POST: Simpan pengajuan baru dari pekerja
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->id_ppe) && !empty($data->jumlah) && !empty($data->keperluan)) {
        try {
            $user_id = $_SESSION['user_id'] ?? 1; // Default user ID jika session belum aktif

            $query = "INSERT INTO pengajuan_apd (user_id, id_ppe, jumlah, keperluan, status, created_at) 
                      VALUES (:user_id, :id_ppe, :jumlah, :keperluan, 'Pending Safety', NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':id_ppe', $data->id_ppe);
            $stmt->bindParam(':jumlah', $data->jumlah);
            $stmt->bindParam(':keperluan', $data->keperluan);

            if ($stmt->execute()) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Pengajuan APD berhasil dikirim dan menunggu persetujuan Safety."
                ]);
            } else {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Gagal menyimpan pengajuan ke database."
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error", 
                "message" => "Error: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Semua kolom form pengajuan harus diisi."
        ]);
    }
}
?>