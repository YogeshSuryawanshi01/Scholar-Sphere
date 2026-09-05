-- Scholar Sphere Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS scholar_sphere;
USE scholar_sphere;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    file_size INT,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Ratings table
CREATE TABLE IF NOT EXISTS ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    note_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rating (user_id, note_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);

-- Messages table (Chat logs)
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message_type ENUM('user', 'ai') DEFAULT 'user',
    message_text LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Contact requests table
CREATE TABLE IF NOT EXISTS contact_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin account
-- Email: admin@scholarsphere.com
-- Password: admin123
INSERT INTO users (name, email, password, role, status)
SELECT 'Administrator', 'admin@scholarsphere.com', '$2y$10$j9fAaPT2hbJeycBujeK/GeQmXts4lgInh4b3hS79LysgDEFeaKpdG', 'admin', 'approved'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@scholarsphere.com'
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Mathematics', 'Mathematics and Algebra'),
('Science', 'Physics, Chemistry, Biology'),
('Literature', 'English, Poetry, Prose'),
('History', 'World History, Ancient Civilizations'),
('Technology', 'Computer Science, Programming'),
('Languages', 'Foreign Languages and Grammar');

-- Create indexes for optimization
CREATE INDEX idx_notes_user_id ON notes(user_id);
CREATE INDEX idx_notes_category_id ON notes(category_id);
CREATE INDEX idx_notes_status ON notes(status);
CREATE INDEX idx_ratings_user_id ON ratings(user_id);
CREATE INDEX idx_ratings_note_id ON ratings(note_id);
CREATE INDEX idx_messages_user_id ON messages(user_id);
CREATE INDEX idx_contact_requests_status ON contact_requests(status);
