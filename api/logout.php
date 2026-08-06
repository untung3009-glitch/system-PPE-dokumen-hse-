<?php
require 'koneksi.php';
add_log($pdo, "Logout dari sistem");
session_unset();
session_destroy();
send_json(['status' => 'success']);
?>