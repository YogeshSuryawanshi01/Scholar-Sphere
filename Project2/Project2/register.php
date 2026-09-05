<?php
/**
 * Registration Page
 * Handles user registration with email validation and prepared statements
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
    $name = sanitize($_POST['name'] ?? '', $conn);
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } else if (strlen($name) < 3) {
        $error = 'Name must be at least 3 characters';
    } else if (!isValidEmail($email)) {
        $error = 'Invalid email format';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // If no admin exists yet, promote the first registered account to admin.
            $adminCheckStmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
            $adminCheckStmt->execute();
            $adminExists = (int) $adminCheckStmt->get_result()->fetch_assoc()['total'] > 0;
            $adminCheckStmt->close();

            $role = $adminExists ? 'user' : 'admin';
            $status = $adminExists ? 'pending' : 'approved';

            // Prepare insert statement
            $insertStmt = $conn->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->bind_param("sssss", $name, $email, $hashed_password, $role, $status);

            if ($insertStmt->execute()) {
                $success = $role === 'admin'
                    ? 'Registration successful! Your account has been created as the administrator.'
                    : 'Registration successful! Your account is pending admin approval.';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Registration failed. Please try again.';
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .register-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 100px);
        padding: 40px 20px;
    }

    .register-card {
        width: 100%;
        max-width: 500px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .register-card h2 {
        text-align: center;
        margin-bottom: 10px;
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .register-card .subtitle {
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

    .login-link {
        text-align: center;
        margin-top: 25px;
        color: var(--text-muted);
    }

    .login-link a {
        color: var(--secondary-dark);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .login-link a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .terms-info {
        background-color: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #0C4A6E;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 0.85rem;
        margin-bottom: 20px;
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

<div class="register-container">
    <div class="register-card">
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        <p class="subtitle">Join Scholar Sphere Today</p>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Success!</strong><br>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Enter your full name" 
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Enter your email" 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Minimum 6 characters" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Re-enter your password" 
                    required
                >
            </div>

            <div class="terms-info">
                <i class="fas fa-info-circle"></i> 
                New user accounts require admin approval. If no admin exists yet, the first registered account becomes the administrator.
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="divider">
            <span>Already have an account?</span>
        </div>

        <div class="login-link">
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Login Here
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
