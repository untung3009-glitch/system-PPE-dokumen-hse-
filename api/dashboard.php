<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthenticated']);
    exit();
}

// Global Statistics Summary
$totalReq = $conn->query("SELECT COUNT(*) AS total FROM ppe_requests")->fetch_assoc()['total'];
$approved = $conn->query("SELECT COUNT(*) AS total FROM ppe_requests WHERE status = 'Approved'")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM ppe_requests WHERE status LIKE 'Pending%'")->fetch_assoc()['total'];
$rejected = $conn->query("SELECT COUNT(*) AS total FROM ppe_requests WHERE status = 'Rejected'")->fetch_assoc()['total'];

// Data untuk Grafik Pengajuan Per Bulan
$chartQuery = $conn->query("
    SELECT DATE_FORMAT(created_at, '%b %Y') as bulan, COUNT(*) as jumlah 
    FROM ppe_requests 
    GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
    ORDER BY created_at ASC LIMIT 6
");
$chartData = [];
while ($row = $chartQuery->fetch_assoc()) {
    $chartData[] = $row;
}

// Stok Menipis Warning
$lowStockQuery = $conn->query("SELECT nama_ppe, stok, stok_minimal, satuan FROM ppe_items WHERE stok <= stok_minimal");
$lowStock = [];
while ($row = $lowStockQuery->fetch_assoc()) {
    $lowStock[] = $row;
}

echo json_encode([
    'status' => 'success',
    'summary' => [
        'total' => $totalReq,
        'approved' => $approved,
        'pending' => $pending,
        'rejected' => $rejected
    ],
    'chart' => $chartData,
    'low_stock' => $lowStock
]);
?>