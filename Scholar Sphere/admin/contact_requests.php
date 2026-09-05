<?php
/**
 * Admin - Contact Requests
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');
require_once __DIR__ . '/../includes/db.php';

requireAdmin();

$page_title = 'Contact Requests';
$status_message = '';
$status_type = 'success';

$conn->query("
    CREATE TABLE IF NOT EXISTS contact_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('new', 'read', 'resolved') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['status'])) {
    $request_id = (int) $_POST['request_id'];
    $status = $_POST['status'];
    $allowed_statuses = ['new', 'read', 'resolved'];

    if ($request_id > 0 && in_array($status, $allowed_statuses, true)) {
        $update_stmt = $conn->prepare("UPDATE contact_requests SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $status, $request_id);

        if ($update_stmt->execute()) {
            $status_message = 'Contact request status updated successfully.';
        } else {
            $status_message = 'Unable to update the contact request status right now.';
            $status_type = 'error';
        }

        $update_stmt->close();
    } else {
        $status_message = 'Invalid contact request update.';
        $status_type = 'error';
    }
}

$contact_requests_stmt = $conn->prepare("
    SELECT id, name, email, subject, message, status, created_at
    FROM contact_requests
    ORDER BY created_at DESC
");
$contact_requests_stmt->execute();
$contact_requests_result = $contact_requests_stmt->get_result();
$contact_requests = [];
while ($request = $contact_requests_result->fetch_assoc()) {
    $contact_requests[] = $request;
}
$contact_requests_stmt->close();

$new_count = count(array_filter($contact_requests, fn($request) => $request['status'] === 'new'));
$read_count = count(array_filter($contact_requests, fn($request) => $request['status'] === 'read'));
$resolved_count = count(array_filter($contact_requests, fn($request) => $request['status'] === 'resolved'));
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
        max-width: 720px;
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

    .status-banner {
        border-radius: 14px;
        padding: 14px 18px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .status-banner.success {
        background: rgba(16, 185, 129, 0.12);
        color: #166534;
        border-color: rgba(16, 185, 129, 0.25);
    }

    .status-banner.error {
        background: rgba(239, 68, 68, 0.10);
        color: #991B1B;
        border-color: rgba(239, 68, 68, 0.22);
    }

    .request-grid {
        display: grid;
        gap: 18px;
    }

    .request-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .request-title {
        margin: 0;
        color: var(--text-dark);
        font-size: 1.1rem;
        font-weight: 700;
    }

    .request-meta {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .request-message {
        color: var(--text-dark);
        line-height: 1.7;
        margin: 0;
    }

    .request-footer {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid rgba(148, 163, 184, 0.16);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .status-chip.new {
        background: rgba(249, 115, 22, 0.14);
        color: #9A3412;
    }

    .status-chip.read {
        background: rgba(2, 132, 199, 0.12);
        color: #0C4A6E;
    }

    .status-chip.resolved {
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
    }

    .request-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .request-actions form {
        margin: 0;
    }

    .request-actions button {
        border: none;
        border-radius: 999px;
        padding: 10px 16px;
        font-weight: 700;
        transition: var(--transition);
    }

    .request-actions button:hover {
        transform: translateY(-2px);
    }

    .btn-status-new {
        background: rgba(249, 115, 22, 0.14);
        color: #9A3412;
    }

    .btn-status-read {
        background: rgba(2, 132, 199, 0.12);
        color: #0C4A6E;
    }

    .btn-status-resolved {
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
    }

    .empty-state {
        text-align: center;
        padding: 56px 24px;
        color: var(--text-muted);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    @media (max-width: 768px) {
        .request-footer {
            align-items: stretch;
        }
    }
</style>

<div class="admin-container">
    <div class="container">
        <div class="admin-shell">
            <div class="admin-hero">
                <div class="admin-header">
                    <h1><i class="fas fa-envelope-open-text"></i> Contact Requests</h1>
                    <p>Review public contact form submissions in one place, track their status, and keep the dashboard focused on admin overview cards.</p>
                </div>
            </div>

            <div class="summary-row">
                <div class="summary-card">
                    <div class="summary-label">Total Requests</div>
                    <div class="summary-value"><?php echo count($contact_requests); ?></div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">New</div>
                    <div class="summary-value"><?php echo $new_count; ?></div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Read</div>
                    <div class="summary-value"><?php echo $read_count; ?></div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Resolved</div>
                    <div class="summary-value"><?php echo $resolved_count; ?></div>
                </div>
            </div>

            <?php if ($status_message !== ''): ?>
            <div class="status-banner <?php echo $status_type; ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </div>
            <?php endif; ?>

            <?php if (count($contact_requests) > 0): ?>
            <div class="request-grid">
                <?php foreach ($contact_requests as $request): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div>
                            <h2 class="request-title"><?php echo htmlspecialchars($request['subject']); ?></h2>
                            <div class="request-meta">
                                <strong><?php echo htmlspecialchars($request['name']); ?></strong><br>
                                <?php echo htmlspecialchars($request['email']); ?><br>
                                <?php echo date('M d, Y h:i A', strtotime($request['created_at'])); ?>
                            </div>
                        </div>
                        <span class="status-chip <?php echo htmlspecialchars($request['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($request['status'])); ?>
                        </span>
                    </div>

                    <p class="request-message"><?php echo nl2br(htmlspecialchars($request['message'])); ?></p>

                    <div class="request-footer">
                        <div class="request-meta">Request #<?php echo (int) $request['id']; ?></div>
                        <div class="request-actions">
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <input type="hidden" name="status" value="new">
                                <button type="submit" class="btn-status-new">Mark New</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <input type="hidden" name="status" value="read">
                                <button type="submit" class="btn-status-read">Mark Read</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" class="btn-status-resolved">Mark Resolved</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p class="mb-0">No contact requests have been submitted yet.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
