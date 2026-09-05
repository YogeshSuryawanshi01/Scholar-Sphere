<?php
/**
 * Logout Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');

// Clear all session variables
session_destroy();

// Redirect to home
header('Location: index.php');
exit;
?>
