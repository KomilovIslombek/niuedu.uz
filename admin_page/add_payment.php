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

$student = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $_REQUEST["code"] ]);
// if (empty($student["id"])) exit(http_response_code(404));

if ($_REQUEST['type'] == "add_payment"){
    validate(["code", "amount"]);

    if ($_FILES['file_1']["size"] != 0){
        $target_dir_1 = "files/upload/kvitansiya/";

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

                $file_id = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file_1["name"],
                    "type" => $file_type_1,
                    "size" => $size_1,
                    "file_folder" => $file_folder_1
                ]);

                if (!$file_id){
                    $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            } else {
                $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
    }

    if (!$errors["forms"] || count($errors["forms"]) == 0) {
        $learn_type = $db->assoc("SELECT * FROM learn_types WHERE name = ?", [ $student["learn_type"] ]);

        if ($student["id"]) {
            $payment_id = $db->insert("payments_contract", [
                "creator_user_id" => $user_id,
                "code" => $student["code"],
                "amount" => str_replace(",", "", $_POST["amount"]),
                "payment_method_id" => $_POST["payment_method_id"],
                "payment_date" => $_POST["payment_date"],
                "direction_id" => $student["direction_id"],
                "learn_type_id" => $learn_type["id"],
                "privilege_percent" => $student["privilege_percent"],
                "privilege_note" => $student["privilege_note"],
                "file_id" => $file_id,
            ]);
            
            if($payment_id && !empty($student["firm_id"]) && $student["payed"] != 1) {
                $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $student["firm_id"] ]);
                $agents_price = $db->assoc("SELECT * FROM settings");

                if($agent["id"]) {
                   
                    $payment_id = $db->insert("agent_balances", [
                        "creator_user_id" => $user_id,
                        "request_id" => $student["code"],
                        "firm_id" => $agent["id"],
                        "amount" => $agents_price["price"],
                        "payment_method_id" => $_POST["payment_method_id"],
                        "payment_date" => $_POST["payment_date"],
                    ]);

                    if($payment_id) {
                        $db->update("firms", [
                            "balance" => ($agent["balance"] + $agents_price["price"]),
                        ], [
                            "id" => $agent["id"]
                        ]);
                        
                        // Student update
                        $db->update("requests", [
                            "payed" => 1,
                        ], [
                            "code" => $student["code"]
                        ]);
                    }
                }
            }
            
            if ($payment_id > 0) {
                header("Location: /$url2[0]/add_payment.php");
                exit;
            }
        }
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">To'lov qo'shish</h4>
                            
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
                                <form action="add_payment.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_payment" required>

                                    <?=getError("code")?>
                                    <div class="form-group col-12">
                                        <label>Talaba (ID)</label>
                                        <select name="code" id="single-select" class="form-control">
                                            <? foreach ($db->in_array("SELECT * FROM requests ORDER BY last_name ASC") as $student) { ?>
                                                <option value="<?=$student["code"]?>"><?=$student["last_name"] . " " . $student["first_name"]?> <?=$student["father_first_name"]?> (<?=$student["code"]?>)</option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <?=getError("amount")?>
                                    <div class="form-group col-12">
                                        <label>To'lov miqdori</label>
                                        <input type="text" name="amount" class="form-control" placeholder="To'lov miqdori" value="<?=$_POST["amount"]?>" id="price-input">
                                    </div>

                                    <?=getError("payment_date")?>
                                    <div class="form-group col-12">
                                        <label>To'lov sanasi</label>
                                        <input type="date" name="payment_date" class="form-control" placeholder="To'lov sanasi" value="<?=$_POST["payment_date"]?>" id="price-input">
                                    </div>

                                    <?=getError("payment_method_id")?>
                                    <div class="form-group col-12">
                                        <label>To'lov uslubi</label>
                                        <select name="payment_method_id" id="single-select" class="form-control">
                                            <? foreach ($db->in_array("SELECT * FROM payment_methods") as $payment_method) { ?>
                                                <option value="<?=$payment_method["id"]?>" <?=($payment_method["id"])?>><?=$payment_method["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-2">
                                        <? if ($file_1_error) { ?>
                                            <h5 class="text-danger"><?=$file_1_error?></h5>
                                        <? } ?>
                                        <label class="fieldlabels" for="file_1">Kvitansiya</label>
                                        <input type="file" name="file_1" id="file_1" clas="form-control">
                                        <!-- <div class="input-image" id="input-image">
                                            <i class="fa fa-plus"></i>
                                            <span><?=t("Yuklash")?></span>
                                        </div> -->
                                    </div>

                                    <div class="form-actions right">
                                        <button type="button" class="btn btn-warning mr-1">
                                            <i class="icon-cross2"></i> bekor qilish
                                        </button>
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
    $("#price-input").on("input", function(){
        var val = $(this).val().replaceAll(",", "").replaceAll(" ", "");
        console.log(val);

        if (val.length > 0) {    
            $(this).val(
                String(val).replace(/(.)(?=(\d{3})+$)/g,'$1,')
            );
        }
    });
</script>

<!-- Select2 -->
<script src="../modules/select2/select2.full.min.js"></script>
<script src="../modules/select2/select2-init.js"></script>

<? include('end.php'); ?>