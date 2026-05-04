<?php
/**
 * SECURE ARTICLES PAGE - SQL Injection Protected
 * Uses prepared statements and schema-compatible joins.
 */

include '../includes/config.php';

$articles_results = array();
$search_query = '';
$executed_query = '';
$query_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_query = $_POST['search'] ?? '';

    if ($search_query !== '') {
        if (strlen($search_query) > 100) {
            $search_query = substr($search_query, 0, 100);
        }

        $search_param = '%' . $search_query . '%';

        $stmt = $conn->prepare("
            SELECT a.id, a.title, a.content, COALESCE(u.full_name, 'Unknown Author') AS author, a.published_date, a.views
            FROM articles a
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.title LIKE ? OR a.content LIKE ? OR u.full_name LIKE ?
            ORDER BY a.published_date DESC
        ");

        if ($stmt) {
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $articles_results[] = $row;
                }

                $executed_query = "SELECT a.id, a.title, a.content, author_name, a.published_date, a.views FROM articles ... WHERE title/content/author match prepared parameters";
            } else {
                $query_error = $stmt->error;
            }

            $stmt->close();
        } else {
            $query_error = $conn->error;
        }
    }
}

$all_articles = array();
$all_articles_query = "
    SELECT a.id, a.title, a.content, COALESCE(u.full_name, 'Unknown Author') AS author, a.published_date, a.views
    FROM articles a
    LEFT JOIN users u ON a.author_id = u.id
    ORDER BY a.published_date DESC
";

$all_articles_result = $conn->query($all_articles_query);
if ($all_articles_result) {
    while ($article = $all_articles_result->fetch_assoc()) {
        $all_articles[] = $article;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles (Secure) - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>School Articles & Resources (Secure)</h1>
        <p>Protected against SQL injection with prepared statements</p>
    </header>

    <nav>
        <a href="../index.php">Home</a>
        <a href="login_vulnerable.php">Login (Vulnerable)</a>
        <a href="login_secure.php">Login (Secure)</a>
        <a href="news_vulnerable.php">News (Vulnerable)</a>
        <a href="articles_secure.php">Articles (Secure)</a>
        <a href="xss_vulnerable.php">Comments (XSS Demo)</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Search Articles - Secured with Prepared Statements</h2>

            <div class="security-box">
                <strong>Security features:</strong><br>
                This page uses prepared statements and keeps user input separate from SQL code.
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="search">Search Articles</label>
                    <input type="text" id="search" name="search" placeholder="Enter keywords or try payloads safely" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit">Search</button>
            </form>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

            <?php if ($executed_query): ?>
                <h3>Query Pattern</h3>
                <div class="code-block">
                    <code><?php echo htmlspecialchars($executed_query, ENT_QUOTES, 'UTF-8'); ?></code>
                </div>
            <?php endif; ?>

            <?php if ($query_error): ?>
                <div class="alert alert-danger">
                    Database error: <?php echo htmlspecialchars($query_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($search_query !== ''): ?>
                <h3>Search Results</h3>
                <?php if (!empty($articles_results)): ?>
                    <?php foreach ($articles_results as $article): ?>
                        <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #28a745;">
                            <h4 style="color: #28a745; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <p style="color: #666; margin-bottom: 0.5rem;">
                                <small>
                                    By <strong><?php echo htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    on <?php echo htmlspecialchars($article['published_date'], ENT_QUOTES, 'UTF-8'); ?>
                                    | Views: <?php echo htmlspecialchars($article['views'], ENT_QUOTES, 'UTF-8'); ?>
                                </small>
                            </p>
                            <p><?php echo htmlspecialchars(substr($article['content'], 0, 200), ENT_QUOTES, 'UTF-8'); ?>...</p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">No articles found for that search.</div>
                <?php endif; ?>
            <?php else: ?>
                <h3>All Articles</h3>
                <?php foreach ($all_articles as $article): ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #28a745;">
                        <h4 style="color: #28a745; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p style="color: #666; margin-bottom: 0.5rem;">
                            <small>
                                By <strong><?php echo htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                on <?php echo htmlspecialchars($article['published_date'], ENT_QUOTES, 'UTF-8'); ?>
                                | Views: <?php echo htmlspecialchars($article['views'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </p>
                        <p><?php echo htmlspecialchars(substr($article['content'], 0, 200), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
