<?php
/**
 * Admin - Manage All Users
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Manage Users';
$status_message = '';
$status_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action_type']) && $_POST['action_type'] === 'discard_user') {
    $user_id = (int) $_POST['user_id'];

    if ($user_id <= 0) {
        $status_message = 'Invalid user selection.';
        $status_type = 'error';
    } else if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === $user_id) {
        $status_message = 'You cannot discard your own administrator account.';
        $status_type = 'error';
    } else {
        $role_stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $role_stmt->bind_param("i", $user_id);
        $role_stmt->execute();
        $user_result = $role_stmt->get_result();
        $user_to_delete = $user_result->fetch_assoc();
        $role_stmt->close();

        if (!$user_to_delete) {
            $status_message = 'That user could not be found.';
            $status_type = 'error';
        } else if ($user_to_delete['role'] === 'admin') {
            $status_message = 'Administrator accounts cannot be discarded from this screen.';
            $status_type = 'error';
        } else {
            $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
            $delete_stmt->bind_param("i", $user_id);

            if ($delete_stmt->execute()) {
                $status_message = 'User discarded successfully.';
            } else {
                $status_message = 'Unable to discard this user right now.';
                $status_type = 'error';
            }

            $delete_stmt->close();
        }
    }
}

// Get all users
$users_stmt = $conn->prepare("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
$users_stmt->execute();
$users_result = $users_stmt->get_result();
$users = [];
while ($user = $users_result->fetch_assoc()) {
    $users[] = $user;
}
$users_stmt->close();
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

    .badge-admin {
        background: var(--secondary);
        color: white;
    }

    .badge-user {
        background: var(--primary);
        color: white;
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

    .muted-note {
        font-size: 0.82rem;
        color: var(--text-muted);
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-shell">
        <div class="admin-hero">
            <div class="admin-header">
                <h1><i class="fas fa-users-cog"></i> Manage All Users</h1>
                <p style="color: var(--text-muted);">Review registered users in the same card-based admin style used across the rest of the site.</p>
            </div>
        </div>

        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Total Users</div>
                <div class="summary-value"><?php echo count($users); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Admins</div>
                <div class="summary-value"><?php echo count(array_filter($users, fn($user) => $user['role'] === 'admin')); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Approved</div>
                <div class="summary-value"><?php echo count(array_filter($users, fn($user) => $user['status'] === 'approved')); ?></div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Pending</div>
                <div class="summary-value"><?php echo count(array_filter($users, fn($user) => $user['status'] === 'pending')); ?></div>
            </div>
        </div>

        <?php if ($status_message !== ''): ?>
        <div class="status-banner <?php echo $status_type; ?>">
            <?php echo htmlspecialchars($status_message); ?>
        </div>
        <?php endif; ?>

        <div class="table-container">
            <?php if (count($users) > 0): ?>
            <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($user['role']); ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($user['status']); ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['role'] === 'user'): ?>
                            <div class="table-actions">
                                <form method="POST" onsubmit="return confirm('Discard this user and their related notes permanently?');">
                                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                    <input type="hidden" name="action_type" value="discard_user">
                                    <button type="submit" class="discard-btn">
                                        <i class="fas fa-user-slash"></i> Discard
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span class="muted-note">Protected admin</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No users are available yet.</p>
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
