<?php
/**
 * CONTENT MANAGEMENT PAGE
 * Manage news and announcements
 */

include '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_secure.php');
    exit();
}

$message = '';
$action = $_GET['action'] ?? 'list';
$news_id = $_GET['id'] ?? '';

// Delete news
if ($action == 'delete' && $news_id) {
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $news_id);
    if ($stmt->execute()) {
        $message = "✅ News deleted successfully!";
    }
    $stmt->close();
    $action = 'list';
}

// Save news
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_news'])) {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? '';
    $published_date = $_POST['published_date'] ?? date('Y-m-d');
    $news_id = $_POST['news_id'] ?? '';
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $message = "❌ Title and content are required!";
    } else {
        if ($news_id) {
            $stmt = $conn->prepare("UPDATE news SET title=?, content=?, category=?, published_date=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $content, $category, $published_date, $news_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO news (title, content, author_id, category, published_date, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssss", $title, $content, $author_id, $category, $published_date);
        }
        if ($stmt->execute()) {
            $message = "✅ News " . ($news_id ? 'updated' : 'added') . " successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
        $action = 'list';
    }
}

// Edit news
$edit_news = null;
if ($action == 'edit' && $news_id) {
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $edit_news = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all news
$news_list = $conn->query("SELECT * FROM news ORDER BY published_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>📰 Content Management</h1>
        <p>Manage news and announcements</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="user_management.php">Users</a>
        <a href="student_management.php">Students</a>
        <a href="content_management.php">Content</a>
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
                <h2><?php echo $action == 'add' ? '➕ Write New Article' : '✏️ Edit Article'; ?></h2>

                <form method="POST">
                    <input type="hidden" name="save_news" value="1">
                    <?php if ($edit_news): ?>
                        <input type="hidden" name="news_id" value="<?php echo $edit_news['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($edit_news['title'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($edit_news['content'] ?? ''); ?></textarea>
                    </div>

                    <div class="column-2">
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category">
                                <option value="General" <?php echo (($edit_news['category'] ?? '') == 'General') ? 'selected' : ''; ?>>General</option>
                                <option value="Events" <?php echo (($edit_news['category'] ?? '') == 'Events') ? 'selected' : ''; ?>>Events</option>
                                <option value="Academic" <?php echo (($edit_news['category'] ?? '') == 'Academic') ? 'selected' : ''; ?>>Academic</option>
                                <option value="Sports" <?php echo (($edit_news['category'] ?? '') == 'Sports') ? 'selected' : ''; ?>>Sports</option>
                                <option value="Infrastructure" <?php echo (($edit_news['category'] ?? '') == 'Infrastructure') ? 'selected' : ''; ?>>Infrastructure</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="published_date">Published Date:</label>
                            <input type="date" id="published_date" name="published_date" value="<?php echo htmlspecialchars($edit_news['published_date'] ?? date('Y-m-d')); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <button type="submit">✅ Publish</button>
                        <a href="content_management.php" class="btn-secondary" style="padding: 0.75rem; text-align: center; text-decoration: none; border-radius: 5px; color: white;">❌ Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <h2>All News & Articles</h2>
                <a href="content_management.php?action=add" class="btn-success" style="display: inline-block; padding: 0.5rem 1rem; margin-bottom: 1rem; text-decoration: none; color: white; border-radius: 5px;">➕ Write New Article</a>

                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Published Date</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($news = $news_list->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars(substr($news['title'], 0, 40)) . "</td>";
                            echo "<td>" . htmlspecialchars($news['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($news['published_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($news['views']) . "</td>";
                            echo "<td><span class='badge badge-" . ($news['is_active'] ? 'success' : 'danger') . "'>" . ($news['is_active'] ? 'Active' : 'Inactive') . "</span></td>";
                            echo "<td>";
                            echo "<a href='content_management.php?action=edit&id={$news['id']}' style='color: #667eea; text-decoration: none; margin-right: 0.5rem;'>Edit</a>";
                            echo "<a href='content_management.php?action=delete&id={$news['id']}' onclick='return confirm(\"Are you sure?\")' style='color: #dc3545; text-decoration: none;'>Delete</a>";
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
        <p>&copy; 2026 School Portal - Content Management</p>
    </footer>
</body>
</html>
