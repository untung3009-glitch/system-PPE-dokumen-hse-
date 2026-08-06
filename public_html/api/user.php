<?php
require_once '../config/database.php';
require_once '../config/helper.php';
header('Content-Type: application/json');

if (!is_logged_in() || $_SESSION['role_name'] !== 'Admin') {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

 $action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'fetch':
        $stmt = $pdo->query("SELECT u.id, u.name, u.email, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id ASC");
        echo json_encode($stmt->fetchAll());
        break;

    case 'store':
        $name = e($_POST['name']);
        $email = e($_POST['email']);
        $password = $_POST['password'];
        $role_id = (int)$_POST['role_id'];

        // Cek email duplikat
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        if($stmtCheck->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Email sudah terdaftar']); exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role_id]);
        log_audit($pdo, "Tambah User: $name");
        echo json_encode(['status' => 'success', 'message' => 'User berhasil ditambahkan']);
        break;

    case 'update':
        $id = (int)$_POST['id'];
        $name = e($_POST['name']);
        $email = e($_POST['email']);
        $role_id = (int)$_POST['role_id'];
        $password = $_POST['password'];

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role_id = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $email, $role_id, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
            $stmt->execute([$name, $email, $role_id, $id]);
        }
        log_audit($pdo, "Update User ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'User berhasil diperbarui']);
        break;

    case 'delete':
        $id = (int)$_POST['id'];
        if($id == $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak bisa menghapus akun sendiri']); exit;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        log_audit($pdo, "Hapus User ID: $id");
        echo json_encode(['status' => 'success', 'message' => 'User berhasil dihapus']);
        break;

    default:
        echo json_encode(['error' => 'Invalid Action']);
}