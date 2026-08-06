<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['error' => 'Unauthorized']); exit; }

 $status = $_GET['status'] ?? 'all';

if ($status === 'all') {
    $stmt = $pdo->query("SELECT p.*, u.name as user_name FROM pengajuan p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC");
} else {
    $stmt = $pdo->prepare("SELECT p.*, u.name as user_name FROM pengajuan p JOIN users u ON p.user_id = u.id WHERE p.status = ? ORDER BY p.id DESC");
    $stmt->execute([$status]);
}

echo json_encode($stmt->fetchAll());