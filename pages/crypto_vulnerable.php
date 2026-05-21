<?php
/**
 * VULNERABLE CRYPTOGRAPHIC FAILURES PAGE - OWASP A02
 * Intentionally weak cryptography and sensitive data handling for education.
 */

include '../includes/config.php';
require_once '../includes/lab_ui.php';

if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', 'secret'); // [VULN] Weak, guessable signing key
}

$password = $_POST['password'] ?? 'student123';
$student_ssn = $_REQUEST['ssn'] ?? '123-45-6789';
$student_id = $_GET['student_id'] ?? '3';

$password = is_array($password) ? 'student123' : (string) $password;
$student_ssn = is_array($student_ssn) ? '123-45-6789' : (string) $student_ssn;
$student_id = is_array($student_id) ? '3' : (string) $student_id;

// Scenario 1: MD5 password hashing (crackable in seconds)
$hashed = md5($password); // [VULN] MD5 is not a password hash

// Scenario 2: Base64 mistaken for encryption
$encrypted = base64_encode($student_ssn); // [VULN] Base64 is encoding, not encryption
$decoded_ssn = base64_decode($encrypted);

$report_url = 'crypto_vulnerable.php?student_id=' . urlencode((string) $student_id) . '&ssn=' . urlencode((string) $student_ssn);
$fake_jwt = base64_encode('{"alg":"HS256","typ":"JWT"}') . '.' . base64_encode('{"sub":"student-' . $student_id . '","role":"student"}') . '.signed-with-' . JWT_SECRET;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('A02 Cryptographic Failures'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">OWASP A02 Demo</div>
            <h1>Cryptographic Failures</h1>
            <p>Weak hashes, reversible encoding, URL-exposed PII, and hardcoded secrets in one intentionally vulnerable school portal lab.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-hero" style="--lab-color:#b86400">
                <div>
                    <div class="lab-kicker">Intentional Vulnerability Simulator</div>
                    <h1>A02: Cryptographic Failures</h1>
                    <p>This page demonstrates mistakes that look like protection but still expose passwords, student PII, and signing secrets.</p>
                    <div class="lab-badges"><span>High</span><span>Beginner</span><span>OWASP Top 10</span></div>
                </div>
                <div class="lab-score-card">
                    <strong>Demo Inputs</strong>
                    <p>Password: <code><?php echo e($password); ?></code></p>
                    <p>Student SSN: <code><?php echo e($student_ssn); ?></code></p>
                </div>
            </section>

            <section class="lab-panel attack-card">
                <div>
                    <span class="section-kicker">Try It</span>
                    <h2>Generate weak hashes and exposed report URLs</h2>
                    <p>Change the values below and watch how the page displays crackable hashes, reversible Base64 output, and sensitive GET parameters.</p>
                </div>
                <form method="POST" class="crypto-form">
                    <div class="form-group">
                        <label for="password">Password to hash with MD5</label>
                        <input type="text" id="password" name="password" value="<?php echo e($password); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ssn">Student SSN to Base64 encode</label>
                        <input type="text" id="ssn" name="ssn" value="<?php echo e($student_ssn); ?>">
                    </div>
                    <button type="submit">Run Vulnerable Crypto Demo</button>
                </form>
            </section>

            <section class="lab-panel">
                <h2>Scenario 1: MD5 Password Hashing</h2>
                <div class="vulnerability-box">
                    <strong>Vulnerable behavior:</strong> MD5 is fast and unsalted, so common passwords can be cracked almost instantly with commodity tools.
                </div>
                <div class="split-compare">
                    <div>
                        <h3>Input Password</h3>
                        <div class="code-block"><code><?php echo e($password); ?></code></div>
                    </div>
                    <div>
                        <h3>Stored Hash</h3>
                        <div class="code-block"><code><?php echo e($hashed); ?></code></div>
                    </div>
                </div>
                <div class="code-block"><code>$hashed = md5($password); // [VULN] MD5 is not a password hash</code></div>
            </section>

            <section class="lab-panel">
                <h2>Scenario 2: Base64 Mistaken for Encryption</h2>
                <div class="vulnerability-box">
                    <strong>Vulnerable behavior:</strong> Base64 is only encoding. Anyone can decode it back to the original student SSN.
                </div>
                <div class="split-compare">
                    <div>
                        <h3>Encoded Value</h3>
                        <div class="code-block"><code><?php echo e($encrypted); ?></code></div>
                    </div>
                    <div>
                        <h3>Decoded Value</h3>
                        <div class="code-block"><code><?php echo e($decoded_ssn); ?></code></div>
                    </div>
                </div>
                <div class="code-block"><code>$encrypted = base64_encode($student_ssn); // [VULN] Base64 is encoding, not encryption</code></div>
            </section>

            <section class="lab-panel">
                <h2>Scenario 3: Sensitive Data in URL</h2>
                <div class="vulnerability-box">
                    <strong>Vulnerable behavior:</strong> PII in query strings can land in browser history, proxy logs, web server logs, analytics, and referrer headers.
                </div>
                <p>Example vulnerable report link:</p>
                <div class="code-block"><code>http://school.local/report.php?student_id=3&amp;ssn=123-45-6789</code></div>
                <a class="btn-primary" href="<?php echo e($report_url); ?>">Open URL with PII in GET Parameters</a>
                <?php if (isset($_GET['ssn'])): ?>
                    <div class="alert alert-warning" style="margin-top: 1rem;">
                        Current URL includes SSN <code><?php echo e($student_ssn); ?></code>. A real server could log this exact request.
                    </div>
                <?php endif; ?>
            </section>

            <section class="lab-panel">
                <h2>Scenario 4: Hardcoded Secret Key</h2>
                <div class="vulnerability-box">
                    <strong>Vulnerable behavior:</strong> A hardcoded JWT key like <code>secret</code> is guessable, reusable across environments, and often leaked through source control.
                </div>
                <div class="split-compare">
                    <div>
                        <h3>Hardcoded Key</h3>
                        <div class="code-block"><code><?php echo e(JWT_SECRET); ?></code></div>
                    </div>
                    <div>
                        <h3>Mock Token</h3>
                        <div class="code-block"><code><?php echo e($fake_jwt); ?></code></div>
                    </div>
                </div>
                <div class="code-block"><code>define('JWT_SECRET', 'secret'); // [VULN] Weak, guessable signing key</code></div>
            </section>

            <?php renderRequestInspector('pages/crypto_vulnerable.php'); ?>
        </div>

        <div class="lab-side">
            <aside class="payload-drawer">
                <h3>A02 Checks</h3>
                <button type="button" class="payload-chip" data-copy="md5(student123)">md5(student123)</button>
                <button type="button" class="payload-chip" data-copy="base64_decode(encoded_ssn)">base64_decode(encoded_ssn)</button>
                <button type="button" class="payload-chip" data-copy="?student_id=3&ssn=123-45-6789">?student_id=3&amp;ssn=123-45-6789</button>
                <button type="button" class="payload-chip" data-copy="JWT_SECRET = secret">JWT_SECRET = secret</button>
            </aside>

            <section class="lab-panel notebook-panel">
                <h2>Secure Pattern</h2>
                <div class="code-block"><code>password_hash($password, PASSWORD_DEFAULT)</code></div>
                <div class="code-block"><code>password_verify($password, $storedHash)</code></div>
                <div class="code-block"><code>Store secrets in environment variables</code></div>
                <p class="muted">Do not send PII in URLs. Use server-side identifiers, authorization checks, and encrypted storage where data protection requires it.</p>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | A02 cryptographic failures lab</p>
    </footer>
</body>
</html>
