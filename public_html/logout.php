<?php
require 'config/database.php';
require 'config/helper.php';
log_audit($pdo, 'User Logout');
session_destroy();
header('Location: login.php');
exit;