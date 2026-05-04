<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/lab_state.php';

$progress = labProgress();
$totals = progressTotals();
$attacks = attackScenarios();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Lab Progress'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Scoreboard</div>
            <h1>Lab Progress Tracker</h1>
            <p>Session-based progress for attack attempts, exploit proof, and report completion.</p>
        </div>
    </header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <section class="lab-command-grid">
            <div class="command-card"><span>Total</span><strong><?php echo e($totals['total']); ?></strong><p>Attack scenarios available.</p></div>
            <div class="command-card"><span>Attempted</span><strong><?php echo e($totals['attempted']); ?></strong><p>Opened or marked as started.</p></div>
            <div class="command-card"><span>Exploited</span><strong><?php echo e($totals['exploited']); ?></strong><p>Marked with exploit proof.</p></div>
            <div class="command-card"><span>Reported</span><strong><?php echo e($totals['reported']); ?></strong><p>Finding drafted or exported.</p></div>
        </section>

        <section class="lab-panel">
            <h2>Attack Scoreboard</h2>
            <table>
                <thead><tr><th>Attack</th><th>Tier</th><th>OWASP</th><th>Attempted</th><th>Exploited</th><th>Reported</th><th>Open</th></tr></thead>
                <tbody>
                    <?php foreach ($attacks as $id => $attack): ?>
                        <tr>
                            <td><?php echo e($attack['title']); ?></td>
                            <td><?php echo e($attack['tier']); ?></td>
                            <td><?php echo e($attack['owasp']); ?></td>
                            <td><?php echo !empty($progress[$id]['attempted']) ? 'Yes' : 'No'; ?></td>
                            <td><?php echo !empty($progress[$id]['exploited']) ? 'Yes' : 'No'; ?></td>
                            <td><?php echo !empty($progress[$id]['reported']) ? 'Yes' : 'No'; ?></td>
                            <td><a href="attack_detail.php?attack=<?php echo e($id); ?>">Open</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Progress tracker</p></footer>
</body>
</html>
