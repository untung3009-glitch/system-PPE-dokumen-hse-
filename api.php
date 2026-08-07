<?php
require_once 'config.php';
header('Content-Type: application/json');

 $method = $_SERVER['REQUEST_METHOD'];
 $action = $_GET['action'] ?? '';

// Fungsi Audit Trail
function log_audit($pdo, $user_id, $aksi) {
    $stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, aksi) VALUES (?, ?)");
    $stmt->execute([$user_id, $aksi]);
}

if ($action === 'login' && $method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $nik = $data['nik'];
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
    $stmt->execute([$nik]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];
        log_audit($pdo, $user['id'], "Login berhasil");
        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'NIK atau Password salah']);
    }
    exit;
}

// Middleware Autentikasi untuk endpoint lain
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

 $user_id = $_SESSION['user_id'];
 $role = $_SESSION['role'];

if ($action === 'submit_request' && $method === 'POST') {
    // Handle Form Data dengan Upload Foto
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $departemen = $_POST['departemen'];
    $jabatan = $_POST['jabatan'];
    $area_kerja = $_POST['area_kerja'];
    $jenis_ppe = $_POST['jenis_ppe'];
    $jumlah = $_POST['jumlah'];
    $alasan = $_POST['alasan'];
    $fotoPath = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fotoPath = $uploadDir . time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath);
    }

    $stmt = $pdo->prepare("INSERT INTO ppe_requests (user_id, nama, nik, departemen, jabatan, area_kerja, jenis_ppe, jumlah, alasan, foto, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending Supervisor')");
    $stmt->execute([$user_id, $nama, $nik, $departemen, $jabatan, $area_kerja, $jenis_ppe, $jumlah, $alasan, $fotoPath]);
    
    log_audit($pdo, $user_id, "Mengajukan PPE: $jenis_ppe ($jumlah)");
    echo json_encode(['status' => 'success', 'message' => 'Pengajuan berhasil dikirim']);
    exit;
}

if ($action === 'get_requests') {
    $query = "SELECT * FROM ppe_requests WHERE 1=1";
    $params = [];

    // Filter berdasarkan role
    if ($role === 'Supervisor') {
        $query .= " AND status = 'Pending Supervisor'";
    } elseif ($role === 'Safety') {
        $query .= " AND status = 'Pending Safety'";
    } elseif ($role === 'Warehouse') {
        $query .= " AND status = 'Pending Warehouse'";
    } elseif ($role === 'Karyawan') {
        $query .= " AND user_id = ?";
        $params[] = $user_id;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'approve_request' && $method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $req_id = $data['id'];
    $decision = $data['decision']; // 'approve' atau 'reject'

    $stmt = $pdo->prepare("SELECT * FROM ppe_requests WHERE id = ?");
    $stmt->execute([$req_id]);
    $req = $stmt->fetch();

    if (!$req) {
        echo json_encode(['status' => 'error', 'message' => 'Request tidak ditemukan']);
        exit;
    }

    if ($decision === 'reject') {
        $pdo->prepare("UPDATE ppe_requests SET status = 'Rejected' WHERE id = ?")->execute([$req_id]);
        log_audit($pdo, $user_id, "Menolak pengajuan PPE ID: $req_id");
    } else {
        // Logika Approval Flow
        if ($role === 'Supervisor' && $req['status'] === 'Pending Supervisor') {
            $pdo->prepare("UPDATE ppe_requests SET status = 'Pending Safety' WHERE id = ?")->execute([$req_id]);
            log_audit($pdo, $user_id, "Approve Supervisor PPE ID: $req_id");
        } elseif ($role === 'Safety' && $req['status'] === 'Pending Safety') {
            $pdo->prepare("UPDATE ppe_requests SET status = 'Pending Warehouse' WHERE id = ?")->execute([$req_id]);
            log_audit($pdo, $user_id, "Approve Safety PPE ID: $req_id");
        } elseif ($role === 'Warehouse' && $req['status'] === 'Pending Warehouse') {
            // Final Approval: Kurangi Stok
            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE ppe_requests SET status = 'Approved' WHERE id = ?")->execute([$req_id]);
                
                $stmtStok = $pdo->prepare("SELECT stok FROM ppe_items WHERE nama_ppe = ?");
                $stmtStok->execute([$req['jenis_ppe']]);
                $item = $stmtStok->fetch();

                if ($item && $item['stok'] >= $req['jumlah']) {
                    $pdo->prepare("UPDATE ppe_items SET stok = stok - ? WHERE nama_ppe = ?")->execute([$req['jumlah'], $req['jenis_ppe']]);
                    $pdo->commit();
                    log_audit($pdo, $user_id, "Approve Warehouse & Stok dikurangi PPE ID: $req_id");
                } else {
                    $pdo->rollBack();
                    echo json_encode(['status' => 'error', 'message' => 'Stok PPE tidak mencukupi!']);
                    exit;
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
                exit;
            }
        }
    }
    echo json_encode(['status' => 'success', 'message' => 'Status pengajuan diperbarui']);
    exit;
}

if ($action === 'get_stats') {
    $total = $pdo->query("SELECT COUNT(*) as count FROM ppe_requests")->fetch()['count'];
    $approved = $pdo->query("SELECT COUNT(*) as count FROM ppe_requests WHERE status='Approved'")->fetch()['count'];
    $rejected = $pdo->query("SELECT COUNT(*) as count FROM ppe_requests WHERE status='Rejected'")->fetch()['count'];
    $pending = $pdo->query("SELECT COUNT(*) as count FROM ppe_requests WHERE status LIKE 'Pending%'")->fetch()['count'];
    $stock = $pdo->query("SELECT SUM(stok) as count FROM ppe_items")->fetch()['count'];

    // Data untuk Chart
    $chartData = $pdo->query("SELECT jenis_ppe, SUM(jumlah) as total FROM ppe_requests WHERE status='Approved' GROUP BY jenis_ppe")->fetchAll();

    echo json_encode([
        'status' => 'success',
        'total' => $total,
        'approved' => $approved,
        'rejected' => $rejected,
        'pending' => $pending,
        'stock' => $stock,
        'chart' => $chartData
    ]);
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['status' => 'success']);
    exit;
}