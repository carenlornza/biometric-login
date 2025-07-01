<?php
function sendOTPWhatsApp($phone, $otp) {
    $token = "orZ1Hxr2LwAZGP6KxKVr"; // Ganti dengan token aslimu
    $message = "Kode OTP Anda: $otp";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $phone,
            'message' => $message
        ],
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function hotp($secret, $counter, $digits = 6) {
    $secret = pack('H*', $secret);
    $binCounter = pack('N*', 0) . pack('N*', $counter);
    $hash = hash_hmac('sha1', $binCounter, $secret, true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $truncatedHash = substr($hash, $offset, 4);
    $code = unpack("N", $truncatedHash)[1] & 0x7FFFFFFF;
    return str_pad($code % pow(10, $digits), $digits, '0', STR_PAD_LEFT);
}

function totp($secret, $digits = 6, $period = 30) {
    $time = floor(time() / $period);
    return hotp($secret, $time, $digits);
}
