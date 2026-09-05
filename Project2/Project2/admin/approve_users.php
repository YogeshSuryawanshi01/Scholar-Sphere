<?php
/**
 * Admin - Approve Users
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Approve Users';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action_type'])) {
    $user_id = (int) $_POST['user_id'];
    $action_type = $_POST['action_type'];

    if ($user_id > 0 && in_array($action_type, ['approve', 'reject'], true)) {
        $new_status = $action_type === 'approve' ? 'approved' : 'rejected';
        $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
        $update_stmt->bind_param("si", $new_status, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

$pending_stmt = $conn->prepare("SELECT id, name, email, role, status, created_at FROM users WHERE role = 'user' ORDER BY created_at ASC");
$pending_stmt->execute();
$pending_result = $pending_stmt->get_result();
$pending_users = [];
while ($user = $pending_result->fetch_assoc()) {
    $pending_users[] = $user;
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

    .table-container {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px;
        overflow: hidden;
        margin: 30px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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

    .btn-approve, .btn-reject {
        padding: 8px 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.85rem;
        vertical-align: middle;
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
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-user-check"></i> Users</h1>
            <p style="color: var(--text-muted);">Approve or reject pending user registrations.</p>
        </div>

        <?php if (count($pending_users) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_users as $user): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['status']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['status'] === 'pending'): ?>
                            <form method="POST" style="display: flex; gap: 8px;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="action_type" value="approve" class="btn-approve">Approve</button>
                                <button type="submit" name="action_type" value="reject" class="btn-reject">Reject</button>
                            </form>
                            <?php else: ?>
                            <span><?php echo ucfirst($user['status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-check-circle"></i>
            <h3>No Pending Users</h3>
            <p>There are no user accounts waiting for review.</p>
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
