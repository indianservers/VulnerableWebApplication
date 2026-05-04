<?php
/**
 * STUDENT DASHBOARD
 * Student view of grades, courses, announcements, etc.
 */

include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_secure.php');
    exit();
}

// Get student information
$student = null;
$courses = array();
$grades = array();

$stmt = $conn->prepare("SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student_result = $stmt->get_result();
if ($student_result->num_rows > 0) {
    $student = $student_result->fetch_assoc();
    
    // Get enrolled courses
    $stmt = $conn->prepare("SELECT c.*, sc.grade, sc.attendance_percentage FROM courses c JOIN student_courses sc ON c.id = sc.course_id WHERE sc.student_id = ?");
    $stmt->bind_param("i", $student['id']);
    $stmt->execute();
    $courses_result = $stmt->get_result();
    while ($course = $courses_result->fetch_assoc()) {
        $courses[] = $course;
    }
    
    // Get grades
    $stmt = $conn->prepare("SELECT g.*, c.course_name FROM grades g JOIN courses c ON g.course_id = c.id WHERE g.student_id = ?");
    $stmt->bind_param("i", $student['id']);
    $stmt->execute();
    $grades_result = $stmt->get_result();
    while ($grade = $grades_result->fetch_assoc()) {
        $grades[] = $grade;
    }
}
$stmt->close();

// Get announcements
$announcements = $conn->query("SELECT * FROM announcements WHERE is_active = 1 AND (target_role = 'student' OR target_role = 'all') ORDER BY published_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <header>
        <h1>🎓 Student Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="news_vulnerable.php">News</a>
        <a href="articles_secure.php">Resources</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <?php if ($student): ?>
            <div class="card">
                <h2>👤 Student Information</h2>

                <div class="dashboard-grid">
                    <div class="info-card">
                        <h4>Name</h4>
                        <p><?php echo htmlspecialchars($student['full_name']); ?></p>
                    </div>
                    <div class="info-card">
                        <h4>Student ID</h4>
                        <p><?php echo htmlspecialchars($student['student_id_number']); ?></p>
                    </div>
                    <div class="info-card">
                        <h4>Class</h4>
                        <p><?php echo htmlspecialchars($student['class_name']); ?></p>
                    </div>
                    <div class="info-card">
                        <h4>GPA</h4>
                        <p><?php echo htmlspecialchars($student['gpa']); ?></p>
                    </div>
                </div>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <h3>📚 Enrolled Courses (<?php echo count($courses); ?>)</h3>
                <?php if (!empty($courses)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Attendance</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($course['attendance_percentage'] ?? 'N/A'); ?>%</td>
                                    <td><?php echo htmlspecialchars($course['grade'] ?? 'Pending'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No courses enrolled yet.</div>
                <?php endif; ?>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <h3>📊 Grade Report (<?php echo count($grades); ?>)</h3>
                <?php if (!empty($grades)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assignment 1</th>
                                <th>Assignment 2</th>
                                <th>Midterm</th>
                                <th>Final</th>
                                <th>Total</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['assignment_1'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['assignment_2'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['midterm'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['final_exam'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['total_marks'] ?? '-'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($grade['grade_letter']); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No grades published yet.</div>
                <?php endif; ?>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <h3>📢 Announcements</h3>
                <?php if ($announcements && $announcements->num_rows > 0): ?>
                    <?php while ($announcement = $announcements->fetch_assoc()): ?>
                        <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea;">
                            <h4 style="color: #667eea; margin-bottom: 0.5rem;">
                                🔔 <?php echo htmlspecialchars($announcement['title']); ?>
                            </h4>
                            <small style="color: #999;"><?php echo htmlspecialchars($announcement['published_date']); ?></small>
                            <p style="margin-top: 0.5rem;"><?php echo htmlspecialchars(substr($announcement['content'], 0, 200)) . '...'; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">No announcements at this time.</div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="alert alert-danger">❌ Student record not found. Please contact administration.</div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Student Dashboard</p>
    </footer>
</body>
</html>
