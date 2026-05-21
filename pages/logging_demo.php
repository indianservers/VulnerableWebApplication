<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';
ensureLabRuntime();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? 'grade_change');
    if (($_POST['mode'] ?? '') === 'vulnerable') {
        $message = 'Vulnerable operation completed with zero audit trail.';
        captureFlag('FLAG{missing_audit_trail_001}', 'Sensitive operation performed without logging');
    } else {
        recordAttackEvent('A09', 'Audited sensitive operation', $action, 'Audit row captured');
        $message = 'Secure operation completed and audit event was recorded.';
    }
}

$events = array_reverse($_SESSION['attack_events'] ?? array());
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Logging Demo'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A09 Demo</div><h1>Logging and Monitoring Comparison</h1><p>Sensitive workflows with and without an audit trail.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <?php if ($message): ?><section class="alert alert-info"><?php echo e($message); ?></section><?php endif; ?>
        <?php if (isset($_SESSION['captured_flags']['FLAG{missing_audit_trail_001}'])) renderFlag('FLAG{missing_audit_trail_001}'); ?>
        <section class="lab-panel split-compare">
            <div><h2>Vulnerable Operation</h2><form method="POST"><input type="hidden" name="mode" value="vulnerable"><input type="hidden" name="action" value="change_grade"><button type="submit">Change Grade Without Logging</button></form><div class="vulnerability-box">No user, IP, action, timestamp, or outcome is stored.</div></div>
            <div><h2>Secure Operation</h2><form method="POST"><input type="hidden" name="mode" value="secure"><input type="hidden" name="action" value="change_grade"><button type="submit">Change Grade With Audit Log</button></form><div class="security-box">Records user, action, IP, timestamp, and result in the lab audit trail.</div></div>
        </section>
        <section class="lab-panel">
            <h2>Audit Log Table</h2>
            <table><thead><tr><th>Time</th><th>User</th><th>IP</th><th>Category</th><th>Action</th><th>Payload</th><th>Result</th></tr></thead><tbody>
                <?php foreach ($events as $event): ?><tr><td><?php echo e($event['time']); ?></td><td><?php echo e($event['user']); ?></td><td><?php echo e($event['ip']); ?></td><td><?php echo e($event['category']); ?></td><td><?php echo e($event['action']); ?></td><td><code><?php echo e($event['payload']); ?></code></td><td><?php echo e($event['result']); ?></td></tr><?php endforeach; ?>
                <?php if (!$events): ?><tr><td colspan="7">No audited events yet.</td></tr><?php endif; ?>
            </tbody></table>
        </section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Logging lab</p></footer>
</body>
</html>
