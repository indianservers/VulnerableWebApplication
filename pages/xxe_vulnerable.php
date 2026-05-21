<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$xml = (string) ($_POST['xml'] ?? "<?xml version=\"1.0\"?>\n<student><name>Asha Rao</name><roll>R-1001</roll></student>");
$vulnerable_output = '';
$secure_output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->resolveExternals = true;
    $dom->substituteEntities = true;
    if (@$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD)) {
        $vulnerable_output = $dom->textContent;
        if (stripos($xml, '<!DOCTYPE') !== false && (stripos($xml, 'file://') !== false || stripos($xml, 'SYSTEM') !== false)) {
            captureFlag('FLAG{xxe_file_read_001}', 'XML parser expanded an external entity');
            recordAttackEvent('A03', 'XXE student import', $xml, 'External entity processed');
        }
    } else {
        $vulnerable_output = 'XML parse error: ' . implode('; ', array_map(function ($e) { return trim($e->message); }, libxml_get_errors()));
    }

    $safe = new DOMDocument();
    if (stripos($xml, '<!DOCTYPE') !== false) {
        $secure_output = 'Secure parser rejected XML containing a DOCTYPE declaration.';
    } elseif (@$safe->loadXML($xml, LIBXML_NONET)) {
        $secure_output = $safe->textContent;
    } else {
        $secure_output = 'Secure parser rejected malformed XML.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('XXE Vulnerable'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A03 Extension</div><h1>XXE Student Import</h1><p>The vulnerable parser expands external entities from uploaded XML.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Import XML</h2>
                <form method="POST"><div class="form-group"><label>XML</label><textarea name="xml" rows="10"><?php echo e($xml); ?></textarea></div><button type="submit">Process XML</button></form>
            </section>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <section class="lab-panel split-compare">
                    <div><h3>Vulnerable Parser Output</h3><div class="code-block"><code><?php echo nl2br(e(substr($vulnerable_output, 0, 3000))); ?></code></div><?php if (isset($_SESSION['captured_flags']['FLAG{xxe_file_read_001}'])) renderFlag('FLAG{xxe_file_read_001}'); ?></div>
                    <div><h3>Secure Parser Output</h3><div class="code-block"><code><?php echo nl2br(e($secure_output)); ?></code></div></div>
                </section>
            <?php endif; ?>
            <section class="lab-panel"><h2>Code Diff</h2><?php renderCodeCompare('$dom->loadXML($xml, LIBXML_NOENT | LIBXML_DTDLOAD);', 'if (stripos($xml, "<!DOCTYPE") !== false) exit("Rejected");
$dom->loadXML($xml, LIBXML_NONET);'); ?></section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Payload</h3><button type="button" class="payload-chip" data-copy="<?php echo e('<?xml version="1.0"?><!DOCTYPE student [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><student><name>&xxe;</name></student>'); ?>">file:///etc/passwd entity</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | XXE lab</p></footer>
</body>
</html>
