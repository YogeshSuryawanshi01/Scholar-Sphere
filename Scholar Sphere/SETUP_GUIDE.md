# Scholar Sphere - Complete Setup Guide

## ✅ Installation Checklist

### Files & Directories Created

#### Root Files
- ✅ `index.php` - Home page with hero section and features
- ✅ `login.php` - User login page
- ✅ `register.php` - User registration page
- ✅ `logout.php` - Logout handler
- ✅ `notes.php` - Notes browsing with filtering and pagination
- ✅ `note_detail.php` - Individual note details and ratings
- ✅ `upload.php` - Upload notes form
- ✅ `ai_chat.php` - AI chat interface
- ✅ `about.php` - About us page
- ✅ `contact.php` - Contact form page
- ✅ `download.php` - File download handler
- ✅ `setup.php` - Installation wizard

#### Admin Folder (`/admin/`)
- ✅ `dashboard.php` - Admin main dashboard
- ✅ `approve_users.php` - Approve/reject user registrations
- ✅ `approve_notes.php` - Approve/reject submitted notes
- ✅ `categories.php` - Manage note categories
- ✅ `users.php` - View all users
- ✅ `notes.php` - View all notes

#### Includes Folder (`/includes/`)
- ✅ `db.php` - Database connection and helper functions
- ✅ `header.php` - Navigation navbar
- ✅ `footer.php` - Footer with company info

#### API Folder (`/api/`)
- ✅ `send_message.php` - AI chat API handler (with OpenAI integration option)

#### Assets Folder (`/assets/`)
- ✅ `css/style.css` - Complete stylesheet with animations
- ✅ `js/main.js` - JavaScript utilities and interactivity
- ✅ `images/` - Directory for image assets

#### Database
- ✅ `db_schema.sql` - Complete MySQL database schema

#### Configuration
- ✅ `.htaccess` - Apache configuration and security
- ✅ `README.md` - Complete documentation
- ✅ `setup.php` - Installation helper

#### Directories (Auto-created)
- ✅ `uploads/` - User file uploads directory

---

## 🚀 Quick Start

### 1. Download & Place Files
```
1. Extract all files to: C:\xampp\htdocs\Scholar-Sphere\
2. Ensure directory structure matches above
```

### 2. Start Services
```
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL
```

### 3. Create Database
```
1. Go to: http://localhost/phpmyadmin
2. Click "New" to create new database
3. Name: scholar_sphere
4. Import db_schema.sql:
   - Click "Import" tab
   - Select db_schema.sql file
   - Click "Import"
```

### 4. Configure (if needed)
```php
Edit: includes/db.php
- DB_HOST: localhost
- DB_USER: root
- DB_PASS: (leave blank if no password)
- DB_NAME: scholar_sphere
```

### 5. Access Application
```
Browser: http://localhost/Scholar-Sphere
Setup Wizard: http://localhost/Scholar-Sphere/setup.php
```

---

## 👤 Default Test Accounts

Create these after registration and admin approval:

### Admin Account
- Email: admin@example.com
- Password: admin123

### Test User Account
- Email: user@example.com
- Password: user123

---

## 📊 Key Features Summary

### Authentication
- ✅ Secure registration with admin approval
- ✅ Login with prepared statements
- ✅ Password hashing (Bcrypt)
- ✅ Session management

### Notes Management
- ✅ Upload with validation (PDF, DOCX, Images, Videos)
- ✅ Category system with CRUD operations
- ✅ Search functionality
- ✅ Category filtering
- ✅ Pagination (12 items per page)
- ✅ Download counter

### Ratings System
- ✅ 1-5 star ratings
- ✅ Prevent duplicate ratings per user
- ✅ Average rating calculation
- ✅ Visual star display

### AI Chat
- ✅ Chat interface with message bubbles
- ✅ Chat history storage
- ✅ OpenAI integration ready (optional)
- ✅ Intelligent fallback responses

### Admin Panel
- ✅ Dashboard with statistics
- ✅ User approval workflow
- ✅ Note approval workflow
- ✅ Category management (CRUD)
- ✅ View all users
- ✅ View all notes

### Design & UX
- ✅ Modern dark theme
- ✅ Glassmorphism effects
- ✅ Smooth animations
- ✅ Responsive grid layouts
- ✅ Toast notifications
- ✅ Loading spinners
- ✅ Color gradient effects

---

## 🔧 Configuration Options

### Database
Edit `includes/db.php` for custom database settings

### Colors
Edit `assets/css/style.css` CSS variables:
```css
--primary: #4F46E5
--secondary: #06B6D4
--bg-dark: #0F172A
```

### File Upload Limits
Edit `upload.php`:
```php
$max_size = 50 * 1024 * 1024; // 50MB
```

### Items per Page
Edit `notes.php`:
```php
$items_per_page = 12;
```

### AI Chat
Edit `api/send_message.php`:
```php
$api_key = getenv('OPENAI_API_KEY');
```

---

## 🔐 Security Features Implemented

- ✅ Prepared SQL statements (prevent SQL injection)
- ✅ Password hashing with Bcrypt
- ✅ Input sanitization
- ✅ File type & size validation
- ✅ MIME type checking
- ✅ Session-based authentication
- ✅ Apache security headers (.htaccess)
- ✅ Directory access restrictions

---

## 📱 Responsive Breakpoints

- Desktop: 1200px+
- Tablet: 768px - 1199px
- Mobile: < 768px

All pages fully responsive with CSS media queries and Bootstrap grid system.

---

## 🎨 Color Palette Reference

| Name | Color | Usage |
|------|-------|-------|
| Primary | #4F46E5 | Buttons, gradients, accents |
| Secondary | #06B6D4 | Links, highlights, hover states |
| Dark BG | #0F172A | Page background |
| Cards | #1E293B | Card backgrounds |
| Text Light | #F1F5F9 | Primary text |
| Text Muted | #94A3B8 | Secondary text |
| Success | #10B981 | Approve, success actions |
| Danger | #EF4444 | Reject, error actions |
| Warning | #F59E0B | Pending, warning states |

---

## 📋 Before Going Live

- [ ] Change default admin credentials
- [ ] Update contact information (footer, contact page)
- [ ] Add company logo and branding
- [ ] Configure OpenAI API (if using AI features)
- [ ] Test all forms and validations
- [ ] Test file uploads with various file types
- [ ] Test user approval workflow
- [ ] Test note approval workflow
- [ ] Backup database regularly
- [ ] Monitor error logs
- [ ] Enable HTTPS (SSL certificate)
- [ ] Update README with deployment info

---

## 🐛 Debugging Tips

### Enable PHP Errors
Add to `includes/db.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Database Logs
XAMPP MySQL Log: `C:\xampp\mysql\data\mysql_error.log`

### Browser Console
F12 → Console tab for JavaScript errors

### Network Tab
F12 → Network tab to check API responses

---

## 📧 Support & Contact

For issues or customization:
- Review README.md
- Check error logs
- Verify database connection
- Ensure file permissions on uploads

---

## ✨ Features Breakdown by Page

### Index.php
- Hero section with CTA buttons
- 6 feature cards (3 slide-left, 3 slide-right)
- Statistics section
- Responsive grid layout

### login.php & register.php
- Form validation
- Prepared statements
- Error/success messages
- Responsive card design

### notes.php
- Advanced filtering
- Search functionality
- Category filter dropdown
- Pagination system
- Note cards with rating display

### upload.php
- File upload with preview
- Category selection
- Drag-and-drop support
- File size display

### note_detail.php
- Full note information
- Star rating system (1-5)
- Download handler
- Rating submission form

### ai_chat.php
- Chat message display
- User/AI message bubbles
- Chat history loading
- Auto-scroll to latest message

### About.php & contact.php
- About company information
- Team showcase cards
- Contact form with validation
- Location and contact details

### Admin Dashboard
- Statistics cards
- Management buttons
- Quick navigation links

---

## 🎯 Ready to Deploy

Your Scholar Sphere application is now complete with:
- ✅ Full authentication system
- ✅ Complete notes management
- ✅ Rating system
- ✅ AI chat integration
- ✅ Admin panel
- ✅ Responsive design
- ✅ Security best practices
- ✅ Professional UI/UX

---

**Last Updated**: April 1, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
