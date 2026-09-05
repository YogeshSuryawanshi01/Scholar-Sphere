<?php
/**
 * Note Detail Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

$page_title = 'Note Details';

$note_id = (int)($_GET['id'] ?? 0);

if ($note_id === 0) {
    header('Location: notes.php');
    exit;
}

// Get note details
$stmt = $conn->prepare("SELECT n.id, n.title, n.description, n.file_path, n.file_type, n.downloads, n.created_at, n.user_id, 
                             u.name as author, c.name as category 
                        FROM notes n 
                        JOIN users u ON n.user_id = u.id 
                        JOIN categories c ON n.category_id = c.id 
                        WHERE n.id = ? AND n.status = 'approved'");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: notes.php');
    exit;
}

$note = $result->fetch_assoc();
$stmt->close();

$viewable_file_types = [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/gif',
    'video/mp4',
    'video/quicktime',
    'text/plain'
];
$can_open_file = !empty($note['file_path']);
$can_preview_in_browser = $can_open_file && in_array($note['file_type'], $viewable_file_types, true);
$file_label = $note['file_type'] ? strtoupper(str_replace(['application/', 'image/', 'video/'], '', $note['file_type'])) : 'FILE';

// Get ratings
$rating_info = getAverageRating($note_id, $conn);

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $rating = (int)($_POST['rating'] ?? 0);
    
    if ($rating >= 1 && $rating <= 5) {
        // Check if user already rated
        $check_stmt = $conn->prepare("SELECT id FROM ratings WHERE user_id = ? AND note_id = ?");
        $check_stmt->bind_param("ii", $_SESSION['user_id'], $note_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Update existing rating
            $update_stmt = $conn->prepare("UPDATE ratings SET rating = ? WHERE user_id = ? AND note_id = ?");
            $update_stmt->bind_param("iii", $rating, $_SESSION['user_id'], $note_id);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Insert new rating
            $insert_stmt = $conn->prepare("INSERT INTO ratings (user_id, note_id, rating) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iii", $_SESSION['user_id'], $note_id, $rating);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        // Refresh rating info
        $rating_info = getAverageRating($note_id, $conn);
        showToast('Rating submitted successfully', 'success');
    }
    
    $check_stmt->close();
}

// Get user rating if logged in
$user_rating = 0;
if (isLoggedIn()) {
    $user_rating_stmt = $conn->prepare("SELECT rating FROM ratings WHERE user_id = ? AND note_id = ?");
    $user_rating_stmt->bind_param("ii", $_SESSION['user_id'], $note_id);
    $user_rating_stmt->execute();
    $user_rating_result = $user_rating_stmt->get_result();
    if ($user_rating_result->num_rows > 0) {
        $user_rating = $user_rating_result->fetch_assoc()['rating'];
    }
    $user_rating_stmt->close();
}
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .detail-container {
        max-width: 900px;
        margin: 60px auto;
        padding: 20px;
    }

    .detail-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.10);
    }

    .note-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 20px;
    }

    .note-meta {
        display: flex;
        gap: 30px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        padding-bottom: 30px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.24);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .meta-item i {
        color: var(--secondary-dark);
        font-size: 1.2rem;
    }

    .meta-item span {
        color: var(--text-muted);
    }

    .meta-item strong {
        color: var(--text-dark);
    }

    .meta-item .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.94) 0%, rgba(11, 127, 123, 0.92) 100%);
        color: white;
        border: 1px solid rgba(249, 115, 22, 0.3);
        padding: 9px 16px;
        border-radius: 999px;
        font-size: 0.9rem;
        font-weight: 700;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14);
    }

    .meta-item .category-badge i {
        color: var(--accent);
    }

    .meta-item .file-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.12);
        color: #9A3412;
        border: 1px solid rgba(249, 115, 22, 0.22);
        font-size: 0.82rem;
        font-weight: 700;
    }

    .meta-item .downloads-text,
    .meta-item .date-text {
        color: var(--text-dark);
    }

    .note-description {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 30px;
        font-size: 1.05rem;
    }

    .rating-section {
        background: rgba(248, 250, 252, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .rating-section h3 {
        color: var(--text-dark);
        margin-bottom: 15px;
    }

    .rating-display {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .rating-stars {
        font-size: 1.5rem;
    }

    .rating-info {
        color: var(--text-muted);
    }

    .rating-form {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(148, 163, 184, 0.18);
    }

    .rating-form label {
        display: block;
        margin-bottom: 15px;
        color: var(--text-dark);
        font-weight: 600;
    }

    .star-rating {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .star {
        font-size: 2rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .star:hover, .star.active {
        color: #FCD34D;
        transform: scale(1.2);
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .btn-download {
        flex: 1;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.24);
        color: white;
    }

    .btn-open {
        flex: 1;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.96) 0%, rgba(11, 127, 123, 0.92) 100%);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
    }

    .btn-open:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.22);
        color: white;
    }

    .btn-back {
        flex: 1;
        background: transparent;
        color: var(--secondary-dark);
        border: 2px solid rgba(14, 165, 164, 0.45);
        padding: 13px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: var(--secondary);
        color: white;
    }

    @media (max-width: 768px) {
        .note-title {
            font-size: 1.5rem;
        }

        .note-meta {
            flex-direction: column;
            gap: 15px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<div class="detail-container">
    <div class="detail-card">
        <h1 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h1>

        <div class="note-meta">
            <div class="meta-item">
                <i class="fas fa-user"></i>
                <span>By <strong><?php echo htmlspecialchars($note['author']); ?></strong></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span class="date-text"><?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-tag"></i>
                <span class="category-badge">
                    <i class="fas fa-bookmark"></i>
                    <?php echo htmlspecialchars($note['category']); ?>
                </span>
            </div>
            <div class="meta-item">
                <i class="fas fa-file-lines"></i>
                <span class="file-chip"><?php echo htmlspecialchars($file_label); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-download"></i>
                <span class="downloads-text"><strong><?php echo $note['downloads']; ?></strong> downloads</span>
            </div>
        </div>

        <p class="note-description"><?php echo nl2br(htmlspecialchars($note['description'])); ?></p>

        <!-- Rating Section -->
        <div class="rating-section">
            <h3><i class="fas fa-star"></i> Community Rating</h3>
            
            <div class="rating-display">
                <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star" <?php echo $i <= round($rating_info['average']) ? 'style="color: #FCD34D;"' : ''; ?>></i>
                    <?php endfor; ?>
                </div>
                <div class="rating-info">
                    <strong><?php echo $rating_info['average']; ?></strong> / 5.0 
                    <span>(<?php echo $rating_info['count']; ?> <?php echo $rating_info['count'] === 1 ? 'rating' : 'ratings'; ?>)</span>
                </div>
            </div>

            <?php if (isLoggedIn()): ?>
            <div class="rating-form">
                <form method="POST">
                    <label>Your Rating:</label>
                    <div class="star-rating" id="ratingContainer">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star star" data-value="<?php echo $i; ?>" <?php echo $user_rating >= $i ? 'style="color: #FCD34D;" class="fas fa-star star active"' : ''; ?>></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="ratingInput" name="rating" value="<?php echo $user_rating; ?>">
                    <button type="submit" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                        Submit Rating
                    </button>
                </form>
            </div>
            <?php else: ?>
            <p style="color: var(--text-muted); margin-top: 10px;">
                <i class="fas fa-info-circle"></i> <a href="login.php" style="color: var(--secondary-dark);">Log in</a> to rate this note.
            </p>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if ($can_open_file): ?>
            <a href="<?php echo htmlspecialchars($note['file_path']); ?>" class="btn-open" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-eye"></i> <?php echo $can_preview_in_browser ? 'Open File' : 'Open / Download File'; ?>
            </a>
            <?php endif; ?>
            <a href="download.php?id=<?php echo $note['id']; ?>" class="btn-download">
                <i class="fas fa-download"></i> Download File
            </a>
            <a href="notes.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Notes
            </a>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-value');
        document.getElementById('ratingInput').value = rating;
        
        document.querySelectorAll('.star').forEach(s => {
            if (s.getAttribute('data-value') <= rating) {
                s.classList.add('active');
            } else {
                s.classList.remove('active');
            }
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
