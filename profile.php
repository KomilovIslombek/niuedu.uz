<?
$is_config = true;
if (empty($load_defined)) include 'load.php';

if (!$user_id || $user_id == 0){
    header("Location: /auth");
    exit;
} else if (empty($systemUser["request_id"])) {
    if ($systemUser["admin"] == 1) {
        echo 'siz admin lavozimidasiz profile bo\'limiga o\'tish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing va ariza topshirilgan akkauntga kiring';
        exit;
    } else {
        header("Location: /cv");
        exit;
    }
} else if (!$url[1]) {
    header("Location: /$url2[0]/profile/my");
    exit;
}

$request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $systemUser["request_id"] ]);
$firm = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $systemUser["request_id"] ]); 

if (empty($request["id"]) && empty($firm["id"])) {
    header("Location: /cv");
    exit;
}

if(!empty($firm["id"])) {
    header("Location: /$url2[0]/agent_profile/my");
}

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);

$confirmed_payment_click = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
    $request["id"],
    $request["code"],
]);

if (empty($confirmed_payment_click["id"])) {
    $confirmed_payment_payme = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
        $request["id"],
        $request["code"]
    ]);
}

if (!empty($confirmed_payment_click["id"])) {
    $payment = [
        "method" => '<b class="text-success">click orqali to\'langan</b>',
        "amount" => number_format($confirmed_payment_click["amount"], 0) . " so'm"
    ];
    $is_paid = true;
} else if (!empty($confirmed_payment_payme["id"])) {
    $payment = [
        "method" => '<b class="text-success">payme orqali to\'langan</b>',
        "amount" => number_format(($confirmed_payment_payme["amount"] / 100), 0) . " so'm"
    ];
    $is_paid = true;
} else {
    $payment = [
        "method" => '<b class="text-danger">to\'lov qilinmagan</b>',
        "amount" => "165,000 so'm"
    ];
    $is_paid = false;
}

if ($url[1] == "ruxsatnoma") {
    if ($is_paid) {
        
    }
}

// $request_ok = false;
$error = false;
$file_1_error = false;
$file_2_error = false;
$file_3_error = false;

if (!empty($request["file_id_1"])) $file_1_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_1"] ]);
if (!empty($request["file_id_2"])) $file_2_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_2"] ]);
if (!empty($request["file_id_3"])) $file_3_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_3"] ]);

if (!empty($file_1_arr["id"])) {
    if (file_exists($file_1_arr["file_folder"]  . "_thumb.jpg")) {
        $file_1_arr["file_folder"] = $file_1_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_1_arr["type"] == "pdf") {
            $file_1_arr["file_folder"] = "pdf/" . encode($file_1_arr["file_folder"]);
        } else {
            $file_1_arr["file_folder"] = $file_1_arr["file_folder"];
        }
    }

}

if (!empty($file_2_arr["id"])) {
    if (file_exists($file_2_arr["file_folder"]  . "_thumb.jpg")) {
        $file_2_arr["file_folder"] = $file_2_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_2_arr["type"] == "pdf") {
            $file_2_arr["file_folder"] = "pdf/" . encode($file_2_arr["file_folder"]);
        } else {
            $file_2_arr["file_folder"] = $file_2_arr["file_folder"];
        }
    }
}

if (!empty($file_3_arr["id"])) {
    if (file_exists($file_3_arr["file_folder"]  . "_thumb.jpg")) {
        $file_3_arr["file_folder"] = $file_3_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_3_arr["type"] == "pdf") {
            $file_3_arr["file_folder"] = "pdf/" . encode($file_3_arr["file_folder"]);
        } else {
            $file_3_arr["file_folder"] = $file_3_arr["file_folder"];
        }
    }
}

$profile_image = $file_1_arr["file_folder"];

if ($_POST["submit"] && !empty($request["id"])) {
    if ($_FILES['file_1']["size"] != 0){
        $target_dir_1 = "files/upload/3x4/";

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
            $file_1_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
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
        if ($request["file_id_1"]) {
            $file_id_1 = $request["file_id_1"];
        } else {
            $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
        }
    }

    if ($_FILES['file_2']["size"] != 0 && !$file_1_error){
        $target_dir_2 = "files/upload/passport/";

        if (!file_exists($target_dir_2)) {
            mkdir($target_dir_2, 0777, true);
        }

        $file_2 = $_FILES['file_2'];
        $random_name_2 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type_2 = basename($file_2["type"]);
        $target_file_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
        $uploadOk_2 = 1;
        $file_type_2 = strtolower(pathinfo($target_file_2,PATHINFO_EXTENSION));
    
        if (file_exists('../'.$target_file_2)) {
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
            if (move_uploaded_file($file_2["tmp_name"], $target_file_2)) {
                $file_folder_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
                
                // istalgan o'lchamdagi rasm
                $size_2 = filesize($target_file_2);
                list($width, $height) = getimagesize($target_file_2);

                $file_id_2 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file_2["name"],
                    "type" => $file_type_2,
                    "size" => $size_2,
                    "file_folder" => $file_folder_2
                ]);

                if (!$file_id_2){
                    $file_2_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            } else {
                $file_2_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        if ($request["file_id_2"]) {
            $file_id_2 = $request["file_id_2"];
        } else {
            $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
        }
    }

    if ($_FILES['file_3']["size"] != 0 && !$file_2_error){
        $target_dir_3 = "files/upload/diplom/";

        if (!file_exists($target_dir_3)) {
            mkdir($target_dir_3, 0777, true);
        }

        $file_3 = $_FILES['file_3'];
        $random_name_3 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type_3 = basename($file_3["type"]);
        $target_file_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
        $uploadOk_3 = 1;
        $file_type_3 = strtolower(pathinfo($target_file_3,PATHINFO_EXTENSION));
    
        if (file_exists('../'.$target_file_3)) {
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
            if (move_uploaded_file($file_3["tmp_name"], $target_file_3)) {
                $file_folder_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
                
                // istalgan o'lchamdagi rasm
                $size_3 = filesize($target_file_3);
                list($width, $height) = getimagesize($target_file_3);

                $file_id_3 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file_3["name"],
                    "type" => $file_type_3,
                    "size" => $size_3,
                    "file_folder" => $file_folder_3
                ]);

                if (!$file_id_3){
                    $file_3_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            } else {
                $file_3_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        if ($request["file_id_3"]) {
            $file_id_3 = $request["file_id_3"];
        } else {
            $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
        }
    }

    if ($file_1_error || $file_2_error || $file_3_error) {
        $error = "Fayl yuklashda xatolik!";
    }
    
    if (!$error) {
        foreach ($_POST as $key => $val) {
            $_POST[$key] = trim($val);
        }

        $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $_POST["direction_id"] ]);
        $new_code = idCode($direction["code"], $request["id"]);

        $db->update("requests", [
            "code" => $new_code,
            "first_name" => $_POST["first_name"],
            "last_name" => $_POST["last_name"],
            "father_first_name" => $_POST["father_first_name"],
            "sex" => $_POST["sex"],
            "birth_date" => $_POST["birth_date"],
            "phone_1" => $_POST["phone_1"],
            "phone_2" => $_POST["phone_2"],
            "direction" => $direction["short_name"],
            "direction_id" => $direction["id"],
            "learn_type" => $_POST["learn_type"],
            "passport_serial_number" => $_POST["passport_serial_number"],
            "exam_lang" => $_POST["exam_lang"],
            "exam_foreign_lang" => $_POST["exam_foreign_lang"],
            "file_id_1" => $file_id_1,
            "file_id_2" => $file_id_2,
            "file_id_3" => $file_id_3
        ], [
            "id" => $request["id"]
        ]);

        // to'lov idlarini ham update qilish uchun
        include "modules/changePaymentCode.php";
        changePaymentId($request["code"], $new_code);

        header("Location: /profile/edit");
    }
}

include "system/head.php";
?>

<link rel="stylesheet" href="theme/main/assets/css/profile.css?v=<?=filemtime("theme/main/assets/css/profile.css")?>">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=<?=filemtime("theme/main/assets/css/cv.css")?>">

<div class="container-fluid pt-100 mb-4 mt-4">
    <div class="row justify-content-center">
        <!-- LEFT MENUS START -->
        <div class="left-menu-wrapper">
            <a href="profile/my" class="menu-item <?=($url[1] == "my" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Profil
            </a>

            <a href="profile/shartnoma/download" class="menu-item <?=($url[1] == "shartnoma" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Shartnomani yuklab olish
            </a>

            <a href="https://test.niuedu.uz" target="_blank" class="menu-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right-circle"><circle cx="12" cy="12" r="10"></circle><polyline points="12 16 16 12 12 8"></polyline><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                Imtihon topshirish
            </a>

            <a href="profile/imtihon-natijasi" class="menu-item <?=($url[1] == "imtihon-natijasi" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Imtihon natijasini bilish
            </a>

            <!-- <a href="profile/edit" class="menu-item <?=($url[1] == "edit" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Arizani tahrirlash
            </a> -->

            <a href="profile/payments" class="menu-item <?=($url[1] == "payments" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                To'lovlarim
            </a>

            <a href="/exit" class="menu-item">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Akkauntdan chiqish
            </a>
        </div>
        <!-- LEFT MENUS - END -->
        
        <? if ($url[1] == "my") { ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper">
                <div class="profile-information-section">
                    <div class="cover-bg">
                        <div class="profile-image">
                            <img src="<?=$profile_image?>" id="avatar" alt="avatar" onclick="$('#input').click()">
                        </div>
                    </div>
                    <div class="full-name">
                        <?=$request["first_name"]." ".$request["last_name"]?>
                    </div>
                    <div class="profile-type">
                        ID: <b><?=$request["code"]?></b>
                    </div>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>

        <? if ($url[1] == "shartnoma") { ?>
            <!-- https://niuedu.uz/uz/cv/check/shartnoma/2-tomonlama?id=20110613&submit=Tekshirish+%C2%BB -->
            <?
            $url[0] = "cv"; $url2[1] = "cv";
            $url[1] = "check"; $url2[2] = "check";
            $url[2] = "shartnoma"; $url2[3] = "shartnoma";
            $url[3] = "2-tomonlama"; $url2[4] = "2-tomonlama";
            $_GET["id"] = $request["code"];
            $_GET["submit"] = "ok";
            $old_request = $request;
            ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3 text-center">
                    <? include "modules/shartnoma.php"; ?>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>

        <? if ($url[1] == "imtihon-natijasi") { ?>
            <!-- https://niuedu.uz/uz/cv/check/shartnoma/2-tomonlama?id=20110613&submit=Tekshirish+%C2%BB -->
            <?
            $url[0] = "cv"; $url2[1] = "cv";
            $url[1] = "natija"; $url2[2] = "natija";
            // $url[2] = "shartnoma"; $url2[3] = "shartnoma";
            // $url[3] = "2-tomonlama"; $url2[4] = "2-tomonlama";
            $_GET["id"] = $request["code"];
            $_GET["submit"] = "ok";
            $old_request = $request;
            ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3 text-center">
                    <? include "modules/shartnoma.php"; ?>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>

        <? if ($url[1] == "edit") { ?>
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2">
                    <form action="/<?=$url[0]?>/<?=$url[1]?>" method="POST" id="msform" enctype="multipart/form-data">
                        <div class="form-card">
                            <? if ($error) { ?>
                                <h3 class="text-center text-danger"><?=$error?></h3>
                            <? } ?>
                            
                            <label class="fieldlabels" for="last_name">Familiyangiz (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="last_name" placeholder="Familiyangiz (Lotin alifbosida)" id="last_name" required="" value="<?=htmlspecialchars($request["last_name"])?>">
            
                            <label class="fieldlabels" for="first_name">Ismingiz (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="first_name" placeholder="Ismingiz (Lotin alifbosida)" id="first_name" required="" value="<?=htmlspecialchars($request["first_name"])?>">
            
                            <label class="fieldlabels" for="father_first_name">Otangizninig ismi (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="father_first_name" placeholder="Otangizninig ismi (Lotin alifbosida)" id="father_first_name" required="" value="<?=htmlspecialchars($request["father_first_name"])?>">

                            <label class="fieldlabels" for="passport_serial_number">Passport seriyasi hamda raqami <label class="text-danger">*</label></label>
                            <input type="text" name="passport_serial_number" placeholder="Passport seriyasi hamda raqami" id="passport_serial_number" required="" value="<?=htmlspecialchars($request["passport_serial_number"])?>">
            
                            <label class="fieldlabels" for="sex">Jinsingiz <label class="text-danger">*</label></label>
                            <label class="checkbox-container">Erkak
                                <input type="radio" name="sex" value="erkak" checked="checked" required="" <?=($request["sex"] == "erkak" ? 'checked=""' : '')?>>
                                <span class="checkmark"></span>
                            </label>
                            <label class="checkbox-container">Ayol
                                <input type="radio" name="sex" value="ayol" required="" <?=($request["sex"] == "ayol" ? 'checked=""' : '')?>>
                                <span class="checkmark"></span>
                            </label>
            
                            <label class="fieldlabels" for="birth_date">Tug'ilgan sanangiz (20.01.2001) <label class="text-danger">*</label></label>
                            <input type="date" name="birth_date" placeholder="Tug'ilgan sanangiz (20.01.2001)" id="birth_date" required="" value="<?=htmlspecialchars($request["birth_date"])?>">
            
                            <label class="fieldlabels" for="phone_1">Telefon raqamingiz <label class="text-danger">*</label></label>
                            <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($request["phone_1"] ? htmlspecialchars($request["phone_1"]) : "+998")?>">
            
                            <label class="fieldlabels" for="phone_2">Qo'shimcha telefon raqam <label class="text-danger">*</label></label>
                            <input type="text" name="phone_2" placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($request["phone_2"] ? htmlspecialchars($request["phone_2"]) : "+998")?>">
            
                            <label for="direction" class="fieldlabels">Ta'lim yo'nalishi <b id="d_h"></b><label class="text-danger">*</label></label>
                            <select name="direction_id" id="direction" required="">
                                <?
                                $directions = $db->in_array("SELECT * FROM directions WHERE active = 1");
                                foreach ($directions as $direction) {
                                    echo '<option value="'.$direction["id"].'" '.($request["direction_id"] == $direction["id"] ? 'selected=""' : '').' data-direction-id="'.$direction["id"].'">'.$direction["short_name"].'</option>';
                                }
                                ?>
                            </select>
            
                            <label for="learn_type" class="fieldlabels">Ta'lim shakli <b id="html"></b><label class="text-danger">*</label></label>
                            <select name="learn_type" id="learn_type" required="">
                                <option value="Kunduzgi" id="option_kunduzgi" <?=($request["learn_type"] == "Kunduzgi" ? 'selected=""' : "")?>>Kunduzgi</option>
                                <option value="Kechki" id="option_kechki" <?=($request["learn_type"] == "Kechki" ? 'selected=""' : "")?>>Kechki</option>
                                <option value="Sirtqi" id="option_sirtqi" <?=($request["learn_type"] == "Sirtqi" ? 'selected=""' : "")?>>Sirtqi</option>
                            </select>

                            <label for="exam_lang" class="fieldlabels">Test imtihonini qaysi tilda topshirasiz? <label class="text-danger">*</label></label>
                            <select name="exam_lang" id="exam_lang" required="">
                                <option value="uz" <?=($request["exam_lang"] == "uz" ? 'selected=""' : "")?>>O’zbek tili</option>
                                <option value="ru" <?=($request["exam_lang"] == "ru" ? 'selected=""' : "")?>>Rus tili</option>
                            </select>

                            <label for="exam_foreign_lang" class="fieldlabels">Chet tili imtihonini qaysi tilda topshirasiz? <label class="text-danger">*</label></label>
                            <select name="exam_foreign_lang" id="exam_foreign_lang" required="">
                                <option value="ar" <?=($request["exam_foreign_lang"] == "ar" ? 'selected=""' : "")?>>Arab tili</option>
                                <option value="en" <?=($request["exam_foreign_lang"] == "en" ? 'selected=""' : "")?>>Ingliz tili</option>
                            </select>
            
                            <p>
                                <h5 style="font-weight:500;">3x4 sm o‘lchamdagi, kamida 1200x720 piksel kenglikka ega rangli fotosurat, yuzning old tomondan olingan aniq tasviri. Suratdagi yuzning o‘lchami fotosurat hajmining kamida 50 foizini tashkil qilishi kerak.</h5>
                            </p>
            
                            <p>
                                <h5 class="text-success" style="font-weight:500;">* Yuklanadigan fayl formatlari: png, jpeg, jpg</h5>
                            </p>
            
                            <p>
                                <h5 class="text-success" style="font-weight:500;">* Fayl hajmi 3 MB dan oshmasin</h5>
                            </p>
            
                            <p>
                                <h5 class="text-danger" style="font-weight:500;">DIQQAT!!! Doimiy aloqada bo’lgan telefon nomeringizni kiriting va qabul bo‘yicha yuborilgan sms kod va xabarnomalarni saqlab qo’ying</h5>
                            </p>

                            <!-- Fayllar -->
                            
                            <? if ($file_1_error) { ?>
                                <h5 class="text-danger"><?=$file_1_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="file_1">3.5X4.5 hajmdagi rasmingizni yuklang <label class="text-danger">*</label></label>
                            <? if (isset($file_1_arr) && file_exists($file_1_arr["file_folder"])) { ?>
                                <img src="<?=($file_1_arr["file_folder"])?>" width="40%">
                            <? } ?>
                            <input type="file" name="file_1" id="file_1" <?=(empty($request["file_id_1"]) ? 'required=""' : '')?>>
                            
                            <!--  -->

                            <? if ($file_2_error) { ?>
                                <h5 class="text-danger"><?=$file_2_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="file_2">Pasportingiz nusxasini yuklang <label class="text-danger">*</label></label>
                            <? if (isset($file_2_arr) && file_exists($file_2_arr["file_folder"])) { ?>
                                <img src="<?=($file_2_arr["file_folder"])?>" width="40%">
                            <? } ?>
                            <input type="file" name="file_2" id="file_2" <?=(empty($request["file_id_1"]) ? 'required=""' : '')?>>
                            
                            <!--  -->

                            <? if ($file_3_error) { ?>
                                <h5 class="text-danger"><?=$file_3_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="file_3">O'rta maktab attestati yoki o'rta maxsus bilim yurti diplomini yuklang <label class="text-danger">*</label></label>
                            <? if (isset($file_3_arr) && file_exists($file_3_arr["file_folder"])) { ?>
                                <img src="<?=($file_3_arr["file_folder"])?>" width="40%">
                            <? } ?>
                            <input type="file" name="file_3" id="file_3" <?=(empty($request["file_id_1"]) ? 'required=""' : '')?>>

                            <!-- end Fayllar -->

                            <input type="submit" name="submit" class="next action-button" value="Saqlash" style="width:130px;">
                        </div>
                    </form>
                </div>
            </div>
        <? } ?>

        <? if ($url[1] == "payments") { ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>nomi</th>
                                <th>summa</th>
                                <th>to'lov holati</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>imtihon uchun to'lov</td>
                                <td><?=$payment["amount"]?></td>
                                <td><?=$payment["method"]?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>
    </div>

</div>


<? include "system/scripts.php"; ?>

<? if ($url[1] == "edit") { ?>
<script>
    $(document).ready(function(){
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        $(document).on("change, input", ".error-form", function(){
            if ($(this).val().length == 0) {
                $(this).addClass("error-form");
            } else {
                $(this).removeClass("error-form");
            }
        });

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
        var selected_learn_type = "<?=$_POST["learn_type"]?>";

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

        $("input[type='file']").on("change", function() {
            console.log(this.files[0]);
            if ((this.files[0].size / 1024 / 1024) > 10) {
                $(this).attr("type", "text");
                $(this).attr("type", "file");
                alert("Fayl hajmi 10MB dan oshlmasligi kerak! Iltimos faylni qayta yuklang!");
            }
        });

        $("#passport_serial_number").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 9));
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

        function changeAcceptOfferta() {
            if ($("#accept_offerta:checked").val() == "ha") {
                $("#payment_link").show();
                $("#payment_link_false").hide();
            } else {
                $("#payment_link").hide();
                $("#payment_link_false").show();
            }
        }

        $("input[name='accept_offerta']").click(function(){
            changeAcceptOfferta();
        });

        $("#payment_link_false").click(function(){
            alert("Tolovni amalga oshirish uchun avval ommaviy offerta shartlariga rozilik bildiring!");
        })
    });
</script>
<? } ?>

<? include 'system/end.php'; ?>