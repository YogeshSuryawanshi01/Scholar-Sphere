<?php
/**
 * Privacy Policy Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
$page_title = 'Privacy Policy';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .policy-page {
        padding: 80px 0;
    }

    .policy-header {
        text-align: center;
        margin-bottom: 45px;
    }

    .policy-header h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .policy-card {
        max-width: 950px;
        margin: 0 auto;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 38px;
    }

    .policy-card h2 {
        color: var(--secondary-dark);
        font-size: 1.25rem;
        margin: 28px 0 12px;
    }

    .policy-card h2:first-child {
        margin-top: 0;
    }

    .policy-card p,
    .policy-card li {
        color: var(--text-muted);
        line-height: 1.75;
    }

    .policy-card ul {
        padding-left: 20px;
        margin-bottom: 0;
    }
</style>

<div class="policy-page">
    <div class="container">
        <div class="policy-header">
            <h1><i class="fas fa-user-shield"></i> Privacy Policy</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">How Scholar Sphere collects, uses, and protects user information.</p>
        </div>

        <div class="policy-card">
            <h2>Information We Collect</h2>
            <p>We collect the information you submit directly, such as your name, email address, login credentials, uploaded notes, ratings, and contact requests.</p>

            <h2>How We Use Information</h2>
            <p>Your information is used to operate the platform, manage accounts, review uploads, process contact requests, improve learning features, and maintain platform security.</p>

            <h2>Content and Uploads</h2>
            <p>Files and descriptions you upload may be reviewed by administrators before being made available to the community.</p>

            <h2>Account Security</h2>
            <p>We take reasonable steps to protect account data and limit administrative access to authorized users only.</p>

            <h2>Data Sharing</h2>
            <p>Scholar Sphere does not sell your personal information. Information may be shared only when needed to operate the service, comply with law, or protect the platform and its users.</p>

            <h2>Your Choices</h2>
            <ul>
                <li>Use accurate account information.</li>
                <li>Contact the platform team if you want help with account-related concerns.</li>
                <li>Avoid uploading sensitive personal data in note content or messages.</li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
