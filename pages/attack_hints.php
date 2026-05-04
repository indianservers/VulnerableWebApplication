<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/attack_config.php';

$attack_id = $_GET['attack'] ?? 'sqli-auth-bypass';
$attack = attackScenario($attack_id);
if (!$attack) {
    $attack_id = 'sqli-auth-bypass';
    $attack = attackScenario($attack_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead($attack['title'] . ' Hints'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Staged Hints</div>
            <h1><?php echo e($attack['title']); ?> Hints</h1>
            <p>Reveal hints gradually. The last hint is intentionally close to the answer.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell">
        <section class="lab-panel attack-card">
            <div>
                <span class="section-kicker"><?php echo e($attack['tier'] . ' | ' . $attack['owasp']); ?></span>
                <h2><?php echo e($attack['goal']); ?></h2>
                <p><?php echo e($attack['story']); ?></p>
            </div>
            <div class="lab-actions">
                <a class="btn-primary" href="attack_detail.php?attack=<?php echo e($attack_id); ?>">Back to Attack</a>
                <a class="btn-secondary-link" href="attack_paths.php">All Paths</a>
            </div>
        </section>

        <section class="hint-list">
            <?php foreach ($attack['hints'] as $index => $hint): ?>
                <details class="hint-card">
                    <summary>Hint <?php echo e($index + 1); ?></summary>
                    <p><?php echo e($hint); ?></p>
                </details>
            <?php endforeach; ?>
        </section>

        <section class="lab-panel">
            <h2>Reflection Questions</h2>
            <div class="check-grid">
                <label><input type="checkbox"> What input or trust boundary failed?</label>
                <label><input type="checkbox"> What evidence proves impact?</label>
                <label><input type="checkbox"> Which OWASP category fits best?</label>
                <label><input type="checkbox"> What would the secure comparison do differently?</label>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | Hints</p>
    </footer>
</body>
</html>
