<?php
/**
 * USER MANAGEMENT PAGE
 * Manage users (create, edit, delete, view)
 */

include '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_secure.php');
    exit();
}

$message = '';
$action = $_GET['action'] ?? 'list';
$user_id = $_GET['id'] ?? '';

// Handle delete
if ($action == 'delete' && $user_id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
    $stmt->bind_param("ii", $user_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        $message = "✅ User deleted successfully!";
    }
    $stmt->close();
    $action = 'list';
}

// Handle add/edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_user'])) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    if (empty($username) || empty($email) || empty($full_name)) {
        $message = "❌ All fields are required!";
    } else {
        if ($user_id) {
            // Update existing user
            if (!empty($password)) {
                $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, role=?, phone=?, password=MD5(?) WHERE id=?");
                $stmt->bind_param("ssssssi", $username, $email, $full_name, $role, $phone, $password, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?, email=?, full_name=?, role=?, phone=? WHERE id=?");
                $stmt->bind_param("sssssi", $username, $email, $full_name, $role, $phone, $user_id);
            }
            if ($stmt->execute()) {
                $message = "✅ User updated successfully!";
            }
            $stmt->close();
        } else {
            // Add new user
            if (empty($password)) {
                $message = "❌ Password is required for new users!";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, email, full_name, role, phone, password) VALUES (?, ?, ?, ?, ?, MD5(?))");
                $stmt->bind_param("ssssss", $username, $email, $full_name, $role, $phone, $password);
                if ($stmt->execute()) {
                    $message = "✅ User added successfully!";
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $action = 'list';
    }
}

// Fetch user for editing
$edit_user = null;
if ($action == 'edit' && $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all users
$users_list = array();
$result = $conn->query("SELECT id, username, email, full_name, role, phone, created_at FROM users ORDER BY created_at DESC");
if ($result) {
    while ($user = $result->fetch_assoc()) {
        $users_list[] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>👥 User Management</h1>
        <p>Manage all system users</p>
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
                <h2><?php echo $action == 'add' ? '➕ Add New User' : '✏️ Edit User'; ?></h2>

                <form method="POST">
                    <input type="hidden" name="save_user" value="1">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                    <?php endif; ?>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($edit_user['full_name'] ?? ''); ?>">
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select id="role" name="role" required>
                                <option value="student" <?php echo (($edit_user['role'] ?? '') == 'student') ? 'selected' : ''; ?>>Student</option>
                                <option value="teacher" <?php echo (($edit_user['role'] ?? '') == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                                <option value="admin" <?php echo (($edit_user['role'] ?? '') == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="parent" <?php echo (($edit_user['role'] ?? '') == 'parent') ? 'selected' : ''; ?>>Parent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($edit_user['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password<?php echo $edit_user ? ' (leave blank to keep current)' : ' (required)'; ?>:</label>
                        <input type="password" id="password" name="password" <?php echo !$edit_user ? 'required' : ''; ?>>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <button type="submit">✅ Save User</button>
                        <a href="user_management.php" class="btn-secondary" style="padding: 0.75rem; text-align: center; text-decoration: none; border-radius: 5px; color: white;">❌ Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>All Users (<?php echo count($users_list); ?>)</h2>
                <a href="user_management.php?action=add" class="btn-success" style="display: inline-block; padding: 0.5rem 1rem; margin-bottom: 1rem; text-decoration: none; color: white; border-radius: 5px;">➕ Add New User</a>

                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users_list as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'teacher' ? 'warning' : 'success'); ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at']))); ?></td>
                                <td>
                                    <a href="user_management.php?action=edit&id=<?php echo $user['id']; ?>" style="color: #667eea; text-decoration: none; margin-right: 0.5rem;">Edit</a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="user_management.php?action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #dc3545; text-decoration: none;">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - User Management</p>
    </footer>
</body>
</html>
