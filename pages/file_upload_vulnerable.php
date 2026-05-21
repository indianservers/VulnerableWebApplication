<?php
include '../includes/config.php';
require_once '../includes/security_lab_helpers.php';

$message = '';
$secure_message = '';
$upload_dir = __DIR__ . '/../uploads/profile_photos';
$secure_dir = __DIR__ . '/../storage/secure_uploads';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
if (!is_dir($secure_dir)) mkdir($secure_dir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $file = $_FILES['profile_photo'];
    if (($_POST['mode'] ?? '') === 'vulnerable') {
        $target = $upload_dir . '/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $message = 'Uploaded to web-accessible path: uploads/profile_photos/' . basename($file['name']);
            if (preg_match('/\.php[0-9]?$/i', $file['name']) || strpos(strtolower($file['name']), '.php.') !== false) {
                captureFlag('FLAG{unsafe_file_upload_001}', 'Uploaded executable PHP-like file');
                recordAttackEvent('A04', 'Unsafe file upload', $file['name'], 'Executable extension accepted');
            }
        } else {
            $message = 'Upload failed.';
        }
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif');
        if (!isset($allowed[$mime])) {
            $secure_message = 'Secure upload rejected: only JPEG, PNG, and GIF images are allowed.';
        } else {
            $safe_name = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
            move_uploaded_file($file['tmp_name'], $secure_dir . '/' . $safe_name);
            $secure_message = 'Secure upload stored outside webroot as ' . $safe_name;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php renderLabHead('File Upload Vulnerable'); ?></head>
<body>
    <header class="lab-header compact"><div class="container"><div class="lab-kicker">OWASP A04/A05 Demo</div><h1>Unsafe File Upload</h1><p>Profile photos are accepted without MIME or extension validation.</p></div></header>
    <?php renderLabNav(); ?>
    <main class="container page-shell lab-layout">
        <div class="lab-main">
            <section class="lab-panel">
                <h2>Upload Profile Photo</h2>
                <?php if ($message): ?><div class="alert alert-warning"><?php echo e($message); ?></div><?php endif; ?>
                <?php if ($secure_message): ?><div class="alert alert-info"><?php echo e($secure_message); ?></div><?php endif; ?>
                <?php if (isset($_SESSION['captured_flags']['FLAG{unsafe_file_upload_001}'])) renderFlag('FLAG{unsafe_file_upload_001}'); ?>
                <div class="split-compare">
                    <div><h3>Vulnerable Upload</h3><form method="POST" enctype="multipart/form-data"><input type="hidden" name="mode" value="vulnerable"><div class="form-group"><label>File</label><input type="file" name="profile_photo"></div><button type="submit">Upload Without Validation</button></form></div>
                    <div><h3>Secure Upload</h3><form method="POST" enctype="multipart/form-data"><input type="hidden" name="mode" value="secure"><div class="form-group"><label>File</label><input type="file" name="profile_photo"></div><button type="submit">Validate and Rename</button></form></div>
                </div>
            </section>
            <section class="lab-panel"><h2>Code Diff</h2><?php renderCodeCompare('move_uploaded_file($_FILES["profile_photo"]["tmp_name"], "uploads/" . $_FILES["profile_photo"]["name"]);', '$mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
if (!isset($allowed[$mime])) exit("Rejected");
$name = bin2hex(random_bytes(12)) . "." . $allowed[$mime];
move_uploaded_file($tmp, "../storage/secure_uploads/" . $name);'); ?></section>
        </div>
        <aside class="lab-side"><section class="payload-drawer"><h3>Test Files</h3><p>Try uploading <code>shell.php</code> or <code>avatar.php.jpg</code> through the vulnerable form.</p><button type="button" class="payload-chip" data-copy="<?php echo e('<?php system($_GET["cmd"] ?? "whoami"); ?>'); ?>">PHP payload sample</button></section></aside>
    </main>
    <footer><p>&copy; 2026 Modern School Portal | File upload lab</p></footer>
</body>
</html>
