<?php
/**
 * OWASP lab metadata for the intentionally vulnerable simulator.
 */

function labCategories(): array {
    return array(
        'A01' => array(
            'title' => 'Broken Access Control',
            'risk' => 'Critical',
            'difficulty' => 'Beginner',
            'color' => '#d94841',
            'icon' => 'lock-open',
            'target' => 'pages/profile_idor.php?student_id=1',
            'secure_target' => 'pages/student_dashboard.php',
            'objective' => 'Access student, attendance, and grade records that should belong to another user.',
            'scenario' => 'A student portal exposes direct identifiers in URLs and trusts the visitor to request only their own data.',
            'payloads' => array('?student_id=2', '?student_id=3', '?course_id=1', 'grade_id=1&total_marks=99.9&grade_letter=A+'),
            'evidence' => 'Capture the exposed full name, student ID, attendance row, or changed grade.',
            'code' => 'WHERE s.id = $student_id',
            'remediation' => 'Validate ownership and role on the server before returning or changing records.'
        ),
        'A02' => array(
            'title' => 'Cryptographic Failures',
            'risk' => 'High',
            'difficulty' => 'Beginner',
            'color' => '#b86400',
            'icon' => 'key',
            'target' => 'pages/lab_scenario.php?cat=A02',
            'secure_target' => 'pages/login_secure.php',
            'objective' => 'Identify weak password hashing and exposed sensitive student fields.',
            'scenario' => 'The sample database stores passwords with MD5 and includes sensitive personal data in ordinary profile views.',
            'payloads' => array('admin hash lookup', 'student PII exposure', 'compare MD5 length', 'offline crack discussion'),
            'evidence' => 'Capture an MD5 hash or sensitive field that should have stronger protection.',
            'code' => "MD5('admin123')",
            'remediation' => 'Use password_hash(), password_verify(), careful data minimization, and encryption where appropriate.'
        ),
        'A03' => array(
            'title' => 'Injection',
            'risk' => 'Critical',
            'difficulty' => 'Beginner',
            'color' => '#c0325a',
            'icon' => 'database-zap',
            'target' => 'pages/login_vulnerable.php',
            'secure_target' => 'pages/login_secure.php',
            'objective' => 'Bypass authentication or extract rows by injecting SQL into form fields.',
            'scenario' => 'Login and search screens concatenate user input directly into SQL queries.',
            'payloads' => array("admin' -- ", "' OR '1'='1' -- ", "' UNION SELECT 1, username, password, email, created_at, 0 FROM users -- ", "' OR SLEEP(5) -- "),
            'evidence' => 'Capture the generated SQL query and successful bypass or expanded search result.',
            'code' => "SELECT * FROM users WHERE username='$username' AND password='$password_hash'",
            'remediation' => 'Use prepared statements, typed parameters, least-privileged DB users, and safe error handling.'
        ),
        'A04' => array(
            'title' => 'Insecure Design',
            'risk' => 'High',
            'difficulty' => 'Intermediate',
            'color' => '#7b5cc8',
            'icon' => 'workflow',
            'target' => 'pages/grade_tampering.php',
            'secure_target' => 'pages/lab_scenario.php?cat=A04',
            'objective' => 'Abuse weak business rules to change marks without a proper workflow.',
            'scenario' => 'The app allows sensitive updates without teacher ownership checks, approval flow, or change review.',
            'payloads' => array('Change grade ID 1 to A+', 'Change total_marks to 100', 'Submit as unauthenticated visitor', 'Try another student record'),
            'evidence' => 'Capture the before and after grade table row.',
            'code' => "UPDATE grades SET total_marks = '$total_marks' WHERE id = $grade_id",
            'remediation' => 'Design approval workflows, server-side invariants, role checks, and audit trails before implementation.'
        ),
        'A05' => array(
            'title' => 'Security Misconfiguration',
            'risk' => 'Medium',
            'difficulty' => 'Beginner',
            'color' => '#177e89',
            'icon' => 'settings-alert',
            'target' => 'pages/lab_scenario.php?cat=A05',
            'secure_target' => 'DEPLOYMENT_CHECKLIST.md',
            'objective' => 'Find unsafe defaults, verbose errors, and lab setup details exposed to learners.',
            'scenario' => 'A training app shows SQL errors, bootstrap behavior, and default credentials for demonstration.',
            'payloads' => array('Trigger SQL syntax error', 'Review displayed query', 'Inspect default credentials', 'Check setup notes'),
            'evidence' => 'Capture a verbose error, generated SQL, or default credential panel.',
            'code' => "die('Database setup error: ' . htmlspecialchars($e->getMessage()))",
            'remediation' => 'Keep verbose lab output behind explicit lab mode and never expose setup internals in production.'
        ),
        'A06' => array(
            'title' => 'Vulnerable Components',
            'risk' => 'Medium',
            'difficulty' => 'Intermediate',
            'color' => '#5865a8',
            'icon' => 'boxes',
            'target' => 'pages/lab_scenario.php?cat=A06',
            'secure_target' => 'pages/lab_scenario.php?cat=A06',
            'objective' => 'Review a simulated software inventory and identify outdated components.',
            'scenario' => 'The portal contains a mock dependency register for learners to assess component risk.',
            'payloads' => array('PHP runtime check', 'MySQL version review', 'CSS/JS inventory', 'CVE triage note'),
            'evidence' => 'Capture an outdated component row and recommend an upgrade path.',
            'code' => 'dependency_inventory(status = outdated)',
            'remediation' => 'Maintain an SBOM, patch dependencies, monitor CVEs, and remove unused packages.'
        ),
        'A07' => array(
            'title' => 'Identification and Authentication Failures',
            'risk' => 'High',
            'difficulty' => 'Beginner',
            'color' => '#a43f8f',
            'icon' => 'user-x',
            'target' => 'pages/login_vulnerable.php',
            'secure_target' => 'pages/login_secure.php',
            'objective' => 'Test weak credentials, auth bypass, session behavior, and account enumeration signals.',
            'scenario' => 'The lab uses simple credentials and a vulnerable authentication flow for practice.',
            'payloads' => array('admin/admin123', 'student1/student123', "admin' -- ", 'wrong password response comparison'),
            'evidence' => 'Capture successful login, bypass result, or distinguishable failure message.',
            'code' => 'session_start(); $_SESSION["role"] = $user["role"];',
            'remediation' => 'Use strong password storage, MFA, lockouts, generic errors, and session regeneration.'
        ),
        'A08' => array(
            'title' => 'Software and Data Integrity Failures',
            'risk' => 'High',
            'difficulty' => 'Intermediate',
            'color' => '#536d21',
            'icon' => 'shield-question',
            'target' => 'pages/content_management.php',
            'secure_target' => 'pages/lab_scenario.php?cat=A08',
            'objective' => 'Tamper with trusted content or hidden fields to change application state.',
            'scenario' => 'Administrative workflows trust submitted IDs and content without integrity checks or review.',
            'payloads' => array('Change hidden news_id', 'Alter published_date', 'Submit unreviewed content', 'Modify trusted role field'),
            'evidence' => 'Capture a changed content record or hidden field manipulation.',
            'code' => '<input type="hidden" name="news_id" value="...">',
            'remediation' => 'Use server-side authorization, signed state, reviews, and integrity verification.'
        ),
        'A09' => array(
            'title' => 'Logging and Monitoring Failures',
            'risk' => 'Medium',
            'difficulty' => 'Beginner',
            'color' => '#3d6f8e',
            'icon' => 'activity',
            'target' => 'pages/lab_scenario.php?cat=A09',
            'secure_target' => 'pages/lab_scenario.php?cat=A09',
            'objective' => 'Perform attacks and observe that no useful audit trail exists.',
            'scenario' => 'The vulnerable pages show exploit effects, but the application lacks a real incident timeline.',
            'payloads' => array('SQLi login attempt', 'Grade update', 'Profile IDOR request', 'XSS comment post'),
            'evidence' => 'Capture the empty monitoring panel after an attack.',
            'code' => 'no audit_log insert on sensitive action',
            'remediation' => 'Log authentication events, sensitive reads/writes, failed attempts, and alert-worthy patterns.'
        ),
        'A10' => array(
            'title' => 'Server-Side Request Forgery',
            'risk' => 'Medium',
            'difficulty' => 'Advanced',
            'color' => '#8f5f25',
            'icon' => 'server-cog',
            'target' => 'pages/lab_scenario.php?cat=A10',
            'secure_target' => 'pages/lab_scenario.php?cat=A10',
            'objective' => 'Use a simulated URL fetcher to request internal resources.',
            'scenario' => 'A mock import tool accepts URLs and demonstrates why server-side fetching needs strict allowlists.',
            'payloads' => array('http://127.0.0.1/admin', 'http://localhost/server-status', 'http://169.254.169.254/latest/meta-data/', 'file:///etc/passwd'),
            'evidence' => 'Capture the simulated internal request warning.',
            'code' => 'fetch_remote_url($_POST["url"])',
            'remediation' => 'Use allowlists, block private ranges, disable redirects, and isolate fetch services.'
        )
    );
}

function labCategory(string $id): ?array {
    $categories = labCategories();
    return $categories[$id] ?? null;
}
?>
