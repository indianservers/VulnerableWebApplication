# Complete Database Setup Guide

## 📋 Prerequisites

- MySQL Server (5.7+)
- PHP (7.4+)
- Web Server (Apache/Nginx)
- phpMyAdmin (optional, for easier management)

## 🚀 Step-by-Step Setup

### Step 1: Create Database User

Connect to MySQL and run:

```sql
CREATE USER 'myuser'@'localhost' IDENTIFIED BY 'StrongPassword123';
GRANT ALL PRIVILEGES ON myapp_db.* TO 'myuser'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Import Database Schema

Option A - Using Command Line:
```bash
mysql -u myuser -p -h localhost myapp_db < database_enhanced.sql
```

Option B - Using phpMyAdmin:
1. Go to phpMyAdmin → Select 'Database' dropdown → Create 'myapp_db'
2. Select myapp_db database
3. Go to 'Import' tab
4. Select 'database_enhanced.sql' file
5. Click 'Go'

Option C - Using SQL Query:
1. Create database manually:
```sql
CREATE DATABASE myapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE myapp_db;
```
2. Copy all SQL from database_enhanced.sql
3. Paste and execute in MySQL query window

### Step 3: Verify Installation

Check if all tables are created:

```sql
USE myapp_db;
SHOW TABLES;
```

You should see these tables:
- announcements
- articles
- attendance
- comments
- courses
- departments
- grades
- news
- student_courses
- students
- users

## 📊 Database Schema Overview

### Users Table
- Stores all system users (admin, teacher, student, parent)
- Includes authentication credentials
- Tracks login history

### Students Table
- Extended student information
- Links to user account
- Personal details, family info, GPA tracking

### Courses Table
- Course information and curriculum
- Class schedules and syllabus
- Department assignments

### Grades Table
- Student performance tracking
- Component-wise marks (assignments, midterm, final)
- Final grades and GPA calculation

### Attendance Table
- Daily attendance records
- Absence tracking
- Attendance percentage calculation

### News & Articles Tables
- School announcements and updates
- Educational content repository
- Publish/expire scheduling

## 👥 Default Test Accounts

### Admin Account
- Username: `admin`
- Password: `admin123`
- Email: `admin@school.edu`

### Teacher Accounts
- Username: `teacher_science`
- Password: `teacher123`
- Username: `teacher_math`
- Password: `teacher123`
- Username: `teacher_english`
- Password: `teacher123`

### Student Accounts
- Username: `student1` to `student4`
- Password: `student123`
- Emails: `student1@school.edu`, etc.

### Parent Account
- Username: `parent1`
- Password: `parent123`
- Email: `parent1@school.edu`

## 🔧 Database Configuration

The database connection is configured in `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'myuser');
define('DB_PASS', 'StrongPassword123');
define('DB_NAME', 'myapp_db');
```

**Note:** Change these values if your MySQL setup is different.

## 📁 Important Tables for Management

### Students Management
- `users` - Base user information
- `students` - Student-specific details
- `student_courses` - Course enrollments

### Academics
- `courses` - Course catalog
- `grades` - Grade records
- `attendance` - Attendance tracking

### Communication
- `news` - School announcements
- `articles` - Educational resources
- `announcements` - Important updates
- `comments` - User feedback (XSS demo)

## 🔐 Security Notes

1. **Change Default Passwords**: Update all default passwords in production
2. **Database Backup**: Regularly backup the database
3. **User Privileges**: Use the provided user account (myuser), don't use root for applications
4. **SQL Injection**: This app demonstrates vulnerabilities - use secure patterns from `login_secure.php` and `articles_secure.php`

## 📝 Common Queries

### Add New Student
```sql
INSERT INTO students (user_id, student_id_number, class_name, roll_number, date_of_admission, status)
VALUES (5, 'STU005', '12-A', 5, '2025-07-01', 'active');
```

### Get Student Report
```sql
SELECT s.student_id_number, u.full_name, s.gpa, COUNT(DISTINCT sc.course_id) as courses
FROM students s
JOIN users u ON s.user_id = u.id
LEFT JOIN student_courses sc ON s.id = sc.student_id
GROUP BY s.id;
```

### Student GPA Summary
```sql
SELECT u.full_name, s.gpa, AVG(g.total_marks) as average_marks
FROM students s
JOIN users u ON s.user_id = u.id
LEFT JOIN grades g ON s.id = g.student_id
GROUP BY s.id
ORDER BY s.gpa DESC;
```

### Course Enrollment Status
```sql
SELECT c.course_code, c.course_name, COUNT(sc.student_id) as enrolled_students, c.max_students
FROM courses c
LEFT JOIN student_courses sc ON c.id = sc.course_id
GROUP BY c.id;
```

## 🐛 Troubleshooting

### Connection Error
**Error:** "Connection failed: Unknown MySQL server host"
**Solution:** 
- Check MySQL is running: `mysqld --version`
- Verify host is 'localhost' (not 127.0.0.1 for some systems)
- Check firewall settings

### Too Many Connections
**Error:** "Too many connections"
**Solution:**
- Close unused database connections in PHP
- Check MAX_CONNECTIONS setting in my.cnf
- Restart MySQL service

### Duplicate Entry
**Error:** "Duplicate entry in unique key"
**Solution:**
- IDs should be unique (student_id_number, username, email)
- Check existing data before inserting
- Use IGNORE or ON DUPLICATE KEY UPDATE

### Table Doesn't Exist
**Error:** "Table 'myapp_db.table_name' doesn't exist"
**Solution:**
- Verify database import completed
- Run `SHOW TABLES;` to check
- Re-import database_enhanced.sql if needed

## 📊 Database Views

Two views are pre-created for reporting:

### student_dashboard_view
Shows comprehensive student data:
```sql
SELECT * FROM student_dashboard_view;
```

### course_statistics_view
Shows course performance metrics:
```sql
SELECT * FROM course_statistics_view;
```

## 🔄 Regular Maintenance

### Monthly Tasks
- Backup database
- Update student GPA/average grades
- Archive old attendances
- Cleanup expired announcements

### Backup Command
```bash
mysqldump -u myuser -p myapp_db > backup_$(date +%Y%m%d).sql
```

### Restore Command
```bash
mysql -u myuser -p myapp_db < backup_20260305.sql
```

## 📞 Support

For issues with database setup:
1. Check MySQL logs: `/var/log/mysql/error.log`
2. Verify permissions: `SHOW GRANTS FOR 'myuser'@'localhost';`
3. Check config.php connection parameters
4. Review error messages in browser console/php-error.log
