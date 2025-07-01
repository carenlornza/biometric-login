<?php
// 3. BUAT FILE BARU: process-biometric.php
// File ini untuk memproses verifikasi biometrik

session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

// Cek apakah user adalah admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if ($input['action'] === 'verify') {
    // Untuk implementasi sederhana, kita langsung set session
    // Dalam implementasi nyata, Anda perlu memverifikasi signature credential
    
    try {
        // Cek apakah credential ID ada di database
        $email = $_SESSION['user']['email'];
$hashedEmail = hash('sha512', $email); // karena kolomnya email_hashed
$credentialId = $input['credential']['id'];

$stmt = $pdo->prepare("SELECT id FROM users WHERE email_hashed = ? AND public_key = ?");
$stmt->execute([$hashedEmail, $credentialId]);

        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['biometric_verified'] = true;
            $_SESSION['biometric_verified_at'] = time();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Verifikasi biometrik berhasil',
                'redirect' => 'user-management.php'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Credential tidak valid'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error server: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Action tidak dikenal']);
}
?>
