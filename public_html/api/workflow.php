<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['error' => 'Unauthorized']); exit; }

 $action = $_POST['action'] ?? '';

if ($action === 'fetch_pending') {
    $role = $_SESSION['role_name'];
    // Tentukan status apa yang harus dilihat oleh masing-masing role
    $status_map = [
        'Safety' => 'Pending Review Safety',
        'Project Manager' => 'Pending Review PM'
    ];
    
    if (!isset($status_map[$role])) {
        echo json_encode([]); exit;
    }

    $status = $status_map[$role];
    $stmt = $pdo->prepare("
        SELECT p.*, u.name as user_name 
        FROM pengajuan p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = ? 
        ORDER BY p.id DESC
    ");
    $stmt->execute([$status]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($action === 'fetch_detail') {
    $pengajuan_id = (int)$_POST['pengajuan_id'];
    
    // Ambil info pengajuan
    $stmtP = $pdo->prepare("SELECT p.*, u.name as user_name FROM pengajuan p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
    $stmtP->execute([$pengajuan_id]);
    $pengajuan = $stmtP->fetch();

    // Ambil detail barang yang diajukan
    $stmtD = $pdo->prepare("
        SELECT d.quantity, a.name, a.size, a.stock 
        FROM detail_pengajuan d 
        JOIN apd a ON d.apd_id = a.id 
        WHERE d.pengajuan_id = ?
    ");
    $stmtD->execute([$pengajuan_id]);
    $details = $stmtD->fetchAll();

    echo json_encode(['pengajuan' => $pengajuan, 'details' => $details]);
    exit;
}

if ($action === 'process_action') {
    $pengajuan_id = (int)$_POST['pengajuan_id'];
    $process_action = $_POST['process_action']; // approve, reject, revise
    $comment = e($_POST['comment']);
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role_name'];
    
    try {
        $pdo->beginTransaction();
        
        $new_status = '';
        $log_action = '';
        
        if ($process_action === 'approve') {
            $log_action = 'Approve';
            if ($role === 'Safety') {
                $new_status = 'Pending Review PM'; // Lanjut ke PM
            } else if ($role === 'Project Manager') {
                $new_status = 'Approved'; // Final Approve
                
                // KURANGI STOCK OTOMATIS
                $stmtDetail = $pdo->prepare("SELECT apd_id, quantity FROM detail_pengajuan WHERE pengajuan_id = ?");
                $stmtDetail->execute([$pengajuan_id]);
                $items = $stmtDetail->fetchAll();
                
                $stmtUpdateStock = $pdo->prepare("UPDATE apd SET stock = stock - ? WHERE id = ?");
                foreach($items as $item) {
                    $stmtUpdateStock->execute([$item['quantity'], $item['apd_id']]);
                }
            }
        } else if ($process_action === 'reject') {
            $log_action = 'Reject';
            $new_status = 'Rejected';
        } else if ($process_action === 'revise') {
            $log_action = 'Revise';
            $new_status = 'Pending Review Safety'; // Kembali ke user/safety
        }

        // Update status pengajuan
        $stmt = $pdo->prepare("UPDATE pengajuan SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $pengajuan_id]);

        // Catat di workflow log
        $stmtLog = $pdo->prepare("INSERT INTO workflow_log (pengajuan_id, user_id, role_action, action, comment) VALUES (?, ?, ?, ?, ?)");
        $stmtLog->execute([$pengajuan_id, $user_id, $role, $log_action, $comment]);

        // Buat notifikasi untuk User yang mengajukan
        $stmtGetUser = $pdo->prepare("SELECT user_id FROM pengajuan WHERE id = ?");
        $stmtGetUser->execute([$pengajuan_id]);
        $target_user = $stmtGetUser->fetchColumn();

        $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmtNotif->execute([$target_user, "Pengajuan #$pengajuan_id Anda telah $log_action oleh $role. Status: $new_status"]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => "Pengajuan berhasil di-$log_action. Status: $new_status"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}