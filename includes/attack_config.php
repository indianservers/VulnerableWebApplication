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
