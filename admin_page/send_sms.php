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

set_time_limit(30000);

include('filter.php');

if (!empty($_GET["sms_id"])) {
    $sms_send = $db->assoc("SELECT * FROM sms_send WHERE id = ?", [ $_GET["sms_id"] ]);
    if (empty($sms_send["id"])) {
        exit(http_response_code(404));
    }
}

if ($_REQUEST['type'] == "send_sms"){
    $phones = isset($_REQUEST['phones']) ? $_REQUEST['phones'] : null;
    if (!$phones) {echo"telefon raqamlarni kiritishni unutdingiz!";exit;}

    $text = isset($_REQUEST['text']) ? $_REQUEST['text'] : null;
    if (!$text) {echo"habarni kiritishni unutdingiz!";exit;}
    $text = trim($text);

    $phones = strtr(
        $phones,
        [
            "(" => "",
            ")" => "",
            "-" => "",
            " " => ""
        ]
    );

    // header("Content-type: text/plain");
    $phones_arr = explode("\n", $phones);
    // print_r($phones_arr);

    // sleep(60 * 60);
    // exit;
    include $_SERVER["DOCUMENT_ROOT"]."/system/classes/sms.php";

    foreach ($phones_arr as $phone) {
        sendSms(str_replace("+", "", $phone), $text);
    }

    $db->insert("sms_send", [
        "creator_user_id" => $user_id,
        "phones" => json_encode($phones, JSON_UNESCAPED_UNICODE),
        "text" => $text
    ]);

    header('Location: /'.$url2[0].'/sms_list.php?page=1');
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">SMS yuborish</h4>
                            
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                    <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form action="send_sms.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="send_sms" required>

                                    <div class="form-group">
                                        <label>Qabul qiluvchi(larning) telefon raqamlari</span></label>
                                        <textarea name="phones" class="form-control border-primary" placeholder="Qabul qiluvchilar telefon raqamlari" rows="10"><?=($sms_send["phones"] ? implode("\n", json_decode($sms_send["phones"], true)) : "")?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Habar</span></label>
                                        <textarea name="text" class="form-control border-primary" placeholder="Habar" rows="10"></textarea>
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Hammaga yuborish
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<? include "scripts.php"; ?>

<? include('end.php'); ?>