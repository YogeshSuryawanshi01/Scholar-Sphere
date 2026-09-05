<?php
/**
 * FAQ Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
$page_title = 'FAQ';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .faq-page {
        padding: 80px 0;
    }

    .faq-hero {
        text-align: center;
        margin-bottom: 45px;
    }

    .faq-hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .faq-wrap {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-item {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 28px 30px;
        margin-bottom: 20px;
    }

    .faq-item h3 {
        color: var(--secondary-dark);
        font-size: 1.2rem;
        margin-bottom: 12px;
    }

    .faq-item p {
        color: var(--text-muted);
        line-height: 1.7;
        margin: 0;
    }
</style>

<div class="faq-page">
    <div class="container">
        <div class="faq-hero">
            <h1><i class="fas fa-circle-question"></i> Frequently Asked Questions</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Quick answers about accounts, uploads, downloads, and platform use.</p>
        </div>

        <div class="faq-wrap">
            <div class="faq-item">
                <h3>Do I need an account to download notes?</h3>
                <p>You can browse the platform publicly, but creating an account gives you the full experience, including ratings, uploads, and member features.</p>
            </div>

            <div class="faq-item">
                <h3>Why is my uploaded note not visible yet?</h3>
                <p>Uploads are reviewed by an administrator before publication. This helps keep the library safe, relevant, and high quality for everyone.</p>
            </div>

            <div class="faq-item">
                <h3>Can I update my rating on a note?</h3>
                <p>Yes. If you rate a note again while logged in, your previous rating is updated instead of creating a duplicate entry.</p>
            </div>

            <div class="faq-item">
                <h3>What file types are supported?</h3>
                <p>The platform supports common study-resource formats, and some file types can be previewed directly in the browser from the note details page.</p>
            </div>

            <div class="faq-item">
                <h3>How do I contact the team?</h3>
                <p>Use the Contact page to send a message. Your request is stored for admin review so the team can respond and track support issues.</p>
            </div>

            <div class="faq-item">
                <h3>Is there an admin area?</h3>
                <p>Yes. Authorized admins can review users, notes, categories, and contact requests from the dedicated admin dashboard.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
