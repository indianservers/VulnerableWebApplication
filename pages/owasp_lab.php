<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';

$categories = labCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('OWASP Mission Control'); ?>
</head>
<body>
    <header class="lab-header">
        <div class="container">
            <div class="lab-kicker">Pentesting Simulator</div>
            <h1>OWASP Mission Control</h1>
            <p>Guided labs for the OWASP Top 10, wrapped around the intentionally vulnerable school portal.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell">
        <section class="lab-command-grid">
            <div class="command-card">
                <span>Coverage</span>
                <strong>10 / 10</strong>
                <p>Every OWASP Top 10 category has a mission page.</p>
            </div>
            <div class="command-card">
                <span>Mode</span>
                <strong>Vulnerable</strong>
                <p>Unsafe behavior is intentional for local lab practice.</p>
            </div>
            <div class="command-card">
                <span>Workflow</span>
                <strong>Exploit → Evidence → Report</strong>
                <p>Each lab includes payloads, notes, and remediation prompts.</p>
            </div>
            <div class="command-card">
                <span>Tracks</span>
                <strong>Beginner → Advanced</strong>
                <p><a href="attack_paths.php">Open the attack learning paths</a> for staged practice and hints.</p>
            </div>
        </section>

        <section class="feature-section">
            <div class="section-heading">
                <span class="section-kicker">OWASP Top 10</span>
                <h3>Choose a mission</h3>
            </div>
            <div class="owasp-grid">
                <?php foreach ($categories as $id => $lab): ?>
                    <?php renderMissionTile($id, $lab); ?>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="lab-panel">
            <h2>Recommended Path</h2>
            <div class="timeline">
                <div><strong>1. Recon</strong><span>Open the realistic portal page and identify user-controlled data.</span></div>
                <div><strong>2. Exploit</strong><span>Use the payload drawer or your own request manipulation.</span></div>
                <div><strong>3. Evidence</strong><span>Record exact URLs, payloads, affected records, and screenshots.</span></div>
                <div><strong>4. Remediate</strong><span>Compare the secure pattern without removing the vulnerable lab.</span></div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | OWASP pentesting simulation lab</p>
    </footer>
</body>
</html>
