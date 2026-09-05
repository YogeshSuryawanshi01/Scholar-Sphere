<?php
/**
 * AI Chat Page
 */
require_once __DIR__ . '/includes/session_bootstrap.php';
bootSession('public');
require_once __DIR__ . '/includes/db.php';

requireLogin();

$page_title = 'AI Chat';

// Get chat history
$history_stmt = $conn->prepare("SELECT message_text, message_type, created_at FROM messages 
                              WHERE user_id = ? ORDER BY created_at ASC LIMIT 50");
$history_stmt->bind_param("i", $_SESSION['user_id']);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
$chat_history = [];
while ($msg = $history_result->fetch_assoc()) {
    $chat_history[] = $msg;
}
$history_stmt->close();
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 150px);
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .chat-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 15px 15px 0 0;
        padding: 20px;
        text-align: center;
        border-bottom: none;
    }

    .chat-header h2 {
        margin: 0;
        font-size: 1.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .chat-messages {
        flex: 1;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 0;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .chat-message {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    .user-message {
        justify-content: flex-end;
    }

    .ai-message {
        justify-content: flex-start;
    }

    .message-content {
        max-width: 70%;
        padding: 12px 18px;
        border-radius: 12px;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .user-message .message-content {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 18px 18px 0 18px;
    }

    .ai-message .message-content {
        background: rgba(241, 245, 249, 0.96);
        color: var(--text-dark);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 18px 18px 18px 0;
    }

    .message-content i {
        margin-right: 8px;
        font-size: 0.85rem;
    }

    .message-text {
        margin-top: 8px;
    }

    .chat-input-area {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(236, 254, 255, 0.92) 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 0 0 15px 15px;
        padding: 15px;
        border-top: none;
        display: flex;
        gap: 10px;
        min-height: 70px;
    }

    .chat-input-area textarea {
        flex: 1;
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-dark);
        padding: 12px;
        font-size: 0.95rem;
        font-family: inherit;
        resize: vertical;
        max-height: 100px;
    }

    .chat-input-area textarea:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(14, 165, 164, 0.14);
    }

    .chat-input-area button {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        align-self: flex-end;
    }

    .chat-input-area button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(15, 23, 42, 0.20);
    }

    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }

    .empty-chat i {
        font-size: 3rem;
        color: var(--secondary);
        opacity: 0.3;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 120px);
        }

        .message-content {
            max-width: 90%;
        }
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        <h2><i class="fas fa-robot"></i> AI Chat Assistant</h2>
        <p style="color: var(--text-muted); margin: 5px 0 0 0;">Ask anything and get instant answers</p>
    </div>

    <div id="chatMessages" class="chat-messages">
        <?php if (count($chat_history) > 0): ?>
            <?php foreach ($chat_history as $msg): ?>
                <div class="chat-message <?php echo $msg['message_type'] === 'user' ? 'user-message' : 'ai-message'; ?>">
                    <div class="message-content">
                        <i class="fas fa-<?php echo $msg['message_type'] === 'user' ? 'user-circle' : 'robot'; ?>"></i>
                        <?php echo $msg['message_type'] === 'user' ? 'You' : 'AI Assistant'; ?>
                        <div class="message-text"><?php echo htmlspecialchars($msg['message_text']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-chat">
                <i class="fas fa-comments"></i>
                <p>Start a conversation with AI Assistant</p>
                <small>Ask questions and get instant responses</small>
            </div>
        <?php endif; ?>
    </div>

    <div class="chat-input-area">
        <form id="chatForm" style="display: flex; gap: 10px; width: 100%; align-items: flex-end;">
            <textarea id="chatInput" placeholder="Type your message..." required></textarea>
            <button type="submit"><i class="fas fa-paper-plane"></i> Send</button>
        </form>
    </div>
</div>

<script src="assets/js/main.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
