<?php
// Fungsi Keamanan XSS
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF Token
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Cek Login & Role
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function has_role($role_name) {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === $role_name;
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Ambil Setting Global (Logo & Background)
function get_settings($pdo) {
    return $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
}

// Audit Log
function log_audit($pdo, $action) {
    $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'] ?? 0, $action, $_SERVER['REMOTE_ADDR']]);
}