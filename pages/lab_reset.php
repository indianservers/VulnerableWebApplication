<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';
require_once '../includes/lab_state.php';
require_once '../includes/security_lab_helpers.php';

$message = '';
$details = '';

function runResetSql(mysqli $conn, string $sql): void {
    if (!$conn->multi_query($sql)) {
        throw new RuntimeException($conn->error);
    }
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    if ($conn->errno) {
        throw new RuntimeException($conn->error);
    }
}

function resetCategoryRuntime(string $category): void {
    ensureLabRuntime();
    $_SESSION['attack_events'] = array_values(array_filter($_SESSION['attack_events'], function ($event) use ($category) {
        return ($event['category'] ?? '') !== $category;
    }));

    $category_flags = array(
        'A01' => array('FLAG{csrf_grade_forgery_001}', 'FLAG{path_traversal_report_001}', 'FLAG{path_traversal_log_002}'),
        'A02' => array(),
        'A03' => array('FLAG{command_injection_ping_001}', 'FLAG{command_injection_file_reader_002}', 'FLAG{xss_reflected_search_001}', 'FLAG{xxe_file_read_001}'),
        'A04' => array('FLAG{unsafe_file_upload_001}'),
        'A05' => array('FLAG{verbose_error_leak_001}'),
        'A07' => array('FLAG{bruteforce_no_lockout_001}'),
        'A08' => array('FLAG{insecure_deserialization_admin_001}'),
        'A09' => array('FLAG{missing_audit_trail_001}'),
        'A10' => array('FLAG{ssrf_internal_fetch_001}')
    );

    foreach ($category_flags[$category] ?? array() as $flag) {
        unset($_SESSION['captured_flags'][$flag]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reset = $_POST['reset'] ?? '';

    try {
        if ($reset === 'progress') {
            resetProgress();
            $_SESSION['captured_flags'] = array();
            $_SESSION['attack_events'] = array();
            $message = 'Progress tracker, captured flags, and attack events reset for this browser session.';
        } elseif ($reset === 'comments') {
            runResetSql($conn, "
                DELETE FROM comments;
                ALTER TABLE comments AUTO_INCREMENT = 1;
                INSERT INTO comments (name, email, message, status) VALUES
                ('John Doe', 'john@example.com', 'Great article! Very helpful for my studies.', 'approved'),
                ('Jane Smith', 'jane@example.com', 'Thanks for sharing this information.', 'approved'),
                ('Alex Johnson', 'alex@example.com', 'Could you provide more details?', 'pending');
            ");
            $message = 'Comments reset to sample lab data.';
        } elseif ($reset === 'grades') {
            runResetSql($conn, "
                DELETE FROM grades;
                ALTER TABLE grades AUTO_INCREMENT = 1;
                INSERT INTO grades (student_id, course_id, assignment_1, assignment_2, midterm, final_exam, total_marks, grade_letter, teacher_id) VALUES
                (1, 1, 4.5, 4.8, 85.0, 88.0, 86.5, 'A+', 2),
                (1, 2, 4.2, 4.5, 82.0, 85.0, 83.5, 'A', 2),
                (2, 1, 4.8, 4.9, 87.0, 90.0, 88.5, 'A+', 2),
                (2, 2, 4.5, 4.7, 84.0, 87.0, 85.5, 'A', 2),
                (3, 1, 4.0, 4.3, 80.0, 82.0, 81.0, 'B+', 2);
            ");
            $message = 'Grades reset to sample lab data.';
        } elseif ($reset === 'full') {
            initializeDatabaseIfNeeded($conn);
            $conn->select_db(DB_NAME);
            resetProgress();
            $_SESSION['captured_flags'] = array();
            $_SESSION['attack_events'] = array();
            $message = 'Full database, session progress, flags, and attack events reset.';
            $details = 'The bundled enhanced schema was reloaded.';
        } elseif ($reset === 'category') {
            $category = strtoupper((string) ($_POST['category'] ?? ''));
            resetCategoryRuntime($category);
            $message = $category . ' flags and attack events reset for this browser session.';
        } else {
            $message = 'Unknown reset option.';
        }
    } catch (Throwable $e) {
        $message = 'Reset failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Lab Reset Console'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Instructor Utility</div>
            <h1>Lab Reset Console</h1>
            <p>Restore intentionally vulnerable lab data after comments, grades, or challenge progress are changed.</p>
        </div>
    </header>
    <?php renderLabNav(); ?>
    <main class="container page-shell">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'failed') === false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo e($message); ?> <?php echo e($details); ?>
            </div>
        <?php endif; ?>

        <section class="lab-command-grid">
            <form method="POST" class="reset-card">
                <h2>Progress Only</h2>
                <p>Clears session scoreboard without touching database data.</p>
                <button type="submit" name="reset" value="progress">Reset Progress</button>
            </form>
            <form method="POST" class="reset-card">
                <h2>Comments</h2>
                <p>Removes XSS payload comments and restores sample comments.</p>
                <button type="submit" name="reset" value="comments">Reset Comments</button>
            </form>
            <form method="POST" class="reset-card">
                <h2>Grades</h2>
                <p>Restores grade rows after tampering exercises.</p>
                <button type="submit" name="reset" value="grades">Reset Grades</button>
            </form>
            <form method="POST" class="reset-card danger-zone">
                <h2>Full Lab</h2>
                <p>Drops and reloads the bundled enhanced schema. This is intentionally destructive for lab reset.</p>
                <button type="submit" name="reset" value="full" onclick="return confirm('Reset the full lab database?')">Reset Full Database</button>
            </form>
        </section>
        <?php renderRequestInspector('Lab reset console'); ?>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | Lab reset</p></footer>
</body>
</html>
