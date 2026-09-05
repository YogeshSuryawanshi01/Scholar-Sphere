<?php
/**
 * Contact Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';
$page_title = 'Contact Us';

$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("
        CREATE TABLE IF NOT EXISTS contact_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'resolved') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $insert_stmt = $conn->prepare("INSERT INTO contact_requests (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $name, $email, $subject, $message);
        $message_sent = $insert_stmt->execute();
        $insert_stmt->close();
    }
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .contact-section {
        padding: 80px 0;
    }

    .contact-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .contact-header h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .contact-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 40px;
    }

    .contact-info {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 40px;
    }

    .contact-info h3 {
        color: var(--secondary-dark);
        font-weight: 700;
        margin-bottom: 30px;
    }

    .info-item {
        margin-bottom: 25px;
    }

    .info-item i {
        color: var(--secondary);
        font-size: 1.3rem;
        margin-right: 15px;
    }

    .info-item strong {
        color: var(--text-dark);
    }

    .info-item p {
        color: var(--text-muted);
        margin: 5px 0 0 35px;
    }

    .contact-form {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 40px;
    }

    .contact-form h3 {
        color: var(--secondary-dark);
        font-weight: 700;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-weight: 600;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-dark);
        font-size: 0.95rem;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        background-color: var(--surface);
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .submit-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.24);
    }

    .success-message {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #166534;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .contact-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contact-section">
    <div class="container">
        <div class="contact-header">
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
            <p style="color: var(--text-muted); font-size: 1.5rem;">We'd love to hear from you</p>
        </div>

        <div class="contact-content">
            <!-- Contact Information -->
            <div class="contact-info">
                <h3>Get In Touch</h3>

                <div class="info-item">
                    <strong><i class="fas fa-map-marker-alt"></i> Address</strong>
                    <p>Scholar Sphere HQ<br>Education District, Global City</p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-phone"></i> Phone</strong>
                    <p>+1 (555) 123-4567</p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-envelope"></i> Email</strong>
                    <p>contact@scholarsphere.com<br>support@scholarsphere.com</p>
                </div>

                <div class="info-item">
                    <strong><i class="fas fa-clock"></i> Business Hours</strong>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form">
                <h3>Send us a Message</h3>

                <?php if ($message_sent): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i> Thank you for your message! We'll get back to you soon.
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="How can we help?" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Your message..." required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
