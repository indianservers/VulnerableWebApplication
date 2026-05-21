<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$q = $_GET['q'] ?? '';
$flag = '';
if ($q !== '' && (stripos((string) $q, '<script') !== false || stripos((string) $q, 'onerror=') !== false || stripos((string) $q, 'onload=') !== false)) {
    $flag = 'FLAG{xss_reflected_search_001}';
    captureFlag($flag, 'Reflected XSS payload rendered in search results');
    recordAttackEvent('A03', 'Reflected XSS search', (string) $q, 'Payload reflected without encoding');
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('Reflected XSS'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A03 Demo</div><h1>Reflected XSS Search</h1><p>Search terms are echoed into the page without output encoding.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Vulnerable Student Search</h2>
                <div class="vulnerability-box"><strong>Unsafe behavior:</strong> the query string is rendered with <code>echo $_GET['q']</code>.</div>
                <form method="GET">
                    <div class="form-group"><label for="q">Search</label><input type="text" id="q" name="q" value="<?php echo e($q); ?>" placeholder="<script>alert('XSS')</script>"></div>
                    <button type="submit">Search</button>
                </form>
                <?php if ($q !== ''): ?>
                    <h3>Results</h3>
                    <div class="alert alert-warning">Search results for: <?php echo $q; ?></div>
                    <?php if ($flag) renderFlag($flag); ?>
                <?php endif; ?>
            </section>
            <section class="lab-panel">
                <h2>Secure Comparison</h2>
                <div class="security-box">The same query becomes harmless text when rendered with <code>htmlspecialchars()</code>.</div>
                <div class="alert alert-info">Search results for: <?php echo e($q); ?></div>
                <?php renderCodeCompare('echo "Search results for: " . $_GET["q"];', 'echo "Search results for: " . htmlspecialchars($_GET["q"], ENT_QUOTES, "UTF-8");'); ?>
            </section>
            <?php renderRequestInspector('pages/xss_reflected.php?q=<script>alert(1)</script>'); ?>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Payloads</h3><button type="button" class="payload-chip" data-copy="?q=<script>alert('XSS')</script>">?q=&lt;script&gt;alert('XSS')&lt;/script&gt;</button><button type="button" class="payload-chip" data-copy="?q=<img src=x onerror=alert(1)>">?q=&lt;img src=x onerror=alert(1)&gt;</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Reflected XSS lab</p></footer>
</body>
</html>
