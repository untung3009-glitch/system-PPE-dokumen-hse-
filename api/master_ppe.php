<?php
require_once 'config.php';
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM master_ppe");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["status" => "success", "data" => $data]);
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nama = $input['nama_apd'] ?? '';
    $kategori = $input['kategori'] ?? '';
    $ukuran = $input['ukuran'] ?? '';
    $stock = $input['stock'] ?? 0;
    $vendor = $input['vendor'] ?? '';
    $harga = $input['harga'] ?? 0;
    $foto = $input['foto'] ?? '';

    try {
        $stmt = $db->prepare("INSERT INTO master_ppe (nama_apd, kategori, ukuran, stock, vendor, harga, foto, status) VALUES (:nama, :kategori, :ukuran, :stock, :vendor, :harga, :foto, 1)");
        $stmt->execute([
            'nama' => $nama, 'kategori' => $kategori, 'ukuran' => $ukuran, 
            'stock' => $stock, 'vendor' => $vendor, 'harga' => $harga, 'foto' => $foto
        ]);
        echo json_encode(["status" => "success", "message" => "Master PPE berhasil ditambahkan!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>