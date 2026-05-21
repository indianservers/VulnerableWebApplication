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
            'target' => 'pages/path_traversal_vulnerable.php?file=reports/student1.pdf',
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
            'target' => 'pages/crypto_vulnerable.php',
            'secure_target' => 'pages/crypto_secure.php',
            'objective' => 'Identify weak password hashing, reversible encoding, exposed PII in URLs, and hardcoded signing keys.',
            'scenario' => 'The school portal demonstrates MD5 password storage, Base64 treated as encryption, student SSNs in GET parameters, and a weak JWT secret.',
            'payloads' => array('student123', '123-45-6789', '?student_id=3&ssn=123-45-6789', 'JWT_SECRET=secret'),
            'evidence' => 'Capture the MD5 hash, decoded Base64 SSN, URL containing PII, or hardcoded secret value.',
            'code' => '$hashed = md5($password);',
            'remediation' => 'Use password_hash(), password_verify(), authenticated encryption for sensitive data, POST/body transport for sensitive workflows, and environment-managed high-entropy secrets.'
        ),
        'A03' => array(
            'title' => 'Injection',
            'risk' => 'Critical',
            'difficulty' => 'Beginner',
            'color' => '#c0325a',
            'icon' => 'database-zap',
            'target' => 'pages/cmd_injection_vulnerable.php',
            'secure_target' => 'pages/xss_reflected.php',
            'objective' => 'Exploit SQL, command, reflected XSS, and XXE injection points.',
            'scenario' => 'School portal utilities concatenate user input into SQL, shell commands, reflected HTML, and XML parsers.',
            'payloads' => array("admin' -- ", "127.0.0.1; whoami", "?q=<script>alert('XSS')</script>", '<!DOCTYPE student [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'),
            'evidence' => 'Capture generated queries, command output, reflected script execution, or XXE parser output.',
            'code' => "shell_exec('ping -c 1 ' . \$_POST['host'])",
            'remediation' => 'Use prepared statements, argument escaping or no-shell APIs, output encoding, and hardened XML parsing.'
        ),
        'A04' => array(
            'title' => 'Insecure Design',
            'risk' => 'High',
            'difficulty' => 'Intermediate',
            'color' => '#7b5cc8',
            'icon' => 'workflow',
            'target' => 'pages/file_upload_vulnerable.php',
            'secure_target' => 'pages/file_upload_vulnerable.php',
            'objective' => 'Upload untrusted profile files without MIME, extension, or storage controls.',
            'scenario' => 'The app accepts profile photos directly into a web-accessible folder.',
            'payloads' => array('shell.php', 'avatar.php.jpg', 'image/jpeg spoof', 'store outside webroot'),
            'evidence' => 'Capture the uploaded path or secure rejection message.',
            'code' => 'move_uploaded_file($tmp, "uploads/" . $_FILES["name"])',
            'remediation' => 'Validate MIME type, rename files, scan content, and store uploads outside the webroot.'
        ),
        'A05' => array(
            'title' => 'Security Misconfiguration',
            'risk' => 'Medium',
            'difficulty' => 'Beginner',
            'color' => '#177e89',
            'icon' => 'settings-alert',
            'target' => 'pages/security_misconfig.php',
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
            'target' => 'pages/auth_lockout_vulnerable.php',
            'secure_target' => 'pages/auth_lockout_vulnerable.php',
            'objective' => 'Demonstrate brute-force login attempts with no rate limiting or lockout.',
            'scenario' => 'The vulnerable login accepts unlimited attempts while the secure panel locks after repeated failures.',
            'payloads' => array('admin/password', 'admin/admin', 'admin/admin123', '5 failed attempts'),
            'evidence' => 'Capture repeated failures with no lockout or secure lockout after 5 failures.',
            'code' => 'if ($password_ok) login(); // no failed-attempt counter',
            'remediation' => 'Use MFA, throttling, generic errors, session regeneration, and short lockouts.'
        ),
        'A08' => array(
            'title' => 'Software and Data Integrity Failures',
            'risk' => 'High',
            'difficulty' => 'Intermediate',
            'color' => '#536d21',
            'icon' => 'shield-question',
            'target' => 'pages/deserialization_vulnerable.php',
            'secure_target' => 'pages/deserialization_vulnerable.php',
            'objective' => 'Tamper with serialized trusted state to elevate role.',
            'scenario' => 'A PHP serialized profile cookie controls the visible role without server-side lookup.',
            'payloads' => array('Set tampered admin cookie', 'role=admin', 'signed state comparison', 'server-side role lookup'),
            'evidence' => 'Capture the decoded serialized object and admin role result.',
            'code' => 'unserialize(base64_decode($_COOKIE["lab_profile"]))',
            'remediation' => 'Avoid native object deserialization for user input; sign state and load roles from the database.'
        ),
        'A09' => array(
            'title' => 'Logging and Monitoring Failures',
            'risk' => 'Medium',
            'difficulty' => 'Beginner',
            'color' => '#3d6f8e',
            'icon' => 'activity',
            'target' => 'pages/logging_demo.php',
            'secure_target' => 'pages/logging_demo.php',
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
            'target' => 'pages/ssrf_vulnerable.php',
            'secure_target' => 'pages/ssrf_vulnerable.php',
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
