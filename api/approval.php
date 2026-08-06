<?php
require 'koneksi.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $role = $_POST['role']; // safety atau pm
    $isApproved = $_POST['isApproved'] === 'true';
    
    $stmt = $pdo->prepare("SELECT * FROM pengajuan_apd WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();

    if (!$doc) send_json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

    if ($role === 'safety') {
        check_auth(['Safety Coordinator', 'Administrator']);
        $doc['app_safety'] = $isApproved ? 'Approved' : 'Rejected';
        $doc['status'] = $isApproved ? 'Pending PM' : 'Rejected';
    } elseif ($role === 'pm') {
        check_auth(['Project Manager', 'Administrator']);
        $doc['app_pm'] = $isApproved ? 'Approved' : 'Rejected';
        $doc['status'] = $isApproved ? 'Completed' : 'Rejected';
        
        // Auto Update Stock jika PM Approve
        if ($isApproved) {
            $updStock = $pdo->prepare("UPDATE master_ppe SET stock = stock - ? WHERE name = ?");
            $updStock->execute([$doc['qty'], $doc['apd_name']]);
        }
    }

    $upd = $pdo->prepare("UPDATE pengajuan_apd SET status=?, app_safety=?, app_pm=? WHERE id=?");
    $upd->execute([$doc['status'], $doc['app_safety'], $doc['app_pm'], $id]);
    
    add_log($pdo, "Approval $role untuk doc $doc[doc_no]: " . ($isApproved ? 'Approved' : 'Rejected'));
    send_json(['status' => 'success', 'message' => 'Status diperbarui']);
}
?>