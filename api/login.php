<?php
require 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        add_log($pdo, "Login ke sistem");
        send_json(['status' => 'success', 'user' => ['name' => $user['name'], 'role' => $user['role']]]);
    } else {
        send_json(['status' => 'error', 'message' => 'Username atau Password salah'], 401);
    }
}
?>