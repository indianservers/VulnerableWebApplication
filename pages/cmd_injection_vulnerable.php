<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$host = (string) ($_POST['host'] ?? '127.0.0.1');
$filename = (string) ($_POST['filename'] ?? 'README.md');
$ping_output = '';
$cat_output = '';
$secure_ping = '';
$secure_cat = '';
$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
$ping_flag = $is_windows ? '-n 1' : '-c 1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['tool'] ?? '') === 'ping') {
    $cmd = 'ping ' . $ping_flag . ' ' . $host;
    $ping_output = shell_exec($cmd . ' 2>&1') ?? 'No command output returned.';
    $safe_cmd = 'ping ' . $ping_flag . ' ' . escapeshellarg($host);
    $secure_ping = shell_exec($safe_cmd . ' 2>&1') ?? 'No command output returned.';
    if (preg_match('/(;|&&|\||`|\$\(|& whoami|; whoami)/i', $host)) {
        captureFlag('FLAG{command_injection_ping_001}', 'Injected OS command through ping host field');
        recordAttackEvent('A03', 'Command injection ping', $host, 'Shell metacharacter detected');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['tool'] ?? '') === 'reader') {
    $cmd = ($is_windows ? 'type ' : 'cat ') . $filename;
    $cat_output = shell_exec($cmd . ' 2>&1') ?? 'No command output returned.';
    $safe_file = basename($filename);
    $safe_cmd = ($is_windows ? 'type ' : 'cat ') . escapeshellarg($safe_file);
    $secure_cat = shell_exec($safe_cmd . ' 2>&1') ?? 'No command output returned.';
    if (strpos($filename, '..') !== false || preg_match('/(;|&&|\||`|\$\()/i', $filename)) {
        captureFlag('FLAG{command_injection_file_reader_002}', 'Abused file reader command input');
        recordAttackEvent('A03', 'Command injection file reader', $filename, 'Traversal or shell metacharacter detected');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Command Injection Vulnerable'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A03 Demo</div><h1>Command Injection</h1><p>School IT tools concatenate user input into operating system commands.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Network Ping Tool</h2>
                <form method="POST">
                    <input type="hidden" name="tool" value="ping">
                    <div class="form-group"><label>Host</label><input type="text" name="host" value="<?php echo e($host); ?>" placeholder="127.0.0.1; whoami"></div>
                    <button type="submit">Run Ping</button>
                </form>
                <?php if ($ping_output): ?>
                    <div class="vulnerability-box"><strong>Executed:</strong> <code>ping <?php echo e($ping_flag . ' ' . $host); ?></code></div>
                    <div class="code-block"><code><?php echo nl2br(e(substr($ping_output, 0, 4000))); ?></code></div>
                    <?php if (isset($_SESSION['captured_flags']['FLAG{command_injection_ping_001}'])) renderFlag('FLAG{command_injection_ping_001}'); ?>
                <?php endif; ?>
            </section>
            <section class="lab-panel">
                <h2>File Reader</h2>
                <form method="POST">
                    <input type="hidden" name="tool" value="reader">
                    <div class="form-group"><label>Filename</label><input type="text" name="filename" value="<?php echo e($filename); ?>" placeholder="../../etc/passwd; whoami"></div>
                    <button type="submit">Read File</button>
                </form>
                <?php if ($cat_output): ?>
                    <div class="vulnerability-box"><strong>Executed:</strong> <code><?php echo e(($is_windows ? 'type ' : 'cat ') . $filename); ?></code></div>
                    <div class="code-block"><code><?php echo nl2br(e(substr($cat_output, 0, 4000))); ?></code></div>
                    <?php if (isset($_SESSION['captured_flags']['FLAG{command_injection_file_reader_002}'])) renderFlag('FLAG{command_injection_file_reader_002}'); ?>
                <?php endif; ?>
            </section>
            <section class="lab-panel">
                <h2>Secure Version</h2>
                <?php renderCodeCompare('$output = shell_exec("ping -c 1 " . $_POST["host"]);
$file = shell_exec("cat " . $_POST["filename"]);', '$output = shell_exec("ping -c 1 " . escapeshellarg($_POST["host"]));
$file = shell_exec("cat " . escapeshellarg(basename($_POST["filename"])));'); ?>
                <?php if ($secure_ping): ?><h3>Escaped Ping Output</h3><div class="code-block"><code><?php echo nl2br(e(substr($secure_ping, 0, 1500))); ?></code></div><?php endif; ?>
                <?php if ($secure_cat): ?><h3>Escaped Reader Output</h3><div class="code-block"><code><?php echo nl2br(e(substr($secure_cat, 0, 1500))); ?></code></div><?php endif; ?>
            </section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Payloads</h3><button type="button" class="payload-chip" data-copy="127.0.0.1; whoami">127.0.0.1; whoami</button><button type="button" class="payload-chip" data-copy="127.0.0.1 && whoami">127.0.0.1 && whoami</button><button type="button" class="payload-chip" data-copy="../../etc/passwd">../../etc/passwd</button><button type="button" class="payload-chip" data-copy="README.md; whoami">README.md; whoami</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Command injection lab</p></footer>
</body>
</html>
