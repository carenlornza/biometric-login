<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['temp_user'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = $_POST['otp'];
    $email_hashed = $_SESSION['temp_user']['email_hashed'];
    $secret = substr(hash('sha1', $email_hashed), 0, 32);
    $expectedOtp = totp($secret);

    if ($inputOtp === $expectedOtp) {
        $_SESSION['user'] = $_SESSION['temp_user'];
        unset($_SESSION['temp_user']);
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Kode OTP salah atau sudah kadaluarsa.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 350px;">
        <h4 class="text-center mb-3">Masukkan Kode OTP</h4>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="otp" class="form-label">Kode OTP:</label>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="6 digit" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Verifikasi</button>
        </form>
    </div>
</body>
</html>
