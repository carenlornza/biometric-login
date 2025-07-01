<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if ($_SESSION['user']['role'] !== 'admin') {
    echo "<div style='padding: 2rem; text-align: center;'>
            <h3>Akses Ditolak</h3>
            <p>Halaman ini hanya bisa diakses oleh admin.</p>
            <a href='logout.php' class='btn btn-danger'>Logout</a>
          </div>";
    exit();
}
// TAMBAHAN: Cek verifikasi biometrik untuk admin
if (!isset($_SESSION['biometric_verified']) || $_SESSION['biometric_verified'] !== true) {
    header('Location: verify-biometric.php');
    exit();
}

$userSession = $_SESSION['user'];
$users = $pdo->query("SELECT * FROM users ORDER BY updated_at DESC")->fetchAll();

$userSession = $_SESSION['user'];
$users = $pdo->query("SELECT * FROM users ORDER BY updated_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengelolaan User</title>
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
        <a href="user-management.php">👤 Pengelolaan User</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Konten -->
    <div class="content">
        <h3 class="mb-4">Pengelolaan User</h3>
        <div class="card p-4 mb-4 shadow-sm">
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($userSession['picture']) ?>" class="rounded-circle me-3" width="60" alt="Foto Profil">
                <div>
                    <h5 class="mb-0"><?= htmlspecialchars($userSession['name']) ?></h5>
                    <small class="text-muted"><?= htmlspecialchars($userSession['email']) ?></small>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Daftar User</h5>
            <a href="create.php" class="btn btn-success btn-sm">+ Tambah User</a>
        </div>

        <table class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email Hashed</th>
                    <th>Foto</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><small><?= $u['email_hashed'] ?></small></td>
                    <td><img src="<?= htmlspecialchars($u['picture']) ?>" width="50"></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td><?= $u['updated_at'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $u['id'] ?>" onclick="return confirm('Hapus user ini?')" class="btn btn-sm btn-danger">Hapus</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

</body>
</html>
