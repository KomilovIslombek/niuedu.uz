<?
date_default_timezone_set("Asia/Tashkent");

$is_config = true;

if (empty($load_defined)) include 'load.php';

// echo "<pre>";
// print_r($_FILES);
// exit;

if(!empty($_GET["agent_id"])) {
    addCookie("agent_id", $_GET["agent_id"]);
    $_COOKIE["agent_id"] = $_GET["agent_id"];
} 

if(!empty($_COOKIE["agent_id"])) {
    $agent_id = $_COOKIE["agent_id"];
} else if(!empty($_GET["agent_id"])) {
    $agent_id = $_GET["agent_id"];
}

$request_ok = false;
$error = false;
$file_1_error = false;
$file_2_error = false;
$file_3_error = false;
$file_4_error = false;
$file_staj_error = false;
$imtihondan_otkan = false;

// $qabul_off = true;
$oqishni_kochirish_off = false;
$ikkinchi_mutaxassislik_off = false;
// if (time() > strtotime("2022-11-14 07:00:00")) $qabul_off = false;
// exit(date("Y-m-d H:i:s"));
// if (strtotime("2023-02-07 20:00") <= strtotime(date("Y-m-d H:i"))) $qabul_off = false;
if ($user_id) $qabul_off = false; // admin uchun ariza topshirishni ochish
if ($user_id) $ikkinchi_mutaxassislik_off = false; // admin uchun ariza topshirishni ochish

if ($url[0] == "shartnoma" && $url[1]) {
    $json = decode($url[1]);
    $json = json_decode($json, true);
    $code = $json["c"];
    $shartnoma = $json["s"];

    $old_request = $db->assoc("SELECT * FROM requests WHERE code = ?", [
        $code
    ]);
    if (empty($old_request["id"])) {
        $old_request = $db->assoc("SELECT * FROM requests WHERE code = ?", [
            $code
        ]);
    }
}

if ($_POST["submit"] || $_GET["submit"]) {
    // header("Content-type: text/plain");
    // print_r([
    //     "POST" => $_POST,
    //     "FILES" => $_FILES
    // ]);
    // exit;

    if (!empty($_POST["passport_serial"]) && !empty($_POST["passport_number"])) {
        $_POST["passport_serial_number"] = $_POST["passport_serial"] . " " . $_POST["passport_number"];
    }

    if (isset($_REQUEST["id"])) {
        $_REQUEST["id"] = trim($_REQUEST["id"]);
        $old_request = $db->assoc("SELECT * FROM requests WHERE code = ?", [
            $_REQUEST["id"]
        ]);
        if (empty($old_request["id"])) {
            $old_request = $db->assoc("SELECT * FROM requests WHERE code = ?", [
                $_REQUEST["id"]
            ]);
        }
    } else {
        $old_request = $db->assoc("SELECT * FROM requests WHERE passport_serial_number = ?", [
            trim($_POST["passport_serial_number"])
        ]);
        if (empty($old_request["id"])) {
            $old_request = $db->assoc("SELECT * FROM requests WHERE passport_serial_number = ?", [
                trim($_POST["passport_serial_number"])
            ]);
        }
    }

    $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $old_request["direction_id"] ]);

    if (isset($_REQUEST["id"])) {
        $_REQUEST["id"] = trim($_REQUEST["id"]);
        $imtihondan_otkan_talaba = $db->assoc("SELECT * FROM requests WHERE code = ? AND suhbat = 1", [
            $_REQUEST["id"]
        ]);

        $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $imtihondan_otkan_talaba["direction_id"] ]);
    } else {
        $imtihondan_otkan_talaba = $db->assoc("SELECT * FROM requests WHERE passport_serial_number = ? AND suhbat = 1", [
            trim($_POST["passport_serial_number"])
        ]);
    }

    if (!empty($imtihondan_otkan_talaba["id"])) {
        $imtihondan_otkan = true;
    } else if (!empty($old_request["id"])) {
        $request_ok = true;

        $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $old_request["direction_id"] ]);

        if ($url[1] == "qayta-imtihon-topshirish") {
            $confirmed_payment_payme = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
                $old_request["id"],
                $old_request["code"]
            ]);

            $db->update("transactions", [
                "hide" => 1
            ], [
                "order_id" => $old_request["code"],
                "state" => 2
            ]);

            $db->update("orders", [
                "hide" => 1
            ], [
                "user_id" => $old_request["code"],
                "state" => 2
            ]);

            $db->update("requests", ["suhbat" => 0], ["code" => $old_request["code"]]);

            $db->delete("quiz_user_block", $old_request["code"], "student_code");
            $db->delete("quiz_user_options", $old_request["code"], "student_code");
            header("Location: /$url2[0]/cv/check?id=".$old_request["code"]."%&submit=Tekshirish");
            exit;
        }
    } else {
        $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $_POST["direction_id"] ]);

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
            // $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
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
            // $file_2_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
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
            // $file_3_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
        }

        if ($_FILES['file_4']["size"] != 0){
            $target_dir_4 = "files/upload/dtm_natija/";
    
            if (!file_exists($target_dir_4)) {
                mkdir($target_dir_4, 0777, true);
            }
    
            $file_4 = $_FILES['file_4'];
            $random_name_4 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_4 = basename($file_4["type"]);
            $target_file_4 = $target_dir_4 . $random_name_4 . ".$file_type_4";
            $uploadOk_4 = 1;
            $file_type_4 = strtolower(pathinfo($target_file_4,PATHINFO_EXTENSION));
        
            if (file_exists('../'.$target_file_4)) {
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
                if (move_uploaded_file($file_4["tmp_name"], $target_file_4)) {
                    $file_folder_4 = $target_dir_4 . $random_name_4 . ".$file_type_4";
                    
                    // istalgan o'lchamdagi rasm
                    $size_4 = filesize($target_file_4);
                    list($width, $height) = getimagesize($target_file_4);
    
                    $file_id_4 = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $file_4["name"],
                        "type" => $file_type_4,
                        "size" => $size_4,
                        "file_folder" => $file_folder_4
                    ]);
    
                    if (!$file_id_4){
                        $file_4_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_4_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        }

        if ($_FILES['file_staj']["size"] != 0){
            $target_dir_staj = "files/upload/staj/";
    
            if (!file_exists($target_dir_staj)) {
                mkdir($target_dir_staj, 0777, true);
            }
    
            $file_staj = $_FILES['file_staj'];
            $random_name_staj = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
            $file_type_staj = basename($file_staj["type"]);
            $target_file_staj = $target_dir_staj . $random_name_staj . ".$file_type_staj";
            $uploadOk_staj = 1;
            $file_type_staj = strtolower(pathinfo($target_file_staj,PATHINFO_EXTENSION));
        
            if (file_exists('../'.$target_file_staj)) {
                $file_staj_error = "Kechirasiz, fayl allaqachon mavjud.";
                $uploadOk_staj = 0;
            }
            if ($file_staj["size"] > 5000000) {
                $file_staj_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
                $uploadOk_staj = 0;
            }
            if($file_type_staj != "jpg" && $file_type_staj != "png" && $file_type_staj != "jpeg" && $file_type_staj != "pdf") {
                $file_staj_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG fayllariga ruxsat berilgan.";
                $uploadOk_staj = 0;
            }
            if ($uploadOk_staj == 0) {
                $file_staj_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
            } else {
                if (move_uploaded_file($file_staj["tmp_name"], $target_file_staj)) {
                    $file_folder_staj = $target_dir_staj . $random_name_staj . ".$file_type_staj";
                    
                    // istalgan o'lchamdagi rasm
                    $size_staj = filesize($target_file_staj);
                    list($width, $height) = getimagesize($target_file_staj);
    
                    $file_id_staj = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $file_staj["name"],
                        "type" => $file_type_staj,
                        "size" => $size_staj,
                        "file_folder" => $file_folder_staj
                    ]);
    
                    if (!$file_id_staj){
                        $file_staj_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $file_staj_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        }

        if ($file_1_error || $file_2_error || $file_3_error || $file_4_error) {
            $error = "Fayl yuklashda xatolik!";
        }

        if (!$_POST["phone_1"]) {
            $error = "telefon raqamni kiritishni unutdingiz !!!";
        } else if (strlen($_POST["phone_1"]) != 17 || substr($_POST["phone_1"], 0, 4) != "+998") {
            $error = "telefon raqam noto'g'ri formatda kiritilgan !!!";
        }

        if (!$_POST["first_name"] || !$_POST["last_name"] || !$_POST["father_first_name"] || !$_POST["sex"]) {
            $error = "Ma'lumotlarni to'ldirishni unutdingiz!";
        }
        
        if (!$error) {
            // foreach ($_POST as $key => $val) {
            //     $_POST[$key] = trim($val);
            // }
            if ($agent_id > 0 && ($url[1] != "oddiy" && $url[1] != "ikkinchi-mutaxassislik")) {
                $agent_id = null;
            }

            $request_id = $db->insert("requests", [
                "first_name" => $_POST["first_name"],
                "last_name" => $_POST["last_name"],
                "father_first_name" => $_POST["father_first_name"],
                "sex" => $_POST["sex"],
                "birth_date" => $_POST["birth_date"],
                "phone_1" => $_POST["phone_1"],
                "phone_2" => $_POST["phone_2"],
                "direction" => lng($direction["short_name"]),
                "direction_id" => $direction["id"],
                "learn_type" => $_POST["learn_type"],
                "passport_serial_number" => $_POST["passport_serial_number"],
                "passport_jshir" => $_POST["passport_jshir"],
                "exam_lang" => $_POST["exam_lang"],
                "exam_foreign_lang" => $_POST["exam_foreign_lang"],
                "file_id_1" => $file_id_1,
                "file_id_2" => $file_id_2,
                "file_id_3" => $file_id_3,
                "file_id_4" => $file_id_4,
                "staj_file_id" => $file_id_staj,
                // "from_country_name" => $_POST["from_country_name"],
                // "from_university" => $_POST["from_university"],
                // "from_fakultet" => $_POST["from_fakultet"],
                // "from_learn_type" => $_POST["from_learn_type"],
                // "from_course" => $_POST["from_course"],
                // "to_university" => $_POST["to_university"],
                "to_course" => ($url[1] == "oqishni-kochirish" || $url[1] == "ikkinchi-mutaxassislik" ? $_POST["to_course"] : "1-kurs"),
                "reg_type" => $url[1],
                "region_id" => $_POST["region_id"],
                "district_id" => $_POST["district_id"],
                "adress" => htmlspecialchars($_POST["adress"]),
                "firm_id" => $agent_id
            ]);

            // if ($agent_id > 0) {
            //     $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $agent_id ]);
            // }
        }
    
        if ($request_id > 0) {
            removeCookie("agent_id");
            // $db2->query("ALTER TABLE requests AUTO_INCREMENT = $request_id");
            // $db->query("ALTER TABLE requests AUTO_INCREMENT = $request_id");

            $db->update("requests", ["code" => idCode($direction["code"], $request_id)], ["id" => $request_id]);

            include "modules/bot.php";

            // if ($file_id_2 > 0 && $file_id_3 > 0) {
            if (1 == 1) {
                $code = idCode($direction["code"], $request_id);

                $text = "";
                if($agent) {
                    $text .= '('.$agent["id"].') '.$agent["last_name"]." ".$agent["first_name"]. " Agent tomonidan qo'shilayotgan ariza";
                }
                $text .= "\n#ariza_".$code . " #yozgi";
                $text .= "\n\nIsm: <b>".$_POST["first_name"]."</b>";
                $text .= "\nFamiliya: <b>".$_POST["last_name"]."</b>";
                $text .= "\nOtasining ismi: <b>".$_POST["father_first_name"]."</b>";
                $text .= "\nPassport seriyasi hamda raqami: <b>".$_POST["passport_serial_number"]."</b>";
                $text .= "\nJinsi: <b>".$_POST["sex"]."</b>";
                $text .= "\nTug'ilgan sanasi: <b>".$_POST["birth_date"]."</b>";
                $text .= "\nTelefon 1: <b>".$_POST["phone_1"]."</b>";
                $text .= "\nTelefon 2: <b>".$_POST["phone_2"]."</b>";

                if (!empty($_POST["to_course"])) {
                    $text .= "\n_____________\n";
                    // $text .= "\nUshu davlatdan o'qishni ko'chirmoqchi: <b>".$_POST["from_country_name"]."</b>";

                    // $text .= "\nUniversitet: <b>".$_POST["from_university"]."</b>";

                    // $text .= "\nFakultet: <b>".$_POST["from_fakultet"]."</b>";

                    // $text .= "\nTa'lim shakli: <b>".$_POST["from_learn_type"]."</b>";

                    // $text .= "\nKursni tugatdi: <b>".$_POST["from_course"]."</b>";

                    $text .= "\nKursga o'tmoqchi: <b>".$_POST["to_course"]."</b>";
                    $text .= "\n_____________\n";
                }

                $text .= "\nTa'lim yo'nalishida o'qimoqchi: <b>".lng($direction["short_name"])."</b>";
                $text .= "\nTa'lim shakli: <b>".$_POST["learn_type"]."</b>";

                $caption = "Ism: <b>".$_POST["first_name"]."</b>\nFamiliya: <b>".$_POST["last_name"]."</b>\nOtasining ismi: <b>".$_POST["father_first_name"]."</b>\nTelefon raqami: <b>".$_POST["phone_1"]."</b>";

                if ($url[1] == "ikkinchi-mutaxassislik") {
                    $groups = ["-1001977142854"];
                } else if ($url[1] == "oqishni-kochirish") {
                    $groups = ["-1001631992544"];
                    // $groups = ["166975358"];
                } else {
                    $groups = ["-1001790361422"];
                    // $groups = ["166975358"];
                }

                foreach ($groups as $admin_id) {
                    $res_msg = bot("sendMessage", [
                        "chat_id" => $admin_id,
                        "text" => "<b>".$_SERVER['HTTP_HOST']."\n\nSayt orqali ariza qoldirishdi!</b>\n\n$text",
                        "parse_mode" => "html"
                    ]);

                    if ($res_msg["ok"] != true) {
                        // $error = "fayllarni yuklashda xatolik yuzaga keldi!";
                    } else {
                        $message_id = $res_msg["result"]["message_id"];
                    }
        
                    // $res_1 = sendDocument($admin_id, $file_folder_1, "#ariza_".$code."_file_1", $message_id);
                    // if ($res_1["ok"] != true) {
                    //     $error = "fayllarni yuklashda xatolik yuzaga keldi!!";
                    // }
        
                    // $res_2 = sendDocument($admin_id, $file_folder_2, "#ariza_".$code."_file_2", $message_id);
                    // if ($res_2["ok"] != true) {
                    //     $error = "fayllarni yuklashda xatolik yuzaga keldi!!!";
                    // }
        
                    // $res_3 = sendDocument($admin_id, $file_folder_3, "#ariza_".$code."_file_3", $message_id);
                    // if ($res_3["ok"] != true) {
                    //     $error = "fayllarni yuklashda xatolik yuzaga keldi!!!!";
                    // }

                    // if ($file_id_4 > 0) {
                    //     $res_4 = sendDocument($admin_id, $file_folder_4, "#ariza_".$code."_file_4", $message_id);
                    //     if ($res_4["ok"] != true) {
                    //         $error = "fayllarni yuklashda xatolik yuzaga keldi!!!!";
                    //     }
                    // }
                }

                if ($error) {
                    $db->delete("requests", $request_id);
                    unlink($file_folder_1);
                    unlink($file_folder_2);
                    unlink($file_folder_3);
                    if ($file_id_4 > 0) {
                        unlink($file_folder_4);
                    }
                }
            } else {
                $error = "fayylarni yuklashda xatolik yuzaga keldi!";
            }

            if ($error == false && $file_1_error == false && $file_2_error == false && $file_3_error == false) {
                $request_ok = true;

                $old_request = $db->assoc("SELECT * FROM requests WHERE id = ?", [ $request_id ]);
            }
        }
    }

}

if ($url[1] == "success" && isset($url[2])) {
    $request_id = decode($url[2]);
    $old_request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $request_id ]);

    if (!empty($old_request["id"])) {
        $url[1] = "check";
        $request_ok = true;

        $direction = $db->assoc("SELECT id, code, name, short_name FROM directions WHERE id = ?", [ $old_request["direction_id"] ]);
    }
}

if (!$no_header) include "system/head.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=1.0.2">

<main>
    <? if (isset($url[1]) && ($url[1] == "check" || $url[1] == "shartnoma") && empty($old_request["id"])) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <H2 id="heading">NAVOIY INNOVATSIYALAR UNIVERSITETI</h2>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        <form action="/<?=$url2[0]?>/<?=$url[0]?>/check<?=($url[2] ? "/shartnoma/$url[2]" : "")?>" method="GET" id="msform" enctype="multipart/form-data">
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-12">
                                        <? if ($url[1] == "shartnoma") { ?>
                                            <h2 class="fs-title text-danger"><?=t("Shartnomani yuklab olish uchun id raqamingizni kiriting")?></h2>
                                        <? } else { ?>
                                            <h2 class="fs-title text-danger"><?=t("Ariza xolatini tekshirish yoki imtihon natijalarini bilish uchun id raqamingizni kiriting")?></h2>
                                        <? } ?>
                                    </div>
                                </div>
    
                                <label class="fieldlabels" for="id">ID<label class="text-danger">*</label></label>
                                <input type="text" name="id" placeholder="ID" id="id" value="<?=htmlspecialchars($_REQUEST["id"])?>" required="">
                            </div>
                                    
                            <input type="submit" name="submit" class="next action-button" value="<?=t("Tekshirish")?> »" style="width: 145px;">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <? } else if (isset($url[1]) && $url[1] == "natija" && empty($old_request["id"])) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <H2 id="heading">NAVOIY INNOVATSIYALAR UNIVERSITETI</h2>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        <form action="/<?=$url2[0]?>/<?=$url[0]?>/natija" method="GET" id="msform" enctype="multipart/form-data">
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-12">
                                        <h2 class="fs-title text-danger"><?=t("Ijodiy (test) imtihon natijasini bilish uchun id raqamingizni kiriting")?></h2>
                                    </div>
                                </div>
    
                                <label class="fieldlabels" for="id">ID<label class="text-danger">*</label></label>
                                <input type="text" name="id" placeholder="ID" id="id" value="<?=htmlspecialchars($_REQUEST["id"])?>" required="">
                            </div>
                                    
                            <input type="submit" name="submit" class="next action-button" value="<?=t("Tekshirish")?> »" style="width: 145px;">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <? } else if (isset($url[1]) && ($url[1] == "check" || $url[1] == "natija" || $url[1] == "shartnoma" || $url[0] == "shartnoma") && (!empty($old_request["id"]))) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <?
                        include "modules/shartnoma.php";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <? } else if ($request_ok == true) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <h2 id="heading"><?=t("NAVOIY INNOVATSIYALAR UNIVERSITETIGA TOPSHIRGAN ARIZANGIZ")?></h2>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        
                        <fieldset>
                            <div class="form-card">
                                <h2 class="purple-text text-center"><strong><?=t("YUBORILDI !")?></strong></h2> <br>
                                <div class="row justify-content-center">
                                    <div class="col-3"> <img src="images/check.png" class="fit-image"> </div>
                                </div> <br><br>
                                <div class="row justify-content-center">
                                    <div class="col-7 text-center">
                                        <h5 class="purple-text text-center mb-2"><?=t("Online imtihon topshirish uchun «Imtihon topshirish» tugmasini bosing")?></h5>

                                        <h3>
                                            <a href="<?=$url2[0]?>/cv/check?id=<?=$old_request["code"]?>&submit=Tekshirish" class="btn btn-success text-white mb-2 btn-lg"><?=t("Imtihon topshirish")?></a>
                                        </h3>

                                        <h5><?=str_replace("https://t.me/niuedu_uz", '<a href="https://t.me/niuedu_uz" class="text-info">https://t.me/niuedu_uz</a>', t("Bizni https://t.me/niuedu_uz orqali kuzatib boring"))?></h5>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                            
                        <table class="table table-hover mt-3 mb-3">
                            <tr>
                                <th><?=t("ID raqami")?></th>
                                <td><b><?=(idCode($direction["code"], $old_request["id"]))?></b></td>
                            </tr>
                            <tr>
                                <th><?=t("Ta'lim yo'nalishi")?></th>
                                <td><b><?=lng($direction["name"])?></b></td>
                            </tr>
                            <tr>
                                <th><?=t("Ta'lim shakli")?></th>
                                <td><b><?=$old_request["learn_type"]?></b></td>
                            </tr>
                            <tr>
                                <th><?=t("Tug'ilgan sanasi")?></th>
                                <td><b><?=$old_request["birth_date"]?></b></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <? } else if ($url[1] == "oqishni-kochirish" && !$qabul_off) {
        include "oqishni-kochirish.php";
    } else if ($url[1] == "ikkinchi-mutaxassislik" && !$qabul_off) {
        include "ikkinchi-mutaxassislik.php";
    } else if ($url[1] == "oddiy" && !$qabul_off) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3 text-center">
                    <h2 id="heading"><?=t("ARIZA TOPSHIRISH")?></h2>
                    <!-- <p>Fill all form field to go to next step</p> -->
                    <form action="/<?=$url2[0]?>/<?=$url2[1]?>/<?=$url2[2]?>" method="POST" id="msform" enctype="multipart/form-data">
                        <div class="form-card">
                            <div class="row">
                                <h2 class="fs-title text-danger text-center mb-4"><?=t("Shaxsiy ma'lumotlar")?></h2>
                            </div>
    
                            <? if ($error) { ?>
                                <h3 class="text-center text-danger"><?=$error?></h3>
                            <? } ?>
    
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2">
                                    <? if ($file_1_error) { ?>
                                        <h5 class="text-danger"><?=$file_1_error?></h5>
                                    <? } ?>
                                    <label class="fieldlabels" for="file_1"><?=t("3.5X4.5 hajmdagi rasmingizni yuklang")?> <label class="text-danger">*</label></label>
                                    <input type="file" name="file_1" id="file_1" style="display:none" required="">
                                    <div class="input-image" id="input-image">
                                        <i class="fa fa-plus"></i>
                                        <span><?=t("Yuklash")?></span>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="first_name"><?=t("Ismingiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="first_name" placeholder="<?=t("Ismingiz (Lotin alifbosida)")?>" id="first_name" required="" value="<?=htmlspecialchars($_POST["first_name"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="last_name"><?=t("Familiyangiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="last_name" placeholder="<?=t("Familiyangiz (Lotin alifbosida)")?>" id="last_name" required="" value="<?=htmlspecialchars($_POST["last_name"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="father_first_name"><?=t("Otangizninig ismi (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="father_first_name" placeholder="<?=t("Otangizninig ismi (Lotin alifbosida)")?>" id="father_first_name" required="" value="<?=htmlspecialchars($_POST["father_first_name"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="birth_date"><?=t("Tug'ilgan sanangiz (20.01.2001)")?> <label class="text-danger">*</label></label>
                                            <input type="date" name="birth_date" placeholder="<?=t("Tug'ilgan sanangiz (20.01.2001)")?>" id="birth_date" required="" value="<?=htmlspecialchars($_POST["birth_date"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label for="sex" class="fieldlabels"><?=t("Jinsingiz")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                            <select name="sex" id="sex" required="">
                                                <option value="erkak" <?=($_POST["sex"] == "erkak" ? 'selected=""' : '')?>><?=t("Erkak")?></option>
                                                <option value="ayol" <?=($_POST["sex"] == "ayol" ? 'selected=""' : '')?>><?=t("Ayol")?></option>
                                            </select>
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label for="region_id" class="fieldlabels"><?=t("Viloyat")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                            <select name="region_id" id="region_id" required="">
                                                <?
                                                $regions = $db->in_array("SELECT * FROM regions");
                                                foreach ($regions as $region) {
                                                    echo '<option value="'.$region["id"].'" '.($_POST["region_id"] == $region["id"] ? 'selected=""' : '').' data-region-id="'.$region["id"].'">'.$region["name"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                    <label for="district_id" class="fieldlabels"><?=t("Tuman")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                    <select name="district_id" id="district_id" required=""></select>
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                    <label class="fieldlabels" for="adress"><?=t("Manzil")?> <label class="text-danger">*</label></label>
                                    <input type="text" name="adress" placeholder="<?=t("Manzil")?>" id="adress" required="" value="<?=htmlspecialchars($_POST["adress"])?>">
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                    <label class="fieldlabels" for="phone_1"><?=t("Telefon raqamingiz")?> <label class="text-danger">*</label></label>
                                    <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3">
                                    <label class="fieldlabels" for="phone_2"><?=t("Qo'shimcha telefon raqam")?> <label class="text-danger">*</label></label>
                                    <input type="text" name="phone_2" placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($_POST["phone_2"] ? htmlspecialchars($_POST["phone_2"]) : "+998")?>">
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                    <h2 class="fs-title text-danger text-center mb-4"><?=t("Passport ma'lumotlar")?></h2>
    
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="row no-gutters">
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label class="fieldlabels" for="passport_serial"><?=t("Passport seriyasi")?> <label class="text-danger">*</label></label>
                                                    <input type="text" name="passport_serial" placeholder="- -" id="passport_serial" required="" value="<?=htmlspecialchars($_POST["passport_serial"])?>">
                                                </div>
            
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label class="fieldlabels" for="passport_number"><?=t("Passport raqami")?> <label class="text-danger">*</label></label>
                                                    <input type="text" name="passport_number" placeholder="- - - - - - -" id="passport_number" required="" value="<?=htmlspecialchars($_POST["passport_number"])?>">
                                                </div>
    
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <? if ($file_2_error) { ?>
                                                        <h5 class="text-danger"><?=$file_2_error?></h5>
                                                    <? } ?>
                                                    <label class="fieldlabels" for="file_1"><?=t("Passport rangli nusxasi")?> <label class="text-danger">*</label></label>
                                                    <input type="file" name="file_1" id="file_1" required="">
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <img src="images/jshr.jpg" alt="jshr" width="100%">
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mt-4">
                                    <h2 class="fs-title text-danger text-center mb-4"><?=t("Ta'lim ma'lumotlar")?></h2>
    
                                    <div class="col-12">
                                        <? if ($file_3_error) { ?>
                                            <h5 class="text-danger"><?=$file_3_error?></h5>
                                        <? } ?>
                                        <label class="fieldlabels" for="file_3"><?=t("O'rta maktab attestati yoki o'rta maxsus bilim yurti diplomini yuklang")?> <label class="text-danger">*</label></label>
                                        <input type="file" name="file_3" id="file_3" required="">
                                    </div>
    
                                    <div class="col-12">
                                        <? if ($file_4_error) { ?>
                                            <h5 class="text-danger"><?=$file_4_error?></h5>
                                        <? } ?>
                                        <label class="fieldlabels" for="file_4"><?=t("Agar DTM dan imtihon topshirgan bo'lsangiz natijani yuklang")?> </label>
                                        <input type="file" name="file_4" id="file_4">
                                    </div>
                                </div>
    
                                <h2 class="fs-title text-danger text-center mb-4"><?=t("Ta'lim ma'lumotlar")?></h2>
    
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                    <label for="direction" class="fieldlabels"><?=t("Ta'lim yo'nalishi")?> <b id="d_h"></b><label class="text-danger">*</label></label>
                                    <select name="direction_id" id="direction" required="">
                                        <?
                                        $directions = $db->in_array("SELECT * FROM directions WHERE active = 1");
                                        foreach ($directions as $direction) {
                                            echo '<option value="'.$direction["id"].'" '.($_POST["direction_id"] == $direction["id"] ? 'selected=""' : '').' data-direction-id="'.$direction["id"].'">'.lng($direction["short_name"]).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
        
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                    <label for="learn_type" class="fieldlabels"><?=t("Ta'lim shakli")?> <b id="html"></b><label class="text-danger">*</label></label>
                                    <select name="learn_type" id="learn_type" required="">
                                        <option value="Kunduzgi" id="option_kunduzgi" <?=($_POST["learn_type"] == "Kunduzgi" ? 'selected=""' : "")?>><?=t("Kunduzgi")?></option>
                                        <option value="Kechki" id="option_kechki" <?=($_POST["learn_type"] == "Kechki" ? 'selected=""' : "")?>><?=t("Kechki")?></option>
                                        <option value="Sirtqi" id="option_sirtqi" <?=($_POST["learn_type"] == "Sirtqi" ? 'selected=""' : "")?>><?=t("Sirtqi")?></option>
                                    </select>
                                </div>
        
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                    <label for="exam_lang" class="fieldlabels"><?=t("Test imtihonini qaysi tilda topshirasiz?")?> <label class="text-danger">*</label></label>
                                    <select name="exam_lang" id="exam_lang" required="">
                                        <option value="uz" <?=($_POST["exam_lang"] == "uz" ? 'selected=""' : "")?>><?=t("O'zbek tili")?></option>
                                        <!-- <option value="ru" <?=($_POST["exam_lang"] == "ru" ? 'selected=""' : "")?>><?=t("Rus tili")?></option> -->
                                    </select>
                                </div>

                                <div class="col-12" id="staj" style="display:none;">
                                    <? if ($file_staj_error) { ?>
                                        <h5 class="text-danger"><?=$file_staj_error?></h5>
                                    <? } ?>
                                    <label class="fieldlabels" for="file_staj"><?=t("Ushbu yo'nalishnining sirtqi ta'lim shaklida o'qishingiz uchun 5 yillik ish stajingiz bo'lishligi talab etiladi, iltimos ish stajingiz bo'yicha hujjatni yuklang")?> </label>
                                    <input type="file" name="file_staj" id="file_staj">
                                </div>
        
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4" style="display:none;">
                                    <label for="exam_foreign_lang" class="fieldlabels"><?=t("Chet tili imtihonini qaysi tilda topshirasiz?")?> <label class="text-danger">*</label></label>
                                    <select name="exam_foreign_lang" id="exam_foreign_lang" required="">
                                        <option value="en" <?=($_POST["exam_foreign_lang"] == "en" ? 'selected=""' : "")?> selected=""><?=t("Ingliz tili")?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                
                        <input type="submit" name="submit" class="next action-button" value="<?=t("Jo'natish")?> »" style="width:150px;">
                    </form>
                </div>
            </div>
        </div>
    <? } else if (!$qabul_off) { ?>
        <div class="container select-reg-type" style="padding-top:160px;padding-bottom:40px;">
            <div class="row justify-content-center cv-row">
                <a href="<?=$url2[0]?>/<?=$url[0]?>/oddiy" class="btn btn-success mb-4"><?=t("Abituriyent")?></a>
                <a href="<?=$url2[0]?>/<?=$url[0]?>/oqishni-kochirish" class="btn btn-success mb-4"><?=t("O'qishni ko'chirish")?></a>
                <a href="<?=$url2[0]?>/<?=$url[0]?>/ikkinchi-mutaxassislik" class="btn btn-success mb-4"><?=t("Ikkinchi mutaxassislik")?></a>
                <a href="<?=$url2[0]?>/vakansiya" class="btn btn-success"><?=t("ishga xujjat topshirish")?></a>
            </div>
        </div>
    <? } else { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <h1 id="heading text-danger"><?=t("Qabul jarayonlari vaqtincha to'xtatildi")?></h1>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</main>

<? include "system/scripts.php"; ?>

<script>
    $("#input-image").click(function(){
        $("#file_1").click();
    });

    $("#file_1").on("change", function(){
        // $('#image').change(function(){
            $("#input-image").html('');
            $("#input-image").append('<img src="'+window.URL.createObjectURL(this.files[0])+'" width="100%" />');
            // for (var i = 0; i < $(this)[0].files.length; i++) {
            // }
        // });
    });
    
    $("#region_id").on("change", function(){
        var region_id = $(this).val();
        $.ajax({
            url: 'api',
            type: 'POST',
            data: {
                method: 'getDistricts',
                region_id: region_id
            },
            dataType: 'json',
            success: function(data) {
                $("#district_id").html("");

                for (var i in data.districts) {
                    var district = data.districts[i];
                    var name = district["name"];
                    var id = district["id"];
                    $("#district_id").append('<option value="'+id+'">'+name+'</option>');
                }
            }
        });
    }).change();
</script>

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
        $direction_learn_types_perevod_arr = [];

        if ($directions) {
            foreach ($directions as $direction) {
                $direction_learn_types = $db->in_array("SELECT learn_type_id FROM direction_learn_types WHERE direction_id = ?", [
                    $direction["id"]
                ]);
    
                $learn_type_id_arr = [];
                foreach ($direction_learn_types as $direction_learn_type) {
                    array_push($learn_type_id_arr, $direction_learn_type["learn_type_id"]);
                }
                $direction_learn_types_arr[$direction["id"]] = $learn_type_id_arr;

                // 

                $direction_learn_types_perevod = $db->in_array("SELECT learn_type_id FROM direction_learn_types_perevod WHERE direction_id = ?", [
                    $direction["id"]
                ]);

                $learn_type_perevod_id_arr = [];
                foreach ($direction_learn_types_perevod as $direction_learn_type) {
                    array_push($learn_type_perevod_id_arr, $direction_learn_type["learn_type_id"]);
                }
                $direction_learn_types_perevod_arr[$direction["id"]] = $learn_type_perevod_id_arr;

                // 

                // 

                $direction_learn_types_ikkinchi_mutaxassislik = $db->in_array("SELECT learn_type_id FROM direction_learn_types_ikkinchi_mutaxassislik WHERE direction_id = ?", [
                    $direction["id"]
                ]);

                $learn_type_ikkinchi_mutaxassislik_id_arr = [];
                foreach ($direction_learn_types_ikkinchi_mutaxassislik as $direction_learn_type) {
                    array_push($learn_type_ikkinchi_mutaxassislik_id_arr, $direction_learn_type["learn_type_id"]);
                }
                $direction_learn_types_ikkinchi_mutaxassislik_arr[$direction["id"]] = $learn_type_ikkinchi_mutaxassislik_id_arr;
            }
        }

        echo "var direction_learn_types = ".json_encode($direction_learn_types_arr).";";
        echo "var direction_learn_types_perevod = ".json_encode($direction_learn_types_perevod_arr).";";
        echo "var direction_learn_types_ikkinchi_mutaxassislik = ".json_encode($direction_learn_types_ikkinchi_mutaxassislik_arr).";";
        // 
        $learn_types = $db->in_array("SELECT id, name FROM learn_types");
        $learn_types_arr = [];

        foreach ($learn_types as $learn_type) {
            $learn_types_arr[$learn_type["id"]] = $learn_type;
            $learn_types_arr[$learn_type["id"]]["name_t"] = t($learn_type["name"]);
        }
        echo "var learn_types = ".json_encode($learn_types_arr).";";
        ?>
        var selected_learn_type = "<?=$_POST["learn_type"]?>";

        function directionChange() {
            $("#learn_type").html("");

            var direction_id = $("#direction").find("option:selected").attr("data-direction-id");
            
            <? if ($url[1] == "oddiy") { ?>
                var direction_learn_types_arr = direction_learn_types[direction_id];
            <? } else if ($url[1] == "oqishni-kochirish") { ?>
                var direction_learn_types_arr = direction_learn_types_perevod[direction_id];

                if ($("#to_course").val() == "3-kurs") {
                    direction_learn_types_arr = ['3'];
                } else if ($("#to_course").val() == "2-kurs") {
                    direction_learn_types_arr = ['1', '3'];
                }
            <? } else if ($url[1] == "ikkinchi-mutaxassislik") { ?>
                var direction_learn_types_arr = direction_learn_types_ikkinchi_mutaxassislik[direction_id];
            <? } ?>

            for (key in direction_learn_types_arr) {
                var learn_type_id = direction_learn_types_arr[key];
                var learn_type = learn_types[learn_type_id];

                var html = '<option value="'+learn_type["name"]+'" '+(selected_learn_type == learn_type["name"] ? 'selected=""' : '')+'>'+learn_type["name_t"]+'</option>';
                $("#learn_type").append(html);
            }

            learnTypeChange();
        }

        $("#to_course").on("change", function(){
            directionChange();
        });

        function learnTypeChange() {
            var learn_type = $("#learn_type").val();
            var direction_id = $("#direction").find("option:selected").attr("data-direction-id");

            if (learn_type == "Sirtqi") {
                console.log("Sirti ta'lim yo'nalishini tanladi");
                <? if ($url[1] == "oddiy") { ?>
                    if (direction_id == "102") {
                        console.log("3 ta yo'nalishga to'g'ri keldi");
                        $("#staj").show();
                        $("#file_staj").attr("required", "");
                    } else {
                        $("#file_staj").removeAttr("required");
                        $("#staj").hide();
                        console.log("3 ta yo'nalishga to'g'ri kelmadi");
                    }
                <? } ?>
            } else {
                $("#file_staj").removeAttr("required");
                $("#staj").hide();
                console.log("Sirtqi emas");
            }
        }

        directionChange();
        learnTypeChange();

        $("#direction").on("change", function(){
            directionChange();
        });

        $("#learn_type").on("change", function(){
            learnTypeChange();
        });

        $("input[type='file']").on("change", function() {
            console.log(this.files[0]);
            if ((this.files[0].size / 1024 / 1024) > 10) {
                $(this).attr("type", "text");
                $(this).attr("type", "file");
                alert("Fayl hajmi 10MB dan oshlmasligi kerak! Iltimos faylni qayta yuklang!");
            }
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

        $("#passport_serial").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 2));
        });

        $("#passport_number").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 7));
        });

        $("#passport_jshir").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 14));
        });
    });
</script>

<script>
    /* Helper function */
    function download_file(fileURL, fileName) {
        // for non-IE
        if (!window.ActiveXObject) {
            var save = document.createElement('a');
            save.href = fileURL;
            save.target = '_blank';
            var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
            save.download = fileName || filename;
            if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                    document.location = save.href; 
        // window event not working here
                }else{
                    var evt = new MouseEvent('click', {
                        'view': window,
                        'bubbles': true,
                        'cancelable': false
                    });
                    save.dispatchEvent(evt);
                    (window.URL || window.webkitURL).revokeObjectURL(save.href);
                }   
        }

        // for IE < 11
        else if ( !! window.ActiveXObject && document.execCommand)     {
            var _window = window.open(fileURL, '_blank');
            _window.document.close();
            _window.document.execCommand('SaveAs', true, fileName || fileURL)
            _window.close();
        }
    }
</script>

<?
if (!$no_footer) include 'system/end.php';
?>