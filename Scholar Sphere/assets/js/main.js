/**
 * Scholar Sphere - Main JavaScript File
 * Handles UI interactions, animations, and utilities
 */

// ======================== TOAST NOTIFICATIONS ========================
class Toast {
    constructor(message, type = 'info', duration = 4000) {
        this.message = message;
        this.type = type; // success, error, warning, info
        this.duration = duration;
        this.show();
    }

    show() {
        const toastContainer = document.getElementById('toastContainer');
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white border-0 toast-${this.type}`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        toastEl.innerHTML = `
            <div class="d-flex gap-3">
                <div>${this.getIcon()} ${this.message}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        // Apply gradient background
        toastEl.style.background = this.getBackground();
        toastEl.style.borderRadius = '10px';
        toastEl.style.backdropFilter = 'blur(10px)';
        toastEl.style.padding = '15px 20px';
        toastEl.style.marginBottom = '10px';
        toastEl.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
        toastEl.style.animation = 'slideIn 0.3s ease';

        toastContainer.appendChild(toastEl);

        const bsToast = new bootstrap.Toast(toastEl, { autohide: true, delay: this.duration });
        bsToast.show();

        // Remove element after toast hides
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    }

    getIcon() {
        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-times-circle"></i>',
            warning: '<i class="fas fa-exclamation-circle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };
        return icons[this.type] || icons.info;
    }

    getBackground() {
        const backgrounds = {
            success: 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
            error: 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)',
            warning: 'linear-gradient(135deg, #F59E0B 0%, #D97706 100%)',
            info: 'linear-gradient(135deg, #4F46E5 0%, #06B6D4 100%)'
        };
        return backgrounds[this.type] || backgrounds.info;
    }
}

// ======================== FORMS ========================
/**
 * Validate email format
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate password strength
 */
function validatePassword(password) {
    return password.length >= 6;
}

/**
 * Show form errors
 */
function showFormError(fieldName, message) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (field) {
        field.classList.add('border-danger');
        const errorEl = document.createElement('small');
        errorEl.className = 'text-danger d-block mt-2';
        errorEl.textContent = message;
        field.parentNode.appendChild(errorEl);
    }
}

/**
 * Clear form errors
 */
function clearFormErrors() {
    document.querySelectorAll('.border-danger').forEach(el => {
        el.classList.remove('border-danger');
    });
    document.querySelectorAll('.text-danger').forEach(el => {
        el.remove();
    });
}

// ======================== RATING SYSTEM ========================
/**
 * Initialize star rating system
 */
function initRatingSystem() {
    const ratingContainers = document.querySelectorAll('.rating-container');
    
    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('input[name="rating"]');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const ratingValue = this.getAttribute('data-value');
                input.value = ratingValue;
                
                // Highlight selected stars
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= ratingValue) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            // Hover effect
            star.addEventListener('mouseenter', function() {
                const ratingValue = this.getAttribute('data-value');
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= ratingValue) {
                        s.style.color = '#FCD34D';
                    } else {
                        s.style.color = 'var(--text-muted)';
                    }
                });
            });
        });

        container.addEventListener('mouseleave', function() {
            stars.forEach(star => {
                if (star.classList.contains('active')) {
                    star.style.color = '#FCD34D';
                } else {
                    star.style.color = 'var(--text-muted)';
                }
            });
        });
    });
}

// ======================== FILE UPLOAD ========================
/**
 * Validate file upload
 */
function validateFile(file, maxSize = 50 * 1024 * 1024, allowedTypes = []) {
    // Check file size
    if (file.size > maxSize) {
        new Toast(`File size exceeds ${formatFileSize(maxSize)}`, 'error');
        return false;
    }

    // Check file type
    if (allowedTypes.length > 0 && !allowedTypes.includes(file.type)) {
        new Toast('File type not allowed', 'error');
        return false;
    }

    return true;
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return size.toFixed(2) + ' ' + units[unitIndex];
}

/**
 * Handle file upload with preview
 */
function setupFileUploadPreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Update file name display
                const fileNameEl = document.getElementById('fileName');
                if (fileNameEl) {
                    fileNameEl.textContent = file.name;
                    fileNameEl.classList.add('d-block');
                }

                // Update file size display
                const fileSizeEl = document.getElementById('fileSize');
                if (fileSizeEl) {
                    fileSizeEl.textContent = `Size: ${formatFileSize(file.size)}`;
                    fileSizeEl.classList.add('d-block');
                }

                // Show preview for images
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (preview) {
                            preview.innerHTML = `<img src="${event.target.result}" alt="Preview" style="max-width: 100%; border-radius: 10px;">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }
}

// ======================== SEARCH & FILTER ========================
/**
 * Initialize search functionality
 */
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', debounce(function(e) {
            const query = e.target.value.trim();
            filterResults(query);
        }, 300));
    }
}

/**
 * Filter results based on search query
 */
function filterResults(query) {
    const cards = document.querySelectorAll('.note-card');
    
    cards.forEach(card => {
        const title = card.querySelector('.note-title').textContent.toLowerCase();
        const description = card.querySelector('.note-description').textContent.toLowerCase();
        
        if (title.includes(query.toLowerCase()) || description.includes(query.toLowerCase())) {
            card.style.display = 'block';
            card.classList.add('fade-in');
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Initialize category filter
 */
function initCategoryFilter() {
    const categorySelect = document.getElementById('categoryFilter');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            filterByCategory(this.value);
        });
    }
}

/**
 * Filter notes by category
 */
function filterByCategory(categoryId) {
    const cards = document.querySelectorAll('.note-card');
    
    cards.forEach(card => {
        if (categoryId === 'all') {
            card.style.display = 'block';
        } else {
            const cardCategory = card.getAttribute('data-category');
            card.style.display = cardCategory === categoryId ? 'block' : 'none';
        }
        card.classList.add('fade-in');
    });
}

// ======================== PAGINATION ========================
/**
 * Initialize pagination
 */
function initPagination() {
    const paginationLinks = document.querySelectorAll('.pagination a');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            paginationLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
        });
    });
}

// ======================== CHAT FUNCTIONALITY ========================
/**
 * Initialize chat interface
 */
function initChat() {
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }

    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
}

/**
 * Send chat message
 */
function sendMessage() {
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');
    const message = chatInput.value.trim();

    if (!message) return;

    // Add user message to chat
    addChatMessage(message, 'user');
    chatInput.value = '';
    chatInput.style.height = 'auto';

    // Show loading indicator
    const loadingEl = document.createElement('div');
    loadingEl.className = 'chat-message ai-message';
    loadingEl.innerHTML = '<div class="spinner"></div>';
    chatMessages.appendChild(loadingEl);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Send to server (relative path)
    fetch('api/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        loadingEl.remove();
        if (data.success) {
            addChatMessage(data.ai_response, 'ai');
            new Toast(data.message, 'success');
        } else {
            new Toast(data.message || 'Error sending message', 'error');
        }
    })
    .catch(error => {
        loadingEl.remove();
        console.error('Error:', error);
        new Toast('Network error. Please try again.', 'error');
    });
}

/**
 * Add message to chat display
 */
function addChatMessage(message, type) {
    const chatMessages = document.getElementById('chatMessages');
    const messageEl = document.createElement('div');
    messageEl.className = `chat-message ${type === 'user' ? 'user-message' : 'ai-message'}`;
    
    messageEl.innerHTML = `
        <div class="message-content">
            ${type === 'user' ? 
                `<i class="fas fa-user-circle"></i> You` :
                `<i class="fas fa-robot"></i> AI Assistant`
            }
            <div class="message-text">${escapeHtml(message)}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageEl);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ======================== UTILITIES ========================
/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Initialize all on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initRatingSystem();
    initSearch();
    initCategoryFilter();
    initPagination();
    initChat();

    // Setup file upload preview
    setupFileUploadPreview('fileInput', 'filePreview');

    // Add animation to feature cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    });

    document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Auto-adjust textarea height
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });
    });
});

// ======================== EXPORT ========================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        Toast,
        isValidEmail,
        validatePassword,
        formatFileSize,
        formatDate,
        debounce
    };
}
