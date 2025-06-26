<?php
session_start();
require 'db.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$job_id = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT jobs.*, users.name AS employer_name FROM jobs JOIN users ON jobs.employer_id = users.id WHERE jobs.id = ?');
$stmt->execute([$job_id]);
$job = $stmt->fetch();
if (!$job) {
    echo '<p>Job not found.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> - Job Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <a href="index.php" class="btn btn-link">&larr; Back to Jobs</a>
    <div class="card">
        <div class="card-body">
            <h2><?= htmlspecialchars($job['title']) ?></h2>
            <h5 class="text-muted">By <?= htmlspecialchars($job['employer_name']) ?> | <?= htmlspecialchars($job['location']) ?></h5>
            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
            <small>Posted at: <?= $job['created_at'] ?></small>
        </div>
    </div>
    <div class="mt-4">
        <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'candidate'): ?>
            <a href="apply.php?job_id=<?= $job['id'] ?>" class="btn btn-success">Apply for this Job</a>
        <?php else: ?>
            <div class="alert alert-info">Login as a candidate to apply for this job.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
