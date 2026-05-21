<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$password = (string) ($_POST['password'] ?? 'student123');
$secret = (string) ($_POST['secret'] ?? '123-45-6789');
$md5 = md5($password);
$bcrypt = password_hash($password, PASSWORD_DEFAULT);
$verified = password_verify($password, $bcrypt);
$base64 = base64_encode($secret);
$key = hash('sha256', 'lab-demo-key', true);
$iv = random_bytes(16);
$ciphertext = openssl_encrypt($secret, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
$aes = base64_encode($iv . $ciphertext);
$plain = openssl_decrypt(substr(base64_decode($aes), 16), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr(base64_decode($aes), 0, 16));
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Secure Crypto Comparison'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A02 Secure Pattern</div><h1>Cryptography: Weak vs Secure</h1><p>Compare MD5 and Base64 against password hashing and AES-256 encryption.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <section class="lab-panel">
            <form method="POST" class="split-compare">
                <div class="form-group"><label>Password</label><input type="text" name="password" value="<?php echo e($password); ?>"></div>
                <div class="form-group"><label>Secret Value</label><input type="text" name="secret" value="<?php echo e($secret); ?>"></div>
                <button type="submit">Compare</button>
            </form>
        </section>
        <section class="lab-panel">
            <h2>Password Storage</h2>
            <div class="split-compare">
                <div><h3>MD5</h3><div class="vulnerability-box">Fast, unsalted, crackable.</div><div class="code-block"><code><?php echo e($md5); ?></code></div></div>
                <div><h3>bcrypt</h3><div class="security-box">Slow adaptive hash with salt.</div><div class="code-block"><code><?php echo e($bcrypt); ?></code></div><p>Verification: <strong><?php echo $verified ? 'passed' : 'failed'; ?></strong></p></div>
            </div>
            <?php renderCodeCompare('$hash = md5($password);', '$hash = password_hash($password, PASSWORD_DEFAULT);
$ok = password_verify($password, $hash);'); ?>
        </section>
        <section class="lab-panel">
            <h2>Data Protection</h2>
            <div class="split-compare">
                <div><h3>Base64</h3><div class="vulnerability-box">Encoding only; reversible by anyone.</div><div class="code-block"><code><?php echo e($base64); ?></code></div></div>
                <div><h3>AES-256-CBC Demo</h3><div class="security-box">Encrypted with a key and IV for this lab comparison.</div><div class="code-block"><code><?php echo e($aes); ?></code></div><p>Decrypted server-side: <code><?php echo e($plain); ?></code></p></div>
            </div>
        </section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Secure crypto lab</p></footer>
</body>
</html>
