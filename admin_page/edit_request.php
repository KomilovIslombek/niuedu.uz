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

$request_id = isset($_REQUEST['request_id']) ? $_REQUEST['request_id'] : null;
if (!$request_id) {echo"error [request_id]";exit;}

$request = $db->assoc("SELECT * FROM requests WHERE id = ?", [ $_REQUEST['request_id'] ]);
if (!$request["id"]) {echo"error (request not found)";exit;}

if (!empty($_GET["download_zapros"])) {
    $url = "https://niuedu.uz:4499/" . encode(json_encode([
        "s" => "z", // zapros
        "c" => $request["code"] // code
    ]));
    header("Location: $url");
    exit;
}

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);

if ($_REQUEST['type'] == "delete_request"){
    $db->delete("requests", $request["id"]);

    $file_1 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_1"] ]);
    $file_2 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_2"] ]);
    $file_3 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_3"] ]);
    $file_4 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_4"] ]);
    $file_staj = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["staj_file_id"] ]);
    
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file_1["file_folder"])) {
        unlink($_SERVER["DOCUMENT_ROOT"]."/".$file_1["file_folder"]);
    }
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file_2["file_folder"])) {
        unlink($_SERVER["DOCUMENT_ROOT"]."/".$file_2["file_folder"]);
    }
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file_3["file_folder"])) {
        unlink($_SERVER["DOCUMENT_ROOT"]."/".$file_3["file_folder"]);
    }
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file_4["file_folder"])) {
        unlink($_SERVER["DOCUMENT_ROOT"]."/".$file_4["file_folder"]);
    }
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$file_staj["file_folder"])) {
        unlink($_SERVER["DOCUMENT_ROOT"]."/".$file_staj["file_folder"]);
    }

    if ($request["file_id_1"] > 0) $db->delete("files", $request["file_id_1"]);
    if ($request["file_id_2"] > 0) $db->delete("files", $request["file_id_2"]);
    if ($request["file_id_3"] > 0) $db->delete("files", $request["file_id_3"]);
    if ($request["file_id_4"] > 0) $db->delete("files", $request["file_id_4"]);
    if ($request["staj_file_id"] > 0) $db->delete("files", $request["staj_file_id"]);

    header("Location: requests_list.php?reg_type=".$request["reg_type"]."&page=".$_GET["page"]);
}

function returnBack() {
    unset($_GET["request_id"]);
    unset($_GET["type"]);
    unset($_GET["request_id"]);
    unset($_GET["accept_payment"]);

    $return_url = "requests_list.php".($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "");
    header("Location: $return_url");
}

if ($_REQUEST['type'] == "change_suhbat"){
    $db->update("requests", ["suhbat" => $_REQUEST["suhbat"]], ["id" => $request["id"]]);
    returnBack();
}

if ($_REQUEST['type'] == "change_firm"){
    $db->update("requests", ["firm_id" => $_REQUEST["firm_id"]], ["id" => $request["id"]]);
    returnBack();
}

if ($_REQUEST['type'] == "change_course"){
    $db->update("requests", ["to_course" => $_REQUEST["to_course"]], ["id" => $request["id"]]);
    returnBack();
}

if ($_REQUEST['type'] == "change_document_type"){
    $db->update("requests", ["document_type_id" => $_REQUEST["document_type_id"]], ["id" => $request["id"]]);
    returnBack();
}

if ($_REQUEST['type'] == "change_contract_type"){
    $db->update("requests", ["contract_type_id" => $_REQUEST["contract_type_id"]], ["id" => $request["id"]]);
    returnBack();
}

if ($_REQUEST['remove_payment'] == "1"){
    $payment = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
        $request["id"]
    ]);
    if (!empty($payment["id"])) {
        $db->delete("payments_kassa_aparat",  $payment["id"]);
    }

    returnBack();
}

if ($_REQUEST['accept_payment'] == "1"){
    $payment = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
        $request["id"]
    ]);
    if (empty($payment["id"])) {
        $db->insert("payments_kassa_aparat", [
            "creator_user_id" => $user_id,
            "order_id" => $request["id"],
            "amount" => "250000.00"
        ]);
    }

    returnBack();
}

if ($_REQUEST['type'] == "edit_request"){
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
        "direction" => lng($direction["short_name"], "uz"),
        "direction_id" => $direction["id"],
        "learn_type" => $_POST["learn_type"],
        "passport_serial_number" => $_POST["passport_serial_number"],
        "exam_lang" => $_POST["exam_lang"],
        "exam_foreign_lang" => $_POST["exam_foreign_lang"],
        "ball" => $_POST["ball"],
        "learn_type" => $_POST["learn_type"],
        "shartnoma_amount" => str_replace(",", "", $_POST["shartnoma_amount"]),
        // "shartnoma_code" => $_POST["shartnoma_code"],
        "zapros_date" => $_POST["zapros_date"],
        "zapros_university_name" => $_POST["zapros_university_name"],
        "zapros_rektor_full_name" => $_POST["zapros_rektor_full_name"],
        "shartnoma_date" => $_POST["shartnoma_date"]
    ], [
        "id" => $request["id"]
    ]);

    // to'lov idlarini ham update qilish uchun
    include $_SERVER["DOCUMENT_ROOT"]."/modules/changePaymentCode.php";
    changePaymentId($request["code"], $new_code);

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
                                <form action="edit_request.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_request" required>
                                    <input type="hidden" name="page" value="<?=($_GET["page"] ? $_GET["page"] : "1")?>" required>
                                    <input type="hidden" name="request_id" value="<?=$request["id"]?>" required>

                                    <div class="form-group">
                                        <label>Talaba ID</label>
                                        <input type="text" class="form-control border-primary" value="<?=$request["code"]?>" readonly>
                                    </div>

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
                                        <input type="text" name="phone_1" class="form-control border-primary" value="<?=($request["phone_1"] ? $request["phone_1"] : "+998")?>" id="phone_1">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_2">Telefon (2)</label>
                                        <input type="text" name="phone_2" class="form-control border-primary" value="<?=($request["phone_2"] ? $request["phone_2"] : "+998")?>" id="phone_2">
                                    </div>

                                    <div class="form-group">
                                        <label>Kurs</label>
                                        <select name="to_course" class="form-control" id="to_course">
                                            <?
                                            $courses = $db->in_array("SELECT * FROM courses");
                                            foreach ($courses as $course) {
                                                echo '<option value="'.$course["name"].'" '.($request["to_course"] && $course["name"] == $request["to_course"] ? 'selected=""' : '').'>'.$course["name"].'</option>';
                                            }
                                            ?>
                                        </select>
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

                                    <!-- <div class="form-group">
                                        <label for="ball">ball</label>
                                        <input type="text" name="ball" class="form-control border-primary" value="<?=$request["ball"]?>" id="ball">
                                    </div> -->

                                    <div class="form-group">
                                        <label for="shartnoma_amount">Shartnoma summasi</label>
                                        <input type="text" name="shartnoma_amount" class="form-control border-primary" value="<?=($request["shartnoma_amount"] ? number_format($request["shartnoma_amount"]) : "")?>" data-price-input>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label for="shartnoma_code">Shartnoma kodi</label>
                                        <input type="text" name="shartnoma_code" class="form-control border-primary" value="<?=$request["shartnoma_code"]?>">
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
                                        <input type="date" name="zapros_date" class="form-control border-primary" value="<?=$request["zapros_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="zapros_university_name">Zapros Universitet nomi</label>
                                        <input type="text" name="zapros_university_name" class="form-control border-primary" value="<?=$request["zapros_university_name"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="zapros_rektor_full_name">Zapros Rektor to'liq nomi</label>
                                        <input type="text" name="zapros_rektor_full_name" class="form-control border-primary" value="<?=$request["zapros_rektor_full_name"]?>">
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label for="shartnoma_date">Shartnoma sanasi</label>
                                        <input type="date" name="shartnoma_date" class="form-control border-primary" value="<?=$request["shartnoma_date"]?>">
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
    $directions = $db->in_array("SELECT * FROM directions");
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
    var selected_learn_type = "<?=$request["learn_type"]?>";

    function directionChange() {
        $("#learn_type").html("");

        var direction_id = $("#direction").find("option:selected").attr("data-direction-id");
        
        <? if ($request["reg_type"] == "oddiy") { ?>
            var direction_learn_types_arr = direction_learn_types[direction_id];
        <? } else if ($request["reg_type"] == "oqishni-kochirish") { ?>
            var direction_learn_types_arr = direction_learn_types_perevod[direction_id];

            if ($("#to_course").val() == "3-kurs") {
                direction_learn_types_arr = ['3'];
            } else if ($("#to_course").val() == "2-kurs") {
                direction_learn_types_arr = ['1', '3'];
            }
        <? } else if ($request["reg_type"] == "ikkinchi-mutaxassislik") { ?>
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
            <? if ($request["reg_type"] == "oddiy") { ?>
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

    // 

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