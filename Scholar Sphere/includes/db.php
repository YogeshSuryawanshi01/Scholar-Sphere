<?php
/**
 * Database Connection File
 * Handles secure MySQL connection using MySQLi
 */

require_once __DIR__ . '/session_bootstrap.php';
bootSession();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'YOGESH01');
define('DB_PASS', 'root');
define('DB_NAME', 'scholar_sphere');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection properly
if ($conn->connect_errno) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset (security)
if (!$conn->set_charset("utf8mb4")) {
    die("Error setting charset: " . $conn->error);
}

// Set timezone
date_default_timezone_set('Asia/Kolkata'); // changed for India

/**
 * Function to sanitize user input
 */
function sanitize($data, $conn) {
    return htmlspecialchars($conn->real_escape_string(trim($data)));
}

/**
 * Toast Notification
 */
function showToast($message, $type = 'info') {
    $_SESSION['toast'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Display Toast
 */
function displayToast() {
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        echo "
        <div class='toast-notification toast-{$toast['type']}' id='toast'>
            {$toast['message']}
        </div>
        ";
        unset($_SESSION['toast']);
    }
}

/**
 * Check login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check admin
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login
 */
function getBasePath() {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if (strpos($uri, '/Scholar-Sphere/') === 0) {
        return '/Scholar-Sphere/';
    }
    return '/';
}

function requireLogin() {
    if (!isLoggedIn()) {
        $base = getBasePath();
        header('Location: ' . $base . 'login.php');
        exit();
    }
}

/**
 * Require admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Get average rating
 */
function getAverageRating($note_id, $conn) {
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM ratings WHERE note_id = ?");
    
    if (!$stmt) {
        return ['average' => 0, 'count' => 0];
    }

    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return [
        'average' => $row['avg_rating'] ? round($row['avg_rating'], 1) : 0,
        'count' => $row['rating_count'] ?? 0
    ];
}
?>
