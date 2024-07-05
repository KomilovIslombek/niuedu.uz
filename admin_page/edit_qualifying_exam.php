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

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
if (!$id) {echo"error";return;}

$qualifying_exam = $db->assoc("SELECT * FROM qualifying_exam WHERE id = ?", [$_REQUEST['id']]);
if (!$qualifying_exam["id"]) {echo"error (qualifying_exam not found)";exit;}

if ($_REQUEST['type'] == "edit_qualifying_exam"){

    include "modules/uploadFile.php";

    $file_name = isset($_REQUEST["file_name"]) ? $_REQUEST["file_name"] : null;
    if (!$file_name) {echo"file nomini kiritishni unutdingiz!";exit;}    
    
    $file = uploadFileWithUpdate("file_id", "files/upload/passport", ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "png", "jpg", "jpeg", "mp4"], false, false, $qualifying_exam["file_id"]);

    // if (!$_POST["first_name"]) {
    //     $error = "Ma'lumotlarni to'ldirishni unutdingiz!";
    // }

    if (!$error) {
        $db->update("qualifying_exam", [
            "file_id" => $file["file_id"],
            "file_name" => json_encode($file_name, JSON_UNESCAPED_UNICODE)
        ], [
            "id" => $qualifying_exam["id"]
        ]);
        
        header('Location: qualifying_exam.php?page=' . $_GET["page"]);
    }
    
}

if ($_REQUEST['type'] == "delete_qualifying_exam"){
    
    $db->delete("qualifying_exam", $qualifying_exam["id"], "id");
    delete_file($qualifying_exam["file_id"]);
    header('Location: qualifying_exam.php?page=' . $_GET["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Malakaviy imtihon ma'lumotlarini tahrirlash</h4>
                            
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
                                <form action="edit_qualifying_exam.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_qualifying_exam" required>
                                    <input type="hidden" name="page" value="<?=$_GET["page"]?>" required>
                                    <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" required>

                                    <? if ($error) { ?>
                                        <h3 class="text-center text-danger"><?=$error?></h3>
                                    <? } ?>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>File nomi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="file_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" ><?=lng($qualifying_exam["file_name"], $lang["flag_icon"])?></textarea>
                                        </div>
                                    <? } ?>

                                    <?
                                    if ($qualifying_exam["file_id"]) {
                                        $passport_file = fileArr($qualifying_exam["file_id"]);
                                        if ($passport_file["file_folder"]) {
                                            echo '<img src="../'.$passport_file["file_folder"].'" width="150px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="file_id">File (pdf) shakli</label>
                                        <input type="file" name="file_id" class="form-control border-primary" id="file_id" accept="file/*">
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