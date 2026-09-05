<?php
/**
 * Admin - Manage Categories
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Manage Categories';

$error = '';
$success = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int)($_POST['delete_id'] ?? 0);

    $usage_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notes WHERE category_id = ?");
    $usage_stmt->bind_param("i", $delete_id);
    $usage_stmt->execute();
    $notes_using_category = (int) $usage_stmt->get_result()->fetch_assoc()['total'];
    $usage_stmt->close();

    if ($notes_using_category > 0) {
        $error = 'This category cannot be deleted because notes are still assigned to it.';
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success = 'Category deleted successfully';
        } else {
            $error = 'Error deleting category';
        }
        $stmt->close();
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $name = sanitize($_POST['category_name'], $conn);
    $description = sanitize($_POST['category_description'] ?? '', $conn);
    $cat_id = (int)($_POST['category_id'] ?? 0);

    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        $duplicate_stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
        $duplicate_stmt->bind_param("si", $name, $cat_id);
        $duplicate_stmt->execute();
        $duplicate_exists = $duplicate_stmt->get_result()->num_rows > 0;
        $duplicate_stmt->close();

        if ($duplicate_exists) {
            $error = 'A category with this name already exists.';
        } else if ($cat_id > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $cat_id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
        }

        if (!$duplicate_exists) {
            if ($stmt->execute()) {
                $success = "Category " . ($cat_id > 0 ? 'updated' : 'added') . " successfully";
                $_POST = array();
            } else {
                $error = "Error saving category";
            }
            $stmt->close();
        }
    }
}

// Get all categories
$categories_stmt = $conn->prepare("SELECT id, name, description, created_at FROM categories ORDER BY name");
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}
$categories_stmt->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .admin-container {
        padding: 60px 0;
    }

    .admin-shell {
        display: grid;
        gap: 28px;
    }

    .admin-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(11, 127, 123, 0.92) 100%);
        border: 1px solid rgba(249, 115, 22, 0.28);
        border-radius: 24px;
        padding: 30px 32px;
        color: var(--text-light);
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.16);
    }

    .admin-header h1 {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .admin-hero .admin-header h1 {
        background: none;
        -webkit-text-fill-color: unset;
        color: var(--text-light);
        margin-bottom: 12px;
    }

    .admin-hero .admin-header p {
        color: rgba(248, 250, 252, 0.82) !important;
        margin: 0;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    .form-card, .categories-list {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .form-card h3 {
        color: var(--secondary-dark);
        font-weight: 700;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-weight: 600;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-dark);
        font-size: 0.9rem;
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .form-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-submit, .btn-reset {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(15, 23, 42, 0.20);
    }

    .btn-reset {
        background: transparent;
        color: var(--secondary-dark);
        border: 2px solid rgba(14, 165, 164, 0.45);
    }

    .btn-reset:hover {
        background: var(--secondary);
        color: white;
    }

    .categories-list h3 {
        color: var(--secondary-dark);
        font-weight: 700;
        margin-bottom: 25px;
    }

    .category-item {
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .category-item:hover {
        border-color: var(--secondary);
    }

    .category-name {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .category-description {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .category-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-edit, .btn-delete {
        flex: 1;
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: #06B6D4;
        color: white;
    }

    .btn-edit:hover {
        background: #0891B2;
    }

    .btn-delete {
        background: #EF4444;
        color: white;
    }

    .btn-delete:hover {
        background: #DC2626;
    }

    .error-message, .success-message {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .error-message {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #991B1B;
    }

    .success-message {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #166534;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-shell">
        <div class="admin-hero">
            <div class="admin-header">
                <h1><i class="fas fa-list"></i> Manage Categories</h1>
                <p style="color: var(--text-muted);">Add, edit, or delete note categories with the same clean admin experience as the rest of the app.</p>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="content-grid">
            <!-- Form -->
            <div class="form-card">
                <h3><i class="fas fa-plus"></i> Add/Edit Category</h3>
                <form method="POST">
                    <input type="hidden" id="category_id" name="category_id" value="">

                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name" placeholder="e.g., Mathematics" required>
                    </div>

                    <div class="form-group">
                        <label for="category_description">Description</label>
                        <textarea id="category_description" name="category_description" placeholder="Category description" rows="4"></textarea>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save</button>
                        <button type="reset" class="btn-reset"><i class="fas fa-redo"></i> Clear</button>
                    </div>
                </form>
            </div>

            <!-- List -->
            <div class="categories-list">
                <h3><i class="fas fa-layer-group"></i> All Categories (<?php echo count($categories); ?>)</h3>
                <div style="max-height: 500px; overflow-y: auto;">
                    <?php foreach ($categories as $cat): ?>
                    <div class="category-item">
                        <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                        <?php if (!empty($cat['description'])): ?>
                            <div class="category-description"><?php echo htmlspecialchars(substr($cat['description'], 0, 100)); ?></div>
                        <?php endif; ?>
                        <div class="category-actions">
                            <button
                                type="button"
                                class="btn-edit"
                                data-category-id="<?php echo $cat['id']; ?>"
                                data-category-name="<?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?>"
                                data-category-description="<?php echo htmlspecialchars($cat['description'] ?? '', ENT_QUOTES); ?>"
                                onclick="editCategory(this)"
                            >
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" style="flex: 1;">
                                <input type="hidden" name="delete_id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="btn-delete" style="width: 100%;" onclick="return confirm('Delete this category?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        </div>
    </div>
</div>

<script>
function editCategory(button) {
    document.getElementById('category_id').value = button.dataset.categoryId || '';
    document.getElementById('category_name').value = button.dataset.categoryName || '';
    document.getElementById('category_description').value = button.dataset.categoryDescription || '';
    document.getElementById('category_name').focus();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
