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

$vakansiya_id = isset($_REQUEST['vakansiya_id']) ? $_REQUEST['vakansiya_id'] : null;
if (!$vakansiya_id) {echo"error [vakansiya_id]";exit;}

$vakansiya = $db->assoc("SELECT * FROM vakansiyalar WHERE id = ?", [ $_REQUEST['vakansiya_id'] ]);
if (!$vakansiya["id"]) {echo"error (vakansiya not found)";exit;}

if ($_REQUEST['type'] == "delete_vakansiya"){
    $db->delete("vakansiyalar", $vakansiya["id"]);

    delete_file($vakansiya["diplom_file_id_1"]);
    delete_file($vakansiya["diplom_file_id_2"]);
    delete_file($vakansiya["diplom_file_id_3"]);
    delete_file($vakansiya["tarjimai_xol_file_id"]);

    header("Location: vakansiyalar_list.php?page=".$_GET["page"]);
}

if ($_REQUEST["type"] == "edit_vakansiya"){
    $diplom_file_id_1 = $vakansiya["diplom_file_id_1"];
    $diplom_file_id_2 = $vakansiya["diplom_file_id_2"];
    $diplom_file_id_3 = $vakansiya["diplom_file_id_3"];
    $tarjimai_xol_file_id = $vakansiya["tarjimai_xol_file_id"];

    if ($_FILES['diplom_file_1']["size"] != 0){
        $target_dir_1 = "files/upload/vakansiyalar/diplomlar/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_1)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_1, 0777, true);
        }

        $diplom_file_1 = $_FILES['diplom_file_1'];
        $random_name_1 = "dipom_1_" . md5(time().rand(0, 10000000000000000));
        $file_type_1 = basename($diplom_file_1["type"]);
        $target_diplom_file_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
        $uploadOk_1 = 1;
        $file_type_1 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_1,PATHINFO_EXTENSION));
    
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_1)) {
            $diplom_file_1_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_1 = 0;
        }
        if ($diplom_file_1["size"] > 5000000) {
            $diplom_file_1_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_1 = 0;
        }
        if($file_type_1 != "jpg" && $file_type_1 != "png" && $file_type_1 != "jpeg" && $file_type_1 != "pdf") {
            $diplom_file_1_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk_1 = 0;
        }
        if ($uploadOk_1 == 0) {
            $diplom_file_1_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($diplom_file_1["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_1)) {
                $file_folder_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
                
                // istalgan o'lchamdagi rasm
                $size_1 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_1);
                list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_1);

                $new_diplom_file_id_1 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $diplom_file_1["name"],
                    "type" => $file_type_1,
                    "size" => $size_1,
                    "file_folder" => $file_folder_1
                ]);

                if (!$new_diplom_file_id_1){
                    echo '<script>alert("faylni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_diplom_file_id_1) {
                    delete_file($diplom_file_id_1);
                    $diplom_file_id_1 = $new_diplom_file_id_1;
                }
            } else {
                $diplom_file_1_error = "Kechirasiz, diplom 1-faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    if ($_FILES['diplom_file_2']["size"] != 0){
        $target_dir_2 = "files/upload/vakansiyalar/diplomlar/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_2)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_2, 0777, true);
        }

        $diplom_file_2 = $_FILES['diplom_file_2'];
        $random_name_2 = "dipom_2_" . md5(time().rand(0, 10000000000000000));
        $file_type_2 = basename($diplom_file_2["type"]);
        $target_diplom_file_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
        $uploadOk_2 = 1;
        $file_type_2 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_2,PATHINFO_EXTENSION));
    
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_2)) {
            $diplom_file_2_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_2 = 0;
        }
        if ($diplom_file_2["size"] > 5000000) {
            $diplom_file_2_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_2 = 0;
        }
        if($file_type_2 != "jpg" && $file_type_2 != "png" && $file_type_2 != "jpeg" && $file_type_2 != "pdf") {
            $diplom_file_2_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk_2 = 0;
        }
        if ($uploadOk_2 == 0) {
            $diplom_file_2_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($diplom_file_2["tmp_name"], $target_diplom_file_2)) {
                $file_folder_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
                
                // istalgan o'lchamdagi rasm
                $size_2 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_2);
                list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_2);

                $new_diplom_file_id_2 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $diplom_file_2["name"],
                    "type" => $file_type_2,
                    "size" => $size_2,
                    "file_folder" => $file_folder_2
                ]);

                if (!$new_diplom_file_id_2){
                    echo '<script>alert("faylni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_diplom_file_id_2) {
                    delete_file($diplom_file_id_2);
                    $diplom_file_id_2 = $new_diplom_file_id_2;
                }
            } else {
                $diplom_file_2_error = "Kechirasiz, diplom 2-faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    if ($_FILES['diplom_file_3']["size"] != 0){
        $target_dir_3 = "files/upload/vakansiyalar/diplomlar/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_3)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_3, 0777, true);
        }

        $diplom_file_3 = $_FILES['diplom_file_3'];
        $random_name_3 = "dipom_3_" . md5(time().rand(0, 10000000000000000));
        $file_type_3 = basename($diplom_file_3["type"]);
        $target_diplom_file_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
        $uploadOk_3 = 1;
        $file_type_3 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_3,PATHINFO_EXTENSION));
    
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_3)) {
            $diplom_file_3_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_3 = 0;
        }
        if ($diplom_file_3["size"] > 5000000) {
            $diplom_file_3_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_3 = 0;
        }
        if($file_type_3 != "jpg" && $file_type_3 != "png" && $file_type_3 != "jpeg" && $file_type_3 != "pdf") {
            $diplom_file_3_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk_3 = 0;
        }
        if ($uploadOk_3 == 0) {
            $diplom_file_3_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($diplom_file_3["tmp_name"], $target_diplom_file_3)) {
                $file_folder_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
                
                // istalgan o'lchamdagi rasm
                $size_3 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_3);
                list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_diplom_file_3);

                $new_diplom_file_id_3 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $diplom_file_3["name"],
                    "type" => $file_type_3,
                    "size" => $size_3,
                    "file_folder" => $file_folder_3
                ]);

                if (!$new_diplom_file_id_3){
                    echo '<script>alert("faylni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_diplom_file_id_3) {
                    delete_file($diplom_file_id_3);
                    $diplom_file_id_3 = $new_diplom_file_id_3;
                }
            } else {
                $diplom_file_3_error = "Kechirasiz, diplom 3-faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    if ($_FILES['tarjimai_xol_file']["size"] != 0){
        $target_dir_4 = "files/upload/vakansiyalar/tarjimai_xol/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_4)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir_4, 0777, true);
        }

        $tarjimai_xol = $_FILES['tarjimai_xol_file'];
        $random_name_4 = "tarjimai_xol_" . md5(time().rand(0, 10000000000000000));
        $file_type_4 = basename($tarjimai_xol["type"]);
        $target_tarjimai_xol_file = $target_dir_4 . $random_name_4 . ".$file_type_4";
        $uploadOk_4 = 1;
        $file_type_4 = strtolower(pathinfo($_SERVER["DOCUMENT_ROOT"]."/".$target_tarjimai_xol_file,PATHINFO_EXTENSION));
    
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_tarjimai_xol_file)) {
            $tarjimai_xol_file_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_4 = 0;
        }
        if ($tarjimai_xol["size"] > 5000000) {
            $tarjimai_xol_file_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_4 = 0;
        }
        if($file_type_4 != "jpg" && $file_type_4 != "png" && $file_type_4 != "jpeg" && $file_type_4 != "pdf") {
            $tarjimai_xol_file_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk_4 = 0;
        }
        if ($uploadOk_4 == 0) {
            $tarjimai_xol_file_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($tarjimai_xol["tmp_name"], $target_tarjimai_xol_file)) {
                $file_folder_4 = $target_dir_4 . $random_name_4 . ".$file_type_4";
                
                // istalgan o'lchamdagi rasm
                $size_4 = filesize($_SERVER["DOCUMENT_ROOT"]."/".$target_tarjimai_xol_file);
                list($width, $height) = getimagesize($_SERVER["DOCUMENT_ROOT"]."/".$target_tarjimai_xol_file);

                $new_tarjimai_xol_file_id = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $tarjimai_xol["name"],
                    "type" => $file_type_4,
                    "size" => $size_4,
                    "file_folder" => $file_folder_4
                ]);

                if (!$new_tarjimai_xol_file_id){
                    echo '<script>alert("faylni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_tarjimai_xol_file_id) {
                    delete_file($tarjimai_xol_file_id);
                    $tarjimai_xol_file_id = $new_tarjimai_xol_file_id;
                }
            } else {
                $tarjimai_xol_file_error = "Kechirasiz, tarjimai xol faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    $db->update("vakansiyalar", [
        "last_name" => $_POST["last_name"],
        "first_name" => $_POST["first_name"],
        "father_first_name" => $_POST["father_first_name"],
        "phone_1" => $_POST["phone_1"],
        "mutaxassisligi" => $_POST["mutaxassisligi"],
        "otm" => $_POST["otm"],
        "ilmiy_daraja" => $_POST["ilmiy_daraja"],
        "birth_date" => $_POST["birth_date"],
        "ish_tajribasi" => $_POST["ish_tajribasi"],
        "ish_tajribasi" => $_POST["ish_tajribasi"],
        "diplom_file_id_1" => $diplom_file_id_1,
        "diplom_file_id_2" => $diplom_file_id_2,
        "diplom_file_id_3" => $diplom_file_id_3,
        "passport_serial_number" => $_POST["passport_serial_number"],
        "tarjimai_xol_file_id" => $tarjimai_xol_file_id
    ], [
        "id" => $vakansiya["id"]
    ]);

    header("Location: vakansiyalar_list.php?page=".$_GET["page"]);
    exit;
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
                                <form action="edit_vakansiya.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_vakansiya" required>
                                    <input type="hidden" name="page" value="<?=($_GET["page"] ? $_GET["page"] : "1")?>" required>
                                    <input type="hidden" name="vakansiya_id" value="<?=$vakansiya["id"]?>" required>

                                    <div class="form-group">
                                        <label for="last_name">Familiya</label>
                                        <textarea name="last_name" class="form-control border-primary"><?=$vakansiya["last_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="first_name">Ism</label>
                                        <textarea name="first_name" class="form-control border-primary"><?=$vakansiya["first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="father_first_name">Otasining ismi</label>
                                        <textarea name="father_first_name" class="form-control border-primary"><?=$vakansiya["father_first_name"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_1">Telefon (1)</label>
                                        <input type="text" name="phone_1" class="form-control border-primary" value="<?=($vakansiya["phone_1"] ? $vakansiya["phone_1"] : "+998")?>" id="phone_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="mutaxassisligi">Mutaxassisligi</label>
                                        <textarea name="mutaxassisligi" class="form-control border-primary"><?=$vakansiya["mutaxassisligi"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="otm">OTM</label>
                                        <textarea name="otm" class="form-control border-primary"><?=$vakansiya["otm"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="ilmiy_daraja">Ilmiy daraja</label>
                                        <textarea name="ilmiy_daraja" class="form-control border-primary"><?=$vakansiya["ilmiy_daraja"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="birth_date">Tug'ilgan sanasi</label>
                                        <input type="date" name="birth_date" class="form-control border-primary" value="<?=$vakansiya["birth_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="ish_tajribasi">Ish tajribasi</label>
                                        <textarea name="ish_tajribasi" class="form-control border-primary"><?=$vakansiya["ish_tajribasi"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="diplom_file_1">Diplomini yuklang</label>
                                        <input type="file" name="diplom_file_1" class="form-control border-primary" id="diplom_file_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="diplom_file_2">Diplomini yuklang 2 (ixtiyoriy)</label>
                                        <input type="file" name="diplom_file_2" class="form-control border-primary" id="diplom_file_2">
                                    </div>

                                    <div class="form-group">
                                        <label for="diplom_file_3">Diplomini yuklang 3 (ixtiyoriy)</label>
                                        <input type="file" name="diplom_file_3" class="form-control border-primary" id="diplom_file_3">
                                    </div>

                                    <div class="form-group">
                                        <label for="tarjimai_xol_file">Tarjimai xolini yuklash</label>
                                        <input type="file" name="tarjimai_xol_file" class="form-control border-primary" id="tarjimai_xol_file">
                                    </div>

                                    <div class="form-group">
                                        <label for="passport_serial_number">Passport seriyasi hamda raqami</label>
                                        <input type="text" name="passport_serial_number" class="form-control border-primary" value="<?=$vakansiya["passport_serial_number"]?>" id="passport_serial_number">
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