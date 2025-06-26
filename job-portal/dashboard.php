<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];

// Employer dashboard: manage jobs
if ($user['role'] === 'employer') {
    // Handle new job post
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $loc = trim($_POST['location'] ?? '');
        if ($title && $desc && $loc) {
            $stmt = $pdo->prepare('INSERT INTO jobs (employer_id, title, description, location) VALUES (?, ?, ?, ?)');
            $stmt->execute([$user['id'], $title, $desc, $loc]);
            $msg = 'Job posted!';
        } else {
            $msg = 'All fields required.';
        }
    }
    // Fetch employer jobs
    $stmt = $pdo->prepare('SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user['id']]);
    $jobs = $stmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Employer Dashboard</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
    <div class="container mt-4">
        <h2>Employer Dashboard</h2>
        <a href="logout.php" class="btn btn-danger btn-sm float-end">Logout</a>
        <h4 class="mt-4">Post a New Job</h4>
        <?php if($msg): ?><div class="alert alert-info"> <?= htmlspecialchars($msg) ?> </div><?php endif; ?>
        <form method="post" class="mb-4">
            <div class="mb-2"><input type="text" name="title" class="form-control" placeholder="Job Title" required></div>
            <div class="mb-2"><textarea name="description" class="form-control" placeholder="Description" required></textarea></div>
            <div class="mb-2"><input type="text" name="location" class="form-control" placeholder="Location" required></div>
            <button type="submit" class="btn btn-primary">Post Job</button>
        </form>
        <h4>Your Jobs</h4>
        <?php foreach($jobs as $job): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5><?= htmlspecialchars($job['title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    <small><?= htmlspecialchars($job['location']) ?> | <?= $job['created_at'] ?></small>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($jobs)): ?><p>No jobs posted yet.</p><?php endif; ?>
    </div>
    </body>
    </html>
    <?php
    exit;
}
// Candidate dashboard: show applications
if ($user['role'] === 'candidate') {
    $stmt = $pdo->prepare('SELECT applications.*, jobs.title FROM applications JOIN jobs ON applications.job_id = jobs.id WHERE candidate_id = ? ORDER BY applications.created_at DESC');
    $stmt->execute([$user['id']]);
    $apps = $stmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Candidate Dashboard</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
    <div class="container mt-4">
        <h2>Candidate Dashboard</h2>
        <a href="logout.php" class="btn btn-danger btn-sm float-end">Logout</a>
        <h4 class="mt-4">Your Applications</h4>
        <?php foreach($apps as $app): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5><?= htmlspecialchars($app['title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($app['message'])) ?></p>
                    <small>Applied at: <?= $app['created_at'] ?></small><br>
                    <?php if($app['cv_path']): ?>
                        <a href="<?= htmlspecialchars($app['cv_path']) ?>" target="_blank">View CV</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($apps)): ?><p>No applications yet.</p><?php endif; ?>
    </div>
    </body>
    </html>
    <?php
    exit;
}
