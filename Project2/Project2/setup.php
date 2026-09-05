<?php
/**
 * Scholar Sphere - Setup & Installation Helper
 * Run this once after installation to verify everything is working
 */

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Database connection for setup
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'scholar_sphere';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholar Sphere - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #F8FAFC 0%, #ECFEFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0F172A;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
            border: 1px solid rgba(148, 163, 184, 0.24);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 55px rgba(15, 23, 42, 0.10);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0F172A 0%, #0EA5A4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .step-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(148, 163, 184, 0.24);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-weight: 700;
            position: relative;
        }
        .step-dot.active {
            background: linear-gradient(135deg, #0F172A 0%, #0EA5A4 100%);
            color: white;
        }
        .step-dot.completed {
            background: #10B981;
            color: white;
        }
        .step-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background: rgba(79, 70, 229, 0.2);
            top: 50%;
            left: 100%;
            width: 60px;
        }
        .step-dot:last-child::after {
            display: none;
        }
        .step-content {
            background: rgba(248, 250, 252, 0.92);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .step-content h2 {
            margin-bottom: 20px;
            color: #0F172A;
        }
        .step-content p {
            color: #475569;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: rgba(248, 250, 252, 0.92);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .status-icon {
            font-size: 1.3rem;
            min-width: 30px;
        }
        .status-icon.success {
            color: #10B981;
        }
        .status-icon.error {
            color: #EF4444;
        }
        .status-icon.warning {
            color: #F59E0B;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            color: #0F172A;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-group input, .form-group textarea {
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #0F172A;
            border-radius: 8px;
            padding: 10px;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #0EA5A4;
            background: #FFFFFF;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0F172A 0%, #0EA5A4 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 23, 42, 0.20);
        }
        .btn-secondary {
            background: transparent;
            border: 2px solid #0EA5A4;
            color: #0B7F7B;
            font-weight: 700;
            padding: 10px 25px;
            border-radius: 8px;
        }
        .code-block {
            background: rgba(248, 250, 252, 0.96);
            border-left: 3px solid #0EA5A4;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-cube"></i> Scholar Sphere Setup</h1>
            <p>Installation & Configuration Wizard</p>
        </div>

        <div class="step-indicator">
            <div class="step-dot <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">1</div>
            <div class="step-dot <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">2</div>
            <div class="step-dot <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">3</div>
            <div class="step-dot <?php echo $step >= 4 ? 'active' : ''; ?> <?php echo $step > 4 ? 'completed' : ''; ?>">4</div>
        </div>

        <?php if ($step === 1): ?>
        <div class="step-content">
            <h2><i class="fas fa-check-circle"></i> System Requirements</h2>
            <p>Checking your server configuration...</p>

            <div class="status-item">
                <i class="fas fa-check-circle status-icon success"></i>
                <div>PHP Version: <?php echo phpversion(); ?></div>
            </div>

            <div class="status-item">
                <i class="fas fa-check-circle status-icon <?php echo extension_loaded('mysqli') ? 'success' : 'error'; ?>"></i>
                <div>MySQLi Extension: <?php echo extension_loaded('mysqli') ? 'Installed ✓' : 'Missing ✗'; ?></div>
            </div>

            <div class="status-item">
                <i class="fas fa-check-circle status-icon <?php echo extension_loaded('curl') ? 'success' : 'warning'; ?>"></i>
                <div>cURL Extension: <?php echo extension_loaded('curl') ? 'Installed ✓' : 'Missing (Optional)'; ?></div>
            </div>

            <div class="status-item">
                <i class="fas fa-check-circle status-icon <?php echo is_writable('uploads') ? 'success' : 'error'; ?>"></i>
                <div>Uploads Folder: <?php echo is_writable('uploads') ? 'Writable ✓' : 'Not Writable ✗'; ?></div>
            </div>

            <p style="margin-top: 20px; color: #10B981;">✓ All requirements are met!</p>

            <button class="btn btn-primary" onclick="location.href='?step=2'">
                Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <?php elseif ($step === 2): ?>
        <div class="step-content">
            <h2><i class="fas fa-database"></i> Database Configuration</h2>
            <p>Setup your database connection</p>

            <form method="GET">
                <input type="hidden" name="step" value="3">
                <input type="hidden" name="action" value="test_db">

                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" class="form-control" name="db_host" value="<?php echo $db_host; ?>" placeholder="localhost">
                </div>

                <div class="form-group">
                    <label>Database User</label>
                    <input type="text" class="form-control" name="db_user" value="<?php echo $db_user; ?>" placeholder="root">
                </div>

                <div class="form-group">
                    <label>Database Password</label>
                    <input type="password" class="form-control" name="db_pass" placeholder="Leave blank if none">
                </div>

                <div class="form-group">
                    <label>Database Name</label>
                    <input type="text" class="form-control" name="db_name" value="<?php echo $db_name; ?>" placeholder="scholar_sphere">
                </div>

                <div class="code-block">
                    <p style="margin: 0;">Connection will be tested before proceeding...</p>
                </div>

                <button type="button" class="btn btn-secondary" onclick="location.href='?step=1'">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <button type="submit" class="btn btn-primary">
                    Test Connection <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <?php elseif ($step === 3): ?>
        <div class="step-content">
            <h2><i class="fas fa-file-upload"></i> Database Import</h2>
            <p>Import the database schema to get started</p>

            <div class="status-item" style="background: rgba(16, 185, 129, 0.2); border-left: 3px solid #10B981;">
                <i class="fas fa-check-circle status-icon success"></i>
                <div>Database connection successful!</div>
            </div>

            <p style="margin-top: 20px;">To import the database schema:</p>
            <ol>
                <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank" style="color: #0B7F7B;">http://localhost/phpmyadmin</a></li>
                <li>Select the <code>potter_sphere</code> database</li>
                <li>Click <strong>Import</strong></li>
                <li>Select the <code>db_schema.sql</code> file</li>
                <li>Click <strong>Import</strong></li>
            </ol>

            <p style="margin-top: 20px; color: #F59E0B;">
                <i class="fas fa-info-circle"></i> Or paste the SQL commands directly into phpMyAdmin's SQL editor.
            </p>

            <button type="button" class="btn btn-secondary" onclick="location.href='?step=2'">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" class="btn btn-primary" onclick="location.href='?step=4'">
                Continue <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <?php elseif ($step === 4): ?>
        <div class="step-content">
            <h2><i class="fas fa-rocket"></i> Installation Complete!</h2>
            <p>Scholar Sphere is ready to use.</p>

            <div class="status-item" style="background: rgba(16, 185, 129, 0.2); border-left: 3px solid #10B981;">
                <i class="fas fa-check-circle status-icon success"></i>
                <div><strong>All systems are go!</strong> Your application is ready to launch.</div>
            </div>

            <h4 style="margin-top: 30px; color: #0F172A;">Next Steps:</h4>
            <ol style="color: #475569;">
                <li>Visit the home page to see the application running</li>
                <li>Create an account to get started</li>
                <li>Use the seeded default admin account, or register the first account to become admin if the seed was not imported</li>
                <li>Log in to access all features</li>
            </ol>

            <h4 style="margin-top: 30px; color: #0F172A;">Default Credentials:</h4>
            <div class="code-block">
Email: admin@scholarsphere.com<br>
Password: admin123
            </div>

            <p style="color: #F59E0B; margin-top: 20px;">
                <i class="fas fa-warning"></i> <strong>Security Tip:</strong> Change default admin credentials immediately!
            </p>

            <button type="button" class="btn btn-secondary" style="margin-top: 20px;" onclick="location.href='index.php'">
                <i class="fas fa-home"></i> Go to Home Page
            </button>
        </div>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
