<?php
/**
 * Terms and Conditions Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
$page_title = 'Terms & Conditions';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .terms-page {
        padding: 80px 0;
    }

    .terms-header {
        text-align: center;
        margin-bottom: 45px;
    }

    .terms-header h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .terms-card {
        max-width: 950px;
        margin: 0 auto;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 38px;
    }

    .terms-card h2 {
        color: var(--secondary-dark);
        font-size: 1.25rem;
        margin: 28px 0 12px;
    }

    .terms-card h2:first-child {
        margin-top: 0;
    }

    .terms-card p,
    .terms-card li {
        color: var(--text-muted);
        line-height: 1.75;
    }

    .terms-card ul {
        padding-left: 20px;
        margin-bottom: 0;
    }
</style>

<div class="terms-page">
    <div class="container">
        <div class="terms-header">
            <h1><i class="fas fa-file-signature"></i> Terms & Conditions</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Rules for using Scholar Sphere responsibly and respectfully.</p>
        </div>

        <div class="terms-card">
            <h2>Acceptance of Terms</h2>
            <p>By registering, uploading content, or using Scholar Sphere, you agree to follow these terms and all applicable platform policies.</p>

            <h2>User Responsibilities</h2>
            <ul>
                <li>Provide accurate registration details.</li>
                <li>Use the platform only for lawful educational purposes.</li>
                <li>Do not upload misleading, harmful, or unauthorized content.</li>
                <li>Respect other users, administrators, and community guidelines.</li>
            </ul>

            <h2>Uploaded Content</h2>
            <p>You are responsible for the materials you submit. Scholar Sphere may review, reject, or remove content that violates policy or harms the platform experience.</p>

            <h2>Account Access</h2>
            <p>You are responsible for maintaining the confidentiality of your login credentials and for activity performed through your account.</p>

            <h2>Service Availability</h2>
            <p>We may update features, review submissions, or restrict access when needed for maintenance, security, or administrative reasons.</p>

            <h2>Limitation of Liability</h2>
            <p>Scholar Sphere is provided as an educational platform. While we work to maintain quality and availability, users remain responsible for how they use downloaded materials and information.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
