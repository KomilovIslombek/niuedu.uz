<?php
$user_id = $systemUser->id;
$rights = $systemUser->rights;
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

$savol_javob_id = isset($_REQUEST['savol_javob_id']) ? $_REQUEST['savol_javob_id'] : null;
if (!$savol_javob_id) {echo"error";return;}

$savol_javob = $db->assoc("SELECT * FROM savol_javoblar WHERE id = ?", [$_REQUEST['savol_javob_id']]);
if (!$savol_javob["id"]) {echo"error (savol_javob not found)";exit;}

if ($_REQUEST['type'] == "edit_savol_javob"){
    $savol_javob_id = isset($_REQUEST['savol_javob_id']) ? $_REQUEST['savol_javob_id'] : null;
    if (!$savol_javob_id) {echo"error [savol_javob_id]";exit;}
    
    $savol = isset($_REQUEST['savol']) ? $_REQUEST['savol'] : null;
    if (!$savol) {echo"error [savol]";exit;}

    $javob = isset($_REQUEST['javob']) ? $_REQUEST['javob'] : null;
    if (!$javob) {echo"error [javob]";exit;}

    $db->update("savol_javoblar", [
        "savol" => json_encode($savol, JSON_UNESCAPED_UNICODE),
        "javob" => json_encode($javob, JSON_UNESCAPED_UNICODE)
    ], [
        "id" => $savol_javob["id"]
    ]);

    header('Location: savol_javoblar_list.php?page=' . ($_REQUEST["page"] ? $_REQUEST["page"] : 1));
}

if ($_REQUEST["type"] == "delete_savol_javob"){
    $db->delete("savol_javoblar", $savol_javob["id"]);
    delete_image($savol_javob["image_id"]);
    header('Location: savol_javoblar_list.php?page=' . ($_REQUEST["page"] ? $_REQUEST["page"] : 1));
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Savol-javobni taxrirlash</h4>
                            
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
                                <form action="edit_savol_javob.php" method="savol_javob" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_savol_javob" required>
                                    <input type="hidden" name="savol_javob_id" value="<?=$_REQUEST['savol_javob_id']?>" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>savol <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="savol[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="savol"><?=lng($savol_javob["savol"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>javob <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="javob[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="javob"><?=lng($savol_javob["javob"], $lang["flag_icon"])?></textarea>
                                        </div>
                                    <? } ?>

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

<? include('end.php'); ?>