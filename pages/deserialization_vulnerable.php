<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

class LabProfile {
    public $username = 'student1';
    public $role = 'student';
    public $isAdmin = false;
}

$default = new LabProfile();
$cookie_value = $_COOKIE['lab_profile'] ?? base64_encode(serialize($default));
$profile = null;
$message = '';

if (isset($_GET['set_admin_cookie'])) {
    $admin = new LabProfile();
    $admin->username = 'student1';
    $admin->role = 'admin';
    $admin->isAdmin = true;
    setcookie('lab_profile', base64_encode(serialize($admin)), time() + 3600, '');
    header('Location: deserialization_vulnerable.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cookie_value = (string) ($_POST['serialized'] ?? $cookie_value);
    setcookie('lab_profile', $cookie_value, time() + 3600, '');
}

$raw = base64_decode((string) $cookie_value, true);
if ($raw !== false) {
    $profile = @unserialize($raw);
}
if ($profile instanceof LabProfile && ($profile->role === 'admin' || $profile->isAdmin)) {
    $message = 'Role changed to admin through user-controlled serialized data.';
    captureFlag('FLAG{insecure_deserialization_admin_001}', 'Serialized profile object granted admin role');
    recordAttackEvent('A08', 'Insecure deserialization', $raw, 'Admin role accepted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Insecure Deserialization'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A08 Extension</div><h1>Insecure Deserialization</h1><p>A trusted profile cookie is a PHP serialized object controlled by the user.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <?php if ($message): ?><section class="alert alert-success"><?php echo e($message); ?></section><?php renderFlag('FLAG{insecure_deserialization_admin_001}'); endif; ?>
            <section class="lab-panel">
                <h2>Serialized Profile Cookie</h2>
                <form method="POST"><div class="form-group"><label>Base64 serialized object</label><textarea name="serialized" rows="5"><?php echo e((string) $cookie_value); ?></textarea></div><button type="submit">Set Profile Cookie</button></form>
                <p><a class="btn-primary" href="deserialization_vulnerable.php?set_admin_cookie=1">Set Tampered Admin Cookie</a></p>
                <div class="code-block"><code><?php echo e($raw ?: 'No decoded object'); ?></code></div>
                <div class="alert alert-info">Current role: <strong><?php echo e($profile->role ?? 'unknown'); ?></strong></div>
            </section>
            <section class="lab-panel"><h2>Secure Pattern</h2><?php renderCodeCompare('$profile = unserialize(base64_decode($_COOKIE["lab_profile"]));
if ($profile->role === "admin") showAdminPanel();', '$profile = json_decode($signedCookie, true);
verify_hmac($cookie, $serverSecret);
$role = lookupRoleFromDatabase($userId);'); ?></section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Payload Idea</h3><p>Change <code>s:7:"student"</code> to <code>s:5:"admin"</code> or use the button to generate the tampered object.</p></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Deserialization lab</p></footer>
</body>
</html>
