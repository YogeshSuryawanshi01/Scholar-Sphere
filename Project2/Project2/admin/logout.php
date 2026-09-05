<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
?>
