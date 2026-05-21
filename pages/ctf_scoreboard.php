<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';
ensureLabRuntime();
$flags = $_SESSION['captured_flags'];
$known = array(
    'FLAG{xss_reflected_search_001}',
    'FLAG{csrf_grade_forgery_001}',
    'FLAG{command_injection_ping_001}',
    'FLAG{command_injection_file_reader_002}',
    'FLAG{ssrf_internal_fetch_001}',
    'FLAG{unsafe_file_upload_001}',
    'FLAG{path_traversal_report_001}',
    'FLAG{path_traversal_log_002}',
    'FLAG{verbose_error_leak_001}',
    'FLAG{bruteforce_no_lockout_001}',
    'FLAG{missing_audit_trail_001}',
    'FLAG{xxe_file_read_001}',
    'FLAG{insecure_deserialization_admin_001}'
);
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('CTF Scoreboard'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">Capture The Flag</div><h1>Lab Scoreboard</h1><p>Flags are revealed only when an exploit path succeeds in this browser session.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <section class="lab-command-grid"><div class="command-card"><span>Captured</span><strong><?php echo count($flags); ?> / <?php echo count($known); ?></strong><p>Session-based progress for this lab run.</p></div></section>
        <section class="lab-panel"><table><thead><tr><th>Flag</th><th>Status</th><th>Reason</th><th>Time</th></tr></thead><tbody><?php foreach ($known as $flag): $row = $flags[$flag] ?? null; ?><tr><td><code><?php echo $row ? e($flag) : 'Hidden until captured'; ?></code></td><td><span class="badge <?php echo $row ? 'badge-success' : 'badge-warning'; ?>"><?php echo $row ? 'Captured' : 'Open'; ?></span></td><td><?php echo e($row['reason'] ?? 'Exploit the matching lab page.'); ?></td><td><?php echo e($row['time'] ?? '-'); ?></td></tr><?php endforeach; ?></tbody></table></section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | CTF scoreboard</p></footer>
</body>
</html>
