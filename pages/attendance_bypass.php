<?php
/**
 * ATTENDANCE EXPOSURE DEMO
 * Attendance records can be queried directly without validating the viewer.
 */

include '../includes/config.php';

$student_id = $_GET['student_id'] ?? '';
$course_id = $_GET['course_id'] ?? '';

$conditions = array();
if ($student_id !== '') {
    $conditions[] = "a.student_id = $student_id";
}
if ($course_id !== '') {
    $conditions[] = "a.course_id = $course_id";
}

$where_clause = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';
$query = "
    SELECT a.id, a.attendance_date, a.status, a.remarks, s.student_id_number, u.full_name, c.course_code, c.course_name
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON a.course_id = c.id
    $where_clause
    ORDER BY a.attendance_date DESC, a.id DESC
";

$records = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Exposure Demo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Attendance Exposure Demo</h1>
        <p>Attendance records visible without proper authorization checks</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="profile_idor.php">IDOR Profile</a>
        <a href="grade_tampering.php">Grade Tampering</a>
        <a href="news_vulnerable.php">SQLi Search</a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="alert alert-warning">
                <strong>Educational demo:</strong> this page exposes attendance information based only on URL parameters. It does not check whether the visitor is the student, teacher, or admin for the requested records.
            </div>

            <form method="GET">
                <div class="column-2">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" placeholder="Example: 1">
                    </div>
                    <div class="form-group">
                        <label for="course_id">Course ID</label>
                        <input type="text" id="course_id" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>" placeholder="Example: 1">
                    </div>
                </div>
                <button type="submit">Load Attendance</button>
            </form>

            <div class="vulnerability-box">
                <strong>Try this:</strong> request <code>?student_id=1</code>, then change it to <code>2</code> or <code>3</code>. If sample attendance exists, unrelated student records will appear.
            </div>

            <h3>Executed Query</h3>
            <div class="code-block">
                <code><?php echo htmlspecialchars($query); ?></code>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records && $records->num_rows > 0): ?>
                        <?php while ($record = $records->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['id']); ?></td>
                                <td><?php echo htmlspecialchars($record['full_name'] . ' (' . $record['student_id_number'] . ')'); ?></td>
                                <td><?php echo htmlspecialchars($record['course_code'] . ' - ' . $record['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                                <td><?php echo htmlspecialchars($record['status']); ?></td>
                                <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No attendance rows found for the current filter.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Attendance exposure demo</p>
    </footer>
</body>
</html>
