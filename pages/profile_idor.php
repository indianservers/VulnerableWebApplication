<?php
/**
 * BROKEN ACCESS CONTROL / IDOR DEMO
 * View student profile data by directly changing the student_id parameter.
 */

include '../includes/config.php';

$student_id = $_GET['student_id'] ?? '1';
$query = "
    SELECT s.*, u.full_name, u.email, u.phone, u.address, u.date_of_birth
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE s.id = $student_id
";

$result = $conn->query($query);
$student = $result ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile IDOR Demo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Student Profile IDOR Demo</h1>
        <p>Broken access control through direct student record lookup</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="login_vulnerable.php">Vulnerable Login</a>
        <a href="grade_tampering.php">Grade Tampering</a>
        <a href="attendance_bypass.php">Attendance Exposure</a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="alert alert-warning">
                <strong>Educational demo:</strong> this page trusts the <code>student_id</code> in the URL and exposes personal student data without validating ownership or role.
            </div>

            <form method="GET">
                <div class="column-2">
                    <div class="form-group">
                        <label for="student_id">Student Record ID</label>
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button type="submit">Load Profile</button>
                    </div>
                </div>
            </form>

            <div class="vulnerability-box">
                <strong>Try this:</strong> change <code>?student_id=1</code> to <code>?student_id=2</code>, <code>3</code>, or <code>4</code> and observe how unrelated student records become visible.
            </div>

            <h3>Executed Query</h3>
            <div class="code-block">
                <code><?php echo htmlspecialchars($query); ?></code>
            </div>

            <?php if ($student): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Full Name</td><td><?php echo htmlspecialchars($student['full_name']); ?></td></tr>
                        <tr><td>Email</td><td><?php echo htmlspecialchars($student['email']); ?></td></tr>
                        <tr><td>Phone</td><td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td></tr>
                        <tr><td>Date of Birth</td><td><?php echo htmlspecialchars($student['date_of_birth'] ?? 'N/A'); ?></td></tr>
                        <tr><td>Student ID Number</td><td><?php echo htmlspecialchars($student['student_id_number']); ?></td></tr>
                        <tr><td>Class</td><td><?php echo htmlspecialchars($student['class_name']); ?></td></tr>
                        <tr><td>Roll Number</td><td><?php echo htmlspecialchars($student['roll_number']); ?></td></tr>
                        <tr><td>Father Name</td><td><?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></td></tr>
                        <tr><td>Father Phone</td><td><?php echo htmlspecialchars($student['father_phone'] ?? 'N/A'); ?></td></tr>
                        <tr><td>Mother Name</td><td><?php echo htmlspecialchars($student['mother_name'] ?? 'N/A'); ?></td></tr>
                        <tr><td>Blood Group</td><td><?php echo htmlspecialchars($student['blood_group'] ?? 'N/A'); ?></td></tr>
                        <tr><td>GPA</td><td><?php echo htmlspecialchars($student['gpa']); ?></td></tr>
                        <tr><td>Status</td><td><?php echo htmlspecialchars($student['status']); ?></td></tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-danger">No student record found for that identifier.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - IDOR demo</p>
    </footer>
</body>
</html>
