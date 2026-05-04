<?php
/**
 * VULNERABLE NEWS PAGE - SQL Injection via Search
 * Intentionally vulnerable search query for educational use.
 */

include '../includes/config.php';

$news_results = array();
$search_query = '';
$executed_query = '';
$query_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_query = $_POST['search'] ?? '';

    if ($search_query !== '') {
        // Vulnerable by design: user input is concatenated directly into SQL.
        $executed_query = "
            SELECT n.id, n.title, n.content, COALESCE(u.full_name, 'Unknown Author') AS author, n.published_date, n.views
            FROM news n
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.title LIKE '%$search_query%'
               OR n.content LIKE '%$search_query%'
               OR u.full_name LIKE '%$search_query%'
            ORDER BY n.published_date DESC
        ";

        try {
            $result = $conn->query($executed_query);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $news_results[] = $row;
                }
            }
        } catch (mysqli_sql_exception $e) {
            $query_error = $e->getMessage();
        }
    }
}

$all_news = array();
$all_news_query = "
    SELECT n.id, n.title, n.content, COALESCE(u.full_name, 'Unknown Author') AS author, n.published_date, n.views
    FROM news n
    LEFT JOIN users u ON n.author_id = u.id
    ORDER BY n.published_date DESC
";

try {
    $all_news_result = $conn->query($all_news_query);
    if ($all_news_result) {
        while ($news = $all_news_result->fetch_assoc()) {
            $all_news[] = $news;
        }
    }
} catch (mysqli_sql_exception $e) {
    $query_error = $query_error ?: $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News (Vulnerable) - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>School News & Updates (Vulnerable)</h1>
        <p>SQL Injection vulnerability in the search feature</p>
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
            <h2>Search News - Vulnerable to SQL Injection</h2>

            <div class="vulnerability-box">
                <strong>Educational warning:</strong><br>
                This page intentionally concatenates the search input directly into SQL. It is meant to demonstrate SQL injection, not prevent it.
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="search">Search News</label>
                    <input type="text" id="search" name="search" placeholder="Enter keyword or SQL injection payload" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
                    <small style="color: #666; margin-top: 0.5rem; display: block;">
                        Try: <code>' OR '1'='1' -- </code> or <code>' UNION SELECT 1,2,3,4,5,6 -- </code>
                    </small>
                </div>
                <button type="submit">Search</button>
            </form>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

            <?php if ($executed_query): ?>
                <h3>Executed Query</h3>
                <div class="code-block">
                    <code><?php echo htmlspecialchars($executed_query, ENT_QUOTES, 'UTF-8'); ?></code>
                </div>
            <?php endif; ?>

            <?php if ($query_error): ?>
                <div class="alert alert-danger">
                    SQL Error: <?php echo htmlspecialchars($query_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($search_query !== '' && !$query_error && empty($news_results)): ?>
                <div class="alert alert-info">
                    No results found for your search. Try a payload like <code>' OR '1'='1' -- </code>.
                </div>
            <?php endif; ?>

            <?php if (!empty($news_results)): ?>
                <h3>Search Results (<?php echo count($news_results); ?> found)</h3>
                <?php foreach ($news_results as $news): ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea;">
                        <h4 style="color: #667eea; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p style="color: #666; margin-bottom: 0.5rem;">
                            <small>
                                By <strong><?php echo htmlspecialchars($news['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                on <?php echo htmlspecialchars($news['published_date'], ENT_QUOTES, 'UTF-8'); ?>
                                | Views: <?php echo htmlspecialchars($news['views'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </p>
                        <p><?php echo htmlspecialchars(substr($news['content'], 0, 200), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <h3>All News</h3>
                <?php foreach ($all_news as $news): ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea;">
                        <h4 style="color: #667eea; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p style="color: #666; margin-bottom: 0.5rem;">
                            <small>
                                By <strong><?php echo htmlspecialchars($news['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                on <?php echo htmlspecialchars($news['published_date'], ENT_QUOTES, 'UTF-8'); ?>
                                | Views: <?php echo htmlspecialchars($news['views'], ENT_QUOTES, 'UTF-8'); ?>
                            </small>
                        </p>
                        <p><?php echo htmlspecialchars(substr($news['content'], 0, 200), ENT_QUOTES, 'UTF-8'); ?>...</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

            <h3>Exploitation Examples</h3>

            <div class="vulnerability-box">
                <strong>Payload 1 - return many rows:</strong>
                <div class="code-block">
                    <code>' OR '1'='1' -- </code>
                </div>
            </div>

            <div class="vulnerability-box">
                <strong>Payload 2 - UNION extraction attempt:</strong>
                <div class="code-block">
                    <code>' UNION SELECT 1, username, password, email, created_at, 0 FROM users -- </code>
                </div>
            </div>

            <div class="vulnerability-box">
                <strong>Payload 3 - time-based testing:</strong>
                <div class="code-block">
                    <code>' OR SLEEP(5) -- </code>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
