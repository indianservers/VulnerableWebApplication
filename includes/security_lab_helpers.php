<?php
require_once __DIR__ . '/lab_ui.php';

function ensureLabRuntime(): void {
    if (!isset($_SESSION['captured_flags']) || !is_array($_SESSION['captured_flags'])) {
        $_SESSION['captured_flags'] = array();
    }
    if (!isset($_SESSION['attack_events']) || !is_array($_SESSION['attack_events'])) {
        $_SESSION['attack_events'] = array();
    }
}

function recordAttackEvent(string $category, string $action, string $payload = '', string $result = ''): void {
    ensureLabRuntime();
    $_SESSION['attack_events'][] = array(
        'time' => date('Y-m-d H:i:s'),
        'category' => $category,
        'action' => $action,
        'payload' => $payload,
        'result' => $result,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        'user' => $_SESSION['username'] ?? 'guest'
    );
    $_SESSION['attack_events'] = array_slice($_SESSION['attack_events'], -100);
}

function captureFlag(string $flag, string $reason): void {
    ensureLabRuntime();
    if (!isset($_SESSION['captured_flags'][$flag])) {
        $_SESSION['captured_flags'][$flag] = array(
            'flag' => $flag,
            'reason' => $reason,
            'time' => date('Y-m-d H:i:s')
        );
    }
}

function renderFlag(string $flag): void {
    echo '<div class="alert alert-success"><strong>Flag captured:</strong> <code>' . e($flag) . '</code></div>';
}

function renderCodeCompare(string $vulnerable, string $secure): void {
    echo '<div class="split-compare">';
    echo '<div><h3>Vulnerable Code</h3><div class="code-block"><code>' . nl2br(e($vulnerable)) . '</code></div></div>';
    echo '<div><h3>Secure Code</h3><div class="code-block"><code>' . nl2br(e($secure)) . '</code></div></div>';
    echo '</div>';
}
?>
