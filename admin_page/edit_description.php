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

$description_id = isset($_REQUEST['description_id']) ? $_REQUEST['description_id'] : null;
if (!$description_id) {echo"error";return;}

$description = $db->assoc("SELECT * FROM descriptions WHERE id = ?", [$_REQUEST['description_id']]);
if (!$description["id"]) {echo"error (description not found)";exit;}

if ($_REQUEST['type'] == "edit_description"){
    $description_id = isset($_REQUEST['description_id']) ? $_REQUEST['description_id'] : null;
    if (!$description_id) {echo"error [description_id]";exit;}
    
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}
    
    $db->update("descriptions", [
        "name" => $name,
    ],
    [
        "id" => $description["id"]
    ]);

    header('Location: /'.$url2[0].'/descriptions.php?page=1');
}

if ($_REQUEST['type'] == "delete_description"){
    $db->delete("descriptions", $description["id"]);
    header('Location: /'.$url2[0].'/descriptions.php?page=1');
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
                                <form action="edit_description.php" method="POST" class="form" enctype="multipart/form-data">

                                    <input type="hidden" name="type" value="edit_description" required>
                                    <input type="hidden" name="description_id" value="<?=$_REQUEST['description_id']?>" required>

                                    <div class="form-group">
                                        <label>yangilik nomi</label>
                                        <input name="name" class="form-control border-primary" value="<?=$description["name"]?>"/>
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

<? include('end.php'); ?>