<?php
session_start();
require 'db.php';

// Fetch jobs
$stmt = $pdo->query("SELECT jobs.*, users.name AS employer_name FROM jobs JOIN users ON jobs.employer_id = users.id ORDER BY jobs.created_at DESC");
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Portal - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Job Portal</a>
        <div>
            <?php if(isset($_SESSION['user'])): ?>
                <span class="me-2">Hello, <?= htmlspecialchars($_SESSION['user']['name']) ?></span>
                <a href="dashboard.php" class="btn btn-primary btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-sm me-2">Login</a>
                <a href="register.php" class="btn btn-outline-success btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">
    <h2 class="mb-4">Latest Jobs</h2>
    <?php foreach($jobs as $job): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="job.php?id=<?= $job['id'] ?>"> <?= htmlspecialchars($job['title']) ?> </a>
                </h5>
                <h6 class="card-subtitle mb-2 text-muted">By <?= htmlspecialchars($job['employer_name']) ?> | <?= htmlspecialchars($job['location']) ?></h6>
                <p class="card-text"> <?= nl2br(htmlspecialchars(substr($job['description'],0,200))) ?>...</p>
                <a href="job.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary btn-sm">View Details</a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($jobs)): ?>
        <p>No jobs found.</p>
    <?php endif; ?>
</div>
</body>
</html>
