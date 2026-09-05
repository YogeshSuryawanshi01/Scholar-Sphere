<?php
/**
 * Notes Browsing Page
 * Display all approved notes with filtering, search, and pagination
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

$page_title = 'Browse Notes';

// Pagination setup
$items_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get search query
$search = isset($_GET['search']) ? sanitize($_GET['search'], $conn) : '';

// Get category filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Build query
$query = "SELECT n.id, n.title, n.description, n.file_path, n.file_type, u.name as author, n.created_at, c.name as category, c.id as category_id 
          FROM notes n 
          JOIN users u ON n.user_id = u.id 
          JOIN categories c ON n.category_id = c.id 
          WHERE n.status = 'approved'";

// Count query for pagination
$count_query = "SELECT COUNT(n.id) as total FROM notes n 
                JOIN users u ON n.user_id = u.id 
                JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'approved'";

if (!empty($search)) {
    $search_term = "%$search%";
    $query .= " AND (n.title LIKE ? OR n.description LIKE ?)";
    $count_query .= " AND (n.title LIKE ? OR n.description LIKE ?)";
}

if ($category_filter > 0) {
    $query .= " AND n.category_id = ?";
    $count_query .= " AND n.category_id = ?";
}

// Count total records
$count_stmt = $conn->prepare($count_query);
if (!empty($search) && $category_filter > 0) {
    $count_stmt->bind_param("ssi", $search_term, $search_term, $category_filter);
} else if (!empty($search)) {
    $count_stmt->bind_param("ss", $search_term, $search_term);
} else if ($category_filter > 0) {
    $count_stmt->bind_param("i", $category_filter);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $items_per_page);
$count_stmt->close();

// Get notes
$query .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

if (!empty($search) && $category_filter > 0) {
    $stmt->bind_param("ssiii", $search_term, $search_term, $category_filter, $items_per_page, $offset);
} else if (!empty($search)) {
    $stmt->bind_param("ssii", $search_term, $search_term, $items_per_page, $offset);
} else if ($category_filter > 0) {
    $stmt->bind_param("iii", $category_filter, $items_per_page, $offset);
} else {
    $stmt->bind_param("ii", $items_per_page, $offset);
}

$stmt->execute();
$notes_result = $stmt->get_result();
$notes = [];
while ($row = $notes_result->fetch_assoc()) {
    $notes[] = $row;
}
$stmt->close();

// Get all categories
$categories_stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name");
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$categories = [];
while ($cat = $categories_result->fetch_assoc()) {
    $categories[] = $cat;
}
$categories_stmt->close();
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .notes-container {
        padding: 60px 0;
    }

    .notes-header {
        margin-bottom: 50px;
        text-align: center;
    }

    .notes-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filters-section {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.94) 0%, rgba(236, 254, 255, 0.92) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        padding: 30px;
        margin-bottom: 40px;
    }

    .filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .filter-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 12px 15px;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-dark);
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .filter-group input::placeholder {
        color: #94A3B8;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        background-color: var(--surface);
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .btn-search {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-search:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.24);
        color: white;
    }

    .btn-reset {
        background: rgba(255, 255, 255, 0.72);
        color: var(--secondary-dark);
        border: 2px solid rgba(14, 165, 164, 0.45);
        padding: 10px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-reset:hover {
        background: var(--secondary);
        color: white;
    }

    .notes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .note-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .note-card:hover {
        border-color: rgba(14, 165, 164, 0.42);
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.14);
        transform: translateY(-10px);
    }

    .note-card-header {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.05) 0%, rgba(14, 165, 164, 0.12) 100%);
        padding: 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .note-category {
        display: inline-block;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .note-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .note-card-body {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .note-description {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .note-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid rgba(148, 163, 184, 0.18);
        margin-bottom: 15px;
        font-size: 0.85rem;
        gap: 12px;
        flex-wrap: wrap;
    }

    .note-author {
        color: var(--secondary-dark);
        font-weight: 600;
    }

    .note-date {
        color: var(--text-muted);
    }

    .note-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #FCD34D;
    }

    .note-footer {
        display: flex;
        gap: 10px;
    }

    .btn-view {
        flex: 1;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        font-size: 0.9rem;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(15, 23, 42, 0.24);
        color: white;
    }

    .btn-download {
        flex: 1;
        background: rgba(255, 255, 255, 0.75);
        color: var(--secondary-dark);
        border: 2px solid rgba(14, 165, 164, 0.45);
        padding: 8px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        font-size: 0.9rem;
    }

    .btn-download:hover {
        background: var(--secondary);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--secondary);
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state h3 {
        color: var(--text-dark);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-muted);
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 40px 0;
    }

    .pagination-btn {
        padding: 10px 15px;
        background-color: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.24);
        color: var(--primary);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .pagination-btn:hover,
    .pagination-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-color: transparent;
    }

    @media (max-width: 768px) {
        .notes-header h1 {
            font-size: 1.8rem;
        }

        .filters-row {
            grid-template-columns: 1fr;
        }

        .notes-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container notes-container">
    <!-- Header -->
    <div class="notes-header">
        <h1><i class="fas fa-book"></i> Browse Notes</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Explore thousands of educational resources</p>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-row">
            <div class="filter-group">
                <label for="searchInput"><i class="fas fa-search"></i> Search Notes</label>
                <input 
                    type="text" 
                    id="searchInput" 
                    name="search" 
                    placeholder="Search by title or description..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </div>

            <div class="filter-group">
                <label for="categoryFilter"><i class="fas fa-filter"></i> Category</label>
                <select id="categoryFilter" name="category">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="notes.php" class="btn-reset" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Notes Grid -->
    <?php if (count($notes) > 0): ?>
        <div class="notes-grid">
            <?php foreach ($notes as $note): 
                $rating = getAverageRating($note['id'], $conn);
            ?>
                <div class="note-card note-card-<?php echo $note['id']; ?>" data-category="<?php echo $note['category_id']; ?>">
                    <div class="note-card-header">
                        <div class="note-category"><?php echo htmlspecialchars($note['category']); ?></div>
                        <h5 class="note-title"><?php echo htmlspecialchars(substr($note['title'], 0, 50)); ?></h5>
                    </div>

                    <div class="note-card-body">
                        <p class="note-description"><?php echo htmlspecialchars(substr($note['description'], 0, 120)); ?>...</p>

                        <div class="note-meta">
                            <span class="note-author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($note['author']); ?></span>
                            <span class="note-date"><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                        </div>

                        <div class="note-rating">
                            <i class="fas fa-star"></i> 
                            <span><?php echo $rating['average']; ?> (<?php echo $rating['count']; ?>)</span>
                        </div>

                        <div class="note-footer">
                            <a href="note_detail.php?id=<?php echo $note['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="download.php?id=<?php echo $note['id']; ?>" class="btn-download">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <?php if ($page > 1): ?>
                    <a href="notes.php?page=1<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?>" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> First
                    </a>
                    <a href="notes.php?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?>" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="notes.php?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?>" 
                       class="pagination-btn <?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="notes.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?>" class="pagination-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="notes.php?page=<?php echo $total_pages; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_filter ? '&category=' . $category_filter : ''; ?>" class="pagination-btn">
                        Last <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>No Notes Found</h3>
            <p><?php echo !empty($search) || $category_filter > 0 ? 'Try adjusting your filters or search terms' : 'No notes have been uploaded yet'; ?></p>
            <?php if (isLoggedIn() && !isAdmin()): ?>
                <a href="upload.php" class="btn-primary-hero" style="margin-top: 20px; display: inline-block;">
                    <i class="fas fa-upload"></i> Upload Your First Note
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
