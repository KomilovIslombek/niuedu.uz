<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

// ECHO "<pre>";
// print_r($_FILES);
// exit;

$request_ok = false;
$error = false;
$diplom_file_1_error = false;
$diplom_file_2_error = false;
$diplom_file_3_error = false;
$tarjimai_xol_file_error = false;


if ($_POST["submit"] || $_GET["submit"]) {
    if (!isset($_REQUEST["id"])) {
        $vakansiya = $db->assoc("SELECT * FROM vakansiyalar WHERE first_name = ? AND last_name = ? AND father_first_name = ? AND birth_date = ?", [
            $_POST["first_name"],
            $_POST["last_name"],
            $_POST["father_first_name"],
            $_POST["birth_date"]
        ]);
    }

    if (!empty($vakansiya["id"])) {
        $request_ok = true;
    } else {
        if ($_FILES['diplom_file_1']["size"] != 0){
            $target_dir_1 = "files/upload/vakansiyalar/diplomlar/";
    
            if (!file_exists($target_dir_1)) {
                mkdir($target_dir_1, 0777, true);
            }
    
            $diplom_file_1 = $_FILES['diplom_file_1'];
            $random_name_1 = "dipom_1_" . md5(time().rand(0, 10000000000000000));
            $file_type_1 = basename($diplom_file_1["type"]);
            $target_diplom_file_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
            $uploadOk_1 = 1;
            $file_type_1 = strtolower(pathinfo($target_diplom_file_1,PATHINFO_EXTENSION));
        
            if (file_exists($target_diplom_file_1)) {
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
                if (move_uploaded_file($diplom_file_1["tmp_name"], $target_diplom_file_1)) {
                    $file_folder_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
                    
                    // istalgan o'lchamdagi rasm
                    $size_1 = filesize($target_diplom_file_1);
                    list($width, $height) = getimagesize($target_diplom_file_1);
    
                    $diplom_file_id_1 = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $diplom_file_1["name"],
                        "type" => $file_type_1,
                        "size" => $size_1,
                        "file_folder" => $file_folder_1
                    ]);
    
                    if (!$diplom_file_id_1){
                        $diplom_file_1_error = "Kechirasiz, diplom 1-faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $diplom_file_1_error = "Kechirasiz, diplom 1-faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $diplom_file_1_error = "Kechirasiz, diplom 1-faylingizni yuklashda xatolik yuz berdi.";
        }
    
        if ($_FILES['diplom_file_2']["size"] != 0){
            $target_dir_2 = "files/upload/vakansiyalar/diplomlar/";
    
            if (!file_exists($target_dir_2)) {
                mkdir($target_dir_2, 0777, true);
            }
    
            $diplom_file_2 = $_FILES['diplom_file_2'];
            $random_name_2 = "dipom_2_" . md5(time().rand(0, 10000000000000000));
            $file_type_2 = basename($diplom_file_2["type"]);
            $target_diplom_file_2 = $target_dir_2 . $random_name_2 . ".$file_type_2";
            $uploadOk_2 = 1;
            $file_type_2 = strtolower(pathinfo($target_diplom_file_2,PATHINFO_EXTENSION));
        
            if (file_exists($target_diplom_file_2)) {
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
                    $size_2 = filesize($target_diplom_file_2);
                    list($width, $height) = getimagesize($target_diplom_file_2);
    
                    $diplom_file_id_2 = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $diplom_file_2["name"],
                        "type" => $file_type_2,
                        "size" => $size_2,
                        "file_folder" => $file_folder_2
                    ]);
    
                    if (!$diplom_file_id_2){
                        $diplom_file_2_error = "Kechirasiz, diplom 2-faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $diplom_file_2_error = "Kechirasiz, diplom 2-faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        }
    
        if ($_FILES['diplom_file_3']["size"] != 0){
            $target_dir_3 = "files/upload/vakansiyalar/diplomlar/";
    
            if (!file_exists($target_dir_3)) {
                mkdir($target_dir_3, 0777, true);
            }
    
            $diplom_file_3 = $_FILES['diplom_file_3'];
            $random_name_3 = "dipom_3_" . md5(time().rand(0, 10000000000000000));
            $file_type_3 = basename($diplom_file_3["type"]);
            $target_diplom_file_3 = $target_dir_3 . $random_name_3 . ".$file_type_3";
            $uploadOk_3 = 1;
            $file_type_3 = strtolower(pathinfo($target_diplom_file_3,PATHINFO_EXTENSION));
        
            if (file_exists($target_diplom_file_3)) {
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
                    $size_3 = filesize($target_diplom_file_3);
                    list($width, $height) = getimagesize($target_diplom_file_3);
    
                    $diplom_file_id_3 = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $diplom_file_3["name"],
                        "type" => $file_type_3,
                        "size" => $size_3,
                        "file_folder" => $file_folder_3
                    ]);
    
                    if (!$diplom_file_id_3){
                        $diplom_file_3_error = "Kechirasiz, diplom 3-faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $diplom_file_3_error = "Kechirasiz, diplom 3-faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        }

        if ($_FILES['tarjimai_xol_file']["size"] != 0){
            $target_dir_4 = "files/upload/vakansiyalar/tarjimai_xol/";
    
            if (!file_exists($target_dir_4)) {
                mkdir($target_dir_4, 0777, true);
            }
    
            $tarjimai_xol = $_FILES['tarjimai_xol_file'];
            $random_name_4 = "tarjimai_xol_" . md5(time().rand(0, 10000000000000000));
            $file_type_4 = basename($tarjimai_xol["type"]);
            $target_tarjimai_xol_file = $target_dir_4 . $random_name_4 . ".$file_type_4";
            $uploadOk_4 = 1;
            $file_type_4 = strtolower(pathinfo($target_tarjimai_xol_file,PATHINFO_EXTENSION));
        
            if (file_exists($target_tarjimai_xol_file)) {
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
                    $size_4 = filesize($target_tarjimai_xol_file);
                    list($width, $height) = getimagesize($target_tarjimai_xol_file);
    
                    $tarjimai_xol_file_id = $db->insert("files", [
                        "creator_user_id" => $user_id,
                        "name" => $tarjimai_xol["name"],
                        "type" => $file_type_4,
                        "size" => $size_4,
                        "file_folder" => $file_folder_4
                    ]);
    
                    if (!$tarjimai_xol_file_id){
                        $tarjimai_xol_file_error = "Kechirasiz, tarjimai xol faylingizni yuklashda xatolik yuz berdi.";
                    }
                } else {
                    $tarjimai_xol_file_error = "Kechirasiz, tarjimai xol faylingizni yuklashda xatolik yuz berdi.";
                }
            }
        } else {
            $tarjimai_xol_file_error = "Kechirasiz, tarjimai xol faylingizni yuklashda xatolik yuz berdi.";
        }

        function deleteUploadedFiles() {
            global $db,
            $target_diplom_file_1, $target_diplom_file_2, $target_diplom_file_3, $target_tarjimai_xol_file,
            $diplom_file_id_1, $diplom_file_id_2, $diplom_file_id_3, $tarjimai_xol_file_id;

            if (file_exists($target_diplom_file_1)) unlink($target_diplom_file_1);
            if (file_exists($target_diplom_file_2)) unlink($target_diplom_file_2);
            if (file_exists($target_diplom_file_3)) unlink($target_diplom_file_3);
            if (file_exists($target_tarjimai_xol_file)) unlink($target_tarjimai_xol_file);

            if ($diplom_file_id_1 > 0) $db->delete("files", $diplom_file_id_1);
            if ($diplom_file_id_2 > 0) $db->delete("files", $diplom_file_id_2);
            if ($diplom_file_id_3 > 0) $db->delete("files", $diplom_file_id_3);
            if ($tarjimai_xol_file_id > 0) $db->delete("files", $tarjimai_xol_file_id);
        }

        if ($diplom_file_1_error || $diplom_file_2_error || $diplom_file_3_error || $tarjimai_xol_file_error) {
            $error = "Fayllarni yuklashda xatolik yuzaga keldi!";
            deleteUploadedFiles();
        }
        
        if (!$error) {
            // foreach ($_POST as $key => $val) {
            //     $_POST[$key] = trim($val);
            // }

            $vakansiya_id = $db->insert("vakansiyalar", [
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
            ]);
        }
    
        if ($vakansiya_id > 0) {
            function sendDocument($telegram_id, $file_path, $caption, $reply_to_message_id = false) {
                global $db, $vakansiya_id;
                $token = "5471695430:AAFEEeYwt3dw_IQIklxYOVyB9trVAewBncc";
                // Create CURL object
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$token."/sendDocument");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
                // Create CURLFile
                $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);
                $cFile = new CURLFile($file_path, $finfo);

                $post_data = [
                    "document" => $cFile,
                    "chat_id" => $telegram_id,
                    "caption" => $caption,
                    "parse_mode" => "html",
                ];

                if ($reply_to_message_id) {
                    $post_data["reply_to_message_id"] = $reply_to_message_id;
                }
            
                // Add CURLFile to CURL request
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            
                // Call
                $res = curl_exec($ch);
                $res_arr = json_decode($res, true);
                
                $db->insert("bot_messages", [
                    "user_id" => $telegram_id,
                    "method" => "sendDocument",
                    "callback_datas" => NULL,
                    "res" => $res,
                    "message_id" => $res_arr["result"]["message_id"],
                    "limit_count" => 0,
                    "time" => time(),
                    "vakansiya_id" => $vakansiya_id
                ]);

                return json_decode($res, true);
            
                // Show result and close curl
                // var_dump($result);
                curl_close($ch);
            }

            function bot($method, $callback_datas=[]){
                global $db, $vakansiya_id;
                
                define("api_key", "5471695430:AAFEEeYwt3dw_IQIklxYOVyB9trVAewBncc");
        
                $url = "https://api.telegram.org/bot".api_key."/".$method;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $callback_datas);

                $res = curl_exec($ch);
                $res_arr = json_decode($res, true);

                $db->insert("bot_messages", [
                    "user_id" => $callback_datas["chat_id"],
                    "method" => $method,
                    "callback_datas" => json_encode($callback_datas, JSON_UNESCAPED_UNICODE),
                    "res" => $res,
                    "message_id" => $res_arr["result"]["message_id"],
                    "limit_count" => 0,
                    "time" => time(),
                    "vakansiya_id" => $vakansiya_id
                ]);
        
                if (curl_error($ch)) {
                    var_dump(curl_error($ch));
                } else {
                    $res_arr = json_decode($res, true);
                }
                return $res_arr;
            }

            if ($diplom_file_id_1 > 0 && $tarjimai_xol_file_id > 0) {
                $text = "";
                $text .= "#vakansiya_$vakansiya_id";
                $text .= "\n\nIsm: <b>".$_POST["first_name"]."</b>";
                $text .= "\nFamiliya: <b>".$_POST["last_name"]."</b>";
                $text .= "\nOtasining ismi: <b>".$_POST["father_first_name"]."</b>";
                $text .= "\nTug'ilgan sanasi: <b>".$_POST["birth_date"]."</b>";
                $text .= "\nTelefon: <b>".$_POST["phone_1"]."</b>";
                $text .= "\nMutaxassisligi: <b>".$_POST["phone_1"]."</b>";
                $text .= "\nOTM: <b>".$_POST["otm"]."</b>";
                $text .= "\nIlmiy daraja: <b>".$_POST["ilmiy_daraja"]."</b>";
                $text .= "\nIsh tajribasi: <b>".$_POST["ish_tajribasi"]."</b>";
                $text .= "\nPASSPORT SERIYA VA RAQAMI: <b>".$_POST["passport_serial_number"]."</b>";

                $caption = "#vakansiya_$vakansiya_id";

                // 41488743
                foreach (["-1001759051204"] as $admin_id) {
                    $res_msg = bot("sendMessage", [
                        "chat_id" => $admin_id,
                        "text" => "<b>".$_SERVER['HTTP_HOST']."\n\nSayt orqali ishga ariza qoldirishdi!</b>\n\n$text",
                        "parse_mode" => "html"
                    ]);
                    if ($res_msg["ok"] != true) {
                        // $error = "fayllarni yuklashda xatolik yuzaga keldi!";
                    } else {
                        $message_id = $res_msg["result"]["message_id"];
                    }
                    
                    if ($target_diplom_file_1) {
                        $res_1 = sendDocument($admin_id, $target_diplom_file_1, $caption."_diplom_1", $message_id); // ahmadjon
                        if ($res_1["ok"] != true) {
                            // $error = "DIPLOMNI yuklashda xatolik yuzaga keldi 1!!";
                        }
                    }
                    
                    if ($target_diplom_file_2) {
                        $res_2 = sendDocument($admin_id, $target_diplom_file_2, $caption."_diplom_2", $message_id); // ahmadjon
                        if ($res_2["ok"] != true) {
                            // $error = "DIPLOMNI yuklashda xatolik yuzaga keldi 2!!!";
                        }
                    }
                    
                    if ($target_diplom_file_3) {
                        $res_3 = sendDocument($admin_id, $target_diplom_file_3, $caption."_diplom_3", $message_id); // ahmadjon
                        if ($res_3["ok"] != true) {
                            // $error = "DIPLOMNI yuklashda xatolik yuzaga keldi 3!!!!";
                        }
                    }

                    if ($target_tarjimai_xol_file) {
                        $res_4 = sendDocument($admin_id, $target_tarjimai_xol_file, $caption."_tarjimai_xol", $message_id); // ahmadjon
                        if ($res_4["ok"] != true) {
                            // $error = "DIPLOMNI yuklashda xatolik yuzaga keldi 3!!!!";
                        }
                    }
                }

                // unlink($file_folder_1);
                // unlink($file_folder_2);
                // unlink($file_folder_3);
            } else {
                $error = "fayylarni yuklashda xatolik yuzaga keldi!";
            }

            if ($error == false && $diplom_file_1_error == false && $diplom_file_2_error == false && $diplom_file_3_error == false) {
                $request_ok = true;

                $vakansiya = $db->assoc("SELECT * FROM vakansiyalar WHERE id = ?", [ $vakansiya_id ]);
            }
        } else {
            deleteUploadedFiles();
        }
    }

}

include "system/head.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=1.0.2">

<? if ($request_ok == true) { ?>
    <div class="container-fluid" style="padding-top:100px;">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                    <H2 id="heading">NAVOIY INNOVATSIYALAR UNIVERSITETIGA TOPSHIRGAN ARIZANGIZ</h2>
                    <!-- <p>Fill all form field to go to next step</p> -->
                    
                    <fieldset>
                        <div class="form-card">
                            <h2 class="purple-text text-center"><strong>YUBORILDI !</strong></h2> <br>
                            <div class="row justify-content-center">
                                <div class="col-3"> <img src="images/check.png" class="fit-image"> </div>
                            </div> <br><br>
                            <div class="row justify-content-center">
                                <div class="col-7 text-center">
                                    <h5 class="purple-text text-center">Tez orada sizning hujjatlaringiz ko'rib chiqilib, bizning xodimlarimiz siz bilan bog'lanishadi.</h5>

                                    <h5>Bizni <a href="https://t.me/niuedu_uz">https://t.me/niuedu_uz</a> orqali kuzatib boring</h5>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <table class="table table-hover mt-3 mb-3">
                        <tr>
                            <th>Ariza ID raqami</th>
                            <td><b><?=$vakansiya["id"]?></b></td>
                        </tr>
                        <tr>
                            <th>F.I.O</th>
                            <td><b><?=$vakansiya["last_name"] . " " . $vakansiya["first_name"] . " " . $vakansiya["father_first_name"]?></b></td>
                        </tr>
                        <tr>
                            <th>Telefon</th>
                            <td><b><?=$vakansiya["phone_1"]?></b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<? } else { ?>
    <div class="container-fluid" style="padding-top:100px;">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                    <H2 id="heading">NAVOIY INNOVATSIYALAR UNIVERSITETIGA ISHGA ARIZA TOPSHIRISH</h2>
                    <!-- <p>Fill all form field to go to next step</p> -->
                    <form action="/<?=$url2[0]?>/<?=$url2[1]?>" method="POST" id="msform" enctype="multipart/form-data">
                        <div class="form-card">
                            <!-- <div class="row">
                                <div class="col-7">
                                    <h2 class="fs-title text-danger">Hujjatlarni topshirish</h2>
                                </div>
                            </div> -->

                            <? if ($error) { ?>
                                <h3 class="text-center text-danger"><?=$error?></h3>
                            <? } ?>
                            
                            <label class="fieldlabels" for="last_name">Familiyangiz (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="last_name" placeholder="Familiyangiz (Lotin alifbosida)" id="last_name" required="" value="<?=htmlspecialchars($_POST["last_name"])?>">
    
                            <label class="fieldlabels" for="first_name">Ismingiz (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="first_name" placeholder="Ismingiz (Lotin alifbosida)" id="first_name" required="" value="<?=htmlspecialchars($_POST["first_name"])?>">
    
                            <label class="fieldlabels" for="father_first_name">Otangizninig ismi (Lotin alifbosida) <label class="text-danger">*</label></label>
                            <input type="text" name="father_first_name" placeholder="Otangizninig ismi (Lotin alifbosida)" id="father_first_name" required="" value="<?=htmlspecialchars($_POST["father_first_name"])?>">

                            <label class="fieldlabels" for="phone_1">Telefon raqamingiz <label class="text-danger">*</label></label>
                            <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">

                            <label class="fieldlabels" for="mutaxassisligi">Mutaxassisligingiz <label class="text-danger">*</label></label>
                            <input type="text" name="mutaxassisligi" placeholder="Mutaxassisligingiz" id="mutaxassisligi" required="" value="<?=htmlspecialchars($_POST["mutaxassisligi"])?>">

                            <label class="fieldlabels" for="otm">Qaysi OTMni bitirgansiz? <label class="text-danger">*</label></label>
                            <input type="text" name="otm" placeholder="Qaysi OTMni bitirgansiz?" id="otm" required="" value="<?=htmlspecialchars($_POST["otm"])?>">

                            <label class="fieldlabels" for="ilmiy_daraja">Ilmiy darajangiz <label class="text-danger">*</label></label>
                            <input type="text" name="ilmiy_daraja" placeholder="Ilmiy darajangiz" id="ilmiy_daraja" required="" value="<?=htmlspecialchars($_POST["ilmiy_daraja"])?>">
    
                            <label class="fieldlabels" for="birth_date">Tug'ilgan sanangiz (20.01.2001) <label class="text-danger">*</label></label>
                            <input type="date" name="birth_date" placeholder="Tug'ilgan sanangiz (20.01.2001)" id="birth_date" required="" value="<?=htmlspecialchars($_POST["birth_date"])?>">

                            <label class="fieldlabels" for="ish_tajribasi">Ish tajribangiz <label class="text-danger">*</label></label>
                            <input type="text" name="ish_tajribasi" placeholder="Ish tajribangiz" id="ish_tajribasi" required="" value="<?=htmlspecialchars($_POST["ish_tajribasi"])?>">
    
                            <? if ($diplom_diplom_file_1_error) { ?>
                                <h5 class="text-danger"><?=$diplom_diplom_file_1_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="diplom_file_1">Diplomini yuklang <label class="text-danger">*</label></label>
                            <input type="file" name="diplom_file_1" id="diplom_file_1" required="">

                            <? if ($diplom_diplom_file_2_error) { ?>
                                <h5 class="text-danger"><?=$diplom_diplom_file_2_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="diplom_file_2">Diplomini yuklang 2 (ixtiyoriy)</label>
                            <input type="file" name="diplom_file_2" id="diplom_file_2">

                            <? if ($diplom_diplom_file_3_error) { ?>
                                <h5 class="text-danger"><?=$diplom_diplom_file_3_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="diplom_file_3">Diplomini yuklang 3 (ixtiyoriy)</label>
                            <input type="file" name="diplom_file_3" id="diplom_file_3">
                            
                            <label class="fieldlabels" for="passport_serial_number">Passport seriyasi hamda raqami <label class="text-danger">*</label></label>
                            <input type="text" name="passport_serial_number" placeholder="Passport seriyasi hamda raqami" id="passport_serial_number" required="" value="<?=htmlspecialchars($request["passport_serial_number"])?>">

                            <? if ($tarjimai_xol_file_error) { ?>
                                <h5 class="text-danger"><?=$tarjimai_xol_file_error?></h5>
                            <? } ?>
                            <label class="fieldlabels" for="tarjimai_xol_file">Tarjimai xolini yuklash <label class="text-danger">*</label></label>
                            <input type="file" name="tarjimai_xol_file" id="tarjimai_xol_file" required="">
                        </div>
                                
                        <input type="submit" name="submit" class="next action-button" value="Jo'natish »" style="width:150px;">
                    </form>
                </div>
            </div>
        </div>
    </div>
<? } ?>


<? include "system/scripts.php"; ?>

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
    });
</script>

<? include 'system/end.php'; ?>