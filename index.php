<?php
require_once 'vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('369264255613-dirakfsnfg5ma0nffr1jng1ianneik3a.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://biometric-login-production.up.railway.app/callback.php');
$client->addScope("email");
$client->addScope("profile");
$login_url = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login SSO Google</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card p-4 shadow-lg" style="width: 400px;">
        <h3 class="text-center mb-4">Login dengan Google</h3>
        <a class="btn btn-danger w-100" href="<?= htmlspecialchars($login_url) ?>">
            <img src="https://img.icons8.com/color/16/000000/google-logo.png"/>
            Login with Google
        </a>
    </div>
</body>
</html>
