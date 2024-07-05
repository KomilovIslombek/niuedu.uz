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

date_default_timezone_set("Asia/Tashkent");

include('filter.php');

$again_call_id = isset($_REQUEST['again_call_id']) ? $_REQUEST['again_call_id'] : null;
if (!$again_call_id) {echo"error [again_call_id]";exit;}

$again_call = $db->assoc("SELECT * FROM again_calls WHERE id = ?", [ $_REQUEST['again_call_id'] ]);
if (!$again_call["id"]) {echo"error (again_call not found)";exit;}

if ($_REQUEST['type'] == "delete_again_call"){
    $db->delete("again_calls", $again_call["id"]);
    returnBack();
}

function returnBack() {
    unset($_GET["again_call_id"]);
    unset($_GET["type"]);

    $return_url = "again_calls_list.php".($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "");
    header("Location: $return_url");
}

if ($_REQUEST['type'] == "change_description"){
    $db->update("again_calls", [
        "description_id" => $_REQUEST["description_id"],
        "interview_date" => date("Y-m-d H:i:s")
    ], [
        "id" => $again_call["id"]
    ]);
    returnBack();
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Arizani tahrirlash</h4>
                            
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
                                <form action="edit_REQ$_REQUEST.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_REQ$_REQUEST" required>
                                    <input type="hidden" name="page" value="<?=($_GET["page"] ? $_GET["page"] : "1")?>" required>
                                    <input type="hidden" name="again_call_id" value="<?=$again_call["id"]?>" required>

                                    <div class="form-group">
                                        <label>Talaba ID</label>
                                        <input type="text" class="form-control border-primary" value="<?=$again_call["code"]?>" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name">Familiya</label>
                                        <textarea name="last_name" class="form-control border-primary"><?=$again_call["last_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="first_name">Ism</label>
                                        <textarea name="first_name" class="form-control border-primary"><?=$again_call["first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="father_first_name">Otasining ismi</label>
                                        <textarea name="father_first_name" class="form-control border-primary"><?=$again_call["father_first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="passport_serial_number">Passport seriyasi hamda raqami</label>
                                        <input type="text" name="passport_serial_number" class="form-control border-primary" value="<?=$again_call["passport_serial_number"]?>" id="passport_serial_number">
                                    </div>

                                    <div class="form-group">
                                        <label>Jinsi</label>
                                        <select name="sex" class="form-control">
                                            <option value="erkak" <?=($again_call["sex"] == "erkak" ? 'selected=""' : '')?>>Erkak</option>
                                            <option value="ayol" <?=($again_call["sex"] == "ayol" ? 'selected=""' : '')?>>Ayol</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tug'ilgan sanasi</label>
                                        <input type="date" name="birth_date" class="form-control border-primary" value="<?=$again_call["birth_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_1">Telefon (1)</label>
                                        <input type="text" name="phone_1" class="form-control border-primary" value="<?=($again_call["phone_1"] ? $again_call["phone_1"] : "+998")?>" id="phone_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_2">Telefon (2)</label>
                                        <input type="text" name="phone_2" class="form-control border-primary" value="<?=($again_call["phone_2"] ? $again_call["phone_2"] : "+998")?>" id="phone_2">
                                    </div>

                                    <div class="form-group">
                                        <label>Ta'lim yo'nalishi</label>
                                        <select name="direction_id" class="form-control" id="direction">
                                            <?
                                            $directions = $db->in_array("SELECT * FROM directions WHERE active = 1");
                                            foreach ($directions as $direction) {
                                                echo '<option value="'.$direction["id"].'" '.($again_call["direction_id"] == $direction["id"] ? 'selected=""' : '').' data-direction-id="'.$direction["id"].'">'.lng($direction["short_name"], "uz").'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="learn_type" class="fieldlabels">Ta'lim shakli <b id="html"></b></label>
                                        <select name="learn_type" class="form-control" id="learn_type" required="">
                                            <option value="Kunduzgi" id="option_kunduzgi" <?=($again_call["learn_type"] == "Kunduzgi" ? 'selected=""' : "")?>>Kunduzgi</option>
                                            <option value="Kechki" id="option_kechki" <?=($again_call["learn_type"] == "Kechki" ? 'selected=""' : "")?>>Kechki</option>
                                            <option value="Sirtqi" id="option_sirtqi" <?=($again_call["learn_type"] == "Sirtqi" ? 'selected=""' : "")?>>Sirtqi</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam_lang" class="fieldlabels">Test imtihonini qaysi tilda topshirasiz?</label>
                                        <select name="exam_lang" class="form-control" id="exam_lang" required="">
                                            <option value="uz" <?=($again_call["exam_lang"] == "uz" ? 'selected=""' : "")?>>O’zbek tili</option>
                                            <option value="ru" <?=($again_call["exam_lang"] == "ru" ? 'selected=""' : "")?>>Rus tili</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam_foreign_lang" class="fieldlabels">Chet tili imtihonini qaysi tilda topshirasiz?</label>
                                        <select name="exam_foreign_lang" class="form-control" id="exam_foreign_lang" required="">
                                            <option value="ar" <?=($again_call["exam_foreign_lang"] == "ar" ? 'selected=""' : "")?>>Arab tili</option>
                                            <option value="en" <?=($again_call["exam_foreign_lang"] == "en" ? 'selected=""' : "")?>>Ingliz tili</option>
                                        </select>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label for="ball">ball</label>
                                        <input type="text" name="ball" class="form-control border-primary" value="<?=$again_call["ball"]?>" id="ball">
                                    </div> -->

                                    <div class="form-group">
                                        <label for="shartnoma_amount">Shartnoma summasi</label>
                                        <input type="text" name="shartnoma_amount" class="form-control border-primary" value="<?=($again_call["shartnoma_amount"] ? number_format($again_call["shartnoma_amount"]) : "")?>" data-price-input>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label for="shartnoma_code">Shartnoma kodi</label>
                                        <input type="text" name="shartnoma_code" class="form-control border-primary" value="<?=$again_call["shartnoma_code"]?>">
                                    </div> -->

                                    <div class="form-group">
                                        <label for="file_1">3.5X4.5 hajmdagi rasmingizni yuklang</label>
                                        <input type="file" name="file_1" class="form-control border-primary" id="file_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_2">Pasportingiz nusxasini yuklang</label>
                                        <input type="file" name="file_2" class="form-control border-primary" id="file_2">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_3">O'rta maktab attestati yoki o'rta maxsus bilim yurti diplomini yuklang</label>
                                        <input type="file" name="file_3" class="form-control border-primary" id="file_3">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_4">Agar DTM dan imtihon topshirgan bo'lsangiz natijani yuklang</label>
                                        <input type="file" name="file_4" class="form-control border-primary" id="file_4">
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label for="zapros_date">Zapros sanasi</label>
                                        <input type="date" name="zapros_date" class="form-control border-primary" value="<?=$again_call["zapros_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="zapros_university_name">Zapros Universitet nomi</label>
                                        <input type="text" name="zapros_university_name" class="form-control border-primary" value="<?=$again_call["zapros_university_name"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="zapros_rektor_full_name">Zapros Rektor to'liq nomi</label>
                                        <input type="text" name="zapros_rektor_full_name" class="form-control border-primary" value="<?=$again_call["zapros_rektor_full_name"]?>">
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label for="shartnoma_date">Shartnoma sanasi</label>
                                        <input type="date" name="shartnoma_date" class="form-control border-primary" value="<?=$again_call["shartnoma_date"]?>">
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Saqlash
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