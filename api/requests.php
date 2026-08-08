<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi berakhir']);
    exit();
}

$action = $_GET['action'] ?? '';
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];

// 1. BUAT PENGAJUAN BARU (Karyawan / Semua Role)
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ppe_item_id = (int)$_POST['ppe_item_id'];
    $jumlah = (int)$_POST['jumlah'];
    $alasan = trim($_POST['alasan']);
    
    $nik = $_SESSION['nik'];
    $nama = $_SESSION['nama'];
    $departemen = $_SESSION['departemen'];
    $jabatan = $_SESSION['jabatan'];
    $area_kerja = $_SESSION['area_kerja'];

    // Handle Upload Foto
    $foto_bukti = NULL;
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION);
        $foto_bukti = 'ppe_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $uploadDir . $foto_bukti);
    }

    $no_pengajuan = 'REQ-PPE-' . date('Ymd') . '-' . rand(100, 999);
    $status_awal = 'Pending Supervisor';

    $stmt = $conn->prepare("INSERT INTO ppe_requests (no_pengajuan, user_id, nik, nama, departemen, jabatan, area_kerja, ppe_item_id, jumlah, alasan, foto_bukti, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssiisss", $no_pengajuan, $userId, $nik, $nama, $departemen, $jabatan, $area_kerja, $ppe_item_id, $jumlah, $alasan, $foto_bukti, $status_awal);

    if ($stmt->execute()) {
        audit_log($conn, $userId, $nama, 'CREATE_REQUEST', "Pengajuan PPE $no_pengajuan sebanyak $jumlah pcs dibuat.");
        echo json_encode(['status' => 'success', 'message' => 'Pengajuan PPE berhasil dibuat. Menunggu approval Supervisor.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat pengajuan: ' . $conn->error]);
    }
    exit();
}

// 2. AMBIL LIST PENGAJUAN
if ($action === 'list') {
    $where = "";
    if ($role === 'Karyawan') {
        $where = "WHERE r.user_id = $userId";
    } elseif ($role === 'Supervisor') {
        $where = "WHERE r.status = 'Pending Supervisor' OR r.user_id = $userId";
    } elseif ($role === 'Safety') {
        $where = "WHERE r.status = 'Pending Safety' OR r.status = 'Pending Warehouse' OR r.status = 'Approved'";
    } elseif ($role === 'Warehouse') {
        $where = "WHERE r.status = 'Pending Warehouse' OR r.status = 'Approved'";
    }

    $sql = "SELECT r.*, i.nama_ppe, i.satuan, i.stok 
            FROM ppe_requests r 
            JOIN ppe_items i ON r.ppe_item_id = i.id 
            $where 
            ORDER BY r.id DESC";
            
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit();
}

// 3. PROSES APPROVAL / REJECTION
if ($action === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = (int)$_POST['request_id'];
    $decision = $_POST['decision']; // 'Approve' or 'Reject'
    $catatan = trim($_POST['catatan'] ?? '');

    // Ambil detail request
    $reqStmt = $conn->prepare("SELECT r.*, i.stok FROM ppe_requests r JOIN ppe_items i ON r.ppe_item_id = i.id WHERE r.id = ?");
    $reqStmt->bind_param("i", $requestId);
    $reqStmt->execute();
    $req = $reqStmt->get_result()->fetch_assoc();

    if (!$req) {
        echo json_encode(['status' => 'error', 'message' => 'Data pengajuan tidak ditemukan.']);
        exit();
    }

    $nextStatus = $req['status'];

    if ($decision === 'Reject') {
        $nextStatus = 'Rejected';
    } else {
        // Alur Approval Berjenjang
        if ($req['status'] === 'Pending Supervisor' && in_array($role, ['Supervisor', 'Admin'])) {
            $nextStatus = 'Pending Safety';
        } elseif ($req['status'] === 'Pending Safety' && in_array($role, ['Safety', 'Admin'])) {
            $nextStatus = 'Pending Warehouse';
        } elseif ($req['status'] === 'Pending Warehouse' && in_array($role, ['Warehouse', 'Admin'])) {
            // Cek Stok Gudang
            if ($req['stok'] < $req['jumlah']) {
                echo json_encode(['status' => 'error', 'message' => 'Stok PPE di gudang tidak mencukupi! Sisa stok: ' . $req['stok']]);
                exit();
            }
            
            // Otomatis Potong Stok Saat Warehouse Setuju
            $nextStatus = 'Approved';
            $updateStok = $conn->prepare("UPDATE ppe_items SET stok = stok - ? WHERE id = ?");
            $updateStok->bind_param("ii", $req['jumlah'], $req['ppe_item_id']);
            $updateStok->execute();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki wewenang untuk approval pada tahap ini.']);
            exit();
        }
    }

    // Update Status Pengajuan
    $updateStmt = $conn->prepare("UPDATE ppe_requests SET status = ?, catatan_penolakan = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $nextStatus, $catatan, $requestId);
    $updateStmt->execute();

    // Catat Log Approval
    $logStmt = $conn->prepare("INSERT INTO approval_logs (request_id, user_id, role_approver, action, catatan) VALUES (?, ?, ?, ?, ?)");
    $logStmt->bind_param("iisss", $requestId, $userId, $role, $decision, $catatan);
    $logStmt->execute();

    audit_log($conn, $userId, $_SESSION['nama'], 'APPROVAL_ACTION', "Pengajuan ID #$requestId di-$decision oleh $role");

    echo json_encode(['status' => 'success', 'message' => "Pengajuan berhasil diproses. Status saat ini: $nextStatus"]);
    exit();
}
?>