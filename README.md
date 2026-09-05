[README.md](https://github.com/user-attachments/files/31869017/README.md)
# Scholar Sphere - Simplified Notes

A modern, full-stack notes sharing platform built with PHP, MySQL, Bootstrap 5, and JavaScript. Perfect for educational institutions and learning communities.

## 🌟 Features

### Core Features
- **User Authentication**: Secure registration and login with admin approval system
- **Notes Management**: Upload, manage, and approve educational resources
- **Category System**: Organize notes into multiple categories
- **Rating System**: 5-star rating system for community feedback
- **File Management**: Support for PDF, Word documents, images, and videos
- **Search & Filter**: Advanced search and category filtering
- **AI Chat Assistant**: Integrated AI chat for instant learning support
- **Admin Dashboard**: Comprehensive admin panel for moderation

### Design Features
- Modern dark theme with glassmorphism effects
- Fully responsive design (desktop, tablet, mobile)
- Smooth animations and transitions
- Custom color palette with gradients
- Toast notifications for user feedback
- Professional UI/UX

## 📋 System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- XAMPP (recommended for local development)
- 100MB disk space

## 🚀 Installation & Setup

### Step 1: Download Project Files
Place the project files in your XAMPP htdocs directory:
```
C:\xampp\htdocs\Scholar-Sphere\
```

### Step 2: Create Database

1. Start XAMPP (Apache & MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import the database schema:
   - Click "New" or select the database
   - Go to "Import"
   - Select `db_schema.sql` file
   - Click "Import"

Or, run SQL commands directly in phpMyAdmin:
```sql
-- Copy all SQL from db_schema.sql and execute
```

### Step 3: Configure Database Connection

Edit `includes/db.php` and update database credentials if needed:
```php
define('DB_HOST', 'localhost');    // Your database host
define('DB_USER', 'root');         // Your database username
define('DB_PASS', '');             // Your database password
define('DB_NAME', 'scholar_sphere');
```

### Step 4: Create Required Directories

Ensure these directories have proper permissions (755):
```
uploads/          (for file uploads)
assets/css/       (for stylesheets)
assets/js/        (for JavaScript)
assets/images/    (for images)
```

### Step 5: Start the Application

1. Start XAMPP
2. Access the site: `http://localhost/Scholar-Sphere`

## 📁 Project Structure

```
Scholar-Sphere/
├── index.php                    # Home page
├── login.php                    # Login page
├── register.php                 # Registration page
├── logout.php                   # Logout handler
├── notes.php                    # Browse notes
├── note_detail.php             # Note details
├── upload.php                   # Upload notes
├── ai_chat.php                 # AI chat interface
├── about.php                   # About page
├── contact.php                 # Contact page
├── download.php                # File download handler
├── db_schema.sql               # Database schema
│
├── admin/
│   ├── dashboard.php           # Admin dashboard
│   ├── approve_users.php       # Approve/reject users
│   ├── approve_notes.php       # Approve/reject notes
│   ├── categories.php          # Manage categories
│   ├── users.php               # View all users
│   └── notes.php               # View all notes
│
├── api/
│   └── send_message.php        # AI chat API handler
│
├── includes/
│   ├── db.php                  # Database connection
│   ├── header.php              # Navigation header
│   └── footer.php              # Footer
│
├── assets/
│   ├── css/
│   │   └── style.css           # Main stylesheet
│   ├── js/
│   │   └── main.js             # Main JavaScript
│   └── images/                 # Image assets
│
└── uploads/                    # User uploaded files
```

## 🔐 Security Features

- **Prepared Statements**: All database queries use prepared statements
- **Password Hashing**: Bcrypt password hashing
- **Session Management**: Secure session handling
- **Input Sanitization**: All user inputs are sanitized
- **File Upload Validation**: Type and size validation
- **CSRF Protection**: Ready for token implementation

## 👥 User Roles

### Admin
- Approve/reject user registrations
- Approve/reject submitted notes
- Manage categories
- View all users and notes
- Access admin dashboard with statistics

### User
- Register and create an account (requires admin approval)
- Upload educational notes
- Browse and download notes
- Rate notes (1-5 stars)
- Use AI chat assistant
- View account information

## 🎨 Customization

### Color Scheme
Edit `assets/css/style.css` to change colors:
```css
:root {
    --primary: #4F46E5;         /* Indigo */
    --secondary: #06B6D4;       /* Cyan */
    --bg-dark: #0F172A;         /* Dark background */
    --bg-cards: #1E293B;        /* Card background */
    --text-light: #F1F5F9;      /* Light text */
}
```

### Logo & Branding
Replace text in header with your own brand logo or SVG

### Categories
Add custom categories through the admin panel after logging in as admin

## 🤖 AI Chat Setup (Optional)

### With OpenAI API
1. Get API key from https://platform.openai.com/
2. Set environment variable:
   ```bash
   OPENAI_API_KEY=your_api_key_here
   ```
3. Or update `api/send_message.php` directly

### Without API
The app includes intelligent fallback responses for study help, math, science, and general questions.

## 📝 Database Schema

### users table
- id (INT, Primary Key)
- name (VARCHAR, 100)
- email (VARCHAR, 100, Unique)
- password (VARCHAR, 255)
- role (ENUM: admin, user)
- status (ENUM: approved, pending, rejected)
- created_at, updated_at (TIMESTAMP)

### notes table
- id (INT, Primary Key)
- user_id (INT, Foreign Key)
- title (VARCHAR, 200)
- description (TEXT)
- category_id (INT, Foreign Key)
- file_path (VARCHAR, 255)
- file_type (VARCHAR, 50)
- file_size (INT)
- status (ENUM: approved, pending, rejected)
- downloads (INT)

### categories table
- id (INT, Primary Key)
- name (VARCHAR, 100, Unique)
- description (TEXT)

### ratings table
- id (INT, Primary Key)
- user_id (INT, Foreign Key)
- note_id (INT, Foreign Key)
- rating (INT, 1-5)
- Unique constraint on (user_id, note_id)

### messages table
- id (INT, Primary Key)
- user_id (INT, Foreign Key)
- message_type (ENUM: user, ai)
- message_text (LONGTEXT)
- created_at (TIMESTAMP)

## 🐛 Troubleshooting

### Problem: Database connection failed
**Solution**: 
- Check MySQL is running in XAMPP
- Verify credentials in `includes/db.php`
- Ensure database exists

### Problem: Uploads folder permission denied
**Solution**:
- Right-click uploads folder → Properties
- Set permissions to allow write access
- Or run: `chmod 755 uploads/`

### Problem: Files not downloading
**Solution**:
- Ensure uploads folder path is correct
- Check file exists at specified location
- Verify web server permissions

### Problem: Styling not loading
**Solution**:
- Clear browser cache (Ctrl+F5)
- Check CSS file path in browser console
- Verify Bootstrap CDN is accessible

## 📧 Contact & Support

For issues or questions:
- Email: contact@scholarsphere.com
- Website: www.scholarsphere.com

## 📄 License

This project is provided as-is for educational purposes.

## 🙏 Acknowledgments

- Bootstrap 5 for UI framework
- Font Awesome for icons
- PHP community for documentation
- MySQL for database

---

**Version**: 1.0  
**Last Updated**: 2026-04-01  
**Created for**: Scholar Sphere Platform
