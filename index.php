<?php
include_once __DIR__ . '/includes/config.php';
$db_bootstrap_status = $GLOBALS['db_bootstrap_status'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern School Portal | Vulnerable Security Demo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <div>
                <div class="eyebrow">Educational Security Playground</div>
                <h1>Modern School Portal</h1>
                <p class="header-copy">A polished school management demo with intentional vulnerabilities and secure comparisons for hands-on learning.</p>
            </div>
            <div class="header-badge">Lab Build 2026</div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container nav-inner">
            <a href="index.php">Home</a>
            <a href="pages/owasp_lab.php">OWASP Labs</a>
            <a href="pages/attack_paths.php">Attack Paths</a>
            <a href="pages/challenges.php">Challenges</a>
            <a href="pages/progress.php">Progress</a>
            <a href="pages/report_builder.php">Reports</a>
            <a href="pages/instructor_mode.php">Instructor</a>
            <a href="pages/login_secure.php">Secure Login</a>
            <a href="pages/login_vulnerable.php">SQLi Login</a>
            <a href="pages/news_vulnerable.php">SQLi Search</a>
            <a href="pages/news_details.php">News Details</a>
            <a href="pages/xss_vulnerable.php">XSS Demo</a>
            <a href="pages/xss_reflected.php">Reflected XSS</a>
            <a href="pages/crypto_vulnerable.php">Crypto Failures</a>
            <a href="pages/cmd_injection_vulnerable.php">Command Injection</a>
            <a href="pages/ssrf_vulnerable.php">SSRF</a>
            <a href="pages/ctf_scoreboard.php">Scoreboard</a>
            <a href="pages/profile_idor.php">IDOR Profile</a>
            <a href="pages/grade_tampering.php">Grade Tampering</a>
        </div>
    </nav>

    <main class="container page-shell">
        <?php if ($db_bootstrap_status === 'initialized'): ?>
            <section class="notice-panel" style="margin-top: 0;">
                <div class="notice-title">Database Ready</div>
                <p>The database schema and sample data were created automatically on first load.</p>
            </section>
        <?php endif; ?>

        <section class="hero-panel">
            <div class="hero-copy">
                <span class="section-kicker">Built for demos, labs, and walkthroughs</span>
                <h2>Train on realistic school workflows while seeing how insecure code breaks.</h2>
                <p>
                    This portal combines a working student management system with intentionally unsafe flows for SQL injection,
                    XSS, broken access control, direct object references, and grade tampering demonstrations.
                </p>
                <div class="hero-actions">
                    <a class="btn-primary" href="pages/owasp_lab.php">Open OWASP Mission Control</a>
                    <a class="btn-secondary-link" href="pages/attack_paths.php">Choose Attack Path</a>
                    <a class="btn-secondary-link" href="pages/challenges.php">Start Challenges</a>
                    <a class="btn-primary" href="pages/login_vulnerable.php">Launch Vulnerable Login</a>
                    <a class="btn-secondary-link" href="pages/login_secure.php">Compare Secure Version</a>
                </div>
            </div>

            <aside class="status-panel">
                <h3>Project Status</h3>
                <ul class="status-list">
                    <li>SQL Injection vulnerabilities visible</li>
                    <li>Secure implementations available</li>
                    <li>XSS attacks demonstrable</li>
                    <li>Full user management working</li>
                    <li>Student tracking system active</li>
                    <li>Grade management ready</li>
                    <li>Attendance system in place</li>
                    <li>Broken access control demos added</li>
                    <li>Student profile leakage enabled</li>
                    <li>Grade tampering workflow exposed</li>
                </ul>
            </aside>
        </section>

        <section class="notice-panel">
            <div class="notice-title">Warning</div>
            <p>
                This project intentionally includes insecure behavior for educational use. It should never be deployed as a real production school portal.
            </p>
        </section>

        <section class="feature-section">
            <div class="section-heading">
                <span class="section-kicker">Security Labs</span>
                <h3>Visible vulnerable and secure modules</h3>
            </div>

            <div class="feature-grid">
                <article class="feature-card vulnerable">
                    <span class="card-tag">Vulnerable</span>
                    <h4>Login SQL Injection</h4>
                    <p>Bypass authentication with crafted payloads and compare the result against the secure login flow.</p>
                    <a href="pages/login_vulnerable.php">Open demo</a>
                </article>

                <article class="feature-card secure">
                    <span class="card-tag">Secure</span>
                    <h4>Prepared Statement Login</h4>
                    <p>See how parameterized queries block injection attempts while still supporting full login flow.</p>
                    <a href="pages/login_secure.php">Open secure flow</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">Vulnerable</span>
                    <h4>News Search SQLi</h4>
                    <p>Use injection payloads against article search to show data exposure risk from unsafe query building.</p>
                    <a href="pages/news_vulnerable.php">Explore search</a>
                </article>

                <article class="feature-card secure">
                    <span class="card-tag">Secure</span>
                    <h4>Secure Article Search</h4>
                    <p>Compare the same search experience backed by safer query handling and cleaner output control.</p>
                    <a href="pages/articles_secure.php">View secure articles</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">Vulnerable</span>
                    <h4>Stored XSS Comments</h4>
                    <p>Demonstrate script injection through comments and observe how unsafe rendering turns content into code.</p>
                    <a href="pages/xss_vulnerable.php">Test XSS</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">OWASP A02</span>
                    <h4>Cryptographic Failures</h4>
                    <p>Inspect MD5 password hashing, Base64 pseudo-encryption, URL-exposed SSNs, and a hardcoded JWT secret.</p>
                    <a href="pages/crypto_vulnerable.php">Open crypto lab</a>
                </article>

                <article class="feature-card secure">
                    <span class="card-tag">OWASP A02</span>
                    <h4>Secure Crypto Comparison</h4>
                    <p>Compare MD5 with bcrypt and Base64 with AES-256 encryption using live demo inputs.</p>
                    <a href="pages/crypto_secure.php">Open secure crypto</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">OWASP A03</span>
                    <h4>Command Injection</h4>
                    <p>Run the school network ping and file reader tools to see why shell concatenation is dangerous.</p>
                    <a href="pages/cmd_injection_vulnerable.php">Open command lab</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">OWASP A10</span>
                    <h4>SSRF Fetcher</h4>
                    <p>Use a server-side URL fetcher to request localhost, loopback ports, and metadata-style endpoints.</p>
                    <a href="pages/ssrf_vulnerable.php">Open SSRF lab</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">OWASP A01</span>
                    <h4>CSRF Grade Change</h4>
                    <p>Compare a tokenless grade form with a secure CSRF-token protected version and attacker auto-submit page.</p>
                    <a href="pages/csrf_vulnerable.php">Open CSRF lab</a>
                </article>

                <article class="feature-card vulnerable">
                    <span class="card-tag">OWASP A05</span>
                    <h4>Security Misconfiguration</h4>
                    <p>Trigger verbose errors, inspect missing headers, and download an intentionally exposed backup file.</p>
                    <a href="pages/security_misconfig.php">Open A05 lab</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">OWASP</span>
                    <h4>Top 10 Mission Control</h4>
                    <p>Work through guided A01-A10 labs with payload drawers, notebook prompts, evidence slots, and instructor flow.</p>
                    <a href="pages/owasp_lab.php">Open dashboard</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">Learning Paths</span>
                    <h4>Beginner to Advanced Attacks</h4>
                    <p>Practice attack scenarios with staged hints, target pages, payloads, walkthroughs, and evidence worksheets.</p>
                    <a href="pages/attack_paths.php">Open attack paths</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">Challenges</span>
                    <h4>Scenario Missions</h4>
                    <p>Use role-based missions for student, teacher, admin, and security engineer practice.</p>
                    <a href="pages/challenges.php">Open challenges</a>
                </article>

                <article class="feature-card secure">
                    <span class="card-tag">Instructor Tool</span>
                    <h4>Reset and Progress</h4>
                    <p>Restore lab data, clear session progress, and track exploited or reported attacks.</p>
                    <a href="pages/progress.php">View progress</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">New Demo</span>
                    <h4>Student Profile IDOR</h4>
                    <p>Browse student records directly by changing identifiers and exposing personal profile data without proper checks.</p>
                    <a href="pages/profile_idor.php">View profile leak</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">New Demo</span>
                    <h4>Grade Tampering</h4>
                    <p>Modify grades using an intentionally unsafe workflow that trusts user-supplied record IDs and values.</p>
                    <a href="pages/grade_tampering.php">Tamper grades</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">New Demo</span>
                    <h4>News Details</h4>
                    <p>A realistic article-details endpoint that reads URL parameters directly into SQL for demonstration purposes.</p>
                    <a href="pages/news_details.php">Open news details</a>
                </article>

                <article class="feature-card dangerous">
                    <span class="card-tag">New Demo</span>
                    <h4>Attendance Record Exposure</h4>
                    <p>Inspect attendance entries by student and course without ownership validation to demonstrate privacy failures.</p>
                    <a href="pages/attendance_bypass.php">Open attendance demo</a>
                </article>
            </div>
        </section>

        <section class="feature-section">
            <div class="section-heading">
                <span class="section-kicker">Application Modules</span>
                <h3>School management system coverage</h3>
            </div>

            <div class="module-grid">
                <div class="module-card">
                    <strong>User Management</strong>
                    <p>Create and manage students, teachers, admins, and parents with role-aware dashboards.</p>
                </div>
                <div class="module-card">
                    <strong>Student Tracking</strong>
                    <p>Maintain class, roll number, family contacts, admission status, and student identity records.</p>
                </div>
                <div class="module-card">
                    <strong>Grade Management</strong>
                    <p>Track assignments, midterms, final exams, total marks, and GPA-oriented reporting.</p>
                </div>
                <div class="module-card">
                    <strong>Attendance System</strong>
                    <p>Monitor attendance percentages, course participation, and attendance record visibility.</p>
                </div>
                <div class="module-card">
                    <strong>Course Management</strong>
                    <p>Organize departments, instructors, semesters, and active enrollments across the portal.</p>
                </div>
                <div class="module-card">
                    <strong>Content Publishing</strong>
                    <p>Publish school news, secure articles, and announcements for students and staff.</p>
                </div>
            </div>
        </section>

        <section class="feature-section credentials-section">
            <div class="section-heading">
                <span class="section-kicker">Ready To Test</span>
                <h3>Sample accounts and payload ideas</h3>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>admin</code></td>
                        <td><code>admin123</code></td>
                        <td>Administrator</td>
                        <td>Admin dashboard and management modules</td>
                    </tr>
                    <tr>
                        <td><code>teacher_science</code></td>
                        <td><code>teacher123</code></td>
                        <td>Teacher</td>
                        <td>Teacher dashboard, grades, and attendance views</td>
                    </tr>
                    <tr>
                        <td><code>student1</code></td>
                        <td><code>student123</code></td>
                        <td>Student</td>
                        <td>Student dashboard with courses and grades</td>
                    </tr>
                    <tr>
                        <td><code>parent1</code></td>
                        <td><code>parent123</code></td>
                        <td>Parent</td>
                        <td>Parent role data in user management</td>
                    </tr>
                </tbody>
            </table>

            <div class="payload-grid">
                <div class="payload-box">
                    <h4>SQL Injection Payloads</h4>
                    <div class="code-block"><code>admin' OR '1'='1</code></div>
                    <div class="code-block"><code>admin' --</code></div>
                    <div class="code-block"><code>' UNION SELECT 1,2,3,4,5,VERSION() --</code></div>
                </div>

                <div class="payload-box">
                    <h4>XSS Payloads</h4>
                    <div class="code-block"><code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code></div>
                    <div class="code-block"><code>&lt;img src=x onerror=alert('XSS')&gt;</code></div>
                    <div class="code-block"><code>&lt;svg onload=alert('XSS')&gt;</code></div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | Educational vulnerability demo only</p>
    </footer>
</body>
</html>
