<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = trim($_POST['nik'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($nik) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'NIK dan Password wajib diisi.']);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, nik, nama, password, role, departemen, jabatan, area_kerja FROM users WHERE nik = ?");
    $stmt->bind_param("s", $nik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nik'] = $user['nik'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['departemen'] = $user['departemen'];
            $_SESSION['jabatan'] = $user['jabatan'];
            $_SESSION['area_kerja'] = $user['area_kerja'];

            audit_log($conn, $user['id'], $user['nama'], 'LOGIN', 'User berhasil login ke sistem');

            echo json_encode([
                'status' => 'success',
                'message' => 'Login berhasil',
                'user' => [
                    'nama' => $user['nama'],
                    'role' => $user['role'],
                    'nik'  => $user['nik']
                ]
            ]);
            exit();
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'NIK atau Password tidak valid!']);
    exit();
}

if ($action === 'session') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'success',
            'user' => [
                'id' => $_SESSION['user_id'],
                'nik' => $_SESSION['nik'],
                'nama' => $_SESSION['nama'],
                'role' => $_SESSION['role'],
                'departemen' => $_SESSION['departemen'],
                'jabatan' => $_SESSION['jabatan'],
                'area_kerja' => $_SESSION['area_kerja']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'unauthenticated']);
    }
    exit();
}
?>