<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['error' => 'Unauthorized']); exit; }

 $action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    $user_id = $_SESSION['user_id'];
    // Ambil 5 notifikasi terakhir
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $notifs = $stmt->fetchAll();

    // Hitung yang belum dibaca
    $stmtUnread = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmtUnread->execute([$user_id]);
    $unread = $stmtUnread->fetchColumn();

    echo json_encode(['notifs' => $notifs, 'unread' => $unread]);
    exit;
}

if ($action === 'read') {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode(['status' => 'success']);
    exit;
}