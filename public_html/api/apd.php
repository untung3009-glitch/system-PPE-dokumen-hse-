<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

 $action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'fetch':
        $stmt = $pdo->query("SELECT * FROM apd ORDER BY id DESC");
        echo json_encode($stmt->fetchAll());
        break;

    case 'store':
        $name = e($_POST['name']);
        $size = e($_POST['size']);
        $stock = (int)$_POST['stock'];
        $stmt = $pdo->prepare("INSERT INTO apd (name, size, stock) VALUES (?, ?, ?)");
        $stmt->execute([$name, $size, $stock]);
        log_audit($pdo, "Tambah APD: $name");
        echo json_encode(['status' => 'success', 'message' => 'APD berhasil ditambahkan']);
        break;

    case 'update':
        $id = (int)$_POST['id'];
        $name = e($_POST['name']);
        $size = e($_POST['size']);
        $stock = (int)$_POST['stock'];
        $stmt = $pdo->prepare("UPDATE apd SET name = ?, size = ?, stock = ? WHERE id = ?");
        $stmt->execute([$name, $size, $stock, $id]);
        log_audit($pdo, "Update APD ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'APD berhasil diperbarui']);
        break;

    case 'delete':
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM apd WHERE id = ?");
        $stmt->execute([$id]);
        log_audit($pdo, "Hapus APD ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'APD berhasil dihapus']);
        break;

    default:
        echo json_encode(['error' => 'Invalid Action']);
}