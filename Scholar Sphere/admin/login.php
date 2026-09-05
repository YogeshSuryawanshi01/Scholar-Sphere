<?php
/**
 * Admin Login Page
 */
require_once __DIR__ . '/../includes/session_bootstrap.php';
bootSession('admin');

if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$page_title = 'Admin Login';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                $error = 'Incorrect password';
            } else if ($user['role'] !== 'admin') {
                $error = 'This account is not authorized for admin access.';
            } else if ($user['status'] !== 'approved') {
                $error = 'This admin account is not approved.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                $updateStmt = $conn->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $user['id']);
                $updateStmt->execute();
                $updateStmt->close();

                $success = 'Admin login successful! Redirecting...';
                header('refresh:1;url=dashboard.php');
            }
        } else {
            $error = 'Email not found';
        }

        $stmt->close();
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<style>
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 100px);
        padding: 40px 20px;
    }

    .login-card {
        width: 100%;
        max-width: 460px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 22px;
        padding: 40px;
        box-shadow: 0 22px 55px rgba(15, 23, 42, 0.14);
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .subtitle {
        text-align: center;
        color: var(--text-muted);
        margin-bottom: 28px;
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

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-dark);
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 auto 18px;
    }

    .error-message,
    .success-message {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 18px;
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

    .submit-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
    }

    .helper-link {
        text-align: center;
        margin-top: 18px;
    }

    .helper-link a {
        color: var(--secondary-dark);
        font-weight: 700;
        text-decoration: none;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div style="text-align: center;">
            <div class="admin-badge"><i class="fas fa-shield-halved"></i> Admin Area</div>
        </div>
        <h2>Admin Login</h2>
        <p class="subtitle">Use a separate admin session without affecting the public site tab.</p>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-right-to-bracket"></i> Enter Admin Dashboard
            </button>
        </form>

        <div class="helper-link">
            <a href="../login.php"><i class="fas fa-user"></i> User Login</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
