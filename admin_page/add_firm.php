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

if ($_REQUEST['type'] == "add_firm"){
    if (!empty($_POST["passport_serial"]) && !empty($_POST["passport_number"])) {
        $_POST["passport_serial_number"] = str_replace(" ", "", $_POST["passport_serial"] . " " . $_POST["passport_number"]);
    }

    if ($_FILES['file_1']["size"] != 0){
        $target_dir_1 = "files/upload/passport/";

        if (!file_exists($target_dir_1)) {
            mkdir($target_dir_1, 0777, true);
        }

        $file_1 = $_FILES['file_1'];
        $random_name_1 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type_1 = basename($file_1["type"]);
        $target_file_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
        $uploadOk_1 = 1;
        $file_type_1 = strtolower(pathinfo($target_file_1,PATHINFO_EXTENSION));
    
        if (file_exists('../'.$target_file_1)) {
            $file_1_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_1 = 0;
        }
        if ($file_1["size"] > 5000000) {
            $file_1_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_1 = 0;
        }
        if($file_type_1 != "jpg" && $file_type_1 != "png" && $file_type_1 != "jpeg" && $file_type_1 != "pdf") {
            $file_1_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG, SVG fayllariga ruxsat berilgan.";
            $uploadOk_1 = 0;
        }
        if ($uploadOk_1 == 0) {
            $file_1_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file_1["tmp_name"], $target_file_1)) {
                $file_folder_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
                
                // istalgan o'lchamdagi rasm
                $size_1 = filesize($target_file_1);
                list($width, $height) = getimagesize($target_file_1);

                $file_id_1 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file_1["name"],
                    "type" => $file_type_1,
                    "size" => $size_1,
                    "file_folder" => $file_folder_1
                ]);

                if (!$file_id_1){
                    $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            } else {
                $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        // $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
    }

    if ($file_1_error) {
        $error = "Fayl yuklashda xatolik!";
    }

    if (!$_POST["phone_1"]) {
        $error = "telefon raqamni kiritishni unutdingiz !!!";
    } else if (strlen($_POST["phone_1"]) != 17 || substr($_POST["phone_1"], 0, 4) != "+998") {
        $error = "telefon raqam noto'g'ri formatda kiritilgan !!!";
    }

    if (!$_POST["first_name"] || !$_POST["last_name"] || !$_POST["father_first_name"] || !$_POST["passport_jshr"] || !$_POST["card_number"] || !$_POST["transit_check"]) {
        $error = "Ma'lumotlarni to'ldirishni unutdingiz!";
    }
    
    
    if (!$error) {

        $agent_id = $db->insert("firms", [
            "creator_user_id" => $user_id,
            "first_name" => $_POST["first_name"],
            "last_name" => $_POST["last_name"],
            "father_first_name" => $_POST["father_first_name"],
            "phone_1" => $_POST["phone_1"],
            "phone_2" => $_POST["phone_2"],
            "passport_serial_number" => $_POST["passport_serial_number"],
            "passport_jshr" => $_POST["passport_jshr"],
            "card_number" => $_POST["card_number"],
            "transit_check" => $_POST["transit_check"],
            "passport_id" => $file_id_1,
        ]);

        if ($agent_id > 0) {
            $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $agent_id ]);

            if($agent["id"]) {
                $insert_user_id = $db->insert("users", [
                    "first_name" => $agent["first_name"],
                    "last_name" => $agent["last_name"],
                    "login" => str_replace(" ", "", $agent["phone_1"]),
                    "password" => md5(md5(encode($agent["passport_serial_number"]))),
                    "password_encrypted" => encode($agent["passport_serial_number"]),
                    "phone" => $agent["phone_1"],
                    "code" => null,
                    "password_sended_time" => null,
                    "datereg" => time(),
                    "lastdate" => time(),
                    "ip" => $env->getIp(),
                    "ip_via_proxy" => $env->getIpViaProxy(),
                    "browser" => $env->getUserAgent(),
                    "sestime" => time(),
                    "request_id" => $agent_id,
                ]);

                // if($insert_user_id) {
                //     header("Location: /admin/firms_list.php");
                // }
            }

        }

        header('Location: firms_list.php?page' . $_GET["page"]);
    }
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Agent qo'shish</h4>
                            
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
                                <form action="add_firm.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_firm" required>

                                    <? if ($error) { ?>
                                        <h3 class="text-center text-danger"><?=$error?></h3>
                                    <? } ?>

                                    <div class="form-group">
                                        <label>Agent familyasi</label>
                                        <input name="last_name" class="form-control border-primary" placeholder="Agent familyasi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Agent ismi</label>
                                        <input name="first_name" class="form-control border-primary" placeholder="Agent ismi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Agent otasini ismi</label>
                                        <input name="father_first_name" class="form-control border-primary" placeholder="Agent otasini ismi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="phone_1">Telefon raqamingiz </label>
                                        <input type="text" name="phone_1"  class="form-control border-primary" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="phone_2">Qo'shimcha telefon raqam</label>
                                        <input type="text" name="phone_2" class="form-control border-primary"  placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($_POST["phone_2"] ? htmlspecialchars($_POST["phone_2"]) : "+998")?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="card_number">Karta raqami</label>
                                        <input type="text" name="card_number" class="form-control border-primary" placeholder="8600"  required="">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="transit_check">Karta egasi</label>
                                        <input type="text" name="transit_check" class="form-control border-primary" placeholder="0000"  required="" >
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_serial">Passport seriyasi</label>
                                        <input type="text" name="passport_serial" class="form-control border-primary" placeholder="- -" id="passport_serial" required="">
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_number">Passport raqami</label>
                                        <input type="text" name="passport_number" class="form-control border-primary" placeholder="- - - - - - -" id="passport_number" required="" >
                                    </div>
                                    
                                    
                                    <img src="../images/jshr.jpg" alt="jshr" width="150px">

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_jshr">Passport jshr</label>
                                        <input type="text" name="passport_jshr" id="passport_jshr" class="form-control border-primary" placeholder="- - - - - - - - - - - - - -"  required="" >
                                    </div>

                                    <div class="form-group">
                                        <? if ($file_1_error) { ?>
                                            <h5 class="text-danger"><?=$file_1_error?></h5>
                                        <? } ?>
                                        <label class="fieldlabels" for="file_1">Passport nusxasi</label>
                                        <input type="file" class="form-control" name="file_1" id="file_1" required="">
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> saqlash
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


    $("#passport_serial").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 2));
    });

    $("#passport_number").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 7));
    });
    
    $("#passport_jshr").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 14));
    });

</script>

<? include('end.php'); ?>