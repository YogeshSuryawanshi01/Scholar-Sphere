<?php
/**
 * Admin dashboard entry point.
 * Allows /dashboard.php to work by forwarding to the admin dashboard
 * or the login page when the user is not authenticated.
 */

require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/includes/db.php';

if (!isLoggedIn()) {
    header('Location: admin/login.php');
    exit;
}

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

header('Location: admin/dashboard.php');
exit;
?>
