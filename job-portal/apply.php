<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'candidate') {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: index.php');
    exit;
}
$job_id = (int)$_GET['job_id'];
// Fetch job
$stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = ?');
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) {
    echo '<p>Job not found.</p>';
    exit;
}
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $cv_path = '';
    // Handle file upload
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cv'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $msg = 'Only PDF files are allowed.';
        } else {
            $target = 'uploads/' . uniqid('cv_') . '.pdf';
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $cv_path = $target;
            } else {
                $msg = 'Failed to upload CV.';
            }
        }
    } else {
        $msg = 'CV file is required.';
    }
    if (!$msg) {
        $stmt = $pdo->prepare('INSERT INTO applications (job_id, candidate_id, message, cv_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$job_id, $_SESSION['user']['id'], $message, $cv_path]);
        $msg = 'Application submitted!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply - <?= htmlspecialchars($job['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4" style="max-width: 600px;">
    <a href="job.php?id=<?= $job['id'] ?>" class="btn btn-link">&larr; Back to Job</a>
    <h2>Apply for: <?= htmlspecialchars($job['title']) ?></h2>
    <?php if($msg): ?><div class="alert alert-info"> <?= htmlspecialchars($msg) ?> </div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Upload CV (PDF only)</label>
            <input type="file" name="cv" class="form-control" accept="application/pdf" required>
        </div>
        <button type="submit" class="btn btn-success">Submit Application</button>
    </form>
</div>
</body>
</html>
