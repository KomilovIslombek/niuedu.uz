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

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);

if ($_REQUEST['type'] == "add_request"){
    $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $_POST["direction_id"] ]);
    
    $insert_arr = [
        "code" => NULL,
        "first_name" => $_POST["first_name"],
        "last_name" => $_POST["last_name"],
        "father_first_name" => $_POST["father_first_name"],
        "sex" => $_POST["sex"],
        "birth_date" => $_POST["birth_date"],
        "phone_1" => $_POST["phone_1"],
        "phone_2" => $_POST["phone_2"],
        "direction" => lng($direction["short_name"], "uz"),
        "direction_id" => $direction["id"],
        "learn_type" => $_POST["learn_type"],
        "passport_serial_number" => $_POST["passport_serial_number"],
        "exam_lang" => $_POST["exam_lang"],
        "exam_foreign_lang" => $_POST["exam_foreign_lang"],
        "ball" => $_POST["ball"],
        "shartnoma_amount" => str_replace(",", "", $_POST["shartnoma_amount"]),
        "shartnoma_code" => $_POST["shartnoma_code"],
        "reg_type" => "oddiy"
    ];

    if ($request_id > 0) {
        // Fayllarni yuklash
        if ($_FILES['file_1']["size"] != 0){
            $target_dir_1 = "files/upload/3x4/";
    
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_1)) {
                mkdir($target_dir_1, 0777, true);
            }
    
            $file_1 = $_FILES['file_1'];
            $random_name_1 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_1 = basename($file_1["type"]);
            $target_file_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
            $uploadOk_1 = 1;
            $file_type_1 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_file_1,PATHINFO_EXTENSION));
        
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_file_1)) {
                $file_1_error = "Kechirasiz, fayl allaqachon mavjud.";
                $uploadOk_1 = 0;
            }
            if ($file_1["size"] > 5000000) {
                $file_1_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
                $uploadOk_1 = 0;
            }
            if($file_type_1 != "jpg" && $file_type_1 != "png" && $file_type_1 != "jpeg" && $file_type_1 != "pdf") {
                $file_1_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
                $uploadOk_1 = 0;
            }
            if ($uploadOk_1 == 0) {
                $file_1_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
            } else {
                if (move_uploaded_file($file_1["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/".$target_file_1)) {
                    $file_folder_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
                    
                    // istalgan o'lchamdagi rasm
                    $size_1 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_1);
                    list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_1);
                    
                    $file_1_insert = [
                        "creator_user_id" => $user_id,
                        "name" => $file_1["name"],
                        "type" => $file_type_1,
                        "size" => $size_1,
                        "file_folder" => $file_folder_1
                    ];
                    $file_id_1 = $db->insert("files", $file_1_insert);

                    if (!$file_id_1){
                        $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $file_id_1 = NULL;
        }
    
        if ($_FILES['file_2']["size"] != 0){
            $target_dir_2 = "files/upload/passport/";
    
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_2)) {
                mkdir($target_dir_2, 0777, true);
            }
    
            $file_2 = $_FILES['file_2'];
            $random_name_2 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_2 = basename($file_2["type"]);
            $target_file_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
            $uploadOk_2 = 1;
            $file_type_2 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_file_2,PATHINFO_EXTENSION));
        
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_file_2)) {
                $file_2_error = "Kechirasiz, fayl allaqachon mavjud.";
                $uploadOk_2 = 0;
            }
            if ($file_2["size"] > 5000000) {
                $file_2_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
                $uploadOk_2 = 0;
            }
            if($file_type_2 != "jpg" && $file_type_2 != "png" && $file_type_2 != "jpeg" && $file_type_2 != "pdf") {
                $file_2_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
                $uploadOk_2 = 0;
            }
            if ($uploadOk_2 == 0) {
                $file_2_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
            } else {
                if (move_uploaded_file($file_2["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/".$target_file_2)) {
                    $file_folder_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
                    
                    // istalgan o'lchamdagi rasm
                    $size_2 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_2);
                    list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_2);
    
                    $file_2_insert = [
                        "creator_user_id" => $user_id,
                        "name" => $file_2["name"],
                        "type" => $file_type_2,
                        "size" => $size_2,
                        "file_folder" => $file_folder_2
                    ];
                    $file_id_2 = $db->insert("files", $file_2_insert);
    
                    if (!$file_id_2){
                        $file_2_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_2_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $file_id_2= NULL;
        }
    
        if ($_FILES['file_3']["size"] != 0){
            $target_dir_3 = "files/upload/diplom/";
    
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_3)) {
                mkdir($target_dir_3, 0777, true);
            }
    
            $file_3 = $_FILES['file_3'];
            $random_name_3 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_3 = basename($file_3["type"]);
            $target_file_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
            $uploadOk_3 = 1;
            $file_type_3 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_file_3,PATHINFO_EXTENSION));
        
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_file_3)) {
                $file_3_error = "Kechirasiz, fayl allaqachon mavjud.";
                $uploadOk_3 = 0;
            }
            if ($file_3["size"] > 5000000) {
                $file_3_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
                $uploadOk_3 = 0;
            }
            if($file_type_3 != "jpg" && $file_type_3 != "png" && $file_type_3 != "jpeg" && $file_type_3 != "pdf") {
                $file_3_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
                $uploadOk_3 = 0;
            }
            if ($uploadOk_3 == 0) {
                $file_3_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
            } else {
                if (move_uploaded_file($file_3["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/".$target_file_3)) {
                    $file_folder_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
                    
                    // istalgan o'lchamdagi rasm
                    $size_3 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_3);
                    list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_3);
    
                    $file_3_insert = [
                        "creator_user_id" => $user_id,
                        "name" => $file_3["name"],
                        "type" => $file_type_3,
                        "size" => $size_3,
                        "file_folder" => $file_folder_3
                    ];
                    $file_id_3 = $db->insert("files", $file_3_insert);
    
                    if (!$file_id_3){
                        $file_3_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_3_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $file_id_3= NULL;
        }
    
        if ($_FILES['file_4']["size"] != 0){
            $target_dir_4 = "files/upload/dtm_natija/";
    
            if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_4)) {
                mkdir($target_dir_4, 0777, true);
            }
    
            $file_4 = $_FILES['file_4'];
            $random_name_4 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_4 = basename($file_4["type"]);
            $target_file_4 = $target_dir_4 . $random_name_4 . ".$file_type_4";
            $uploadOk_4 = 1;
            $file_type_4 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_file_4,PATHINFO_EXTENSION));
        
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_file_4)) {
                $file_4_error = "Kechirasiz, fayl allaqachon mavjud.";
                $uploadOk_4 = 0;
            }
            if ($file_4["size"] > 5000000) {
                $file_4_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
                $uploadOk_4 = 0;
            }
            if($file_type_4 != "jpg" && $file_type_4 != "png" && $file_type_4 != "jpeg" && $file_type_4 != "pdf") {
                $file_4_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
                $uploadOk_4 = 0;
            }
            if ($uploadOk_4 == 0) {
                $file_4_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
            } else {
                if (move_uploaded_file($file_4["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/".$target_file_4)) {
                    $file_folder_4 = $target_dir_4 . $random_name_4 . ".$file_type_4";
                    
                    // istalgan o'lchamdagi rasm
                    $size_4 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_4);
                    list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_file_4);
    
                    $file_4_insert = [
                        "creator_user_id" => $user_id,
                        "name" => $file_4["name"],
                        "type" => $file_type_4,
                        "size" => $size_4,
                        "file_folder" => $file_folder_4
                    ];
                    $file_id_4 = $db->insert("files", $file_4_insert);
    
                    if (!$file_id_4){
                        $file_4_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_4_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $file_id_4 = NULL;
        }

        if ($request_id > 0) {
            $new_code = idCode($direction["code"], $request_id);

            $update_arr = [
                "code" => $new_code,
                "file_id_1" => $file_id_1,
                "file_id_2" => $file_id_2,
                "file_id_3" => $file_id_3,
                "file_id_4" => $file_id_4
            ];
            
            $db->update("requests", $update_arr, [
                "id" => $request_id
            ]);

            include $_SERVER["DOCUMENT_ROOT"] . "/modules/bot.php"; 
        
            $text = "";
            $text .= "#ariza_".$new_code . " #".($url2[0] == "admin" ? 1 : 2)."_bosqich";
            $text .= "\n\nIsm: <b>".$_POST["first_name"]."</b>";
            $text .= "\nFamiliya: <b>".$_POST["last_name"]."</b>";
            $text .= "\nOtasining ismi: <b>".$_POST["father_first_name"]."</b>";
            $text .= "\nPassport seriyasi hamda raqami: <b>".$_POST["passport_serial_number"]."</b>";
            $text .= "\nJinsi: <b>".$_POST["sex"]."</b>";
            $text .= "\nTug'ilgan sanasi: <b>".$_POST["birth_date"]."</b>";
            $text .= "\nTelefon 1: <b>".$_POST["phone_1"]."</b>";
            $text .= "\nTelefon 2: <b>".$_POST["phone_2"]."</b>";
        
            $text .= "\nTa'lim yo'nalishida o'qimoqchi: <b>".lng($direction["short_name"], "uz")."</b>";
            $text .= "\nTa'lim shakli: <b>".$_POST["learn_type"]."</b>";
        
            $text .= "\nAriza qoldiruvchi admin: <b>".$systemUser["first_name"]." ".$systemUser["last_name"]."</b>";
        
            $groups = ["-1001790361422"];
        
            foreach ($groups as $admin_id) {
                $res_msg = bot("sendMessage", [
                    "chat_id" => $admin_id,
                    "text" => "<b>".$_SERVER['HTTP_HOST']."\n\nAdmin panel orqali ariza qoldirishdi!</b>\n\n$text",
                    "parse_mode" => "html"
                ]);
        
                if ($res_msg["ok"] != true) {
                    // $error = "fayllarni yuklashda xatolik yuzaga keldi!";
                } else {
                    $message_id = $res_msg["result"]["message_id"];
                }
            }
        }

    }

    
    header("Location: requests_list.php?reg_type=oddiy&page=1");
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Ariza qo'shish</h4>
                            
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
                                <form action="add_request.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_request" required>

                                    <div class="form-group">
                                        <label for="last_name">Familiya</label>
                                        <textarea name="last_name" class="form-control border-primary"><?=$request["last_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="first_name">Ism</label>
                                        <textarea name="first_name" class="form-control border-primary"><?=$request["first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="father_first_name">Otasining ismi</label>
                                        <textarea name="father_first_name" class="form-control border-primary"><?=$request["father_first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="passport_serial_number">Passport seriyasi hamda raqami</label>
                                        <input type="text" name="passport_serial_number" class="form-control border-primary" value="<?=$request["passport_serial_number"]?>" id="passport_serial_number">
                                    </div>

                                    <div class="form-group">
                                        <label>Jinsi</label>
                                        <select name="sex" class="form-control">
                                            <option value="erkak" <?=($request["sex"] == "erkak" ? 'selected=""' : '')?>>Erkak</option>
                                            <option value="ayol" <?=($request["sex"] == "ayol" ? 'selected=""' : '')?>>Ayol</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tug'ilgan sanasi</label>
                                        <input type="date" name="birth_date" class="form-control border-primary" value="<?=$request["birth_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_1">Telefon (1)</label>
                                        <input type="text" name="phone_1" class="form-control border-primary" value="+998" id="phone_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_2">Telefon (2)</label>
                                        <input type="text" name="phone_2" class="form-control border-primary" value="" id="phone_2">
                                    </div>

                                    <div class="form-group">
                                        <label>Ta'lim yo'nalishi</label>
                                        <select name="direction_id" class="form-control" id="direction">
                                            <?
                                            $directions = $db->in_array("SELECT * FROM directions WHERE active = 1");
                                            foreach ($directions as $direction) {
                                                echo '<option value="'.$direction["id"].'" '.($request["direction_id"] == $direction["id"] ? 'selected=""' : '').' data-direction-id="'.$direction["id"].'">'.lng($direction["short_name"], "uz").'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="learn_type" class="fieldlabels">Ta'lim shakli <b id="html"></b></label>
                                        <select name="learn_type" class="form-control" id="learn_type" required="">
                                            <option value="Kunduzgi" id="option_kunduzgi" <?=($request["learn_type"] == "Kunduzgi" ? 'selected=""' : "")?>>Kunduzgi</option>
                                            <option value="Kechki" id="option_kechki" <?=($request["learn_type"] == "Kechki" ? 'selected=""' : "")?>>Kechki</option>
                                            <option value="Sirtqi" id="option_sirtqi" <?=($request["learn_type"] == "Sirtqi" ? 'selected=""' : "")?>>Sirtqi</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam_lang" class="fieldlabels">Test imtihonini qaysi tilda topshirasiz?</label>
                                        <select name="exam_lang" class="form-control" id="exam_lang" required="">
                                            <option value="uz" <?=($request["exam_lang"] == "uz" ? 'selected=""' : "")?>>O’zbek tili</option>
                                            <option value="ru" <?=($request["exam_lang"] == "ru" ? 'selected=""' : "")?>>Rus tili</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam_foreign_lang" class="fieldlabels">Chet tili imtihonini qaysi tilda topshirasiz?</label>
                                        <select name="exam_foreign_lang" class="form-control" id="exam_foreign_lang" required="">
                                            <option value="ar" <?=($request["exam_foreign_lang"] == "ar" ? 'selected=""' : "")?>>Arab tili</option>
                                            <option value="en" <?=($request["exam_foreign_lang"] == "en" ? 'selected=""' : "")?>>Ingliz tili</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="ball">ball</label>
                                        <input type="text" name="ball" class="form-control border-primary" value="<?=$request["ball"]?>" id="ball">
                                    </div>

                                    <div class="form-group">
                                        <label for="shartnoma_amount">Shartnoma summasi</label>
                                        <input type="text" name="shartnoma_amount" class="form-control border-primary" value="<?=($request["shartnoma_amount"] ? number_format($request["shartnoma_amount"]) : "")?>" data-price-input>
                                    </div>

                                    <div class="form-group">
                                        <label for="shartnoma_code">Shartnoma kodi</label>
                                        <input type="text" name="shartnoma_code" class="form-control border-primary" value="<?=$request["shartnoma_code"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_1">3.5X4.5 hajmdagi rasmingizni yuklang</label>
                                        <input type="file" name="file_1" class="form-control border-primary" value="<?=$request["file_1"]?>" id="file_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_2">Pasportingiz nusxasini yuklang</label>
                                        <input type="file" name="file_2" class="form-control border-primary" value="<?=$request["file_2"]?>" id="file_2">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_3">O'rta maktab attestati yoki o'rta maxsus bilim yurti diplomini yuklang</label>
                                        <input type="file" name="file_3" class="form-control border-primary" value="<?=$request["file_3"]?>" id="file_3">
                                    </div>

                                    <div class="form-group">
                                        <label for="file_4">Agar DTM dan imtihon topshirgan bo'lsangiz natijani yuklang</label>
                                        <input type="file" name="file_4" class="form-control border-primary" value="<?=$request["file_4"]?>" id="file_4">
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

<script>
    <?
    $direction_learn_types_arr = [];

    foreach ($directions as $direction) {
        $direction_learn_types = $db->in_array("SELECT learn_type_id FROM direction_learn_types WHERE direction_id = ?", [
            $direction["id"]
        ]);

        $learn_type_id_arr = [];
        foreach ($direction_learn_types as $direction_learn_type) {
            array_push($learn_type_id_arr, $direction_learn_type["learn_type_id"]);
        }
        $direction_learn_types_arr[$direction["id"]] = $learn_type_id_arr;
    }

    echo "var direction_learn_types = ".json_encode($direction_learn_types_arr).";";
    // 
    $learn_types = $db->in_array("SELECT id, name FROM learn_types");
    $learn_types_arr = [];

    foreach ($learn_types as $learn_type) {
        $learn_types_arr[$learn_type["id"]] = $learn_type;
    }
    echo "var learn_types = ".json_encode($learn_types_arr).";";
    ?>
    var selected_learn_type = "<?=$request["learn_type"]?>";

    function directionChange() {
        $("#learn_type").html("");

        var direction_id = $("#direction").find("option:selected").attr("data-direction-id");
        
        for (key in direction_learn_types[direction_id]) {
            var learn_type_id = direction_learn_types[direction_id][key];
            var learn_type = learn_types[learn_type_id];

            var html = '<option value="'+learn_type["name"]+'" '+(selected_learn_type == learn_type["name"] ? 'selected=""' : '')+'>'+learn_type["name"]+'</option>';
            $("#learn_type").append(html);
        }
    }

    directionChange();

    $("#direction").on("change", function(){
        directionChange();
    });

    $("#phone_1").on('input keyup', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        // console.log(x);
        e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });

    $("#phone_2").on('input keyup', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        // console.log(x);
        e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });

    $("*[data-price-input]").on("input", function(){
        var val = $(this).val().replaceAll(",", "").replaceAll(" ", "");
        console.log(val);

        if (val.length > 0) {    
            $(this).val(
                String(val).replace(/(.)(?=(\d{3})+$)/g,'$1,')
            );
        }
    });
</script>

<? include('end.php'); ?>