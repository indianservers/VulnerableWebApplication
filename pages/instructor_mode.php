<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';

$categories = labCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Instructor Mode'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Teaching Console</div>
            <h1>Instructor Mode</h1>
            <p>Guide learners through OWASP coverage while keeping the environment intentionally vulnerable.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell">
        <section class="lab-command-grid">
            <div class="command-card">
                <span>Reset Strategy</span>
                <strong>Manual DB Reload</strong>
                <p>Use the bundled SQL file to restore demo data after tampering labs.</p>
            </div>
            <div class="command-card">
                <span>Audience</span>
                <strong>Beginner to Advanced</strong>
                <p>Hints and payloads are visible for guided training.</p>
            </div>
            <div class="command-card">
                <span>Safety</span>
                <strong>Local Lab</strong>
                <p>Do not expose this simulator as a production application.</p>
            </div>
            <div class="command-card">
                <span>Utilities</span>
                <strong>Reset + Progress</strong>
                <p><a href="lab_reset.php">Reset data</a> or <a href="progress.php">review progress</a>.</p>
            </div>
        </section>

        <section class="lab-panel">
            <h2>Walkthrough Queue</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Lab Goal</th>
                        <th>Suggested Evidence</th>
                        <th>Open</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $id => $lab): ?>
                        <tr>
                            <td><span class="badge badge-warning"><?php echo e($id); ?></span> <?php echo e($lab['title']); ?></td>
                            <td><?php echo e($lab['objective']); ?></td>
                            <td><?php echo e($lab['evidence']); ?></td>
                            <td><a href="lab_scenario.php?cat=<?php echo e($id); ?>">Start</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="lab-panel">
            <h2>Reset Notes</h2>
            <div class="code-block"><code>mysql -u myuser -p myapp_db &lt; database_enhanced.sql</code></div>
            <p class="muted">This app is intentionally unsafe. Resetting data should be an instructor action, not a student-facing production feature.</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | Instructor console</p>
    </footer>
</body>
</html>
