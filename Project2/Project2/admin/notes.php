<?php
/**
 * Admin - Manage All Notes
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Manage Notes';
$status_message = '';
$status_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note_id'], $_POST['action_type']) && $_POST['action_type'] === 'discard_note') {
    $note_id = (int) $_POST['note_id'];

    if ($note_id > 0) {
        $file_stmt = $conn->prepare("SELECT file_path FROM notes WHERE id = ?");
        $file_stmt->bind_param("i", $note_id);
        $file_stmt->execute();
        $note_result = $file_stmt->get_result();
        $note_to_delete = $note_result->fetch_assoc();
        $file_stmt->close();

        if ($note_to_delete) {
            $delete_stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
            $delete_stmt->bind_param("i", $note_id);

            if ($delete_stmt->execute()) {
                if (!empty($note_to_delete['file_path'])) {
                    $absolute_file_path = __DIR__ . '/../' . ltrim($note_to_delete['file_path'], '/\\');
                    if (is_file($absolute_file_path)) {
                        @unlink($absolute_file_path);
                    }
                }
                $status_message = 'Note discarded successfully.';
            } else {
                $status_message = 'Unable to discard this note right now.';
                $status_type = 'error';
            }

            $delete_stmt->close();
        } else {
            $status_message = 'That note could not be found.';
            $status_type = 'error';
        }
    } else {
        $status_message = 'Invalid note selection.';
        $status_type = 'error';
    }
}

// Get all notes
$notes_stmt = $conn->prepare("SELECT n.id, n.title, u.name as author, c.name as category, n.status, n.downloads, n.created_at 
                             FROM notes n 
                             JOIN users u ON n.user_id = u.id 
                             JOIN categories c ON n.category_id = c.id 
                             ORDER BY n.created_at DESC");
$notes_stmt->execute();
$notes_result = $notes_stmt->get_result();
$notes = [];
while ($note = $notes_result->fetch_assoc()) {
    $notes[] = $note;
}
$notes_stmt->close();
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

    .summary-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .summary-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .summary-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .summary-value {
        font-size: 1.7rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .table-container {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px;
        overflow: hidden;
        margin: 30px 0;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .table-scroll {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    thead {
        background: rgba(15, 23, 42, 0.06);
    }

    th {
        padding: 15px;
        text-align: left;
        color: var(--primary);
        font-weight: 700;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    td {
        padding: 15px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        color: var(--text-muted);
    }

    tr:hover {
        background: rgba(14, 165, 164, 0.08);
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .badge-approved {
        background: #10B981;
        color: white;
    }

    .badge-pending {
        background: #F59E0B;
        color: white;
    }

    .badge-rejected {
        background: #EF4444;
        color: white;
    }

    .title-link {
        color: var(--secondary-dark);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .title-link:hover {
        color: var(--primary);
    }

    .empty-state {
        text-align: center;
        padding: 50px 24px;
        color: var(--text-muted);
    }

    .status-banner {
        border-radius: 14px;
        padding: 14px 18px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .status-banner.success {
        background: rgba(16, 185, 129, 0.12);
        color: #166534;
        border-color: rgba(16, 185, 129, 0.24);
    }

    .status-banner.error {
        background: rgba(239, 68, 68, 0.10);
        color: #991B1B;
        border-color: rgba(239, 68, 68, 0.22);
    }

    .table-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .discard-btn {
        border: none;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        background: rgba(239, 68, 68, 0.12);
        color: #991B1B;
        transition: var(--transition);
    }

    .discard-btn:hover {
        background: rgba(239, 68, 68, 0.18);
        transform: translateY(-2px);
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-shell">
        <div class="admin-hero">
            <div class="admin-header">
                <h1><i class="fas fa-book-medical"></i> Manage All Notes</h1>
                <p style="color: var(--text-muted);">Browse every uploaded note with a layout that matches the rest of the modern admin UI.</p>
            </div>
        </div>

        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Total Notes</div>
                <div class="summary-value"><?php echo count($notes); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Approved</div>
                <div class="summary-value"><?php echo count(array_filter($notes, fn($note) => $note['status'] === 'approved')); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Pending</div>
                <div class="summary-value"><?php echo count(array_filter($notes, fn($note) => $note['status'] === 'pending')); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Rejected</div>
                <div class="summary-value"><?php echo count(array_filter($notes, fn($note) => $note['status'] === 'rejected')); ?></div>
            </div>
        </div>

        <?php if ($status_message !== ''): ?>
        <div class="status-banner <?php echo $status_type; ?>">
            <?php echo htmlspecialchars($status_message); ?>
        </div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (count($notes) > 0): ?>
            <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Downloads</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $note): ?>
                    <tr>
                        <td>
                            <a href="../note_detail.php?id=<?php echo $note['id']; ?>" class="title-link">
                                <strong><?php echo htmlspecialchars(substr($note['title'], 0, 50)); ?></strong>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($note['author']); ?></td>
                        <td><?php echo htmlspecialchars($note['category']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($note['status']); ?>">
                                <?php echo ucfirst($note['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $note['downloads']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($note['created_at'])); ?></td>
                        <td>
                            <div class="table-actions">
                                <form method="POST" onsubmit="return confirm('Discard this note permanently?');">
                                    <input type="hidden" name="note_id" value="<?php echo (int) $note['id']; ?>">
                                    <input type="hidden" name="action_type" value="discard_note">
                                    <button type="submit" class="discard-btn">
                                        <i class="fas fa-trash"></i> Discard
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <p>No notes are available yet.</p>
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 30px;">
            <a href="dashboard.php" class="btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
