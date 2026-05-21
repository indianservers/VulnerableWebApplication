<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$error = '';
$headers = array(
    'Content-Security-Policy' => 'Missing',
    'Strict-Transport-Security' => 'Missing',
    'X-Frame-Options' => 'Missing',
    'X-Content-Type-Options' => 'Missing'
);

if (isset($_GET['break'])) {
    try {
        $conn->query("SELECT * FROM users WHERE id = " . $_GET['break']);
    } catch (Throwable $e) {
        $error = $e->getMessage() . "\nDB_HOST=" . DB_HOST . "\nDB_USER=" . DB_USER . "\nDB_PASS=" . DB_PASS . "\n" . $e->getTraceAsString();
        captureFlag('FLAG{verbose_error_leak_001}', 'Triggered verbose SQL error with configuration disclosure');
        recordAttackEvent('A05', 'Verbose error disclosure', (string) $_GET['break'], 'Exception displayed');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Security Misconfiguration'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A05 Demo</div><h1>Security Misconfiguration</h1><p>Verbose errors, missing headers, and exposed backup files.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Broken Query Error Disclosure</h2>
                <p><a class="btn-primary" href="security_misconfig.php?break='">Trigger Broken Query</a> <a class="btn-secondary-link" href="../includes/config.php.bak">Download Exposed Backup</a></p>
                <?php if ($error): ?><div class="code-block"><code><?php echo nl2br(e($error)); ?></code></div><?php renderFlag('FLAG{verbose_error_leak_001}'); endif; ?>
            </section>
            <section class="lab-panel">
                <h2>Missing Security Headers</h2>
                <table><thead><tr><th>Header</th><th>Status</th></tr></thead><tbody><?php foreach ($headers as $name => $status): ?><tr><td><code><?php echo e($name); ?></code></td><td><span class="badge badge-danger"><?php echo e($status); ?></span></td></tr><?php endforeach; ?></tbody></table>
            </section>
            <section class="lab-panel"><h2>Secure Configuration</h2><?php renderCodeCompare('display_errors = On
die($e->getMessage() . DB_PASS);', 'display_errors = Off
error_log($e);
header("Content-Security-Policy: default-src self");
header("X-Frame-Options: DENY");'); ?></section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Checks</h3><button type="button" class="payload-chip" data-copy="security_misconfig.php?break='">SQL syntax error</button><button type="button" class="payload-chip" data-copy="../includes/config.php.bak">config.php.bak</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Misconfiguration lab</p></footer>
</body>
</html>
