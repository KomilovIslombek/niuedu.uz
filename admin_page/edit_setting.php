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

$setting_id = isset($_REQUEST['setting_id']) ? $_REQUEST['setting_id'] : null;
if (!$setting_id) {echo"error";return;}

$setting = $db->assoc("SELECT * FROM settings WHERE id = ?", [$_REQUEST['setting_id']]);
if (!$setting["id"]) {echo"error (firm not found)";exit;}

if ($_REQUEST['type'] == "edit_setting"){
    $price = isset($_REQUEST['price']) ? $_REQUEST['price'] : null;
    if (!$price) {echo"setting familyasini kiritishni unutdingiz!";exit;}
    

    $db->update("settings", [
        "creator_user_id" => $user_id,
        "price" => str_replace(",", "", $price),
        "about_system" => json_encode($_POST["about_system"], JSON_UNESCAPED_UNICODE)
    ], [
        "id" => $setting["id"]
    ]);

    header('Location: settings.php?page=' . $_GET["page"]);
}

if ($_REQUEST['type'] == "delete_firm"){
    $db->delete("settings", $setting["id"]);
    header('Location: settings.php?page=' . $_GET["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Agentni tahrirlash</h4>
                            
                            <?
                            foreach ($db->in_array("SELECT * FROM langs_list") as $lang) {
                                echo'<span select-form-lang="'.$lang["flag_icon"].'" style="cursor:pointer;font-size:30px;border:1px solid #f1f1f1;margin:15px 10px" class="flag-icon flag-icon-'.$lang["flag_icon"].'" id="form_lang_select"></span> ';
                            }
                            ?>

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
                                <form action="edit_setting.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_setting" required>
                                    <input type="hidden" name="page" value="<?=$_GET["page"]?>" required>
                                    <input type="hidden" name="setting_id" value="<?=$_REQUEST['setting_id']?>" required>
                                    
                                    <div class="form-group">
                                        <label for="price">beriladigon summa</label>
                                        <input type="text" name="price" class="form-control border-primary" value="<?=($setting["price"] ? number_format($setting["price"]) : "")?>" data-price-input>
                                    </div>


                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Tizim haqidagi bo'limning ma'lumoti <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="about_system[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="ma'lumot" editor=""><?=lng($setting["about_system"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                    <? } ?>

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