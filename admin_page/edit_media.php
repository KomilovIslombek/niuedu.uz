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

$media_id = isset($_REQUEST['media_id']) ? $_REQUEST['media_id'] : null;
if (!$media_id) {echo"error";return;}

$media = $db->assoc("SELECT * FROM medias WHERE id = ?", [$_REQUEST['media_id']]);
if (!$media["id"]) {echo"error (media not found)";exit;}

if ($_REQUEST["type"] == "edit_media"){
    $iframe = isset($_REQUEST["iframe"]) ? $_REQUEST["iframe"] : null;
    if (!$iframe) {echo"mediani kiritishni unutdingiz!";exit;}

    $name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : null;
    if (!$name) {echo"[name]na kiritishni unutdingiz!";exit;}

    $db->update("medias", [
        "iframe" => $iframe,
        "name" => json_encode($name, JSON_UNESCAPED_UNICODE)
    ], [
        "id" => $media["id"]
    ]);

    header('Location: medias_list.php?page=' . $_REQUEST["page"]);
}

if ($_REQUEST["type"] == "delete_media"){
    $db->delete("medias", $media["id"]);
    header('Location: medias_list.php?page=' . $_REQUEST["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Media ni tahrirlash</h4>

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
                                <form action="edit_media.php" method="POST" class="form">
                                    <input type="hidden" name="type" value="edit_media" required>
                                    <input type="hidden" name="media_id" value="<?=$_REQUEST['media_id']?>" required>
                                    <input type="hidden" name="page" value="<?=$_REQUEST['page']?>" required>

                                    <div class="form-group">
                                        <label>media (iframe youtube)</span></label>
                                        <textarea type="text" name="iframe" class="form-control border-primary" placeholder="iframe" style="min-height:150px"><?=$media["iframe"]?></textarea>
                                    </div>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>nomi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea type="text" name="name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="nomi"><?=lng($media["name"], $lang["flag_icon"])?></textarea>
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

<? include('end.php'); ?>