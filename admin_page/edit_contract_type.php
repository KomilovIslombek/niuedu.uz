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

$contract_type_id = isset($_REQUEST['contract_type_id']) ? $_REQUEST['contract_type_id'] : null;
if (!$contract_type_id) {echo"error";return;}

$contract_type = $db->assoc("SELECT * FROM contract_types WHERE id = ?", [$_REQUEST['contract_type_id']]);
if (!$contract_type["id"]) {echo"error (contract_type not found)";exit;}

if ($_REQUEST['type'] == "edit_contract_type"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"tur nomini kiritishni unutdingiz!";exit;}

    $db->update("contract_types", [
        "creator_user_id" => $user_id,
        "name" => $name
    ], [
        "id" => $contract_type["id"]
    ]);

    header('Location: contract_types_list.php?page=' . $_GET["page"]);
}

if ($_REQUEST['type'] == "delete_contract_type"){
    $db->delete("contract_types", $contract_type["id"]);
    header('Location: contract_types_list.php?page=' . $_GET["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">xujjat turini taxrirlash</h4>
                            
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
                                <form action="edit_contract_type.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_contract_type" required>
                                    <input type="hidden" name="page" value="<?=$_GET["page"]?>" required>
                                    <input type="hidden" name="contract_type_id" value="<?=$_REQUEST['contract_type_id']?>" required>

                                    <div class="form-group">
                                        <label>Kontrakt turi</label>
                                        <textarea name="name" class="form-control border-primary"><?=$contract_type["name"]?></textarea>
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