<?php
/**
 * NEWS DETAILS PAGE
 * Intentionally vulnerable GET-based lookup for educational testing.
 */

include '../includes/config.php';

$id = $_GET['id'] ?? '1';
$category = $_GET['category'] ?? '';
$results = array();
$executed_query = '';
$query_error = '';

$conditions = array();
$conditions[] = "n.id = $id";

if ($category !== '') {
    $conditions[] = "n.category = '$category'";
}

$where_clause = implode(' AND ', $conditions);
$executed_query = "
    SELECT n.id, n.title, n.content, COALESCE(u.full_name, 'Unknown Author') AS author, n.category, n.published_date, n.views
    FROM news n
    LEFT JOIN users u ON n.author_id = u.id
    WHERE $where_clause
";

try {
    $result = $conn->query($executed_query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
} catch (mysqli_sql_exception $e) {
    $query_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Details - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>News Details</h1>
        <p>Article lookup by URL parameters</p>
    </header>

    <nav>
        <a href="../index.php">Home</a>
        <a href="news_details.php">News Details</a>
        <a href="login_vulnerable.php">Login (Vulnerable)</a>
        <a href="news_vulnerable.php">News Search</a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="vulnerability-box">
                <strong>Educational warning:</strong><br>
                This page uses GET parameters directly in SQL without validation or prepared statements.
            </div>

            <h2>Example URLs</h2>
            <div class="code-block">
                <code>/pages/news_details.php?id=1</code>
            </div>
            <div class="code-block">
                <code>/pages/news_details.php?id=1&amp;category=Events</code>
            </div>

            <h3>Executed Query</h3>
            <div class="code-block">
                <code><?php echo htmlspecialchars($executed_query, ENT_QUOTES, 'UTF-8'); ?></code>
            </div>

            <?php if ($query_error): ?>
                <div class="alert alert-danger">
                    SQL Error: <?php echo htmlspecialchars($query_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <h3>Results</h3>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $row): ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea;">
                        <h4 style="color: #667eea; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p style="color: #666; margin-bottom: 0.5rem;">
                            <small>
                                By <strong><?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                | Category: <?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?>
                                | Date: <?php echo htmlspecialchars($row['published_date'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </p>
                        <p><?php echo htmlspecialchars(substr($row['content'], 0, 220), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No rows returned for the current parameters.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
