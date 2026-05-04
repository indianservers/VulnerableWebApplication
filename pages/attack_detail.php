<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/attack_config.php';
require_once '../includes/lab_state.php';

$attack_id = $_GET['attack'] ?? 'sqli-auth-bypass';
$attack = attackScenario($attack_id);
if (!$attack) {
    $attack_id = 'sqli-auth-bypass';
    $attack = attackScenario($attack_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateAttackProgress($attack_id, $_POST['status'] ?? 'attempted');
}

$progress = labProgress();
$target_href = preg_match('/^pages\//', $attack['target']) ? '../' . $attack['target'] : $attack['target'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead($attack['title']); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker"><?php echo e($attack['tier']); ?> Attacker Learner</div>
            <h1><?php echo e($attack['title']); ?></h1>
            <p><?php echo e($attack['story']); ?></p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel attack-card">
                <div>
                    <span class="section-kicker"><?php echo e($attack['owasp']); ?></span>
                    <h2><?php echo e($attack['goal']); ?></h2>
                    <p>Success condition: <?php echo e($attack['success']); ?></p>
                </div>
                <div class="lab-actions">
                    <a class="btn-primary" href="<?php echo e($target_href); ?>">Open Target</a>
                    <a class="btn-secondary-link" href="attack_hints.php?attack=<?php echo e($attack_id); ?>">Open Hints Page</a>
                    <a class="btn-secondary-link" href="walkthrough.php?attack=<?php echo e($attack_id); ?>">Walkthrough Mode</a>
                </div>
            </section>

            <section class="lab-panel">
                <h2>Progress Controls</h2>
                <div class="progress-actions">
                    <form method="POST"><button type="submit" name="status" value="attempted">Mark Attempted</button></form>
                    <form method="POST"><button type="submit" name="status" value="exploited">Mark Exploited</button></form>
                    <form method="POST"><button type="submit" name="status" value="reported">Mark Reported</button></form>
                </div>
                <div class="tile-meta">
                    <span>Attempted: <?php echo !empty($progress[$attack_id]['attempted']) ? 'Yes' : 'No'; ?></span>
                    <span>Exploited: <?php echo !empty($progress[$attack_id]['exploited']) ? 'Yes' : 'No'; ?></span>
                    <span>Reported: <?php echo !empty($progress[$attack_id]['reported']) ? 'Yes' : 'No'; ?></span>
                </div>
            </section>

            <section class="lab-panel">
                <h2>Attack Steps</h2>
                <div class="timeline">
                    <?php foreach ($attack['steps'] as $index => $step): ?>
                        <div>
                            <strong>Step <?php echo e($index + 1); ?></strong>
                            <span><?php echo e($step); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="lab-panel">
                <h2>Evidence Worksheet</h2>
                <div class="column-2">
                    <div class="form-group">
                        <label>Target URL</label>
                        <input type="text" value="<?php echo e($attack['target']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Observed Result</label>
                        <input type="text" placeholder="<?php echo e($attack['success']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea rows="7" placeholder="Record payload, account/record touched, before-after proof, and what made the attack possible."></textarea>
                </div>
            </section>

            <?php renderRequestInspector($attack['target']); ?>
        </div>

        <div class="lab-side">
            <aside class="payload-drawer">
                <h3>Payloads</h3>
                <p>Use these in the lab target, then explain why they worked.</p>
                <?php foreach ($attack['payloads'] as $payload): ?>
                    <button type="button" class="payload-chip" data-copy="<?php echo e($payload); ?>"><?php echo e($payload); ?></button>
                <?php endforeach; ?>
            </aside>

            <section class="lab-panel">
                <h2>Skills</h2>
                <div class="tile-meta">
                    <?php foreach ($attack['skills'] as $skill): ?>
                        <span><?php echo e($skill); ?></span>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | <?php echo e($attack['title']); ?></p>
    </footer>
</body>
</html>
