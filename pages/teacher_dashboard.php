<?php
/**
 * TEACHER DASHBOARD
 * Teacher view of students, grades, attendance, etc.
 */

include '../includes/config.php';

// Check if user is logged in and is teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login_secure.php');
    exit();
}

// Get teacher's courses
$courses = array();
$stmt = $conn->prepare("SELECT c.*, d.name as department_name FROM courses c LEFT JOIN departments d ON c.department_id = d.id WHERE c.instructor_id = ? ORDER BY c.course_code");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$courses_result = $stmt->get_result();
while ($course = $courses_result->fetch_assoc()) {
    $courses[] = $course;
}
$stmt->close();

// Get students in selected course
$selected_course = $_GET['course'] ?? ($courses[0]['id'] ?? null);
// [VULN: A01 Broken Access Control] No ownership check - any teacher can see any course
// Try: teacher_science logs in, then visits ?course=4 (an English course)
$students_in_course = array();

if ($selected_course) {
    $stmt = $conn->prepare("
        SELECT s.id, s.student_id_number, u.full_name, u.email, sc.attendance_percentage, g.id as grade_id, g.total_marks, g.grade_letter
        FROM students s
        JOIN users u ON s.user_id = u.id
        JOIN student_courses sc ON s.id = sc.student_id
        LEFT JOIN grades g ON s.id = g.student_id AND g.course_id = ?
        WHERE sc.course_id = ?
        ORDER BY s.roll_number
    ");
    $stmt->bind_param("ii", $selected_course, $selected_course);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($student = $result->fetch_assoc()) {
        $students_in_course[] = $student;
    }
    $stmt->close();
}

// Get statistics
$total_students = 0;
$total_assignments_submitted = 0;
$grade_update_msg = '';

// [VULN: A03 SQL Injection] $selected_course comes from $_GET['course'] unvalidated
// Payload: ?course=1 UNION SELECT password,2 FROM users--
if ($selected_course) {
    $result = $conn->query("SELECT COUNT(*) as count FROM student_courses WHERE course_id = $selected_course");
    $total_students = $result->fetch_assoc()['count'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    // [VULN: A08 CSRF] No CSRF token - any page can submit this form cross-origin
    $grade_id  = $_POST['grade_id'];
    $new_marks = $_POST['marks'];
    $conn->query("UPDATE grades SET total_marks = '$new_marks' WHERE id = $grade_id");
    $grade_update_msg = "Grade updated!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .course-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 1rem;
        }
        .course-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .course-card.active {
            background: #f0f5ff;
            border-left-color: #667eea;
        }
    </style>
</head>
<body>
    <header>
        <h1>👨‍🏫 Teacher Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="teacher_dashboard.php">Dashboard</a>
        <a href="news_vulnerable.php">News</a>
        <a href="content_management.php">Manage Content (Admin Only)</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="column-2">
            <div>
                <div class="card">
                    <h2>📚 My Courses (<?php echo count($courses); ?>)</h2>
                    <div style="background:#f8d7da;border:1px solid #dc3545;padding:10px;border-radius:5px;margin-bottom:1rem;font-size:0.85rem;">
                        <strong>Lab Hint [A01]:</strong> Course links use <code>?course=</code>, but this page does not verify that the selected course belongs to the logged-in teacher.
                    </div>
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card <?php echo ($selected_course == $course['id']) ? 'active' : ''; ?>">
                            <a href="teacher_dashboard.php?course=<?php echo $course['id']; ?>" style="text-decoration: none; color: inherit;">
                                <h4><?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['course_name']); ?></h4>
                                <small><?php echo htmlspecialchars($course['department_name']); ?> | Semester <?php echo htmlspecialchars($course['semester']); ?></small>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2>📊 Course Statistics</h2>
                    <div style="background:#fff3cd;border:1px solid #ffc107;padding:10px;border-radius:5px;margin-bottom:1rem;font-size:0.85rem;">
                        🔍 <strong>Lab Hint [A03]:</strong> The <code>?course=</code> parameter is used directly in a SQL query. Try injecting into it.
                    </div>
                    <?php if ($selected_course): ?>
                        <?php if ($grade_update_msg): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($grade_update_msg); ?></div>
                        <?php endif; ?>
                        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 5px; margin-bottom: 1rem;">
                            <p><strong>Total Students Enrolled:</strong> <?php echo $total_students; ?></p>
                            <p><strong>Course Progress:</strong> Faculty Management</p>
                        </div>

                        <h3>👥 Students in This Course (<?php echo count($students_in_course); ?>)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Attendance %</th>
                                    <th>Grade ID</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students_in_course as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['student_id_number']); ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['attendance_percentage'] ?? 'N/A'); ?>%</td>
                                        <td><?php echo htmlspecialchars($student['grade_id'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($student['total_marks'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($student['grade_letter'] ?? 'Pending'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div style="background:#fff3cd;border:1px solid #ffc107;padding:10px;border-radius:5px;margin:1rem 0;font-size:0.85rem;">
                            <strong>Lab Hint [A08]:</strong> This grade update form has no CSRF token. A malicious page could submit it for a logged-in teacher.
                        </div>
                        <form method="POST">
                            <div class="form-group">
                                <label for="grade_id">Grade ID</label>
                                <input type="text" id="grade_id" name="grade_id" placeholder="Example: 1">
                            </div>
                            <div class="form-group">
                                <label for="marks">New Total Marks</label>
                                <input type="text" id="marks" name="marks" placeholder="Example: 99.9">
                            </div>
                            <button type="submit" name="update_grade">Update Grade Without CSRF Token</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">Select a course to view details.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Teacher Dashboard</p>
    </footer>
</body>
</html>
