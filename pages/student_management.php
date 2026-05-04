<?php
/**
 * STUDENT MANAGEMENT PAGE
 * Manage student records
 */

include '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_secure.php');
    exit();
}

$message = '';
$action = $_GET['action'] ?? 'list';
$student_id = $_GET['id'] ?? '';

// Handle delete
if ($action == 'delete' && $student_id) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    if ($stmt->execute()) {
        $message = "✅ Student deleted successfully!";
    }
    $stmt->close();
    $action = 'list';
}

// Handle add/edit student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_student'])) {
    $user_id = $_POST['user_id'] ?? '';
    $student_id_number = $_POST['student_id_number'] ?? '';
    $class_name = $_POST['class_name'] ?? '';
    $roll_number = $_POST['roll_number'] ?? '';
    $father_name = $_POST['father_name'] ?? '';
    $father_phone = $_POST['father_phone'] ?? '';
    $mother_name = $_POST['mother_name'] ?? '';
    $student_id = $_POST['student_id'] ?? '';

    if (empty($user_id) || empty($student_id_number) || empty($class_name)) {
        $message = "❌ Required fields missing!";
    } else {
        if ($student_id) {
            // Update
            $stmt = $conn->prepare("UPDATE students SET student_id_number=?, class_name=?, roll_number=?, father_name=?, father_phone=?, mother_name=? WHERE id=?");
            $stmt->bind_param("ssisssi", $student_id_number, $class_name, $roll_number, $father_name, $father_phone, $mother_name, $student_id);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO students (user_id, student_id_number, class_name, roll_number, father_name, father_phone, mother_name, date_of_admission) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("issiiss", $user_id, $student_id_number, $class_name, $roll_number, $father_name, $father_phone, $mother_name);
        }
        if ($stmt->execute()) {
            $message = "✅ Student " . ($student_id ? 'updated' : 'added') . " successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
        $action = 'list';
    }
}

// Fetch student for editing
$edit_student = null;
if ($action == 'edit' && $student_id) {
    $stmt = $conn->prepare("SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $edit_student = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all students with user info
$students_list = array();
$result = $conn->query("SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON s.user_id = u.id ORDER BY s.class_name, s.roll_number");
if ($result) {
    while ($student = $result->fetch_assoc()) {
        $students_list[] = $student;
    }
}

// Get available users who are students
$available_users = $conn->query("SELECT id, full_name FROM users WHERE role = 'student' AND id NOT IN (SELECT user_id FROM students)");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>🎓 Student Management</h1>
        <p>Manage student records and profiles</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="user_management.php">Users</a>
        <a href="student_management.php">Students</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="card">
                <h2><?php echo $action == 'add' ? '➕ Add New Student' : '✏️ Edit Student'; ?></h2>

                <form method="POST">
                    <input type="hidden" name="save_student" value="1">
                    <?php if ($edit_student): ?>
                        <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $edit_student['user_id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="user_id">Student User:</label>
                        <select id="user_id" name="user_id" required <?php echo $edit_student ? 'disabled' : ''; ?>>
                            <option value="">Select a student user</option>
                            <?php
                            while ($user = $available_users->fetch_assoc()) {
                                $selected = ($edit_student && $edit_student['user_id'] == $user['id']) ? 'selected' : '';
                                echo "<option value='{$user['id']}' $selected>{$user['full_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="student_id_number">Student ID Number:</label>
                            <input type="text" id="student_id_number" name="student_id_number" required value="<?php echo htmlspecialchars($edit_student['student_id_number'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="class_name">Class:</label>
                            <input type="text" id="class_name" name="class_name" required value="<?php echo htmlspecialchars($edit_student['class_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="roll_number">Roll Number:</label>
                            <input type="number" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($edit_student['roll_number'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="father_phone">Father's Phone:</label>
                            <input type="tel" id="father_phone" name="father_phone" value="<?php echo htmlspecialchars($edit_student['father_phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="father_name">Father's Name:</label>
                            <input type="text" id="father_name" name="father_name" value="<?php echo htmlspecialchars($edit_student['father_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="mother_name">Mother's Name:</label>
                            <input type="text" id="mother_name" name="mother_name" value="<?php echo htmlspecialchars($edit_student['mother_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <button type="submit">✅ Save Student</button>
                        <a href="student_management.php" class="btn-secondary" style="padding: 0.75rem; text-align: center; text-decoration: none; border-radius: 5px; color: white;">❌ Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>All Students (<?php echo count($students_list); ?>)</h2>
                <a href="student_management.php?action=add" class="btn-success" style="display: inline-block; padding: 0.5rem 1rem; margin-bottom: 1rem; text-decoration: none; color: white; border-radius: 5px;">➕ Add New Student</a>

                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Class</th>
                            <th>Roll No.</th>
                            <th>Father's Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_list as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['class_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="student_management.php?action=edit&id=<?php echo $student['id']; ?>" style="color: #667eea; text-decoration: none; margin-right: 0.5rem;">Edit</a>
                                    <a href="student_management.php?action=delete&id=<?php echo $student['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #dc3545; text-decoration: none;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Student Management</p>
    </footer>
</body>
</html>
