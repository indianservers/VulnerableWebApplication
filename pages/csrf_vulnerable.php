<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

if (empty($_SESSION['csrf_demo_token'])) {
    $_SESSION['csrf_demo_token'] = bin2hex(random_bytes(16));
}

$message = '';
$secure_message = '';
$current_grade = $_SESSION['csrf_demo_grade'] ?? 'B';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'vulnerable') {
    $current_grade = (string) ($_POST['grade'] ?? 'A+');
    $_SESSION['csrf_demo_grade'] = $current_grade;
    $message = 'Vulnerable grade changed to ' . $current_grade . ' with no CSRF token check.';
    $flag = 'FLAG{csrf_grade_forgery_001}';
    captureFlag($flag, 'Grade changed through tokenless request');
    recordAttackEvent('A01', 'CSRF grade change', http_build_query($_POST), 'Grade changed');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'secure') {
    if (hash_equals($_SESSION['csrf_demo_token'], (string) ($_POST['csrf_token'] ?? ''))) {
        $_SESSION['csrf_secure_grade'] = (string) ($_POST['grade'] ?? 'A');
        $secure_message = 'Secure grade accepted after token validation.';
    } else {
        $secure_message = 'Secure grade rejected because the CSRF token was missing or invalid.';
    }
}

$attacker = ($_GET['attacker'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('CSRF Vulnerable'); ?></head>
<body<?php echo $attacker ? ' onload="document.getElementById(\'csrf-attack\').submit()"' : ''; ?>>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A01 Demo</div><h1>CSRF Grade Change</h1><p>A teacher workflow accepts state-changing POST requests with no anti-CSRF protection.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <?php if ($attacker): ?>
                <section class="lab-panel">
                    <h2>Attacker Auto-submit Page</h2>
                    <div class="vulnerability-box">This page silently posts a forged grade update as soon as it loads.</div>
                    <form id="csrf-attack" method="POST" action="csrf_vulnerable.php">
                        <input type="hidden" name="mode" value="vulnerable">
                        <input type="hidden" name="student_id" value="1">
                        <input type="hidden" name="grade" value="A+">
                    </form>
                    <p class="muted">If JavaScript is disabled, submit manually.</p>
                    <button form="csrf-attack" type="submit">Submit Forged Request</button>
                </section>
            <?php endif; ?>
            <section class="lab-panel">
                <h2>Side-by-side Demo</h2>
                <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php renderFlag('FLAG{csrf_grade_forgery_001}'); endif; ?>
                <?php if ($secure_message): ?><div class="alert alert-info"><?php echo e($secure_message); ?></div><?php endif; ?>
                <div class="split-compare">
                    <div>
                        <h3>Vulnerable Grade Form</h3>
                        <form method="POST">
                            <input type="hidden" name="mode" value="vulnerable">
                            <div class="form-group"><label>Student ID</label><input type="text" name="student_id" value="1"></div>
                            <div class="form-group"><label>New Grade</label><input type="text" name="grade" value="<?php echo e($current_grade); ?>"></div>
                            <button type="submit">Change Grade Without Token</button>
                        </form>
                    </div>
                    <div>
                        <h3>Secure Grade Form</h3>
                        <form method="POST">
                            <input type="hidden" name="mode" value="secure">
                            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_demo_token']); ?>">
                            <div class="form-group"><label>Student ID</label><input type="text" name="student_id" value="1"></div>
                            <div class="form-group"><label>New Grade</label><input type="text" name="grade" value="<?php echo e($_SESSION['csrf_secure_grade'] ?? 'B'); ?>"></div>
                            <button type="submit">Change Grade With Token</button>
                        </form>
                    </div>
                </div>
            </section>
            <section class="lab-panel">
                <h2>Code Diff</h2>
                <?php renderCodeCompare('$grade = $_POST["grade"];
updateGrade($studentId, $grade);', 'if (!hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
    http_response_code(403);
    exit("Invalid CSRF token");
}
updateGrade($studentId, $grade);'); ?>
            </section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Attack Links</h3><a class="btn-primary" href="csrf_vulnerable.php?attacker=1">Open attacker page</a><button type="button" class="payload-chip" data-copy="curl -X POST -d mode=vulnerable -d student_id=1 -d grade=A+ http://localhost/pages/csrf_vulnerable.php">curl forged POST</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | CSRF lab</p></footer>
</body>
</html>
