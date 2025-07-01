<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$userSession = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            margin: 10px 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .content {
            flex: 1;
            padding: 30px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Menu</h4>
        <a href="dashboard.php">🏠 Dashboard</a>
        <?php if ($userSession['role'] === 'admin'): ?>
            <a href="verify-biometric.php">👤 Pengelolaan User</a>
        <?php endif; ?>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Konten Utama -->
    <div class="content">
        <h2>Selamat Datang, <?= htmlspecialchars($userSession['name']) ?>!</h2>
        <p>Email: <?= htmlspecialchars($userSession['email']) ?></p>
        <img src="<?= htmlspecialchars($userSession['picture']) ?>" width="100" class="rounded-circle mt-3 shadow">
        <div class="mt-4">
            <p>Silakan pilih menu di sidebar.</p>
        </div>
    </div>
</body>
</html>
