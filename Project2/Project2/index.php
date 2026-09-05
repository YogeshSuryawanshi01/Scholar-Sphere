<?php
/**
 * Home Page / Landing Page
 * Displays hero section and feature cards
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

$page_title = 'Home';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .home-page {
        padding: 32px 0 0;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.84) 0%, rgba(236, 254, 255, 0.88) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 28px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.10);
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(14, 165, 164, 0.15) 0%, transparent 70%);
        animation: float 20s ease-in-out infinite;
        z-index: 0;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -30%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(2, 132, 199, 0.12) 0%, transparent 70%);
        animation: float 25s ease-in-out infinite reverse;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .hero-content h1 {
        font-size: 4.5rem;
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: slideInDown 0.8s ease-out;
        color: var(--text-dark);
    }

    .hero-content p {
        font-size: 1.3rem;
        color: var(--text-muted);
        margin-bottom: 40px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        animation: slideInUp 0.8s ease-out 0.2s both;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: slideInUp 0.8s ease-out 0.4s both;
    }

    .btn-primary-hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.28);
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary-hero:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.34);
        color: white;
    }

    .btn-secondary-hero {
        background: transparent;
        color: var(--secondary);
        border: 2px solid var(--secondary);
        padding: 13px 38px;
        border-radius: 30px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary-hero:hover {
        background: var(--secondary);
        color: white;
        transform: translateY(-5px);
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-30px);
        }
    }

    /* Features Section */
    .features-section {
        padding: 100px 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.70) 0%, rgba(241, 245, 249, 0.92) 55%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 28px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .features-section h2 {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 60px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: var(--text-dark);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    .feature-card {
        background: linear-gradient(135deg, rgba(14, 165, 164, 0.1) 0%, rgba(2, 132, 199, 0.1) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(14, 165, 164, 0.25);
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
        z-index: 0;
    }

    .feature-card:hover::before {
        left: 100%;
    }

    .feature-card:hover {
        border-color: rgba(14, 165, 164, 0.6);
        transform: translateY(-15px);
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .feature-card-content {
        position: relative;
        z-index: 1;
    }

    .feature-card i {
        font-size: 3.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 20px;
        display: inline-block;
    }

    .feature-card h5 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 15px;
    }

    .feature-card p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .feature-card.reveal-card {
        opacity: 0;
        transform: translateY(36px);
        transition: opacity 0.65s ease, transform 0.65s ease;
    }

    .feature-card.reveal-card.is-visible {
        opacity: 1;
        transform: translateY(0);
        animation: none;
    }

    .features-grid .reveal-card:nth-child(2) {
        transition-delay: 0.08s;
    }

    .features-grid .reveal-card:nth-child(3) {
        transition-delay: 0.16s;
    }

    .features-grid .reveal-card:nth-child(4) {
        transition-delay: 0.24s;
    }

    .features-grid .reveal-card:nth-child(5) {
        transition-delay: 0.32s;
    }

    .features-grid .reveal-card:nth-child(6) {
        transition-delay: 0.4s;
    }

    @keyframes slideInFromLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInFromRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Stats Section */
    .stats-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(14, 165, 164, 0.22);
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.10);
        padding: 50px 30px;
        margin: 100px 0;
        text-align: center;
    }

    .stats-section h2 {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .stat-item h3 {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }

    .stat-item p {
        color: var(--text-muted);
    }

    .cta-section {
        padding: 80px 0 110px;
    }

    .cta-panel {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(11, 127, 123, 0.92) 100%);
        border: 1px solid rgba(249, 115, 22, 0.32);
        border-radius: 28px;
        box-shadow: 0 24px 55px rgba(15, 23, 42, 0.18);
        padding: 56px 32px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(249, 115, 22, 0.20), transparent 35%),
            radial-gradient(circle at bottom left, rgba(14, 165, 164, 0.24), transparent 42%);
        pointer-events: none;
    }

    .cta-panel > * {
        position: relative;
        z-index: 1;
    }

    .cta-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 24px;
        color: var(--text-light);
    }

    .cta-description {
        font-size: 1.1rem;
        color: rgba(248, 250, 252, 0.86);
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .home-page {
            padding-top: 20px;
        }

        .hero-section,
        .features-section,
        .stats-section,
        .cta-panel {
            border-radius: 22px;
        }

        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-content p {
            font-size: 1rem;
        }

        .features-section h2 {
            font-size: 1.8rem;
        }

        .stats-section h2,
        .cta-title {
            font-size: 1.9rem;
        }

        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }

        .btn-primary-hero, .btn-secondary-hero {
            width: 100%;
            max-width: 300px;
        }
    }
</style>

<div class="home-page">
<!-- Hero Section -->
<section class="hero-section container">
    <div class="container py-2">
        <div class="hero-content">
            <h1>Scholar Sphere</h1>
            <p>Simplifying Knowledge Sharing and Learning for Everyone</p>
            <div class="hero-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="notes.php" class="btn-primary-hero">
                        <i class="fas fa-book"></i> Browse Notes
                    </a>
                    <a href="upload.php" class="btn-secondary-hero">
                        <i class="fas fa-upload"></i> Upload Notes
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn-primary-hero">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                    <a href="login.php" class="btn-secondary-hero">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section container my-5">
    <div class="container py-2">
        <h2><i class="fas fa-star"></i> Why Choose Scholar Sphere?</h2>
        
        <div class="features-grid">
            <!-- Features sliding from left -->
            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-book-open"></i>
                    <h5>Vast Resource Library</h5>
                    <p>Access thousands of notes, documents, and educational materials from expert contributors worldwide.</p>
                </div>
            </div>

            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-lock-open"></i>
                    <h5>Easy Access</h5>
                    <p>Simple, intuitive interface to find, filter, and download the exact materials you need instantly.</p>
                </div>
            </div>

            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-star"></i>
                    <h5>Quality Rated</h5>
                    <p>All resources are rated by the community, ensuring you access only high-quality educational content.</p>
                </div>
            </div>

            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-robot"></i>
                    <h5>AI Chat Assistant</h5>
                    <p>Get instant answers and explanations powered by advanced AI technology. Learn faster and smarter.</p>
                </div>
            </div>

            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-share-alt"></i>
                    <h5>Share & Collaborate</h5>
                    <p>Upload your own notes, share knowledge with others, and contribute to the global learning community.</p>
                </div>
            </div>

            <div class="feature-card reveal-card">
                <div class="feature-card-content">
                    <i class="fas fa-mobile-alt"></i>
                    <h5>Mobile Friendly</h5>
                    <p>Access all materials on any device, anytime, anywhere. Learn on the go with full device compatibility.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="container">
    <div class="stats-section">
        <h2>Our Growing Community</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <h3>10K+</h3>
                <p><i class="fas fa-users"></i> Active Users</p>
            </div>
            <div class="stat-item">
                <h3>50K+</h3>
                <p><i class="fas fa-file-alt"></i> Notes Available</p>
            </div>
            <div class="stat-item">
                <h3>100+</h3>
                <p><i class="fas fa-tags"></i> Categories</p>
            </div>
            <div class="stat-item">
                <h3>4.8★</h3>
                <p><i class="fas fa-star"></i> Average Rating</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-panel">
            <h2 class="cta-title">Ready to Start Learning?</h2>
            <p class="cta-description">
                Join thousands of students and educators using Scholar Sphere to enhance their learning experience.
            </p>
            <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn-primary-hero">
                <i class="fas fa-rocket"></i> Join for Free
            </a>
            <?php else: ?>
            <a href="notes.php" class="btn-primary-hero">
                <i class="fas fa-arrow-right"></i> Start Exploring
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const revealCards = document.querySelectorAll('.reveal-card');

    if (!revealCards.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        revealCards.forEach(function (card) {
            card.classList.add('is-visible');
        });
        return;
    }

    const revealObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px 0px -40px 0px'
    });

    revealCards.forEach(function (card) {
        revealObserver.observe(card);
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

