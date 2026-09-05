<?php
/**
 * Download Handler
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

$note_id = (int)($_GET['id'] ?? 0);

if ($note_id === 0) {
    header('Location: notes.php');
    exit;
}

// Get note file path
$stmt = $conn->prepare("SELECT file_path, title FROM notes WHERE id = ? AND status = 'approved'");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: notes.php');
    exit;
}

$note = $result->fetch_assoc();
$stmt->close();

// Increment download counter
$update_stmt = $conn->prepare("UPDATE notes SET downloads = downloads + 1 WHERE id = ?");
$update_stmt->bind_param("i", $note_id);
$update_stmt->execute();
$update_stmt->close();

// Add log entry
$log_stmt = $conn->prepare("INSERT INTO messages (user_id, message_type, message_text) VALUES (?, 'user', ?)");
$user_id = isLoggedIn() ? $_SESSION['user_id'] : 0;
$log_text = "Downloaded: " . $note['title'];
$log_stmt->bind_param("is", $user_id, $log_text);
$log_stmt->execute();
$log_stmt->close();

// Download file (path from project root)
$file_path = __DIR__ . '/' . $note['file_path'];

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($note['file_path']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    
    readfile($file_path);
    exit;
} else {
    header('Location: notes.php');
    exit;
}
?>
