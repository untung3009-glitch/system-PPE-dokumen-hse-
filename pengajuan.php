<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['error' => 'Unauthorized']); exit; }

 $action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'fetch_apd_list') {
    $stmt = $pdo->query("SELECT id, name, size, stock FROM apd ORDER BY name ASC");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'store') {
    $user_id = $_SESSION['user_id'];
    $request_date = date('Y-m-d');
    $apd_ids = $_POST['apd_id'];
    $qtys = $_POST['quantity'];

    try {
        $pdo->beginTransaction();
        
        // 1. Insert ke tabel pengajuan (Status awal: Pending Review Safety)
        $stmt = $pdo->prepare("INSERT INTO pengajuan (user_id, request_date, status) VALUES (?, ?, 'Pending Review Safety')");
        $stmt->execute([$user_id, $request_date]);
        $pengajuan_id = $pdo->lastInsertId();

        // 2. Insert ke detail_pengajuan
        $stmtDetail = $pdo->prepare("INSERT INTO detail_pengajuan (pengajuan_id, apd_id, quantity) VALUES (?, ?, ?)");
        
        for ($i = 0; $i < count($apd_ids); $i++) {
            $apd_id = (int)$apd_ids[$i];
            $qty = (int)$qtys[$i];
            
            if ($qty > 0) {
                $stmtDetail->execute([$pengajuan_id, $apd_id, $qty]);
            }
        }

        // 3. Buat notifikasi untuk Safety
        $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmtNotif->execute([2, "Pengajuan APD Baru #$pengajuan_id menunggu review Anda."]); // Asumsi role Safety memiliki id=2

        $pdo->commit();
        log_audit($pdo, "Buat Pengajuan APD #$pengajuan_id");
        echo json_encode(['status' => 'success', 'message' => 'Pengajuan berhasil dikirim!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'history') {
    $stmt = $pdo->prepare("SELECT * FROM pengajuan WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode($stmt->fetchAll());
    exit;
}