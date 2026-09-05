<?php
/**
 * Header and Navigation File
 */
require_once __DIR__ . '/session_bootstrap.php';
$current_session_area = bootSession();

// Include database file for helper functions
require_once __DIR__ . '/db.php';

$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$is_admin_area = strpos(str_replace('\\', '/', $script_name), '/admin/') !== false;
$is_admin_login_page = $is_admin_area && basename(str_replace('\\', '/', $script_name)) === 'login.php';
$show_admin_nav = $is_admin_area && isLoggedIn() && isAdmin() && !$is_admin_login_page;
$asset_prefix = $is_admin_area ? '../' : '';
$login_href = $is_admin_area ? 'login.php' : $asset_prefix . 'login.php';
$register_href = $is_admin_area ? $asset_prefix . 'register.php' : $asset_prefix . 'register.php';
$logout_href = $is_admin_area ? 'logout.php' : $asset_prefix . 'logout.php';
$brand_href = $is_admin_area ? 'dashboard.php' : $asset_prefix . 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Scholar Sphere' : 'Scholar Sphere - Simplified Notes'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $asset_prefix; ?>assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--secondary) 100%) !important;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--secondary);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.15);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 900;
            color: white !important;
            background: linear-gradient(135deg, var(--accent) 0%, white 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .navbar-brand i {
            font-size: 2rem;
            color: var(--accent);
            -webkit-text-fill-color: unset;
            background: unset;
        }

        .navbar-brand:hover {
            opacity: 0.9;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            margin: 0 5px;
            padding: 8px 15px !important;
        }

        .nav-link i {
            margin-right: 5px;
        }

        .nav-link:hover {
            color: var(--accent) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: var(--transition);
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: calc(100% - 30px);
        }

        .navbar-toggler {
            border-color: white !important;
            transition: var(--transition);
        }

        .navbar-toggler:hover {
            opacity: 0.8;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Auth Buttons */
        .btn-login, .btn-register, .btn-logout {
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-login {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-login:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255,255,255, 0.2);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--accent) 0%, white 100%);
            color: var(--primary);
            font-weight: 700;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(221, 174, 211, 0.4);
        }

        .btn-logout {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
        }

        .btn-logout:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        /* Dropdown Styling */
        .dropdown-menu {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-cards) 100%) !important;
            border: 1px solid var(--secondary) !important;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.2);
        }

        .dropdown-item {
            color: white !important;
            font-weight: 500;
            transition: var(--transition);
            border-radius: 5px;
            margin: 3px 8px;
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(249, 115, 22, 0.16) !important;
            color: var(--accent) !important;
        }

        .dropdown-divider {
            border-color: rgba(249, 115, 22, 0.18) !important;
        }

        .user-menu-btn {
            background: rgba(255, 255, 255, 0.10);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            max-width: 240px;
        }

        .user-menu-btn:hover,
        .user-menu-btn:focus {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            border-color: rgba(255, 255, 255, 0.42);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.20);
        }

        .user-menu-btn.admin-user {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.18) 0%, rgba(255, 255, 255, 0.12) 100%);
            border-color: rgba(249, 115, 22, 0.45);
        }

        .user-menu-btn .user-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Toast Container */
        #toastContainer {
            z-index: 9999 !important;
        }

        main {
            flex: 1;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $brand_href; ?>">
                <i class="fas fa-graduation-cap"></i>
                Scholar Sphere
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($show_admin_nav): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approve_users.php">
                            <i class="fas fa-user-check"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approve_notes.php">
                            <i class="fas fa-file-check"></i> Notes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-layer-group"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact_requests.php">
                            <i class="fas fa-envelope-open-text"></i> Contacts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users-cog"></i> Manage Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notes.php">
                            <i class="fas fa-book"></i> Manage Notes
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (!$is_admin_area): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>notes.php">
                            <i class="fas fa-book"></i> Notes
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>ai_chat.php">
                            <i class="fas fa-robot"></i> AI Chat
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>contact.php">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $asset_prefix; ?>admin/dashboard.php">
                            <i class="fas fa-gauge"></i> Admin
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <div class="d-flex gap-2 ms-3">
                    <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo $login_href; ?>" class="btn btn-login btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="<?php echo $register_href; ?>" class="btn btn-register btn-sm">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                    <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-sm user-menu-btn <?php echo isAdmin() ? 'admin-user' : ''; ?>" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                            <span class="user-label"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (!$is_admin_area && !isAdmin()): ?>
                            <li><a class="dropdown-item" href="<?php echo $asset_prefix; ?>upload.php"><i class="fas fa-upload"></i> Upload Notes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <?php if ($is_admin_area): ?>
                            <li><a class="dropdown-item" href="../index.php" target="_blank"><i class="fas fa-up-right-from-square"></i> Open Public Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?php echo $logout_href; ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 11"></div>

    <main>
