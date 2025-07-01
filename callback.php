<?php
require_once 'vendor/autoload.php';
require_once 'db.php';
require_once 'functions.php'; // Kirim OTP & TOTP
session_start();

$client = new Google_Client();
$client->setClientId('369264255613-dirakfsnfg5ma0nffr1jng1ianneik3a.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $userInfo = $oauth->userinfo->get();

        $name = $userInfo->name;
        $email = $userInfo->email;
        $picture = $userInfo->picture;
        $email_hashed = hash('sha512', $email);

        // Cek apakah user sudah ada
        $stmt = $pdo->prepare("SELECT id, role, phone FROM users WHERE email_hashed = ?");
        $stmt->execute([$email_hashed]);
        $user = $stmt->fetch();

        if ($user) {
            // Update user data
            $stmt = $pdo->prepare("UPDATE users SET name = ?, picture = ?, updated_at = NOW() WHERE email_hashed = ?");
            $stmt->execute([$name, $picture, $email_hashed]);
            $role = $user['role'];
            $phone = $user['phone'];
        } else {
            // User baru, default role 'user'
            $stmt = $pdo->prepare("INSERT INTO users (name, email_hashed, picture, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email_hashed, $picture]);
            $role = 'user';
            $phone = null;
        }

        // Simpan sementara user
        $_SESSION['temp_user'] = [
            'name' => $name,
            'email' => $email,
            'email_hashed' => $email_hashed,
            'picture' => $picture,
            'role' => $role
        ];

        // Kalau admin → kirim OTP WhatsApp dan arahkan ke otp.php
        if ($role === 'admin') {
            if ($phone) {
                $secret = substr(hash('sha1', $email_hashed), 0, 32);
                $otp = totp($secret);
                sendOTPWhatsApp($phone, $otp);
                header('Location: otp.php');
                exit();
            } else {
                echo "<h3>Nomor WhatsApp belum diset di database.</h3>";
                echo "<a href='index.php'>Kembali</a>";
                exit();
            }
        } else {
            // Bukan admin → langsung login
            $_SESSION['user'] = $_SESSION['temp_user'];
            unset($_SESSION['temp_user']);
            header('Location: dashboard.php');
            exit();
        }
    } else {
        echo "Login gagal: " . $token['error_description'];
    }
} else {
    echo "Kode otorisasi tidak ditemukan.";
}
