-- Enhanced School Web Application Database
-- Complete Schema with User & Student Management

SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS course_statistics_view;
DROP VIEW IF EXISTS student_dashboard_view;

-- Drop existing tables if needed for fresh install
DROP TABLE IF EXISTS student_courses;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS departments;

-- Create Database
CREATE DATABASE IF NOT EXISTS myapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE myapp_db;

-- ============================================
-- DEPARTMENTS TABLE
-- ============================================
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    head_of_department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- USERS TABLE (Login Management)
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'parent') DEFAULT 'student',
    department_id INT,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    profile_picture VARCHAR(255),
    is_active TINYINT DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- ============================================
-- STUDENTS TABLE (Student-Specific Data)
-- ============================================
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    student_id_number VARCHAR(50) NOT NULL UNIQUE,
    class_name VARCHAR(50) NOT NULL,
    roll_number INT,
    father_name VARCHAR(100),
    father_phone VARCHAR(20),
    father_email VARCHAR(100),
    mother_name VARCHAR(100),
    mother_phone VARCHAR(20),
    guardian_phone VARCHAR(20),
    date_of_admission DATE,
    blood_group VARCHAR(10),
    emergency_contact VARCHAR(20),
    health_issues TEXT,
    status ENUM('active', 'inactive', 'graduated', 'transferred', 'dropped') DEFAULT 'active',
    gpa DECIMAL(3,2) DEFAULT 0.00,
    total_attendance_hours INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id_number),
    INDEX idx_class (class_name),
    INDEX idx_status (status)
);

-- ============================================
-- COURSES TABLE
-- ============================================
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(50) NOT NULL UNIQUE,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    department_id INT,
    instructor_id INT,
    credits INT DEFAULT 3,
    total_hours INT DEFAULT 45,
    semester INT,
    academic_year VARCHAR(10),
    max_students INT DEFAULT 50,
    enrolled_students INT DEFAULT 0,
    status ENUM('active', 'inactive', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (instructor_id) REFERENCES users(id),
    INDEX idx_course_code (course_code),
    INDEX idx_semester (semester),
    INDEX idx_status (status)
);

-- ============================================
-- STUDENT COURSES (Enrollment)
-- ============================================
CREATE TABLE student_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('enrolled', 'completed', 'dropped', 'withdrawn') DEFAULT 'enrolled',
    grade VARCHAR(2),
    marks_obtained DECIMAL(5,2),
    attendance_percentage DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    UNIQUE KEY unique_enrollment (student_id, course_id),
    INDEX idx_enrollment_date (enrollment_date),
    INDEX idx_status (status)
);

-- ============================================
-- GRADES TABLE
-- ============================================
CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    assignment_1 DECIMAL(5,2),
    assignment_2 DECIMAL(5,2),
    midterm DECIMAL(5,2),
    final_exam DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade_letter VARCHAR(2),
    teacher_id INT,
    graded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    UNIQUE KEY unique_grade (student_id, course_id),
    INDEX idx_grade_letter (grade_letter)
);

-- ============================================
-- ATTENDANCE TABLE
-- ============================================
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present',
    teacher_id INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    INDEX idx_attendance_date (attendance_date),
    INDEX idx_student_course (student_id, course_id)
);

-- ============================================
-- NEWS TABLE (School Announcements)
-- ============================================
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT NOT NULL,
    author_id INT,
    category VARCHAR(50),
    published_date DATE,
    views INT DEFAULT 0,
    is_featured TINYINT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    INDEX idx_published_date (published_date),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
);

-- ============================================
-- ARTICLES TABLE (Educational Content)
-- ============================================
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content LONGTEXT NOT NULL,
    author_id INT,
    category VARCHAR(50),
    published_date DATE,
    views INT DEFAULT 0,
    is_featured TINYINT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    INDEX idx_published_date (published_date),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
);

-- ============================================
-- ANNOUNCEMENTS TABLE
-- ============================================
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author_id INT,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    target_role VARCHAR(50),
    published_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATETIME,
    is_active TINYINT DEFAULT 1,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    INDEX idx_published_date (published_date),
    INDEX idx_priority (priority),
    INDEX idx_active (is_active)
);

-- ============================================
-- COMMENTS TABLE (XSS Vulnerability Demo)
-- ============================================
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    message LONGTEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_date (created_at)
);

-- ============================================
-- sample DATA
-- ============================================

-- Insert Departments
INSERT INTO departments (name, description, head_of_department) VALUES
('Science', 'Science Department covering Physics, Chemistry, Biology', 'Dr. Rajesh Kumar'),
('Commerce', 'Commerce Department with Economics and Business Studies', 'Mrs. Priya Singh'),
('Arts', 'Arts Department with History, Geography, and Literature', 'Prof. Amit Sharma'),
('Information Technology', 'IT Department for Computer Science and Programming', 'Mr. Vikram Patel');

-- Insert Users (Admin, Teachers, Students)
INSERT INTO users (username, password, email, full_name, role, department_id, phone, date_of_birth) VALUES
('admin', MD5('admin123'), 'admin@school.edu', 'Administrator', 'admin', 1, '9876543210', '1985-05-15'),
('teacher_science', MD5('teacher123'), 'teacher.science@school.edu', 'Dr. Rajesh Kumar', 'teacher', 1, '9876543211', '1980-03-20'),
('teacher_math', MD5('teacher123'), 'teacher.math@school.edu', 'Mrs. Priya Singh', 'teacher', 1, '9876543212', '1982-07-10'),
('teacher_english', MD5('teacher123'), 'teacher.english@school.edu', 'Prof. Amit Sharma', 'teacher', 3, '9876543213', '1978-11-25'),
('student1', MD5('student123'), 'student1@school.edu', 'Aarav Patel', 'student', 1, '9876543220', '2008-01-15'),
('student2', MD5('student123'), 'student2@school.edu', 'Anaya Singh', 'student', 1, '9876543221', '2008-03-22'),
('student3', MD5('student123'), 'student3@school.edu', 'Arjun Verma', 'student', 1, '9876543222', '2008-05-10'),
('student4', MD5('student123'), 'student4@school.edu', 'Diya Nair', 'student', 3, '9876543223', '2008-07-18'),
('parent1', MD5('parent123'), 'parent1@school.edu', 'Rajesh Patel', 'parent', NULL, '9876543230', '1975-02-12');

-- Insert Courses
INSERT INTO courses (course_code, course_name, description, department_id, instructor_id, credits, semester, academic_year, status) VALUES
('SC101', 'Physics - Mechanics', 'Introduction to classical mechanics and motion', 1, 2, 4, 1, '2025-26', 'active'),
('SC102', 'Chemistry - Organic', 'Organic chemistry fundamentals', 1, 2, 4, 1, '2025-26', 'active'),
('SC103', 'Biology - Genetics', 'Genetics and heredity principles', 1, 3, 4, 1, '2025-26', 'active'),
('EN101', 'English Literature', 'Classic and modern English literature', 3, 4, 3, 1, '2025-26', 'active');

-- Insert Students (Link users to students)
INSERT INTO students (user_id, student_id_number, class_name, roll_number, father_name, father_phone, mother_name, date_of_admission, blood_group, status, gpa) VALUES
(5, 'STU001', '12-A', 1, 'Mr. Rajesh Patel', '9876543320', 'Mrs. Sunita Patel', '2023-04-01', 'O+', 'active', 3.75),
(6, 'STU002', '12-A', 2, 'Mr. Amit Singh', '9876543321', 'Mrs. Neha Singh', '2023-04-01', 'A+', 'active', 3.85),
(7, 'STU003', '12-B', 3, 'Mr. Vikram Verma', '9876543322', 'Mrs. Shalini Verma', '2023-04-01', 'B+', 'active', 3.65),
(8, 'STU004', '11-A', 4, 'Mr. Suresh Nair', '9876543323', 'Mrs. Anjali Nair', '2024-04-01', 'O+', 'active', 3.55);

-- Insert Student Course Enrollments
INSERT INTO student_courses (student_id, course_id, enrollment_date, status) VALUES
(1, 1, '2025-07-01', 'enrolled'),
(1, 2, '2025-07-01', 'enrolled'),
(1, 3, '2025-07-01', 'enrolled'),
(2, 1, '2025-07-01', 'enrolled'),
(2, 2, '2025-07-01', 'enrolled'),
(2, 3, '2025-07-01', 'enrolled'),
(3, 1, '2025-07-01', 'enrolled'),
(3, 3, '2025-07-01', 'enrolled'),
(4, 4, '2025-07-01', 'enrolled');

-- Insert Grades
INSERT INTO grades (student_id, course_id, assignment_1, assignment_2, midterm, final_exam, total_marks, grade_letter, teacher_id) VALUES
(1, 1, 4.5, 4.8, 85.0, 88.0, 86.5, 'A+', 2),
(1, 2, 4.2, 4.5, 82.0, 85.0, 83.5, 'A', 2),
(2, 1, 4.8, 4.9, 87.0, 90.0, 88.5, 'A+', 2),
(2, 2, 4.5, 4.7, 84.0, 87.0, 85.5, 'A', 2),
(3, 1, 4.0, 4.3, 80.0, 82.0, 81.0, 'B+', 2);

-- Insert Attendance
INSERT INTO attendance (student_id, course_id, attendance_date, status, teacher_id, remarks) VALUES
(1, 1, '2026-03-25', 'present', 2, 'On time'),
(1, 2, '2026-03-26', 'late', 2, 'Reached after first bell'),
(2, 1, '2026-03-25', 'present', 2, 'Participated actively'),
(2, 2, '2026-03-26', 'absent', 2, 'Medical leave not submitted'),
(3, 1, '2026-03-25', 'excused', 2, 'Approved competition leave');

-- Insert News
INSERT INTO news (title, content, author_id, category, published_date, is_active) VALUES
('School Annual Fest 2026', 'Our school held a spectacular annual fest with cultural performances, competitions, and exhibitions.', 1, 'Events', '2026-03-20', 1),
('New Science Lab Inaugurated', 'The state-of-the-art science laboratory has been inaugurated with modern equipment and facilities.', 1, 'Infrastructure', '2026-03-15', 1),
('Sports Day Results', 'Congratulations to all winners of our annual sports day. See detailed results below.', 2, 'Sports', '2026-03-10', 1),
('Merit List Announced', 'The merit list for the academic year 2025-26 has been announced on the notice board.', 1, 'Academic', '2026-03-05', 1);

-- Insert Articles
INSERT INTO articles (title, content, author_id, category, published_date, is_active) VALUES
('Effective Study Techniques', 'Learn proven study methods: spaced repetition, active recall, Pomodoro technique, mind mapping.', 4, 'Education', '2026-03-18', 1),
('Career Paths in Science', 'Explore career options in medicine, engineering, research, environmental science, and more.', 2, 'Career', '2026-03-12', 1),
('Digital Literacy Guide', 'Essential skills for the digital world: email, cybersecurity, online safety, coding basics.', 1, 'Technology', '2026-03-05', 1),
('Time Management Tips', 'Master your schedule with these essential time management strategies for students.', 3, 'Life Skills', '2026-02-28', 1);

-- Insert Announcements
INSERT INTO announcements (title, content, author_id, priority, target_role, published_date, is_active) VALUES
('Board Exams Schedule Released', 'The board examination schedule for April-May 2026 is now available on the portal.', 1, 'high', 'student', '2026-03-20 10:00:00', 1),
('Staff Meeting - 25th March', 'All staff members are requested to attend the meeting on March 25th at 2 PM.', 1, 'medium', 'teacher', '2026-03-18 09:00:00', 1),
('Holiday Announcement', 'School will remain closed on March 21st for Holi celebrations.', 1, 'medium', 'all', '2026-03-15 08:00:00', 1),
('Admission Open for New Session', 'Admission for the academic year 2026-27 is now open. Apply before March 31st.', 1, 'high', 'parent', '2026-03-10 08:00:00', 1);

-- Insert Sample Comments
INSERT INTO comments (name, email, message, status) VALUES
('John Doe', 'john@example.com', 'Great article! Very helpful for my studies.', 'approved'),
('Jane Smith', 'jane@example.com', 'Thanks for sharing this information.', 'approved'),
('Alex Johnson', 'alex@example.com', 'Could you provide more details?', 'pending');

-- Create database views for common queries
CREATE VIEW student_dashboard_view AS
SELECT 
    s.id,
    u.full_name,
    s.student_id_number,
    s.class_name,
    s.gpa,
    u.email,
    COUNT(DISTINCT sc.course_id) as enrolled_courses,
    COUNT(DISTINCT a.attendance_date) as attendances
FROM students s
JOIN users u ON s.user_id = u.id
LEFT JOIN student_courses sc ON s.id = sc.student_id
LEFT JOIN attendance a ON s.id = a.student_id
GROUP BY s.id;

CREATE VIEW course_statistics_view AS
SELECT 
    c.id,
    c.course_code,
    c.course_name,
    COUNT(DISTINCT sc.student_id) as enrolled_count,
    AVG(g.grade_letter) as avg_grade,
    AVG(g.total_marks) as avg_marks
FROM courses c
LEFT JOIN student_courses sc ON c.id = sc.course_id
LEFT JOIN grades g ON c.id = g.course_id
GROUP BY c.id;

SET FOREIGN_KEY_CHECKS = 1;
