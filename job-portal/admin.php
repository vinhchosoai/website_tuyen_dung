<?php
session_start();
require 'db.php';
// Simple admin page (no authentication for MVP)
$users = $pdo->query('SELECT id, name, email, role FROM users ORDER BY id')->fetchAll();
$jobs = $pdo->query('SELECT jobs.*, users.name AS employer_name FROM jobs JOIN users ON jobs.employer_id = users.id ORDER BY jobs.id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Job Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Admin Panel</h2>
    <h4 class="mt-4">All Users</h4>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
        <tbody>
        <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['role'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h4 class="mt-4">All Job Posts</h4>
    <table class="table table-bordered">
        <thead><tr><th>ID</th><th>Title</th><th>Employer</th><th>Location</th><th>Created At</th></tr></thead>
        <tbody>
        <?php foreach($jobs as $j): ?>
            <tr>
                <td><?= $j['id'] ?></td>
                <td><?= htmlspecialchars($j['title']) ?></td>
                <td><?= htmlspecialchars($j['employer_name']) ?></td>
                <td><?= htmlspecialchars($j['location']) ?></td>
                <td><?= $j['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
