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

$home_image = $db->assoc("SELECT * FROM home_images2 WHERE id = ?", [ $_REQUEST["home_image_id"]]);
if (!$home_image["id"]) exit(http_response_code(404));

if ($_REQUEST['type'] == "edit_home_image"){
    $image_id = $home_image["image_id"];

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/home/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir, 0777, true);
        }

        $file = $_FILES['image'];
        $random_name = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type = basename($file["type"]);
        $target_file = $_SERVER["DOCUMENT_ROOT"]."/".$target_dir . $random_name . ".$file_type";
        $uploadOk = 1;
        $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
        if (file_exists(''.$target_file)) {
            exit("Kechirasiz, fayl allaqachon mavjud.");
            $uploadOk = 0;
        }
        if ($file["size"] > 5000000) {
            exit("Kechirasiz, sizning faylingiz juda katta.");
            $uploadOk = 0;
        }
        if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg") {
            exit("Kechirasiz, faqat JPG, JPEG, PNG fayllariga ruxsat berilgan.");
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            exit("Kechirasiz, sizning faylingiz yuklanmadi.");
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_folder = $target_dir . $random_name . ".$file_type";
                
                // istalgan o'lchamdagi rasm
                $size = filesize($target_file);
                list($width, $height) = getimagesize($target_file);

                $new_image_id = $db->insert("images", [
                  "creator_user_id" => $user_id,
                  "width" => $width,
                  "height" => $height,
                  "size" => $size,
                  "file_folder" => $file_folder,
                ]);

                if (!$new_image_id){
                    echo '<script>alert("rasmni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_image_id) {
                    delete_image($image_id);
                    $image_id = $new_image_id;
                }
            } else {
                exit("Kechirasiz, faylingizni yuklashda xatolik yuz berdi.");
            }
        }
    }

    $db->update("home_images2", [
        "image_id" => $image_id,
        "text_1" => $_REQUEST["text_1"],
        "text_2" => $_REQUEST["text_2"],
        "text_3" => $_REQUEST["text_3"],
        "text_4" => $_REQUEST["text_4"],
        "text_5" => $_REQUEST["text_5"],
        "text_6" => $_REQUEST["text_6"],
        "text_7" => $_REQUEST["text_7"],
        "text_8" => $_REQUEST["text_8"],
        "text_9" => $_REQUEST["text_9"],
        "text_10" => $_REQUEST["text_10"],
        "text_11" => $_REQUEST["text_11"],
        "text_12" => $_REQUEST["text_12"],
        "text_13" => $_REQUEST["text_13"],
    ], [
        "id" => $_REQUEST['home_image_id']
    ]);

    header('Location: images_list2.php');
}

if ($_REQUEST['type'] == "delete_home_image"){
    $db->delete("home_images2", $home_image["id"]);

    delete_image($home_image["image_id"]);

    header('Location: images_list2.php');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Rasmni tahrirlash</h4>
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
                                <form action="" method="POST" class="form"  enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_home_image">
                                    <input type="hidden" name="home_image_id" value="<?=$_REQUEST['home_image_id']?>">
                                    
                                    <?
                                    if ($home_image["image_id"]) {
                                        $image = image($home_image["image_id"]);
                                        if ($image["file_folder"]) {
                                            echo '<image src="../'.$image["file_folder"].'" width="400px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <p>Rasm yuklash</p>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <? foreach (range(1, 13) as $number) { ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="bio">Matn (<?=$number?>)</label>
                                                    <textarea type="text" name="text_<?=$number?>" class="form-control border-primary" placeholder="Matn (<?=$number?>)" id="text_<?=$number?>"><?=$home_image["text_" . $number]?></textarea>
                                                </div>
                                            </div>
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
