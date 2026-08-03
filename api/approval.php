<?php
require_once 'config.php';
header("Content-Type: application/json; charset=UTF-8");

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
$action = $input['action'] ?? ''; // approve atau reject
$level = $input['level'] ?? '';   // safety atau pm

if (!$id || !$action || !$level) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
    exit();
}

try {
    if ($level === 'safety') {
        $newStatus = ($action === 'approve') ? 'Pending PM' : 'Rejected';
        $stmt = $db->prepare("UPDATE pengajuan_apd SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $newStatus, 'id' => $id]);
    } elseif ($level === 'pm') {
        $newStatus = ($action === 'approve') ? 'Siap Serah Terima' : 'Rejected';
        $tanggalApprove = date('Y-m-d');
        $stmt = $db->prepare("UPDATE pengajuan_apd SET status = :status, tgl_approve = :tgl WHERE id = :id");
        $stmt->execute(['status' => $newStatus, 'tgl' => $tanggalApprove, 'id' => $id]);
    }

    echo json_encode(["status" => "success", "message" => "Approval berhasil diperbarui!"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>