<?php

// tekshiruv
if (!$user_id || $user_id == 0) {
    header('Location:/login');
    exit;
} else if ($systemUser->admin != 1){
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        siz admin lavozimida emassiz!<br>admin lavozimidagi akkauntga kirish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing.
    </body>
    </html>';
    exit;
}




include('filter.php');

$page = (int)$_GET["page"];
if (empty($page)) $page = 1;

$withdrawal_of_money_agent_id = isset($_REQUEST["withdrawal_of_money_agent_id"]) ? $_REQUEST["withdrawal_of_money_agent_id"] : null;
if (!$withdrawal_of_money_agent_id) {echo"error withdrawal_of_money_agent_id not found";return;}

$withdrawal_of_money_agent = $db->assoc("SELECT * FROM withdrawal_of_money_agents WHERE id = ?", [$withdrawal_of_money_agent_id]);
if (!$withdrawal_of_money_agent["id"]) {echo"error (withdrawal_of_money_agent not found)";exit;}

if ($_REQUEST["type"] == "delete_withdrawal_of_money_agent") {
    $db->delete("withdrawal_of_money_agents", $withdrawal_of_money_agent["id"]);
    header("Location: withdrawal_of_money_agents.php?page=" . $page);
    exit;
}

include('head.php');
?>


<? include "scripts.php"; ?>

<!-- Select2 -->
<script src="../modules/select2/select2.full.min.js"></script>
<script src="../modules/select2/select2-init.js"></script>

<? include('end.php'); ?>