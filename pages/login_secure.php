<?php
/**
 * SECURE LOGIN PAGE - Proper Implementation
 * Uses prepared statements to prevent SQL Injection.
 */

include '../includes/config.php';

$login_attempted = false;
$login_success = false;
$error_message = '';

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

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error_message = 'Invalid username format';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");

        if ($stmt) {
            $stmt->bind_param("s", $username);

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    if (md5($password) === $user['password']) {
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
                        $error_message = 'Invalid credentials';
                    }
                } else {
                    $error_message = 'Invalid credentials';
                }
            } else {
                $error_message = 'Database error: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = 'Database error: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login (Secure) - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>School Portal - Login (Secure)</h1>
        <p>Properly secured with prepared statements</p>
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
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <h2>Login - Secured with Prepared Statements</h2>

                <?php if ($login_attempted): ?>
                    <div class="alert <?php echo $login_success ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="security-box">
                    <strong>Security features:</strong><br>
                    Successful login goes to the correct dashboard. Failed login stays on this page.
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required placeholder="Enter username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter password">
                    </div>

                    <button type="submit">Login (Secure)</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
