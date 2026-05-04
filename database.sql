-- Database Setup for School Web Application
-- SQL Injection & XSS Demonstration

-- Create Database
CREATE DATABASE IF NOT EXISTS myapp_db;
USE myapp_db;

-- Users Table (for login pages)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role VARCHAR(20) DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- News Table (for vulnerable page)
CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT NOT NULL,
    author VARCHAR(100),
    category VARCHAR(50),
    published_date DATE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Articles Table (for secure page)
CREATE TABLE IF NOT EXISTS articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT NOT NULL,
    author VARCHAR(100),
    category VARCHAR(50),
    published_date DATE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- XSS Vulnerable Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    message LONGTEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Users
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', MD5('admin123'), 'admin@school.edu', 'Administrator', 'admin'),
('teacher', MD5('teacher123'), 'teacher@school.edu', 'John Teacher', 'teacher'),
('student', MD5('student123'), 'student@school.edu', 'Jane Student', 'student');

-- Sample News
INSERT INTO news (title, content, author, category, published_date) VALUES
('School Annual Fest 2026', 'Our annual fest was a grand success with performances, competitions and cultural activities.', 'Admin', 'Events', '2026-03-20'),
('New Library Wing Inaugurated', 'The state-of-the-art library wing with 200 new books has been opened for all students.', 'Principal', 'Infrastructure', '2026-03-15'),
('Sports Day Results', 'Congratulations to all winners of our sports day. Check the detailed results on the bulletin board.', 'Sports Teacher', 'Sports', '2026-03-10');

-- Sample Articles
INSERT INTO articles (title, content, author, category, published_date) VALUES
('Tips for Effective Study', 'Here are some proven techniques for effective studying: 1. Take regular breaks, 2. Create a study schedule, 3. Use active recall...', 'Guidance Counselor', 'Education', '2026-03-18'),
('Career Guidance for Science Students', 'Science students have numerous career options. In this article, we discuss engineering, medicine, research and other fields.', 'Career Advisor', 'Career', '2026-03-12'),
('Digital Literacy in Modern World', 'Digital literacy is essential in todays world. Learn about cybersecurity, online safety, and proper digital etiquette.', 'IT Coordinator', 'Technology', '2026-03-05');

-- Sample Comments (for XSS demonstration)
INSERT INTO comments (name, email, message, status) VALUES
('John Doe', 'john@example.com', 'Great article! Very helpful for my studies.', 'approved'),
('Jane Smith', 'jane@example.com', 'Thanks for sharing this information.', 'approved');
