<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $picture = $_POST['picture'];
    $email_hashed = hash('sha512', $email);

   $role = $_POST['role'];
$stmt = $pdo->prepare("INSERT INTO users (name, email_hashed, picture, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email_hashed, $picture, $role]);

    header('Location: dashboard.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <h3>Tambah User Baru</h3>
        <form method="POST" class="card p-4 shadow-sm bg-white">
            <div class="mb-3">
    <label>Role:</label>
    <select name="role" class="form-select">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>
</div>

            <div class="mb-3">
                <label>Nama:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Foto URL (Opsional):</label>
                <input type="text" name="picture" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
