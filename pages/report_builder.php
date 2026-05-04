<?php
include '../includes/config.php';
require_once '../includes/lab_ui.php';

$categories = labCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderLabHead('Report Builder'); ?>
</head>
<body>
    <header class="lab-header compact">
        <div class="container">
            <div class="lab-kicker">Evidence to Finding</div>
            <h1>Pentest Report Builder</h1>
            <p>Draft concise findings while practicing each intentionally vulnerable lab.</p>
        </div>
    </header>

    <?php renderLabNav(); ?>

    <main class="container page-shell">
        <section class="report-layout">
            <div class="lab-panel">
                <h2>Finding Draft</h2>
                <div class="column-2">
                    <div class="form-group">
                        <label>OWASP Category</label>
                        <select>
                            <?php foreach ($categories as $id => $lab): ?>
                                <option><?php echo e($id . ' - ' . $lab['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Severity</label>
                        <select>
                            <option>Critical</option>
                            <option>High</option>
                            <option>Medium</option>
                            <option>Low</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" value="Unauthorized access to student records">
                </div>
                <div class="form-group">
                    <label>Evidence</label>
                    <textarea rows="7" placeholder="Payload, affected URL, observed result, and screenshot notes."></textarea>
                </div>
                <div class="form-group">
                    <label>Impact</label>
                    <textarea rows="5" placeholder="Explain business impact in school portal terms."></textarea>
                </div>
                <div class="form-group">
                    <label>Recommendation</label>
                    <textarea rows="5" placeholder="Describe the secure design without removing the lab exercise."></textarea>
                </div>
                <button type="button" class="btn-primary">Generate Finding Preview</button>
            </div>

            <aside class="lab-panel">
                <h2>Evidence Checklist</h2>
                <div class="check-grid vertical">
                    <label><input type="checkbox"> Target URL recorded</label>
                    <label><input type="checkbox"> Payload recorded</label>
                    <label><input type="checkbox"> Before state captured</label>
                    <label><input type="checkbox"> After state captured</label>
                    <label><input type="checkbox"> OWASP category mapped</label>
                    <label><input type="checkbox"> Remediation written</label>
                </div>
            </aside>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Modern School Portal | Pentest report practice</p>
    </footer>
</body>
</html>
