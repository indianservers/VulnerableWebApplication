<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$base_reports = realpath(__DIR__ . '/../storage/reports');
$base_logs = realpath(__DIR__ . '/../storage/logs');
if (!$base_reports) { mkdir(__DIR__ . '/../storage/reports', 0777, true); file_put_contents(__DIR__ . '/../storage/reports/student1.pdf', "Demo report for student 1\n"); $base_reports = realpath(__DIR__ . '/../storage/reports'); }
if (!$base_logs) { mkdir(__DIR__ . '/../storage/logs', 0777, true); file_put_contents(__DIR__ . '/../storage/logs/access.log', "127.0.0.1 GET /index.php\n"); $base_logs = realpath(__DIR__ . '/../storage/logs'); }

$file = (string) ($_GET['file'] ?? 'reports/student1.pdf');
$log = (string) ($_GET['log'] ?? 'access.log');
$download_content = '';
$log_content = '';

if (isset($_GET['file'])) {
    $path = __DIR__ . '/../storage/' . $file;
    $download_content = is_file($path) ? file_get_contents($path) : 'File not found or not readable: ' . $path;
    if (strpos($file, '..') !== false) {
        captureFlag('FLAG{path_traversal_report_001}', 'Traversed out of reports directory');
        recordAttackEvent('A01', 'Path traversal report download', $file, 'Traversal sequence detected');
    }
}

if (isset($_GET['log'])) {
    $path = __DIR__ . '/../storage/logs/' . $log;
    $log_content = is_file($path) ? file_get_contents($path) : 'Log not found or not readable: ' . $path;
    if (strpos($log, '..') !== false) {
        captureFlag('FLAG{path_traversal_log_002}', 'Traversed out of log directory');
        recordAttackEvent('A01', 'Path traversal log viewer', $log, 'Traversal sequence detected');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Path Traversal Vulnerable'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A01 Demo</div><h1>Path Traversal</h1><p>Report downloads and log viewers trust path parameters.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Download Student Report</h2>
                <form method="GET"><div class="form-group"><label>file</label><input type="text" name="file" value="<?php echo e($file); ?>"></div><button type="submit">Open Report</button></form>
                <?php if ($download_content): ?><div class="code-block"><code><?php echo nl2br(e(substr($download_content, 0, 3000))); ?></code></div><?php endif; ?>
                <?php if (isset($_SESSION['captured_flags']['FLAG{path_traversal_report_001}'])) renderFlag('FLAG{path_traversal_report_001}'); ?>
            </section>
            <section class="lab-panel">
                <h2>Log Viewer</h2>
                <form method="GET"><div class="form-group"><label>log</label><input type="text" name="log" value="<?php echo e($log); ?>"></div><button type="submit">View Log</button></form>
                <?php if ($log_content): ?><div class="code-block"><code><?php echo nl2br(e(substr($log_content, 0, 3000))); ?></code></div><?php endif; ?>
                <?php if (isset($_SESSION['captured_flags']['FLAG{path_traversal_log_002}'])) renderFlag('FLAG{path_traversal_log_002}'); ?>
            </section>
            <section class="lab-panel"><h2>Secure Pattern</h2><?php renderCodeCompare('$path = "../storage/" . $_GET["file"];
readfile($path);', '$requested = basename($_GET["file"]);
$path = realpath($baseDir . "/" . $requested);
if (!$path || !str_starts_with($path, $baseDir)) {
    http_response_code(403);
    exit("Blocked");
}'); ?></section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Payloads</h3><button type="button" class="payload-chip" data-copy="?file=reports/student1.pdf">?file=reports/student1.pdf</button><button type="button" class="payload-chip" data-copy="?file=../includes/config.php">?file=../includes/config.php</button><button type="button" class="payload-chip" data-copy="?log=../../includes/config.php">?log=../../includes/config.php</button><button type="button" class="payload-chip" data-copy="?file=../../../etc/passwd">?file=../../../etc/passwd</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Path traversal lab</p></footer>
</body>
</html>
