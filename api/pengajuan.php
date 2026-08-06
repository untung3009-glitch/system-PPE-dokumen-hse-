<?php
require 'koneksi.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM pengajuan_apd ORDER BY created_at DESC");
    send_json(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_auth(['Employee', 'Administrator']); // Hanya User biasa dan Admin yang bisa mengajukan
    
    $doc_no = 'PPE-' . date('Ymd') . '-' . rand(100,999);
    $uploadDir = '../uploads/';
    
    $apdPhotoPath = '-';
    if (!empty($_FILES['photoApd']['name'])) {
        $ext = pathinfo($_FILES['photoApd']['name'], PATHINFO_EXTENSION);
        $fileName = $doc_no . '_apd.' . $ext;
        move_uploaded_file($_FILES['photoApd']['tmp_name'], $uploadDir . $fileName);
        $apdPhotoPath = 'uploads/' . $fileName;
    }

    $stmt = $pdo->prepare("INSERT INTO pengajuan_apd (doc_no, user_id, name, nik, dept, loc, apd_name, qty, apd_photo, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $doc_no, $_SESSION['user_id'], $_POST['reqName'], $_POST['reqNik'], $_POST['reqDept'],
        $_POST['reqLoc'], $_POST['apdName'], $_POST['qty'], $apdPhotoPath, $_POST['note']
    ]);
    
    add_log($pdo, "Submit pengajuan APD: $doc_no");
    send_json(['status' => 'success', 'message' => 'Pengajuan berhasil dikirim']);
}
?>