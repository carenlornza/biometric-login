<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once 'db.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("User tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $picture = $_POST['picture'];

 $role = $_POST['role'];
$stmt = $pdo->prepare("UPDATE users SET name = ?, picture = ?, role = ?, updated_at = NOW() WHERE id = ?");
$stmt->execute([$name, $picture, $role, $id]);

    header('Location: dashboard.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container">
        <h3>Edit User</h3>
        <form method="POST" class="card p-4 shadow-sm bg-white">
            <div class="mb-3">
    <label>Role:</label>
    <select name="role" class="form-select">
        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
</div>

            <div class="mb-3">
                <label>Nama:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Foto URL:</label>
                <input type="text" name="picture" class="form-control" value="<?= htmlspecialchars($user['picture']) ?>">
            </div>
            <button type="submit" class="btn btn-warning">Update</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
