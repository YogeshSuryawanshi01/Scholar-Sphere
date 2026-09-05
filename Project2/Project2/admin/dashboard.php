<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Admin Dashboard';

$user_count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$user_count_stmt->execute();
$user_count = $user_count_stmt->get_result()->fetch_assoc()['total'];
$user_count_stmt->close();

$pending_users_stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE status = 'pending'");
$pending_users_stmt->execute();
$pending_users = $pending_users_stmt->get_result()->fetch_assoc()['total'];
$pending_users_stmt->close();

$notes_count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM notes");
$notes_count_stmt->execute();
$notes_count = $notes_count_stmt->get_result()->fetch_assoc()['total'];
$notes_count_stmt->close();

$pending_notes_stmt = $conn->prepare("SELECT COUNT(*) as total FROM notes WHERE status = 'pending'");
$pending_notes_stmt->execute();
$pending_notes = $pending_notes_stmt->get_result()->fetch_assoc()['total'];
$pending_notes_stmt->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .admin-container {
        padding: 60px 0;
    }

    .admin-shell {
        display: grid;
        gap: 30px;
    }

    .admin-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(11, 127, 123, 0.92) 100%);
        border: 1px solid rgba(249, 115, 22, 0.28);
        border-radius: 24px;
        padding: 32px;
        color: var(--text-light);
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.16);
        position: relative;
        overflow: hidden;
    }

    .admin-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(249, 115, 22, 0.18), transparent 32%),
            radial-gradient(circle at bottom left, rgba(14, 165, 164, 0.22), transparent 40%);
        pointer-events: none;
    }

    .admin-hero > * {
        position: relative;
        z-index: 1;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-start;
    }

    .admin-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .admin-hero .admin-header h1 {
        background: none;
        -webkit-text-fill-color: unset;
        color: var(--text-light);
    }

    .admin-subtitle {
        color: rgba(248, 250, 252, 0.82);
        max-width: 620px;
        margin: 12px 0 0;
    }

    .admin-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .admin-action-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: var(--transition);
    }

    .admin-action-link.primary {
        background: linear-gradient(135deg, var(--accent) 0%, #FB923C 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.28);
    }

    .admin-action-link.secondary {
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: var(--text-light);
    }

    .admin-action-link:hover {
        transform: translateY(-2px);
        color: inherit;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px;
        padding: 30px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        border-color: rgba(14, 165, 164, 0.42);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        transform: translateY(-5px);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        inset: auto -20% -55% auto;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(14, 165, 164, 0.08);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: var(--secondary);
        margin-bottom: 15px;
    }

    .stat-card.pending .stat-icon {
        color: #F59E0B;
    }

    .stat-name {
        color: var(--text-muted);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .admin-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .admin-btn {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--text-dark);
    }

    .admin-btn:hover {
        border-color: var(--secondary);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        transform: translateY(-5px);
        color: var(--text-dark);
    }

    .admin-btn i {
        font-size: 2rem;
        color: var(--secondary-dark);
        margin-bottom: 15px;
        display: block;
    }

    .admin-btn h4 {
        margin: 10px 0 5px 0;
    }

    .admin-btn p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    .admin-section-title h2 {
        font-size: 1.5rem;
        margin: 0;
        color: var(--text-dark);
    }

    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
        }

        .admin-actions {
            justify-content: flex-start;
        }
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-shell">
            <div class="admin-hero">
                <div class="admin-header">
                    <div>
                        <h1><i class="fas fa-chart-line"></i> Admin Dashboard</h1>
                        <p class="admin-subtitle">A focused overview of your platform so approvals, content, and user management stay easy to track.</p>
                    </div>
                    <div class="admin-actions">
                        <a href="categories.php" class="admin-action-link primary">
                            <i class="fas fa-layer-group"></i> Categories
                        </a>
                        <a href="../dashboard.php" class="admin-action-link secondary">
                            <i class="fas fa-up-right-from-square"></i> Root Admin URL
                        </a>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-name">Total Users</div>
                    <div class="stat-value"><?php echo $user_count; ?></div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-name">Pending Approvals</div>
                    <div class="stat-value"><?php echo $pending_users; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-name">Total Notes</div>
                    <div class="stat-value"><?php echo $notes_count; ?></div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="stat-name">Pending Notes</div>
                    <div class="stat-value"><?php echo $pending_notes; ?></div>
                </div>
            </div>

            <div class="admin-section-title">
                <h2>Management</h2>
            </div>

            <div class="admin-buttons">
                <a href="approve_users.php" class="admin-btn">
                    <i class="fas fa-user-check"></i>
                    <h4>Approve Users</h4>
                    <p><?php echo $pending_users; ?> pending approvals</p>
                </a>

                <a href="approve_notes.php" class="admin-btn">
                    <i class="fas fa-file-check"></i>
                    <h4>Approve Notes</h4>
                    <p><?php echo $pending_notes; ?> pending approvals</p>
                </a>

                <a href="categories.php" class="admin-btn">
                    <i class="fas fa-list"></i>
                    <h4>Manage Categories</h4>
                    <p>Create and edit categories</p>
                </a>

                <a href="users.php" class="admin-btn">
                    <i class="fas fa-users-cog"></i>
                    <h4>Manage Users</h4>
                    <p>View and manage all users</p>
                </a>

                <a href="notes.php" class="admin-btn">
                    <i class="fas fa-book-medical"></i>
                    <h4>Manage Notes</h4>
                    <p>View and manage all notes</p>
                </a>

                <a href="contact_requests.php" class="admin-btn">
                    <i class="fas fa-envelope-open-text"></i>
                    <h4>Contact Requests</h4>
                    <p>Review public contact form submissions</p>
                </a>

                <a href="../index.php" class="admin-btn">
                    <i class="fas fa-arrow-left"></i>
                    <h4>Back to Home</h4>
                    <p>Return to main site</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
