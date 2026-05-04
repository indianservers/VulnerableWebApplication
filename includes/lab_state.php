<?php
require_once __DIR__ . '/attack_config.php';

function progressDefaults(): array {
    return array('attempted' => false, 'exploited' => false, 'reported' => false);
}

function labProgress(): array {
    if (!isset($_SESSION['lab_progress']) || !is_array($_SESSION['lab_progress'])) {
        $_SESSION['lab_progress'] = array();
    }

    foreach (attackScenarios() as $id => $attack) {
        if (!isset($_SESSION['lab_progress'][$id])) {
            $_SESSION['lab_progress'][$id] = progressDefaults();
        }
    }

    return $_SESSION['lab_progress'];
}

function updateAttackProgress(string $attackId, string $status): void {
    labProgress();
    if (!isset($_SESSION['lab_progress'][$attackId])) {
        $_SESSION['lab_progress'][$attackId] = progressDefaults();
    }

    if ($status === 'attempted') {
        $_SESSION['lab_progress'][$attackId]['attempted'] = true;
    }
    if ($status === 'exploited') {
        $_SESSION['lab_progress'][$attackId]['attempted'] = true;
        $_SESSION['lab_progress'][$attackId]['exploited'] = true;
    }
    if ($status === 'reported') {
        $_SESSION['lab_progress'][$attackId]['attempted'] = true;
        $_SESSION['lab_progress'][$attackId]['exploited'] = true;
        $_SESSION['lab_progress'][$attackId]['reported'] = true;
    }
}

function resetProgress(): void {
    $_SESSION['lab_progress'] = array();
}

function progressTotals(): array {
    $progress = labProgress();
    $totals = array('total' => count($progress), 'attempted' => 0, 'exploited' => 0, 'reported' => 0);

    foreach ($progress as $row) {
        foreach (array('attempted', 'exploited', 'reported') as $field) {
            if (!empty($row[$field])) {
                $totals[$field]++;
            }
        }
    }

    return $totals;
}
?>
