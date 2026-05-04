<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/attack_config.php';

$attack_id = $_GET['attack'] ?? 'sqli-auth-bypass';
$attack = attackScenario($attack_id) ?: attackScenario('sqli-auth-bypass');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead($attack['title'] . ' Walkthrough'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Beginner Walkthrough Mode</div>
            <h1><?php echo e($attack['title']); ?></h1>
            <p>Step through the lab slowly, capture proof, and then repeat it without hints.</p>
        </div>
    </header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <section class="lab-panel">
            <h2>Guided Steps</h2>
            <div class="walkthrough-steps">
                <?php foreach ($attack['steps'] as $index => $step): ?>
                    <details class="hint-card" <?php echo $index === 0 ? 'open' : ''; ?>>
                        <summary>Step <?php echo e($index + 1); ?></summary>
                        <p><?php echo e($step); ?></p>
                        <?php if (isset($attack['hints'][$index])): ?>
                            <div class="security-box"><strong>Hint:</strong> <?php echo e($attack['hints'][$index]); ?></div>
                        <?php endif; ?>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
        <?php renderRequestInspector($attack['target']); ?>
        <section class="lab-panel">
            <h2>Finish</h2>
            <p>When the success condition is met, mark progress from the attack detail page and draft the report.</p>
            <div class="attack-links">
                <a href="attack_detail.php?attack=<?php echo e($attack_id); ?>">Open Attack Detail</a>
                <a href="report_builder.php">Open Report Builder</a>
            </div>
        </section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Walkthrough</p></footer>
</body>
</html>
