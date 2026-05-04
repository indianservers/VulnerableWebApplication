<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/attack_config.php';

$tiers = attacksByTier();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Attack Learning Paths'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Learner Tracks</div>
            <h1>Attack Learning Paths</h1>
            <p>Practice 5 attack types across beginner, intermediate, and advanced tracks.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell">
        <section class="lab-command-grid">
            <div class="command-card">
                <span>Beginner</span>
                <strong>Observe and Trigger</strong>
                <p>Use visible payloads and learn what proof looks like.</p>
            </div>
            <div class="command-card">
                <span>Intermediate</span>
                <strong>Tamper and Compare</strong>
                <p>Change IDs and trusted fields to understand authorization gaps.</p>
            </div>
            <div class="command-card">
                <span>Advanced</span>
                <strong>Chain and Extract</strong>
                <p>Reason about query shape, data mapping, and impact.</p>
            </div>
        </section>

        <?php foreach ($tiers as $tier => $attacks): ?>
            <section class="feature-section">
                <div class="section-heading">
                    <span class="section-kicker"><?php echo e($tier); ?> Attacker Learner</span>
                    <h3><?php echo e($tier); ?> attack scenarios</h3>
                </div>
                <div class="attack-grid">
                    <?php foreach ($attacks as $id => $attack): ?>
                        <article class="attack-card-small">
                            <span class="card-tag"><?php echo e($attack['owasp']); ?></span>
                            <h4><?php echo e($attack['title']); ?></h4>
                            <p><?php echo e($attack['goal']); ?></p>
                            <div class="tile-meta">
                                <?php foreach ($attack['skills'] as $skill): ?>
                                    <span><?php echo e($skill); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="attack-links">
                                <a href="attack_detail.php?attack=<?php echo e($id); ?>">Start Attack</a>
                                <a href="attack_hints.php?attack=<?php echo e($id); ?>">Hints</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | Attack learning paths</p>
    </footer>
</body>
</html>
