<?php
/**
 * Documentation Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
$page_title = 'Documentation';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .info-page {
        padding: 80px 0;
    }

    .info-hero {
        text-align: center;
        margin-bottom: 50px;
    }

    .info-hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }

    .info-card,
    .info-panel {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .info-card {
        padding: 28px;
    }

    .info-card i {
        font-size: 2rem;
        color: var(--secondary);
        margin-bottom: 16px;
    }

    .info-card h3,
    .info-panel h2 {
        color: var(--text-dark);
        font-weight: 700;
        margin-bottom: 12px;
    }

    .info-card p,
    .info-panel p,
    .info-panel li {
        color: var(--text-muted);
        line-height: 1.7;
    }

    .info-panel {
        padding: 36px;
    }

    .info-panel h2 {
        color: var(--secondary-dark);
        margin-top: 28px;
    }

    .info-panel h2:first-child {
        margin-top: 0;
    }

    .info-panel ul {
        padding-left: 20px;
        margin-bottom: 0;
    }
</style>

<div class="info-page">
    <div class="container">
        <div class="info-hero">
            <h1><i class="fas fa-book-open"></i> Scholar Sphere Documentation</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">A quick guide for students, contributors, and administrators using the platform.</p>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <i class="fas fa-user-plus"></i>
                <h3>Create an Account</h3>
                <p>Register with your name, email, and password to access note downloads, ratings, uploads, and your personalized learning journey.</p>
            </div>
            <div class="info-card">
                <i class="fas fa-magnifying-glass"></i>
                <h3>Browse Notes</h3>
                <p>Open the Notes page to explore approved resources, review descriptions, and check ratings before you download.</p>
            </div>
            <div class="info-card">
                <i class="fas fa-upload"></i>
                <h3>Share Content</h3>
                <p>Logged-in users can upload study materials for review so the platform stays useful, safe, and well organized.</p>
            </div>
            <div class="info-card">
                <i class="fas fa-shield-halved"></i>
                <h3>Admin Review</h3>
                <p>Admins approve users, notes, and contact requests to keep the library accurate and the community trustworthy.</p>
            </div>
        </div>

        <div class="info-panel">
            <h2>Getting Started</h2>
            <p>Start on the home page, create your account, and sign in. Once logged in, you can browse notes, open note details, submit ratings, and download approved files.</p>

            <h2>How Uploads Work</h2>
            <p>When you upload a note, it enters the approval flow. An administrator checks the submission before it becomes visible to other learners.</p>

            <h2>AI Chat Access</h2>
            <p>Signed-in users can access the AI Chat feature from the main navigation to get help with studying and note understanding.</p>

            <h2>Best Practices</h2>
            <ul>
                <li>Use clear note titles and meaningful descriptions.</li>
                <li>Upload readable, accurate, and course-relevant materials.</li>
                <li>Rate notes honestly to help other users choose quality resources.</li>
                <li>Use the contact page if you spot errors or need support.</li>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
