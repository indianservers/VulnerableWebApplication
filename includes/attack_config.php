<?php
/**
 * Attack learning paths for the intentionally vulnerable simulator.
 */

function attackScenarios(): array {
    return array(
        'sqli-auth-bypass' => array(
            'title' => 'SQL Injection Auth Bypass',
            'tier' => 'Beginner',
            'owasp' => 'A03 Injection',
            'target' => 'pages/login_vulnerable.php',
            'goal' => 'Log in without knowing the real password by changing the SQL logic.',
            'story' => 'The login form builds a SQL query by joining username and password strings directly.',
            'skills' => array('Form testing', 'SQL comments', 'Boolean logic', 'Evidence capture'),
            'steps' => array(
                'Open the vulnerable login page.',
                'Submit a normal wrong password and observe the failure.',
                'Use a payload that changes the WHERE clause logic.',
                'Capture the generated SQL and redirected dashboard.'
            ),
            'payloads' => array("admin' -- ", "' OR '1'='1' -- "),
            'success' => 'A dashboard opens or the generated query shows that the password check was bypassed.',
            'hints' => array(
                'Start by looking at how the app displays the generated SQL after a login attempt.',
                'A SQL comment marker can remove the password condition after the username check.',
                "Try placing admin' -- in the username field and any text in the password field.",
                'Write down why the rest of the SQL query no longer matters after the comment marker.'
            )
        ),
        'stored-xss-comment' => array(
            'title' => 'Stored XSS in Comments',
            'tier' => 'Beginner',
            'owasp' => 'A03 Injection',
            'target' => 'pages/xss_vulnerable.php',
            'goal' => 'Store browser-executable markup in the comment system.',
            'story' => 'Comments are saved to the database and printed back without output encoding.',
            'skills' => array('HTML injection', 'Stored payloads', 'Browser execution', 'Output encoding review'),
            'steps' => array(
                'Open the XSS comments page.',
                'Post a normal comment first.',
                'Post a harmless script or image-event payload.',
                'Refresh and confirm the payload executes from stored content.'
            ),
            'payloads' => array("<script>alert('XSS')</script>", "<img src=x onerror=alert('XSS')>"),
            'success' => 'The payload executes when the comment list renders.',
            'hints' => array(
                'Check whether the page escapes comment text before displaying it.',
                'Stored XSS persists because the payload is saved, then executed later for viewers.',
                'If a script tag is blocked by the browser context, try an image tag event handler.',
                'The fix would be output encoding, but keep this page vulnerable for the lab.'
            )
        ),
        'idor-profile-access' => array(
            'title' => 'IDOR Student Profile Access',
            'tier' => 'Intermediate',
            'owasp' => 'A01 Broken Access Control',
            'target' => 'pages/profile_idor.php?student_id=1',
            'goal' => 'Read another student profile by changing a direct object identifier.',
            'story' => 'The profile page trusts the student_id URL parameter and does not verify ownership.',
            'skills' => array('URL tampering', 'Authorization testing', 'Sensitive data review', 'Role thinking'),
            'steps' => array(
                'Open the profile page with student_id=1.',
                'Record the visible student identity fields.',
                'Change the URL to student_id=2 or student_id=3.',
                'Capture the unrelated profile data that becomes visible.'
            ),
            'payloads' => array('?student_id=2', '?student_id=3', '?student_id=4'),
            'success' => 'A different student record appears without a server-side authorization check.',
            'hints' => array(
                'Look for numeric IDs in the URL.',
                'Change only one number and compare the profile fields.',
                'The bug is not just SQL injection; it is missing authorization on the object.',
                'A secure design checks whether the current user may view the requested student record.'
            )
        ),
        'grade-parameter-tamper' => array(
            'title' => 'Grade Parameter Tampering',
            'tier' => 'Intermediate',
            'owasp' => 'A04 Insecure Design',
            'target' => 'pages/grade_tampering.php',
            'goal' => 'Modify a grade record by submitting trusted IDs and marks directly.',
            'story' => 'The grade update workflow accepts record IDs and marks without role or ownership checks.',
            'skills' => array('POST tampering', 'Business logic testing', 'Integrity impact', 'Before-after evidence'),
            'steps' => array(
                'Open the grade tampering page.',
                'Pick an existing grade ID from the table.',
                'Submit new marks and a grade letter.',
                'Capture the table row before and after the update.'
            ),
            'payloads' => array('grade_id=1, total_marks=99.9, grade_letter=A+', 'grade_id=2, total_marks=35, grade_letter=F'),
            'success' => 'The grade table changes even if the visitor is not the authorized teacher.',
            'hints' => array(
                'Use the record IDs already shown in the table.',
                'The vulnerability is the trusted workflow, not only the SQL syntax.',
                'Try changing one harmless row first, then reset the lab data after practice.',
                'A proper workflow would validate teacher ownership and create an audit trail.'
            )
        ),
        'union-data-extraction' => array(
            'title' => 'UNION-Based Data Extraction',
            'tier' => 'Advanced',
            'owasp' => 'A03 Injection',
            'target' => 'pages/news_vulnerable.php',
            'goal' => 'Use a UNION query against search to expose data from another table.',
            'story' => 'The news search query returns six columns and directly concatenates the search term.',
            'skills' => array('Column counting', 'UNION SELECT', 'Schema reasoning', 'Result interpretation'),
            'steps' => array(
                'Open the vulnerable news search page.',
                'Use a broad boolean payload to confirm injection.',
                'Match the number of selected columns.',
                'Map user table fields into visible result columns.'
            ),
            'payloads' => array("' OR '1'='1' -- ", "' UNION SELECT 1, username, password, email, created_at, 0 FROM users -- "),
            'success' => 'Search results include values sourced from the users table.',
            'hints' => array(
                'First prove the search is injectable with a simple boolean payload.',
                'UNION attacks require the same number of columns as the original SELECT.',
                'The visible news result expects id, title, content, author, published date, and views.',
                "Try the provided UNION payload, then explain which user fields mapped into title/content/author."
            )
        ),
        'csrf-admin-delete' => array(
            'title' => 'CSRF Admin Delete Action',
            'tier' => 'Intermediate',
            'owasp' => 'A01 Broken Access Control',
            'target' => 'pages/user_management.php',
            'goal' => 'Understand how state-changing GET links can be triggered without a CSRF token.',
            'story' => 'Admin delete actions are represented as links, making the workflow easy to trigger from a crafted URL.',
            'skills' => array('CSRF reasoning', 'State change review', 'Admin workflow testing', 'Token analysis'),
            'steps' => array(
                'Log in as admin in the lab environment.',
                'Inspect a delete link in user management.',
                'Observe that the action is a GET request with an ID.',
                'Document why a POST form with CSRF token would be safer.'
            ),
            'payloads' => array('user_management.php?action=delete&id=2', '<img src="/pages/user_management.php?action=delete&id=2">'),
            'success' => 'The learner can explain how a browser could trigger the state change with an authenticated admin session.',
            'hints' => array(
                'Look at the action links in the management table.',
                'A state-changing operation should not be a simple GET link.',
                'The missing ingredient is an unpredictable per-session CSRF token.',
                'Use this as a reasoning lab; do not delete users you still need for later exercises.'
            )
        ),
        'file-upload-bypass' => array(
            'title' => 'File Upload Bypass Simulation',
            'tier' => 'Intermediate',
            'owasp' => 'A05 Security Misconfiguration',
            'target' => 'pages/lab_scenario.php?cat=A05',
            'goal' => 'Analyze unsafe upload design and propose bypass tests.',
            'story' => 'A simulated school document upload accepts file names and types without a server-side policy.',
            'skills' => array('Extension review', 'MIME reasoning', 'Storage path review', 'Execution risk'),
            'steps' => array(
                'Open the A05 scenario page.',
                'List the file checks a real upload feature should perform.',
                'Compare extension-only checks against MIME and content validation.',
                'Write a finding for unsafe upload design.'
            ),
            'payloads' => array('shell.php.jpg', 'report.pdf.php', 'image/svg+xml with script', '../../uploads/report.php'),
            'success' => 'The learner documents how upload validation and storage isolation should work.',
            'hints' => array(
                'Extension checks alone are not enough.',
                'Uploaded files should not execute as server code.',
                'Randomized names and storage outside web root reduce impact.',
                'This is a design simulation until a real upload page is added.'
            )
        ),
        'account-enumeration' => array(
            'title' => 'Account Enumeration',
            'tier' => 'Beginner',
            'owasp' => 'A07 Identification and Authentication Failures',
            'target' => 'pages/login_secure.php',
            'goal' => 'Compare login responses and timing to infer whether accounts exist.',
            'story' => 'Authentication pages can accidentally reveal account validity through messages, redirects, or behavior.',
            'skills' => array('Response comparison', 'Username testing', 'Auth UX review', 'Evidence notes'),
            'steps' => array(
                'Try a real username with a wrong password.',
                'Try a made-up username with a wrong password.',
                'Compare response text and behavior.',
                'Record whether the app leaks account existence.'
            ),
            'payloads' => array('admin / wrongpass', 'not_a_user / wrongpass', 'student1 / wrongpass'),
            'success' => 'The learner records whether responses are distinguishable or properly generic.',
            'hints' => array(
                'Use the same wrong password for each username.',
                'Compare response text, HTTP behavior, and timing.',
                'Generic errors help prevent enumeration.',
                'Known lab accounts are listed on the home page.'
            )
        ),
        'session-role-review' => array(
            'title' => 'Weak Session Role Review',
            'tier' => 'Advanced',
            'owasp' => 'A07 Identification and Authentication Failures',
            'target' => 'pages/lab_scenario.php?cat=A07',
            'goal' => 'Review how session role values drive access decisions across dashboards.',
            'story' => 'The portal stores role information in session state and checks it directly on protected pages.',
            'skills' => array('Session review', 'Role boundaries', 'Access matrix', 'Fix design'),
            'steps' => array(
                'Log in as different roles.',
                'Try direct dashboard URLs.',
                'Record which pages enforce role checks.',
                'Design a centralized authorization helper.'
            ),
            'payloads' => array('Direct /pages/admin_dashboard.php', 'Direct /pages/teacher_dashboard.php', 'Direct /pages/student_dashboard.php'),
            'success' => 'The learner maps role checks and identifies where centralized authorization would reduce risk.',
            'hints' => array(
                'Direct URL access is part of the test.',
                'Look for repeated role checks in page headers.',
                'Session regeneration after login matters.',
                'Centralized auth helpers reduce inconsistent page behavior.'
            )
        ),
        'path-traversal-report' => array(
            'title' => 'Path Traversal Report Access Simulation',
            'tier' => 'Advanced',
            'owasp' => 'A01 Broken Access Control',
            'target' => 'pages/lab_scenario.php?cat=A01',
            'goal' => 'Reason about unsafe file path parameters for report-card downloads.',
            'story' => 'A simulated download feature accepts a file name without normalizing or restricting it.',
            'skills' => array('Path normalization', 'Allowlist design', 'Sensitive file thinking', 'Download controls'),
            'steps' => array(
                'Open the A01 scenario page.',
                'Identify where a file parameter would cross a trust boundary.',
                'Test traversal strings in the worksheet.',
                'Write a fix using allowlisted report IDs instead of file paths.'
            ),
            'payloads' => array('../config.php', '..\\..\\includes\\config.php', '../../database_enhanced.sql'),
            'success' => 'The learner explains why direct file path input should be replaced with authorized object IDs.',
            'hints' => array(
                'Traversal payloads try to escape the intended directory.',
                'Windows and Linux separators can differ.',
                'Never use a user-supplied path as the source of truth.',
                'Map report IDs to server-side paths after authorization.'
            )
        ),
        'open-redirect' => array(
            'title' => 'Open Redirect Simulation',
            'tier' => 'Beginner',
            'owasp' => 'A04 Insecure Design',
            'target' => 'pages/lab_scenario.php?cat=A04',
            'goal' => 'Learn how unsafe return URL parameters can support phishing.',
            'story' => 'A simulated post-login redirect trusts a next parameter without validating the destination.',
            'skills' => array('URL review', 'Phishing impact', 'Allowlist thinking', 'Redirect validation'),
            'steps' => array(
                'Open the A04 scenario page.',
                'Add a fake next URL to the worksheet.',
                'Explain how a trusted domain can redirect to an attacker-controlled page.',
                'Write an allowlist-based remediation.'
            ),
            'payloads' => array('?next=https://evil.example/login', '?return=//evil.example', '?redirect=/pages/admin_dashboard.php'),
            'success' => 'The learner documents how unvalidated redirects can support credential theft.',
            'hints' => array(
                'Redirects are dangerous when users trust the starting domain.',
                'Protocol-relative URLs can be surprising.',
                'Internal relative paths are usually safer than full external URLs.',
                'Allowlist known local destinations.'
            )
        ),
        'clickjacking-framing' => array(
            'title' => 'Clickjacking Framing Review',
            'tier' => 'Intermediate',
            'owasp' => 'A05 Security Misconfiguration',
            'target' => 'pages/lab_scenario.php?cat=A05',
            'goal' => 'Check whether sensitive pages define anti-framing protections.',
            'story' => 'Sensitive workflows can be hidden in transparent frames if the site lacks frame restrictions.',
            'skills' => array('Header review', 'UI redress', 'Sensitive action mapping', 'Browser security'),
            'steps' => array(
                'Choose a sensitive action page.',
                'Review whether frame-ancestors or X-Frame-Options is present.',
                'Document which pages should not be framed.',
                'Recommend a frame-ancestors CSP policy.'
            ),
            'payloads' => array('<iframe src="/pages/grade_tampering.php"></iframe>', "Content-Security-Policy: frame-ancestors 'self'"),
            'success' => 'The learner identifies missing anti-framing headers and writes the expected policy.',
            'hints' => array(
                'Clickjacking is often about missing headers, not form code.',
                'Sensitive state-changing pages deserve stronger protection.',
                'Modern CSP frame-ancestors is the preferred control.',
                'X-Frame-Options is older but still common.'
            )
        ),
        'cors-misconfiguration' => array(
            'title' => 'CORS Misconfiguration Simulation',
            'tier' => 'Advanced',
            'owasp' => 'A05 Security Misconfiguration',
            'target' => 'pages/lab_scenario.php?cat=A05',
            'goal' => 'Analyze how permissive cross-origin settings can expose authenticated data.',
            'story' => 'A simulated API returns student data and reflects Origin headers without a strict allowlist.',
            'skills' => array('Origin review', 'Credentialed requests', 'API exposure', 'Header analysis'),
            'steps' => array(
                'Open the A05 scenario page.',
                'Write a mock Origin header in the HTTP inspector.',
                'Decide whether credentials should ever be allowed cross-origin.',
                'Draft a strict CORS policy.'
            ),
            'payloads' => array('Origin: https://evil.example', 'Access-Control-Allow-Origin: *', 'Access-Control-Allow-Credentials: true'),
            'success' => 'The learner explains why wildcard origins and credentials are a dangerous combination.',
            'hints' => array(
                'CORS is a browser permission model, not authentication.',
                'Wildcard origins are risky for private APIs.',
                'Credentials require exact trusted origins.',
                'Prefer no CORS unless a real cross-origin client needs it.'
            )
        )
    );
}

function attackScenario(string $id): ?array {
    $attacks = attackScenarios();
    return $attacks[$id] ?? null;
}

function attacksByTier(): array {
    $tiers = array('Beginner' => array(), 'Intermediate' => array(), 'Advanced' => array());
    foreach (attackScenarios() as $id => $attack) {
        $tiers[$attack['tier']][$id] = $attack;
    }
    return $tiers;
}
?>
