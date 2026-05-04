<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';

$category_id = strtoupper($_GET['cat'] ?? 'A01');
$lab = labCategory($category_id);

if (!$lab) {
    $category_id = 'A01';
    $lab = labCategory($category_id);
}

$target = $lab['target'];
$target_href = preg_match('/^pages\//', $target) ? '../' . $target : $target;
$secure_href = preg_match('/^pages\//', $lab['secure_target']) ? '../' . $lab['secure_target'] : '../' . $lab['secure_target'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead($category_id . ' Lab'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">OWASP Guided Scenario</div>
            <h1><?php echo e($category_id . ': ' . $lab['title']); ?></h1>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <?php renderLabHeader($category_id, $lab); ?>

            <section class="lab-panel attack-card">
                <div>
                    <span class="section-kicker">Objective</span>
                    <h2><?php echo e($lab['objective']); ?></h2>
                    <p><?php echo e($lab['scenario']); ?></p>
                </div>
                <div class="lab-actions">
                    <a class="btn-primary" href="<?php echo e($target_href); ?>">Open Vulnerable Target</a>
                    <a class="btn-secondary-link" href="<?php echo e($secure_href); ?>">Compare Secure Pattern</a>
                </div>
            </section>

            <section class="lab-tabs">
                <button type="button" class="tab-button active" data-tab="exploit">Exploit</button>
                <button type="button" class="tab-button" data-tab="code">Code Lens</button>
                <button type="button" class="tab-button" data-tab="http">HTTP Inspector</button>
                <button type="button" class="tab-button" data-tab="evidence">Evidence</button>
                <button type="button" class="tab-button" data-tab="fix">Remediation</button>
            </section>

            <section class="lab-panel tab-panel active" id="tab-exploit">
                <h2>Attack Lab Card</h2>
                <div class="check-grid">
                    <label><input type="checkbox"> Identify input or identifier under attacker control</label>
                    <label><input type="checkbox"> Send baseline request</label>
                    <label><input type="checkbox"> Apply payload or tamper with request</label>
                    <label><input type="checkbox"> Confirm unauthorized result</label>
                </div>
                <div class="result-banner">Exploit result will appear in the target page. Capture the proof here.</div>
            </section>

            <section class="lab-panel tab-panel" id="tab-code">
                <h2>Code Lens</h2>
                <p>The line below is the teaching focus for this mission.</p>
                <div class="code-block"><code><?php echo e($lab['code']); ?></code></div>
                <div class="split-compare">
                    <div>
                        <h3>Vulnerable Pattern</h3>
                        <p>Trusts user-controlled data or workflow state.</p>
                    </div>
                    <div>
                        <h3>Secure Pattern</h3>
                        <p><?php echo e($lab['remediation']); ?></p>
                    </div>
                </div>
            </section>

            <section class="lab-panel tab-panel" id="tab-http">
                <h2>HTTP Inspector</h2>
                <div class="inspector-grid">
                    <div><strong>Method</strong><span>GET / POST</span></div>
                    <div><strong>Role</strong><span>student / teacher / admin / parent</span></div>
                    <div><strong>Target</strong><span><?php echo e($target); ?></span></div>
                    <div><strong>Session</strong><span><?php echo e($_SESSION['role'] ?? 'guest'); ?></span></div>
                </div>
                <textarea rows="6" placeholder="Paste raw request, changed parameter, cookie, or generated SQL here."></textarea>
            </section>

            <section class="lab-panel tab-panel" id="tab-evidence">
                <h2>Evidence Slots</h2>
                <div class="evidence-grid">
                    <div>Before Exploit</div>
                    <div>Payload Used</div>
                    <div>After Exploit</div>
                    <div>Business Impact</div>
                </div>
            </section>

            <section class="lab-panel tab-panel" id="tab-fix">
                <h2>Remediation</h2>
                <p><?php echo e($lab['remediation']); ?></p>
                <div class="risk-meter"><span style="width: 78%"></span></div>
                <p class="muted">Keep the vulnerable lab available, but document the secure design next to it.</p>
            </section>
        </div>

        <div class="lab-side">
            <?php renderPayloadDrawer($lab); ?>
            <?php renderNotebook($lab); ?>
            <section class="lab-panel role-switcher">
                <h2>Persona Switcher</h2>
                <button type="button">Student</button>
                <button type="button">Teacher</button>
                <button type="button">Admin</button>
                <button type="button">Parent</button>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | <?php echo e($category_id); ?> lab</p>
    </footer>
</body>
</html>
