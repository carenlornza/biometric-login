<?php 
session_start();
require 'db.php'; // Penting untuk mengakses $pdo

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Setelah fingerprint valid
if (isset($_SESSION['biometric_verified']) && $_SESSION['biometric_verified'] === true) {
    header("Location: user-management.php");
    exit();
}
// Ambil credential ID untuk user yang login
$email = $_SESSION['user']['email'];
$hashedEmail = hash('sha512', $email); // Enkripsi sebelum dicocokkan
$stmt = $pdo->prepare("SELECT raw_id_base64url FROM users WHERE email_hashed = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$hashedEmail]);
$credentialId = $stmt->fetchColumn();

if (!$credentialId) {
    echo "<div class='alert alert-danger'>❌ Fingerprint belum terdaftar untuk email: " . htmlspecialchars($email) . "</div>";
    echo "<p><a href='register-fingerprint.php'>Daftar Fingerprint</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Biometrik Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .biometric-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background: white;
        }
        .fingerprint-icon {
            font-size: 4rem;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-fingerprint {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="biometric-container">
            <div class="fingerprint-icon">
                🔐
            </div>
            <h3 class="text-center mb-4">Verifikasi Biometrik Diperlukan</h3>
            <p class="text-center text-muted">
                Sebagai admin, Anda perlu melakukan verifikasi fingerprint untuk mengakses pengelolaan user.
            </p>
            
            <div class="text-center">
                <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
            </div>

            <button onclick="verifyFingerprint()" class="btn btn-primary btn-fingerprint">
                🔍 Scan Fingerprint Sekarang
            </button>
            
            <div id="status" class="mt-3"></div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
    </div>

    <script>
    function base64urlToArrayBuffer(base64url) {
        const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/')
            + '='.repeat((4 - base64url.length % 4) % 4);
        const binaryString = atob(base64);
        const len = binaryString.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        return bytes;
    }

    async function verifyFingerprint() {
        const statusDiv = document.getElementById('status');
        
        try {
            statusDiv.innerHTML = '<div class="alert alert-info">🔄 Memproses verifikasi fingerprint...</div>';
            
            const credentialId = "<?= $credentialId ?>";
            const credentialIdBuffer = base64urlToArrayBuffer(credentialId);

            // Generate challenge
            const challenge = new Uint8Array(32);
            window.crypto.getRandomValues(challenge);

            const options = {
                publicKey: {
                    challenge: challenge,
                    timeout: 60000,
                    userVerification: "required",
                    allowCredentials: [{
                        id: credentialIdBuffer,
                        type: "public-key",
                        transports: ["internal"]
                    }]
                }
            };

            const credential = await navigator.credentials.get(options);
            console.log("Credential received:", credential);

            // Kirim ke server untuk verifikasi
            const response = await fetch("process-biometric.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    action: "verify",
                    credential: {
                        id: credential.id,
                        rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                        response: {
                            authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))),
                            clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                            signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature)))
                        }
                    }
                })
            });

            const result = await response.json();
            
            if (result.success) {
                statusDiv.innerHTML = '<div class="alert alert-success">✅ Verifikasi berhasil! Mengalihkan ke pengelolaan user...</div>';
                setTimeout(() => {
                    window.location.href = 'user-management.php';
                }, 1500);
            } else {
                statusDiv.innerHTML = '<div class="alert alert-danger">❌ Verifikasi gagal: ' + result.message + '</div>';
            }

        } catch (err) {
            console.error("Fingerprint error:", err);
            statusDiv.innerHTML = '<div class="alert alert-danger">❌ Error: ' + err.message + '</div>';
        }
    }
    </script>
</body>
</html>
