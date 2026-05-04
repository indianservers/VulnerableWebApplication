<?php
require_once __DIR__ . '/lab_config.php';

function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function labAssetPath(string $path): string {
    return (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/pages/') !== false) ? '../' . $path : $path;
}

function renderLabHead(string $title): void {
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . e($title) . ' - OWASP Pentest Lab</title>';
    echo '<link rel="stylesheet" href="' . e(labAssetPath('css/style.css')) . '">';
    echo '<script defer src="' . e(labAssetPath('js/lab.js')) . '"></script>';
}

function renderLabNav(): void {
    $base = labAssetPath('');
    echo '<nav class="lab-top-nav">';
    echo '<a href="' . e($base . 'index.php') . '">Portal</a>';
    echo '<a href="' . e($base . 'pages/owasp_lab.php') . '">OWASP Labs</a>';
    echo '<a href="' . e($base . 'pages/attack_paths.php') . '">Attack Paths</a>';
    echo '<a href="' . e($base . 'pages/challenges.php') . '">Challenges</a>';
    echo '<a href="' . e($base . 'pages/progress.php') . '">Progress</a>';
    echo '<a href="' . e($base . 'pages/lab_reset.php') . '">Reset</a>';
    echo '<a href="' . e($base . 'pages/report_builder.php') . '">Reports</a>';
    echo '<a href="' . e($base . 'pages/instructor_mode.php') . '">Instructor</a>';
    echo '<a href="' . e($base . 'pages/login_vulnerable.php') . '">SQLi Login</a>';
    echo '</nav>';
}

function renderMissionTile(string $id, array $lab): void {
    $href = labAssetPath('pages/lab_scenario.php?cat=' . urlencode($id));
    echo '<article class="owasp-tile" style="--lab-color:' . e($lab['color']) . '">';
    echo '<div class="tile-code">' . e($id) . '</div>';
    echo '<div class="tile-status">Not Tried</div>';
    echo '<h3>' . e($lab['title']) . '</h3>';
    echo '<p>' . e($lab['objective']) . '</p>';
    echo '<div class="tile-meta"><span>' . e($lab['risk']) . '</span><span>' . e($lab['difficulty']) . '</span></div>';
    echo '<a href="' . e($href) . '">Open Lab</a>';
    echo '</article>';
}

function renderLabHeader(string $id, array $lab): void {
    echo '<section class="lab-hero" style="--lab-color:' . e($lab['color']) . '">';
    echo '<div>';
    echo '<div class="lab-kicker">Intentional Vulnerability Simulator</div>';
    echo '<h1>' . e($id . ': ' . $lab['title']) . '</h1>';
    echo '<p>' . e($lab['scenario']) . '</p>';
    echo '<div class="lab-badges"><span>' . e($lab['risk']) . '</span><span>' . e($lab['difficulty']) . '</span><span>OWASP Top 10</span></div>';
    echo '</div>';
    echo '<div class="lab-score-card">';
    echo '<strong>Mission Status</strong>';
    echo '<div class="progress-ring" aria-label="Lab progress">0%</div>';
    echo '<p>Use the payloads, capture evidence, then write the finding.</p>';
    echo '</div>';
    echo '</section>';
}

function renderPayloadDrawer(array $lab): void {
    echo '<aside class="payload-drawer">';
    echo '<h3>Payload Playground</h3>';
    echo '<p>Click a payload to copy it into your notebook.</p>';
    foreach ($lab['payloads'] as $payload) {
        echo '<button type="button" class="payload-chip" data-copy="' . e($payload) . '">' . e($payload) . '</button>';
    }
    echo '</aside>';
}

function renderNotebook(array $lab): void {
    echo '<section class="lab-panel notebook-panel">';
    echo '<h2>Pentester Notebook</h2>';
    echo '<div class="form-group"><label>Finding Title</label><input type="text" value="' . e($lab['title']) . ' in School Portal"></div>';
    echo '<div class="form-group"><label>Evidence</label><textarea rows="5" placeholder="' . e($lab['evidence']) . '"></textarea></div>';
    echo '<div class="form-group"><label>Remediation Note</label><textarea rows="4">' . e($lab['remediation']) . '</textarea></div>';
    echo '<button type="button" class="btn-primary mark-complete">Mark Exploited</button>';
    echo '</section>';
}

function renderRequestInspector(?string $target = null): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $role = $_SESSION['role'] ?? 'guest';
    $query = $_GET;
    $post = $_POST;

    echo '<section class="lab-panel request-inspector">';
    echo '<h2>Live Request Inspector</h2>';
    echo '<div class="inspector-grid">';
    echo '<div><strong>Method</strong><span>' . e($method) . '</span></div>';
    echo '<div><strong>Current Role</strong><span>' . e($role) . '</span></div>';
    echo '<div><strong>URI</strong><span>' . e($uri) . '</span></div>';
    echo '<div><strong>Lab Target</strong><span>' . e($target ?? 'Current page') . '</span></div>';
    echo '</div>';
    echo '<div class="split-compare">';
    echo '<div><h3>GET Params</h3><div class="code-block"><code>' . e(json_encode($query, JSON_PRETTY_PRINT)) . '</code></div></div>';
    echo '<div><h3>POST Params</h3><div class="code-block"><code>' . e(json_encode($post, JSON_PRETTY_PRINT)) . '</code></div></div>';
    echo '</div>';
    echo '</section>';
}
?>
