<?php
require 'koneksi.php';
check_auth();

 $total = $pdo->query("SELECT COUNT(*) FROM pengajuan_apd")->fetchColumn();
 $pending = $pdo->query("SELECT COUNT(*) FROM pengajuan_apd WHERE status IN ('Pending Safety','Pending PM')")->fetchColumn();
 $stock = $pdo->query("SELECT SUM(stock) FROM master_ppe WHERE active=1")->fetchColumn();

send_json([
    'total' => $total,
    'pending' => $pending,
    'stock' => $stock ?: 0
]);
?>