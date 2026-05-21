<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$url = (string) ($_POST['url'] ?? 'http://localhost/admin');
$body = '';
$secure_result = '';

function isPrivateUrl(string $url): bool {
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) return true;
    $ip = gethostbyname($host);
    return $host === 'localhost' || strpos($ip, '127.') === 0 || strpos($ip, '10.') === 0 || strpos($ip, '192.168.') === 0 || preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip) || $ip === '169.254.169.254';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $context = stream_context_create(array('http' => array('timeout' => 2, 'ignore_errors' => true)));
    if (preg_match('#^https?://#i', $url)) {
        $body = @file_get_contents($url, false, $context);
        if ($body === false) {
            $body = "Request attempted by the server but no response was returned.\nTarget: " . $url;
        }
    } else {
        $body = 'Only http:// and https:// URLs are accepted by this demo fetcher.';
    }

    if (isPrivateUrl($url) || strpos($url, '169.254.169.254') !== false) {
        captureFlag('FLAG{ssrf_internal_fetch_001}', 'Server attempted to fetch an internal or metadata URL');
        recordAttackEvent('A10', 'SSRF fetch', $url, 'Internal target requested');
    }

    $secure_result = isPrivateUrl($url) ? 'Blocked by allowlist/private-range validation.' : 'Allowed external URL.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('SSRF Vulnerable'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A10 Demo</div><h1>Server-Side Request Forgery</h1><p>A resource preview tool makes server-side HTTP requests to attacker-controlled URLs.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Fetch External Resource</h2>
                <form method="POST"><div class="form-group"><label>URL</label><input type="text" name="url" value="<?php echo e($url); ?>"></div><button type="submit">Fetch From Server</button></form>
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="vulnerability-box"><strong>Server requested:</strong> <code><?php echo e($url); ?></code></div>
                    <div class="code-block"><code><?php echo nl2br(e(substr((string) $body, 0, 4000))); ?></code></div>
                    <?php if (isset($_SESSION['captured_flags']['FLAG{ssrf_internal_fetch_001}'])) renderFlag('FLAG{ssrf_internal_fetch_001}'); ?>
                    <div class="security-box"><strong>Secure version:</strong> <?php echo e($secure_result); ?></div>
                <?php endif; ?>
            </section>
            <section class="lab-panel"><h2>Secure Pattern</h2><?php renderCodeCompare('$response = file_get_contents($_POST["url"]);', 'if (!in_array(parse_url($url, PHP_URL_HOST), $allowedHosts, true)) {
    exit("Blocked");
}
if (isPrivateIp(gethostbyname($host))) {
    exit("Blocked");
}'); ?></section>
            <?php renderRequestInspector('pages/ssrf_vulnerable.php'); ?>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>SSRF Targets</h3><button type="button" class="payload-chip" data-copy="http://localhost/admin">http://localhost/admin</button><button type="button" class="payload-chip" data-copy="http://127.0.0.1:3306">http://127.0.0.1:3306</button><button type="button" class="payload-chip" data-copy="http://169.254.169.254/latest/meta-data/">metadata endpoint pattern</button><button type="button" class="payload-chip" data-copy="curl -d url=http://localhost/admin http://localhost/pages/ssrf_vulnerable.php">curl SSRF test</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | SSRF lab</p></footer>
</body>
</html>
