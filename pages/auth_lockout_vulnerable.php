<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$username = (string) ($_POST['username'] ?? 'admin');
$password = (string) ($_POST['password'] ?? '');
$mode = (string) ($_POST['mode'] ?? 'vulnerable');
$message = '';

if (!isset($_SESSION['secure_attempts'])) $_SESSION['secure_attempts'] = array();
if (!isset($_SESSION['secure_lockouts'])) $_SESSION['secure_lockouts'] = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('SELECT username, password, full_name FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $md5 = md5($password);

    if ($mode === 'vulnerable') {
        $ok = $user && hash_equals((string) $user['password'], $md5);
        $message = $ok ? 'Vulnerable login succeeded.' : 'Vulnerable login failed. No rate limit, no lockout, try again immediately.';
        recordAttackEvent('A07', 'No-lockout login attempt', $username, $ok ? 'success' : 'failed');
        if (!$ok) captureFlag('FLAG{bruteforce_no_lockout_001}', 'Repeated vulnerable login attempts are never locked out');
    } else {
        $now = time();
        $locked_until = $_SESSION['secure_lockouts'][$username] ?? 0;
        if ($locked_until > $now) {
            $message = 'Secure login blocked. Account locked until ' . date('H:i:s', $locked_until) . '.';
        } else {
            $ok = $user && hash_equals((string) $user['password'], $md5);
            if ($ok) {
                $_SESSION['secure_attempts'][$username] = 0;
                $message = 'Secure login succeeded and cleared failed attempt counter.';
            } else {
                $_SESSION['secure_attempts'][$username] = ($_SESSION['secure_attempts'][$username] ?? 0) + 1;
                if ($_SESSION['secure_attempts'][$username] >= 5) {
                    $_SESSION['secure_lockouts'][$username] = $now + 900;
                    $message = 'Secure login locked this account for 15 minutes after 5 failures.';
                } else {
                    $message = 'Secure login failed. Attempt ' . $_SESSION['secure_attempts'][$username] . ' of 5.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Authentication Lockout Demo'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A07 Demo</div><h1>Brute Force Without Lockout</h1><p>Compare unlimited login attempts against a simple 15-minute lockout control.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <?php if ($message): ?><section class="alert alert-info"><?php echo e($message); ?></section><?php endif; ?>
        <?php if (isset($_SESSION['captured_flags']['FLAG{bruteforce_no_lockout_001}'])) renderFlag('FLAG{bruteforce_no_lockout_001}'); ?>
        <section class="lab-panel split-compare">
            <div><h2>Vulnerable Login</h2><form method="POST"><input type="hidden" name="mode" value="vulnerable"><div class="form-group"><label>Username</label><input type="text" name="username" value="<?php echo e($username); ?>"></div><div class="form-group"><label>Password</label><input type="password" name="password"></div><button type="submit">Try Without Lockout</button></form></div>
            <div><h2>Secure Login</h2><form method="POST"><input type="hidden" name="mode" value="secure"><div class="form-group"><label>Username</label><input type="text" name="username" value="<?php echo e($username); ?>"></div><div class="form-group"><label>Password</label><input type="password" name="password"></div><button type="submit">Try With Lockout</button></form></div>
        </section>
        <section class="lab-panel"><h2>Code Diff</h2><?php renderCodeCompare('if ($password_ok) login();
// no counter, no lockout, no MFA', 'if ($failures >= 5) {
    $locked_until = time() + 900;
}
// generic errors, MFA, monitoring'); ?></section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Auth lockout lab</p></footer>
</body>
</html>
