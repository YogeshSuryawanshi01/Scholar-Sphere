<?php
/**
 * Upload Notes Page
 * Allow users to upload notes
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

requireLogin();

$page_title = 'Upload Notes';
$error = '';
$success = '';

// Get categories
$categories_stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name");
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}
$categories_stmt->close();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '', $conn);
    $description = sanitize($_POST['description'] ?? '', $conn);
    $category_id = (int)($_POST['category_id'] ?? 0);

    // Validate inputs
    if (empty($title) || empty($description) || $category_id === 0) {
        $error = 'All fields are required';
    } else if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload failed';
    } else {
        $file = $_FILES['file'];
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                         'image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'];
        $max_size = 50 * 1024 * 1024; // 50MB

        // Check file size
        if ($file['size'] > $max_size) {
            $error = 'File size exceeds 50MB limit';
        } else if (!in_array($file['type'], $allowed_types)) {
            $error = 'File type not allowed';
        } else {
            // Generate unique filename
            $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $upload_dir_path = __DIR__ . '/uploads/';
            $file_path = 'uploads/' . $file_name;

            // Ensure uploads directory exists
            if (!is_dir($upload_dir_path)) {
                mkdir($upload_dir_path, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_dir_path . $file_name)) {
                $note_status = isAdmin() ? 'approved' : 'pending';

                // Insert into database
                $stmt = $conn->prepare("INSERT INTO notes (user_id, title, description, category_id, file_path, file_type, file_size, status) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ississis", $_SESSION['user_id'], $title, $description, $category_id, $file_path, $file['type'], $file['size'], $note_status);

                if ($stmt->execute()) {
                    $success = $note_status === 'approved'
                        ? 'Note uploaded successfully and published.'
                        : 'Note uploaded successfully! It is now pending admin approval.';
                    $_POST = array();
                    $_FILES = array();
                } else {
                    $error = 'Database error. Please try again.';
                    unlink($_SERVER['DOCUMENT_ROOT'] . $file_path);
                }
                $stmt->close();
            } else {
                $error = 'File move failed';
            }
        }
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .upload-container {
        max-width: 800px;
        margin: 60px auto;
        padding: 20px;
    }

    .upload-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 48px rgba(15, 23, 42, 0.10);
    }

    .upload-card h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 30px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        color: var(--text-dark);
        font-weight: 600;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 12px 15px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-dark);
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        background-color: var(--surface);
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .file-upload-area {
        border: 2px dashed rgba(14, 165, 164, 0.35);
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: rgba(248, 250, 252, 0.92);
    }

    .file-upload-area:hover {
        border-color: var(--secondary);
        background: rgba(14, 165, 164, 0.10);
    }

    .file-upload-area i {
        font-size: 2.5rem;
        color: var(--secondary);
        margin-bottom: 15px;
    }

    .file-upload-area p {
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .file-input {
        display: none;
    }

    .file-info {
        margin-top: 15px;
        padding: 15px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 8px;
        color: #166534;
        display: none;
    }

    .error-message {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #991B1B;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .success-message {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #166534;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .submit-btn {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.18);
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.24);
    }
</style>

<div class="upload-container">
    <div class="upload-card">
        <h1><i class="fas fa-cloud-upload-alt"></i> Upload Notes</h1>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Note Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    placeholder="Enter note title" 
                    required
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                >
            </div>

            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="Describe your note..." 
                    required
                ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="category_id"><i class="fas fa-list"></i> Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="file"><i class="fas fa-file"></i> Upload File</label>
                <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('file').click();">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><strong>Click to upload</strong> or drag and drop</p>
                    <p style="font-size: 0.85rem;">PDF, DOC, DOCX, Images, or Videos (Max 50MB)</p>
                </div>
                <input type="file" id="file" name="file" required>
                <div class="file-info" id="fileInfo">
                    <i class="fas fa-check-circle"></i> 
                    <span id="fileName"></span> - <span id="fileSize"></span>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-upload"></i> Upload Note
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);
        document.getElementById('fileInfo').style.display = 'block';
    }
});

function formatFileSize(bytes) {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    return size.toFixed(2) + ' ' + units[unitIndex];
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
