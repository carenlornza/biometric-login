<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['temp_user'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $email_hashed = $_SESSION['temp_user']['email_hashed'];

    // Simpan ke database
    $stmt = $pdo->prepare("UPDATE users SET phone = ? WHERE email_hashed = ?");
    $stmt->execute([$phone, $email_hashed]);

    // Kirim OTP via WhatsApp
    $secret = substr(hash('sha1', $email_hashed), 0, 32);
    $otp = totp($secret);
    sendOTPWhatsApp($phone, $otp);

    header('Location: otp.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masukkan Nomor WhatsApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 350px;">
        <h4 class="text-center mb-3">Verifikasi WhatsApp</h4>
        <form method="POST">
            <div class="mb-3">
                <label for="phone" class="form-label">Nomor WhatsApp (format 62...)</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="6281234567890" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kirim OTP</button>
        </form>
    </div>
</body>
</html>
