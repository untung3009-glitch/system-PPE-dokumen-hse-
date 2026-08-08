<?php
require_once 'config/config.php';
if (isset($_SESSION['user_id'])) {
    audit_log($conn, $_SESSION['user_id'], $_SESSION['nama'], 'LOGOUT', 'User logout dari sistem');
}
session_destroy();
header('Location: index.php');
exit();
?>