<?php
/**
 * Footer File
 */

$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$is_admin_area = strpos(str_replace('\\', '/', $script_name), '/admin/') !== false;
$is_admin_login_page = $is_admin_area && basename(str_replace('\\', '/', $script_name)) === 'login.php';
$show_admin_footer = $is_admin_area && function_exists('isLoggedIn') && function_exists('isAdmin') && isLoggedIn() && isAdmin() && !$is_admin_login_page;
$asset_prefix = $is_admin_area ? '../' : '';
$footer_brand_href = $show_admin_footer ? 'dashboard.php' : $asset_prefix . 'index.php';
?>
    </main>

    <!-- Footer -->
    <footer class="footer-modern">
        <style>
            .footer-modern {
                background: linear-gradient(135deg, #213C51 0%, #6594B1 100%) !important;
                color: white;
            }

            .footer-modern .footer-section h5 {
                color: #DDAED3;
                font-weight: 700;
                margin-bottom: 20px;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .footer-modern .footer-section h5 i {
                color: #DDAED3;
                font-size: 1.3rem;
            }

            .footer-modern .footer-section p {
                color: rgba(255,255,255,0.8);
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .footer-modern .footer-section a {
                color: rgba(255,255,255,0.8);
                text-decoration: none;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 0.95rem;
            }

            .footer-modern .footer-section a:hover {
                color: #DDAED3;
                transform: translateX(5px);
            }

            .footer-modern .footer-section ul {
                list-style: none;
            }

            .footer-modern .footer-section li {
                margin-bottom: 12px;
            }

            .footer-modern .footer-section ul li a::before {
                content: '→';
                margin-right: 8px;
                opacity: 0.5;
            }

            .social-links {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .social-links a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(255,255,255, 0.1);
                border: 2px solid rgba(221, 174, 211, 0.3);
                color: #DDAED3;
                transition: var(--transition);
                font-size: 1.2rem;
                text-decoration: none;
            }

            .social-links a:hover {
                background: linear-gradient(135deg, #DDAED3 0%, #c499b8 100%);
                color: white;
                border-color: #DDAED3;
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(221, 174, 211, 0.3);
            }

            .footer-bottom {
                text-align: center;
                padding: 25px 0;
                border-top: 1px solid rgba(221, 174, 211, 0.2);
                color: rgba(255,255,255,0.7);
                font-size: 0.9rem;
                background: rgba(33, 60, 81, 0.3);
                margin-top: 30px;
            }

            .footer-bottom i.fa-heart {
                animation: heartBeat 1s infinite;
            }

            @keyframes heartBeat {
                0%, 100% { transform: scale(1); }
                25% { transform: scale(1.3); }
                50% { transform: scale(1); }
            }

            @media (max-width: 768px) {
                .footer-modern .footer-section {
                    margin-bottom: 30px;
                }
                
                .footer-modern .footer-section h5 {
                    font-size: 1rem;
                }

                .social-links {
                    justify-content: center;
                }
            }
        </style>

        <div class="container">
            <div class="row g-4 mb-4">
                <!-- About Section -->
                <div class="col-md-3 footer-section">
                    <h5><i class="fas fa-graduation-cap"></i> Scholar Sphere</h5>
                    <p>
                        <?php echo $show_admin_footer
                            ? 'The admin workspace for reviewing users, notes, categories, and user contact requests.'
                            : 'A modern platform for sharing educational resources and connecting learners worldwide.'; ?>
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="col-md-3 footer-section">
                    <h5><i class="fas fa-link"></i> Quick Links</h5>
                    <ul class="list-unstyled">
                        <?php if ($show_admin_footer): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="approve_users.php">Approve Users</a></li>
                        <li><a href="approve_notes.php">Approve Notes</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="contact_requests.php">Contact Requests</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $asset_prefix; ?>index.php">Home</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>notes.php">Browse Notes</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>contact.php">Contact</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Resources -->
                <div class="col-md-3 footer-section">
                    <h5><i class="fas fa-book"></i> <?php echo $show_admin_footer ? 'Management' : 'Resources'; ?></h5>
                    <ul class="list-unstyled">
                        <?php if ($show_admin_footer): ?>
                        <li><a href="users.php"><i class="fas fa-users-cog"></i> Manage Users</a></li>
                        <li><a href="notes.php"><i class="fas fa-book-medical"></i> Manage Notes</a></li>
                        <li><a href="contact_requests.php"><i class="fas fa-envelope-open-text"></i> Contact Requests</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>index.php" target="_blank"><i class="fas fa-up-right-from-square"></i> Public Site</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $asset_prefix; ?>documentation.php"><i class="fas fa-file-pdf"></i> Documentation</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>privacy.php"><i class="fas fa-lock"></i> Privacy</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>terms.php"><i class="fas fa-file-contract"></i> Terms</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Connect -->
                <div class="col-md-3 footer-section">
                    <h5><i class="fas fa-share-nodes"></i> <?php echo $show_admin_footer ? 'Admin Access' : 'Connect'; ?></h5>
                    <?php if ($show_admin_footer): ?>
                    <p style="margin-bottom: 15px;">Stay inside the admin workspace or open the public experience in a separate tab.</p>
                    <ul class="list-unstyled">
                        <li><a href="dashboard.php"><i class="fas fa-gauge"></i> Dashboard Home</a></li>
                        <li><a href="approve_users.php"><i class="fas fa-user-check"></i> Review Users</a></li>
                        <li><a href="approve_notes.php"><i class="fas fa-file-check"></i> Review Notes</a></li>
                        <li><a href="contact_requests.php"><i class="fas fa-envelope-open-text"></i> Review Contacts</a></li>
                        <li><a href="<?php echo $asset_prefix; ?>index.php" target="_blank"><i class="fas fa-globe"></i> Open Public Site</a></li>
                    </ul>
                    <?php else: ?>
                    <p style="margin-bottom: 15px;">Follow us on social media</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/yogesh_suryawanshi__09/" title="Facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/yogesh_suryawanshi__09/" title="Twitter" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/yogesh-suryawanshi-570811265?utm_source=share_via&utm_content=profile&utm_medium=member_android" title="LinkedIn" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://www.instagram.com/yogesh_suryawanshi__09/" title="Instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                    <p style="margin-top: 15px; font-size: 0.85rem;">
                        <i class="fas fa-envelope" style="color: #DDAED3;"></i> yogeshsuryawanshi7684@gmail.com<br>
                        <i class="fas fa-phone" style="color: #DDAED3;"></i> +917666421674
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p class="mb-2">
                    &copy; 2026 Scholar Sphere. All rights reserved.
                </p>
                <p class="mb-0">
                    Crafted with <i class="fas fa-heart" style="color: #DDAED3;"></i> for learners worldwide.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo $asset_prefix; ?>assets/js/main.js"></script>

    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        main {
            flex: 1;
        }

        footer {
            margin-top: auto;
        }

        .dropdown-menu {
            background: linear-gradient(135deg, #213C51 0%, #6594B1 100%) !important;
            border: 1px solid #DDAED3 !important;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(33, 60, 81, 0.2);
        }

        .dropdown-item {
            color: white !important;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(221, 174, 211, 0.2) !important;
            color: #DDAED3 !important;
            transform: translateX(5px);
        }

        .dropdown-divider {
            border-color: rgba(221, 174, 211, 0.2) !important;
        }

        /* Toast Styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            animation: slideIn 0.3s ease;
            z-index: 10000;
            max-width: 400px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .toast-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }

        .toast-error {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
        }

        .toast-info {
            background: linear-gradient(135deg, #213C51 0%, #6594B1 100%);
            color: white;
        }

        .toast-warning {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast-notification.out {
            animation: slideOut 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .nav-link {
                padding: 8px 10px !important;
                font-size: 0.95rem;
            }
        }
    </style>
</body>
</html>
