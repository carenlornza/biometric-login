<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Fingerprint</title>
</head>
<body>
<h3>Registrasi Fingerprint untuk: <?= htmlspecialchars($_SESSION['user']['email']) ?></h3>
<button onclick="register()">Registrasi Fingerprint</button>
<pre id="output"></pre>

<script>
async function register() {
    try {
        const challenge = new Uint8Array(32);
        window.crypto.getRandomValues(challenge);

        const userId = new Uint8Array(16);
        window.crypto.getRandomValues(userId);

        const credential = await navigator.credentials.create({
            publicKey: {
                challenge: challenge,
                rp: { name: "Contoh Web", id: window.location.hostname },
                user: {
                    id: userId,
                    name: "<?= $_SESSION['user']['email'] ?>",
                    displayName: "<?= $_SESSION['user']['email'] ?>"
                },
                pubKeyCredParams: [
                    { type: "public-key", alg: -7 },
                    { type: "public-key", alg: -257 }
                ],
                authenticatorSelection: {
                    authenticatorAttachment: "platform",
                    userVerification: "required",
                    residentKey: "required",
                    requireResidentKey: true
                },
                timeout: 60000,
                attestation: "none"
            }
        });

        const cred = {
            id: credential.id,
            rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
            type: credential.type,
            userId: btoa(String.fromCharCode(...userId)),
            response: {
                clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject)))
            }
        };

        const res = await fetch("store-credential.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
body: JSON.stringify({ email: "<?= $_SESSION['user']['email'] ?>" , credential: cred })
        });

        const result = await res.text();
document.getElementById("output").textContent = result;

if (result.includes("✅")) {
    setTimeout(() => {
        window.location.href = "verify-biometric.php";
    }, 2000); // kasih delay biar user lihat pesan sukses dulu
}


    } catch (err) {
        document.getElementById("output").textContent = "Error: " + err;
    }
}
</script>
</body>
</html>
