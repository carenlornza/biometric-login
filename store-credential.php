<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Cek apakah data lengkap
if (!isset($data['email'], $data['credential']['rawId'], $data['credential']['id'])) {
    echo "❌ Data tidak lengkap. Pastikan semua data dikirim.";
    exit;
}

$email = $data['email'];
$rawId = $data['credential']['rawId'];
$publicKey = $data['credential']['id'];

// Debug output (boleh kamu hapus nanti)
echo "📥 Email: $email\n📥 rawId: $rawId\n📥 publicKey: $publicKey\n";

// Simpan ke database
try {
$emailHashed = hash('sha512', $email);
$stmt = $pdo->prepare("UPDATE users SET raw_id_base64url = ?, public_key = ? WHERE email_hashed = ?");
$stmt->execute([$rawId, $publicKey, $emailHashed]);
    echo "✅ Registrasi fingerprint berhasil!";
} catch (PDOException $e) {
    echo "❌ Error saat simpan DB: " . $e->getMessage();
}
?>
