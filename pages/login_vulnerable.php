<?php
/**
 * VULNERABLE LOGIN PAGE - SQL Injection Demonstration
 * Intentionally unsafe query construction for educational use.
 */

include '../includes/config.php';

$login_attempted = false;
$login_success = false;
$error_message = '';
$query = '';

function getRoleRedirect($role) {
    if ($role === 'admin') {
        return 'admin_dashboard.php';
    }
    if ($role === 'teacher') {
        return 'teacher_dashboard.php';
    }
    if ($role === 'student') {
        return 'student_dashboard.php';
    }
    return '../index.php';
}

function getWelcomeLabel($user) {
    if (($user['role'] ?? '') === 'admin') {
        return 'Administrator';
    }
    return $user['full_name'] ?? $user['username'] ?? 'User';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_attempted = true;

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hash = md5($password);

    // Intentionally unsafe: both values are interpolated directly into SQL.
    // Using the precomputed hash keeps normal logins working while making auth bypass payloads easier to demonstrate.
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password_hash'";
    $_SESSION['last_query'] = $query;

    try {
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'] ?? '';
            $_SESSION['flash_welcome'] = 'Welcome ' . getWelcomeLabel($user);
            $login_success = true;
            $error_message = 'Login Successful as ' . htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8');
            header('Location: ' . getRoleRedirect($user['role']));
            exit();
        } else {
            $error_message = 'Login Failed - Invalid credentials';
        }
    } catch (mysqli_sql_exception $e) {
        $error_message = 'SQL Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login (Vulnerable) - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>School Portal - Login (Vulnerable)</h1>
        <p>SQL Injection vulnerability demonstration</p>
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
        <div style="max-width: 680px; margin: 0 auto;">
            <div class="card">
                <h2>Login - Vulnerable to SQL Injection</h2>

                <?php if ($login_attempted): ?>
                    <div class="alert <?php echo $login_success ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="vulnerability-box">
                    <strong>Educational warning:</strong><br>
                    This page intentionally concatenates user input into SQL. Invalid payloads may trigger SQL errors, which are now shown on the page instead of crashing PHP.
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required placeholder="Enter username or SQL payload">
                        <small style="color: #666; margin-top: 0.5rem; display: block;">
                            Try: <code>admin' -- </code> or <code>' OR '1'='1' -- </code>
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter password">
                    </div>

                    <button type="submit">Login (Vulnerable)</button>
                </form>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <h3>Working Test Credentials</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>admin</code></td>
                            <td><code>admin123</code></td>
                        </tr>
                        <tr>
                            <td><code>teacher_science</code></td>
                            <td><code>teacher123</code></td>
                        </tr>
                        <tr>
                            <td><code>student1</code></td>
                            <td><code>student123</code></td>
                        </tr>
                    </tbody>
                </table>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <h3>Suggested Payloads</h3>

                <div class="vulnerability-box">
                    <strong>Payload 1 - auth bypass:</strong>
                    <div class="code-block">
                        <code>admin' -- </code>
                    </div>
                </div>

                <div class="vulnerability-box">
                    <strong>Payload 2 - OR-based bypass:</strong>
                    <div class="code-block">
                        <code>' OR '1'='1' -- </code>
                    </div>
                </div>

                <div class="vulnerability-box">
                    <strong>Note:</strong> malformed payloads can produce SQL syntax errors. That is expected on an intentionally vulnerable page.
                </div>

                <?php if ($login_attempted && isset($_SESSION['last_query'])): ?>
                    <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">
                    <h3>Generated SQL Query</h3>
                    <div class="code-block">
                        <code><?php echo htmlspecialchars($_SESSION['last_query'], ENT_QUOTES, 'UTF-8'); ?></code>
                    </div>
                <?php endif; ?>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                <div class="security-box">
                    <strong>Compare with the secure version</strong>
                    <p style="margin-top: 0.5rem;">Use prepared statements on the secure page to see the same workflow without injection risk.</p>
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <p><a href="login_secure.php" style="color: #0f5ea6; font-weight: 700; text-decoration: none;">View the secure version</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
