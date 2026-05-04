<?php
/**
 * ADMIN DASHBOARD
 * Main admin control panel
 */

include '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_secure.php');
    exit();
}

$flash_welcome = $_SESSION['flash_welcome'] ?? '';
unset($_SESSION['flash_welcome']);

// Get dashboard statistics
$stats = array();

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total students
$result = $conn->query("SELECT COUNT(*) as count FROM students");
$stats['total_students'] = $result->fetch_assoc()['count'];

// Total courses
$result = $conn->query("SELECT COUNT(*) as count FROM courses");
$stats['total_courses'] = $result->fetch_assoc()['count'];

// Active announcements
$result = $conn->query("SELECT COUNT(*) as count FROM announcements WHERE is_active = 1 AND expiry_date IS NULL");
$stats['active_announcements'] = $result->fetch_assoc()['count'];

// Recent news
$recent_news = $conn->query("SELECT id, title, published_date FROM news ORDER BY published_date DESC LIMIT 5");

// Recent users
$recent_users = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .admin-nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .admin-nav a {
            display: block;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            text-align: center;
            transition: 0.3s;
            border-left: 4px solid;
        }
        .admin-nav a.users {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-left-color: #f5576c;
        }
        .admin-nav a.students {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-left-color: #00f2fe;
        }
        .admin-nav a.courses {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border-left-color: #38f9d7;
        }
        .admin-nav a.content {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border-left-color: #fee140;
        }
        .admin-nav a:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .recent-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>🎓 Admin Dashboard</h1>
        <p>School Portal Administration Panel</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="user_management.php">Users</a>
        <a href="student_management.php">Students</a>
        <a href="course_management.php">Courses</a>
        <a href="content_management.php">Content</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="card">
            <?php if ($flash_welcome): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($flash_welcome, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <h2>Welcome Administrator</h2>
            <p>Here's an overview of your school portal statistics.</p>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_students']; ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['total_courses']; ?></h3>
                    <p>Total Courses</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['active_announcements']; ?></h3>
                    <p>Active Announcements</p>
                </div>
            </div>

            <h3 style="margin-top: 2rem;">Quick Actions</h3>
            <div class="admin-nav">
                <a href="user_management.php" class="users">👥 Manage Users</a>
                <a href="student_management.php" class="students">🎓 Manage Students</a>
                <a href="course_management.php" class="courses">📚 Manage Courses</a>
                <a href="content_management.php" class="content">📰 Manage Content</a>
            </div>

            <div class="column-2">
                <div class="recent-section">
                    <h3>Recent News</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        if ($recent_news && $recent_news->num_rows > 0) {
                            while ($news = $recent_news->fetch_assoc()) {
                                echo "<li style='padding: 0.5rem 0; border-bottom: 1px solid #ddd;'>";
                                echo "<strong>" . htmlspecialchars($news['title']) . "</strong>";
                                echo "<br><small style='color: #999;'>" . htmlspecialchars($news['published_date']) . "</small>";
                                echo "</li>";
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="recent-section">
                    <h3>Recent Users</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        if ($recent_users && $recent_users->num_rows > 0) {
                            while ($user = $recent_users->fetch_assoc()) {
                                echo "<li style='padding: 0.5rem 0; border-bottom: 1px solid #ddd;'>";
                                echo "<strong>" . htmlspecialchars($user['username']) . "</strong>";
                                echo "<br><small style='color: #999;'>" . htmlspecialchars($user['role']) . " - " . htmlspecialchars($user['created_at']) . "</small>";
                                echo "</li>";
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Admin Panel</p>
    </footer>
</body>
</html>
