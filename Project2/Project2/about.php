<?php
/**
 * About Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
$page_title = 'About Us';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .about-section {
        padding: 80px 0;
    }

    .about-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .about-header h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .about-content {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 40px;
        margin-bottom: 40px;
        line-height: 1.8;
        color: var(--text-muted);
    }

    .about-content h3 {
        color: var(--secondary-dark);
        font-weight: 700;
        margin: 30px 0 15px 0;
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }

    .team-member {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .team-member:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    }

    .team-member i {
        font-size: 3rem;
        color: var(--secondary);
        margin-bottom: 15px;
    }

    .team-member h4 {
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .team-member p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
    }
</style>

<div class="about-section">
    <div class="container">
        <div class="about-header">
            <h1><i class="fas fa-graduation-cap"></i> About Scholar Sphere</h1>
            <p style="color: var(--text-muted); font-size: 1.5rem;">Our Mission & Vision</p>
        </div>

        <div class="about-content">
            <h3>Our Mission</h3>
            <p>Scholar Sphere is dedicated to democratizing education by creating a centralized platform where students and educators can easily share, access, and collaborate on educational resources. We believe that knowledge should be freely accessible to everyone, regardless of their background or economic status.</p>

            <h3>Our Vision</h3>
            <p>To become the world's leading platform for educational resource sharing, fostering a global community of learners and educators who actively contribute to the collective knowledge base and continuously improve the quality of educational materials available to all.</p>

            <h3>What We Offer</h3>
            <p>Scholar Sphere provides a comprehensive suite of tools designed to support modern learning and teaching:</p>
            <ul style="margin-left: 20px; color: var(--text-muted);">
                <li>Vast library of educational notes and resources</li>
                <li>Advanced search and filtering capabilities</li>
                <li>Community ratings and reviews</li>
                <li>AI-powered chat assistant for instant learning support</li>
                <li>Easy upload and sharing of educational materials</li>
                <li>Secure and private user accounts</li>
            </ul>

            <h3>Why Scholar Sphere?</h3>
            <p>Unlike traditional learning platforms, Scholar Sphere is built from the ground up with user experience in mind. Our clean, intuitive interface combined with powerful features makes it easy for anyone to find resources, share knowledge, and grow as a learner.</p>
        </div>

        <h2 style="text-align: center; margin: 60px 0 40px 0; font-size: 2rem; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Our Team</h2>

        <div class="team-grid">
            <div class="team-member">
                <i class="fas fa-user-circle"></i>
                <h4>Founder & CEO</h4>
                <p>Mr. Yogesh Suryawanshi</p>
            </div>
            <div class="team-member">
                <i class="fas fa-code"></i>
                <h4>Development Team</h4>
                <p>From DYPTC SE CS C</p>
            </div>
            <div class="team-member">
                <i class="fas fa-pencil-alt"></i>
                <h4>Content Team</h4>
                <p>Helping Students With Easy Share</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
