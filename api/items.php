<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir, silakan login kembali.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $query = "SELECT * FROM ppe_items ORDER BY nama_ppe ASC";
    $result = $conn->query($query);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $items]);
    exit();
}

if ($method === 'POST') {
    if (!in_array($_SESSION['role'], ['Admin', 'Warehouse'])) {
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
        exit();
    }

    $kode = $_POST['kode_ppe'] ?? '';
    $nama = $_POST['nama_ppe'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $stok = (int)($_POST['stok'] ?? 0);
    $satuan = $_POST['satuan'] ?? '';
    $stok_minimal = (int)($_POST['stok_minimal'] ?? 5);

    $stmt = $conn->prepare("INSERT INTO ppe_items (kode_ppe, nama_ppe, kategori, stok, satuan, stok_minimal) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisi", $kode, $nama, $kategori, $stok, $satuan, $stok_minimal);
    
    if ($stmt->execute()) {
        audit_log($conn, $_SESSION['user_id'], $_SESSION['nama'], 'ADD_PPE', "Menambahkan item PPE: $nama ($stok $satuan)");
        echo json_encode(['status' => 'success', 'message' => 'Item PPE berhasil ditambahkan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambah item PPE: ' . $conn->error]);
    }
    exit();
}
?>