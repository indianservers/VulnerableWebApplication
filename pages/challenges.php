<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/attack_config.php';
require_once '../includes/challenge_config.php';

$challenges = labChallenges();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Scenario Challenges'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Scenario-Based Practice</div>
            <h1>Challenge Missions</h1>
            <p>Role-based exercises that turn the vulnerable school portal into realistic pentest tasks.</p>
        </div>
    </header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <section class="attack-grid">
            <?php foreach ($challenges as $challenge): ?>
                <?php $attack = attackScenario($challenge['attack']); ?>
                <article class="attack-card-small challenge-card">
                    <span class="card-tag"><?php echo e($challenge['difficulty'] . ' | ' . $challenge['persona']); ?></span>
                    <h4><?php echo e($challenge['title']); ?></h4>
                    <p><?php echo e($challenge['mission']); ?></p>
                    <div class="result-banner"><?php echo e($challenge['win']); ?></div>
                    <div class="attack-links">
                        <a href="attack_detail.php?attack=<?php echo e($challenge['attack']); ?>">Start</a>
                        <a href="walkthrough.php?attack=<?php echo e($challenge['attack']); ?>">Walkthrough</a>
                        <a href="attack_hints.php?attack=<?php echo e($challenge['attack']); ?>">Hints</a>
                    </div>
                    <?php if ($attack): ?><small><?php echo e($attack['owasp']); ?></small><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Challenges</p></footer>
</body>
</html>
