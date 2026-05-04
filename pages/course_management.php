<?php
/**
 * COURSE MANAGEMENT PAGE
 * Manage courses
 */

include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_secure.php');
    exit();
}

$message = '';
$action = $_GET['action'] ?? 'list';
$course_id = $_GET['id'] ?? '';

// Delete course
if ($action == 'delete' && $course_id) {
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    if ($stmt->execute()) {
        $message = "✅ Course deleted successfully!";
    }
    $stmt->close();
    $action = 'list';
}

// Save course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_course'])) {
    $course_code = $_POST['course_code'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $department_id = $_POST['department_id'] ?? '';
    $credits = $_POST['credits'] ?? 3;
    $semester = $_POST['semester'] ?? 1;
    $course_id = $_POST['course_id'] ?? '';

    if (empty($course_code) || empty($course_name) || empty($department_id)) {
        $message = "❌ Required fields missing!";
    } else {
        if ($course_id) {
            $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_name=?, description=?, department_id=?, credits=?, semester=? WHERE id=?");
            $stmt->bind_param("sssiiii", $course_code, $course_name, $description, $department_id, $credits, $semester, $course_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, description, department_id, credits, semester, academic_year) VALUES (?, ?, ?, ?, ?, ?, '2025-26')");
            $stmt->bind_param("sssiii", $course_code, $course_name, $description, $department_id, $credits, $semester);
        }
        if ($stmt->execute()) {
            $message = "✅ Course " . ($course_id ? 'updated' : 'added') . " successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
        $action = 'list';
    }
}

// Edit course
$edit_course = null;
if ($action == 'edit' && $course_id) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $edit_course = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all courses
$courses_list = $conn->query("SELECT c.*, d.name as department_name FROM courses c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.course_code");

// Get departments
$departments = $conn->query("SELECT id, name FROM departments");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>📚 Course Management</h1>
        <p>Manage courses and curriculum</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="user_management.php">Users</a>
        <a href="student_management.php">Students</a>
        <a href="course_management.php">Courses</a>
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
                <h2><?php echo $action == 'add' ? '➕ Add New Course' : '✏️ Edit Course'; ?></h2>

                <form method="POST">
                    <input type="hidden" name="save_course" value="1">
                    <?php if ($edit_course): ?>
                        <input type="hidden" name="course_id" value="<?php echo $edit_course['id']; ?>">
                    <?php endif; ?>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="course_code">Course Code:</label>
                            <input type="text" id="course_code" name="course_code" required value="<?php echo htmlspecialchars($edit_course['course_code'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="course_name">Course Name:</label>
                            <input type="text" id="course_name" name="course_name" required value="<?php echo htmlspecialchars($edit_course['course_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($edit_course['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="department_id">Department:</label>
                            <select id="department_id" name="department_id" required>
                                <option value="">Select department</option>
                                <?php
                                while ($dept = $departments->fetch_assoc()) {
                                    $selected = ($edit_course && $edit_course['department_id'] == $dept['id']) ? 'selected' : '';
                                    echo "<option value='{$dept['id']}' $selected>{$dept['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="semester">Semester:</label>
                            <select id="semester" name="semester">
                                <option value="1" <?php echo (($edit_course['semester'] ?? '') == 1) ? 'selected' : ''; ?>>1st Semester</option>
                                <option value="2" <?php echo (($edit_course['semester'] ?? '') == 2) ? 'selected' : ''; ?>>2nd Semester</option>
                                <option value="3" <?php echo (($edit_course['semester'] ?? '') == 3) ? 'selected' : ''; ?>>3rd Semester</option>
                                <option value="4" <?php echo (($edit_course['semester'] ?? '') == 4) ? 'selected' : ''; ?>>4th Semester</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="credits">Credits:</label>
                        <input type="number" id="credits" name="credits" value="<?php echo htmlspecialchars($edit_course['credits'] ?? 3); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <button type="submit">✅ Save Course</button>
                        <a href="course_management.php" class="btn-secondary" style="padding: 0.75rem; text-align: center; text-decoration: none; border-radius: 5px; color: white;">❌ Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>All Courses</h2>
                <a href="course_management.php?action=add" class="btn-success" style="display: inline-block; padding: 0.5rem 1rem; margin-bottom: 1rem; text-decoration: none; color: white; border-radius: 5px;">➕ Add New Course</a>

                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Credits</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($course = $courses_list->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($course['course_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($course['course_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($course['department_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($course['semester']) . "</td>";
                            echo "<td>" . htmlspecialchars($course['credits']) . "</td>";
                            echo "<td>" . htmlspecialchars($course['enrolled_students']) . "</td>";
                            echo "<td>";
                            echo "<a href='course_management.php?action=edit&id={$course['id']}' style='color: #667eea; text-decoration: none; margin-right: 0.5rem;'>Edit</a>";
                            echo "<a href='course_management.php?action=delete&id={$course['id']}' onclick='return confirm(\"Are you sure?\")' style='color: #dc3545; text-decoration: none;'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Course Management</p>
    </footer>
</body>
</html>
