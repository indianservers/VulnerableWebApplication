<?php
/**
 * GRADE TAMPERING DEMO
 * Unsafe direct update of grade records without authorization checks.
 */

include '../includes/config.php';

$message = '';
$executed_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_id = $_POST['grade_id'] ?? '';
    $total_marks = $_POST['total_marks'] ?? '';
    $grade_letter = $_POST['grade_letter'] ?? '';

    $executed_query = "UPDATE grades SET total_marks = '$total_marks', grade_letter = '$grade_letter' WHERE id = $grade_id";
    if ($conn->query($executed_query)) {
        $message = 'Grade record updated. This demonstrates insecure tampering because no permission check is enforced.';
    } else {
        $message = 'Update failed: ' . $conn->error;
    }
}

$grades = $conn->query("
    SELECT g.id, u.full_name, c.course_code, c.course_name, g.total_marks, g.grade_letter
    FROM grades g
    JOIN students s ON g.student_id = s.id
    JOIN users u ON s.user_id = u.id
    JOIN courses c ON g.course_id = c.id
    ORDER BY g.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Tampering Demo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Grade Tampering Demo</h1>
        <p>Unsafe direct update workflow for marks and grade letters</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="profile_idor.php">IDOR Profile</a>
        <a href="attendance_bypass.php">Attendance Exposure</a>
        <a href="login_secure.php">Secure Login</a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="alert alert-warning">
                <strong>Educational demo:</strong> anyone who can reach this form can change grade records by guessing a grade ID. No role validation, record ownership check, or server-side restriction exists.
            </div>

            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'failed') === false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="column-2">
                    <div class="form-group">
                        <label for="grade_id">Grade Record ID</label>
                        <input type="text" id="grade_id" name="grade_id" placeholder="Example: 1" required>
                    </div>
                    <div class="form-group">
                        <label for="total_marks">Total Marks</label>
                        <input type="text" id="total_marks" name="total_marks" placeholder="Example: 99.9" required>
                    </div>
                </div>

                <div class="column-2">
                    <div class="form-group">
                        <label for="grade_letter">Grade Letter</label>
                        <input type="text" id="grade_letter" name="grade_letter" placeholder="Example: A+" required>
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button type="submit">Update Grade</button>
                    </div>
                </div>
            </form>

            <div class="vulnerability-box">
                <strong>Try this:</strong> pick any row below, change the record ID, and overwrite marks or grade letters directly. This simulates insecure parameter tampering and broken authorization.
            </div>

            <?php if ($executed_query): ?>
                <h3>Executed Update</h3>
                <div class="code-block">
                    <code><?php echo htmlspecialchars($executed_query); ?></code>
                </div>
            <?php endif; ?>

            <h3>Available Grade Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>Grade ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Total Marks</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($grade = $grades->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['id']); ?></td>
                            <td><?php echo htmlspecialchars($grade['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['course_code'] . ' - ' . $grade['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['total_marks']); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade_letter']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Grade tampering demo</p>
    </footer>
</body>
</html>
