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

$page = (int)$_GET["page"];
if (empty($page)) $page = 1;

$payment_id = isset($_REQUEST["payment_id"]) ? $_REQUEST["payment_id"] : null;
if (!$payment_id) {echo"error payment_id not found";return;}

$payment = $db->assoc("SELECT * FROM payments_contract WHERE id = ?", [$payment_id]);
if (!$payment["id"]) {echo"error (payment not found)";exit;}

if ($_REQUEST["type"] == "edit_payment"){
    validate(["code", "amount", "payment_method_id"]);

    include "modules/uploadFile.php";
    
    $uploadedFile = uploadFileWithUpdate("file_1", "files/upload/kvitansiya", ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "png", "jpg", "jpeg", "mp4"], false, false, $payment["file_id"]);

    if (!$errors["forms"] || count($errors["forms"]) == 0) {
        $student = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $_POST["code"] ]);
        $learn_type = $db->assoc("SELECT * FROM learn_types WHERE name = ?", [ $student["learn_type"] ]);

        if ($student["code"]) {
            $db->update("payments_contract", [
                "code" => $student["code"],
                "amount" => str_replace(",", "", $_POST["amount"]),
                "payment_method_id" => $_POST["payment_method_id"],
                "payment_date" => $_POST["payment_date"],
                "direction_id" => $student["direction_id"],
                "learn_type_id" => $learn_type["id"],
                "privilege_percent" => $student["privilege_percent"],
                "privilege_note" => $student["privilege_note"],
                "file_id" => $uploadedFile["file_id"],
            ], [
                "id" => $payment["id"]
            ]);

            header("Location: /$url2[0]/payments_list.php?page=" . $page);
            exit;
        }
        
    }
}

if ($_REQUEST["type"] == "delete_payment") {
    $db->delete("payments_contract", $payment["id"]);
    delete_file($payment["file_id"]);
    header("Location: /$url2[0]/payments_list.php?page=" . $page);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Yangilikni taxrirlash</h4>
                            
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
                                <form action="edit_payment.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_payment" required>
                                    <input type="hidden" name="payment_id" value="<?=$_REQUEST["payment_id"]?>" required>

                                    <?=getError("code")?>
                                    <div class="form-group col-12">
                                        <label>Talaba (ID)</label>
                                        <select name="code" id="single-select">
                                            <? foreach ($db->in_array("SELECT * FROM requests") as $student) { ?>
                                                <option value="<?=$student["code"]?>" <?=($payment["code"] == $student["code"] ? 'selected=""' : "")?>><?=$student["first_name"] . " " . $student["last_name"]?> <?=$student["father_first_name"]?> (<?=$student["code"]?>)</option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <?=getError("amount")?>
                                    <div class="form-group col-12">
                                        <label>To'lov miqdori</label>
                                        <input type="text" name="amount" class="form-control" placeholder="To'lov miqdori" value="<?=number_format($payment["amount"])?>" id="price-input">
                                    </div>

                                    <?=getError("payment_method_id")?>
                                    <div class="form-group col-12">
                                        <label>To'lov uslubi</label>
                                        <select name="payment_method_id" class="form-control">
                                            <? foreach ($db->in_array("SELECT * FROM payment_methods") as $payment_method) { ?>
                                                <option value="<?=$payment_method["id"]?>" <?=($payment_method["id"] == $payment["payment_method_id"] ? 'selected=""' : '')?>><?=$payment_method["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <?=getError("payment_date")?>
                                    <div class="form-group col-12">
                                        <div class="form-group col-12">
                                            <label>To'lov sanasi</label>
                                            <input type="date" name="payment_date" class="form-control" placeholder="To'lov sanasi" value="<?=$payment["payment_date"]?>" id="price-input">
                                        </div>
                                    </div>

                                    <?
                                    if ($payment["file_id"]) {
                                        $file = fileArr($payment["file_id"]);
                                        if ($file["file_folder"]) {
                                            echo '<image src="../'.$file["file_folder"].'" width="150px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="file_1">Kvitansiya</label>
                                        <input type="file" name="file_1" class="form-control border-primary" id="file_1" accept="file/*">
                                    </div>  

                                    <div class="form-actions right">
                                        <button type="button" class="btn btn-warning mr-1">
                                            <i class="icon-cross2"></i> bekor qilish
                                        </button>
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