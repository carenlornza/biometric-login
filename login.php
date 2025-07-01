<?php
session_start();
require_once 'db.php';

// Jika fingerprint sukses (dikirim via POST), proses verifikasi server-side:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan user yang login adalah admin
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
        $_SESSION['biometric_verified'] = true;
        echo json_encode(['success' => true, 'redirect' => 'user-management.php']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Anda bukan admin.']);
        exit();
    }
}

// Default tampilkan UI fingerprint
$email = $_SESSION['user']['email'] ?? null;
if (!$email) {
    echo "Anda belum login.";
    exit;
}

$stmt = $pdo->prepare("SELECT raw_id_base64url FROM users WHERE email = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$email]);
$credentialId = $stmt->fetchColumn();

if (!$credentialId) {
    echo "❌ Credential ID tidak ditemukan untuk email: $email";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Login Fingerprint</title></head>
<body>
<h2>Login Fingerprint untuk: <?= htmlspecialchars($email) ?></h2>
<button onclick="getFingerprint()">Login Sekarang</button>
<pre id="output"></pre>

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

async function getFingerprint() {
    try {
        const credentialId = "<?= $credentialId ?>";
        const credentialIdBuffer = base64urlToArrayBuffer(credentialId);

        const options = {
            publicKey: {
$challengeBin = random_bytes(32);
$challenge = base64_encode($challengeBin); $_SESSION['webauthn_challenge'] = $challenge;
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
        console.log("Credential:", credential);

        // Setelah fingerprint berhasil → kirim POST ke PHP
const response = await fetch("process-biometric.php", {
            method: "POST"
        });

        const result = await response.json();
        if (result.success) {
            document.getElementById("output").innerText = "✅ Login fingerprint berhasil! Redirecting...";
            window.location.href = result.redirect;
        } else {
            document.getElementById("output").innerText = "❌ Gagal: " + result.message;
        }

    } catch (err) {
        console.error(err);
        document.getElementById("output").innerText = "❌ Gagal: " + err.name + ": " + err.message;
    }
}
</script>
</body>
</html>
