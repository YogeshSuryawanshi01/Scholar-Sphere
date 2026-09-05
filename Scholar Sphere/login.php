<?php
/**
 * Login Page
 * Handles user authentication with prepared statements
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else if (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if user is approved
                if ($user['status'] === 'pending') {
                    $error = 'Your account is pending approval. Please wait for admin verification.';
                } else if ($user['status'] === 'rejected') {
                    $error = 'Your account has been rejected. Please contact support.';
                } else if ($user['role'] === 'admin') {
                    $error = 'Admin accounts must use the admin login page.';
                } else {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Update last login
                    $updateStmt = $conn->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
                    $updateStmt->bind_param("i", $user['id']);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $success = 'Login successful! Redirecting...';
                    $redirect_url = $user['role'] === 'admin' ? 'dashboard.php' : 'index.php';
                    header('refresh:2;url=' . $redirect_url);
                }
            } else {
                $error = 'Incorrect password';
            }
        } else {
            $error = 'Email not found';
        }
        $stmt->close();
    }
}

// For email validation function
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

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
        max-width: 450px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .login-card .subtitle {
        text-align: center;
        color: var(--text-muted);
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-group input {
        width: 100%;
        padding: 12px 15px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-dark);
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        background-color: var(--surface);
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .form-group input::placeholder {
        color: #94A3B8;
    }

    .error-message {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #991B1B;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInLeft 0.3s ease;
    }

    .success-message {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #166534;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInLeft 0.3s ease;
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
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.18);
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.24);
    }

    .submit-btn:active {
        transform: translateY(-1px);
    }

    .divider {
        text-align: center;
        margin: 30px 0;
        position: relative;
        color: var(--text-muted);
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background: rgba(148, 163, 184, 0.24);
    }

    .divider span {
        position: relative;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.94) 0%, rgba(236, 254, 255, 0.92) 100%);
        padding: 0 10px;
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
        color: var(--text-muted);
    }

    .register-link a {
        color: var(--secondary-dark);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .register-link a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    @keyframes bounceIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
        <p class="subtitle">Welcome back to Scholar Sphere</p>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required
                >
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Login Now
            </button>
        </form>

        <div class="divider">
            <span>Don't have an account?</span>
        </div>

        <div class="register-link">
            <a href="register.php">
                <i class="fas fa-user-plus"></i> Create Account
            </a>
        </div>

        <div class="register-link" style="margin-top: 12px;">
            <a href="admin/login.php">
                <i class="fas fa-shield-halved"></i> Admin Login
            </a>
        </div>
    </div>
</div>

