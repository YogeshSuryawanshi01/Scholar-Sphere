<?php
/**
 * Admin - Approve Notes
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Approve Notes';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note_id'], $_POST['action_type'])) {
    $note_id = (int) $_POST['note_id'];
    $action_type = $_POST['action_type'];

    if ($note_id > 0 && in_array($action_type, ['approve', 'reject'], true)) {
        $new_status = $action_type === 'approve' ? 'approved' : 'rejected';
        $update_stmt = $conn->prepare("UPDATE notes SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $note_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

$pending_stmt = $conn->prepare("SELECT n.id, n.title, n.description, u.name as author, c.name as category, n.status, n.created_at 
                               FROM notes n 
                               JOIN users u ON n.user_id = u.id 
                               JOIN categories c ON n.category_id = c.id 
                               ORDER BY n.created_at ASC");
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
$pending_notes = [];
while ($note = $pending_result->fetch_assoc()) {
    $pending_notes[] = $note;
}
$pending_stmt->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .admin-container {
        padding: 60px 0;
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

    .note-review-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .note-review-card:hover {
        border-color: rgba(14, 165, 164, 0.42);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    }

    .note-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .note-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        font-size: 0.9rem;
        flex-wrap: wrap;
    }

    .meta-item {
        color: var(--text-muted);
    }

    .meta-item i {
        margin-right: 5px;
        color: var(--secondary-dark);
    }

    .note-description {
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .note-actions {
        display: flex;
        gap: 10px;
    }

    .btn-approve, .btn-reject {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        flex: 1;
    }

    .btn-approve {
        background: #10B981;
        color: white;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #EF4444;
        color: white;
    }

    .btn-reject:hover {
        background: #DC2626;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .category-badge {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-file-check"></i> Notes</h1>
            <p style="color: var(--text-muted);">Approve or reject note submissions before they appear publicly.</p>
        </div>

        <?php if (count($pending_notes) > 0): ?>
        <div>
            <?php foreach ($pending_notes as $note): ?>
            <div class="note-review-card">
                <h3 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h3>
                
                <div class="note-meta">
                    <span class="meta-item"><i class="fas fa-user"></i> <?php echo htmlspecialchars($note['author']); ?></span>
                    <span class="meta-item"><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                    <span class="category-badge"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($note['category']); ?></span>
                    <span class="meta-item" style="margin-left:auto;"><strong>Status:</strong> <?php echo htmlspecialchars($note['status']); ?></span>
                </div>

                <p class="note-description"><?php echo htmlspecialchars(substr($note['description'], 0, 200)); ?>...</p>

                <div class="note-actions" style="justify-content: flex-end;">
                    <?php if ($note['status'] === 'pending'): ?>
                    <form method="POST" style="display: flex; gap: 10px; width: 100%;">
                        <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                        <button type="submit" name="action_type" value="approve" class="btn-approve">Approve</button>
                        <button type="submit" name="action_type" value="reject" class="btn-reject">Reject</button>
                    </form>
                    <?php else: ?>
                    <span style="font-weight: 700; color: <?php echo $note['status'] === 'approved' ? '#166534' : '#991B1B'; ?>;">
                        <?php echo ucfirst($note['status']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>No Pending Notes</h3>
            <p>There are no note submissions waiting for review.</p>
        </div>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
