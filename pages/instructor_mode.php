<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$categories = labCategories();
ensureLabRuntime();
$events = array_reverse($_SESSION['attack_events'] ?? array());
$flags = array_reverse($_SESSION['captured_flags'] ?? array());
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
                <span>Attack Events</span>
                <strong><?php echo count($events); ?></strong>
                <p>Recent exploit attempts recorded by the lab pages.</p>
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
                <span>Flags</span>
                <strong><?php echo count($flags); ?></strong>
                <p><a href="ctf_scoreboard.php">Open scoreboard</a> or <a href="lab_reset.php">reset progress</a>.</p>
            </div>
        </section>

        <section class="lab-panel">
            <h2>Real-time Attack Log</h2>
            <table>
                <thead>
                    <tr><th>Time</th><th>User</th><th>IP</th><th>Category</th><th>Action</th><th>Payload</th><th>Result</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($events, 0, 20) as $event): ?>
                        <tr>
                            <td><?php echo e($event['time']); ?></td>
                            <td><?php echo e($event['user']); ?></td>
                            <td><?php echo e($event['ip']); ?></td>
                            <td><?php echo e($event['category']); ?></td>
                            <td><?php echo e($event['action']); ?></td>
                            <td><code><?php echo e(substr($event['payload'], 0, 120)); ?></code></td>
                            <td><?php echo e($event['result']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$events): ?><tr><td colspan="7">No attack events recorded yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="lab-panel">
            <h2>Per-student Progress Dashboard</h2>
            <table>
                <thead><tr><th>Student</th><th>Captured Flags</th><th>Recent Progress</th><th>Reset</th></tr></thead>
                <tbody>
                    <tr>
                        <td><?php echo e($_SESSION['username'] ?? 'guest learner'); ?></td>
                        <td><?php echo count($flags); ?></td>
                        <td><?php echo $events ? e($events[0]['action'] . ' at ' . $events[0]['time']) : 'No attempts yet'; ?></td>
                        <td><a href="lab_reset.php">One-click reset</a></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="lab-panel">
            <h2>Captured Flags</h2>
            <table>
                <thead><tr><th>Flag</th><th>Reason</th><th>Time</th></tr></thead>
                <tbody>
                    <?php foreach ($flags as $flag): ?><tr><td><code><?php echo e($flag['flag']); ?></code></td><td><?php echo e($flag['reason']); ?></td><td><?php echo e($flag['time']); ?></td></tr><?php endforeach; ?>
                    <?php if (!$flags): ?><tr><td colspan="3">No flags captured yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
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
                            <td>
                                <a href="lab_scenario.php?cat=<?php echo e($id); ?>">Start</a>
                                <form method="POST" action="lab_reset.php" style="margin-top: 8px;">
                                    <input type="hidden" name="reset" value="category">
                                    <input type="hidden" name="category" value="<?php echo e($id); ?>">
                                    <button type="submit" class="btn-secondary">Reset <?php echo e($id); ?></button>
                                </form>
                            </td>
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
