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

if ($_REQUEST['type'] == "add_home_slide"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    // if (!$name) {echo"kichik ustgi matnni kiritishni unutdingiz!";exit;}

    $mini_text = isset($_REQUEST['mini_text']) ? $_REQUEST['mini_text'] : null;
    // if (!$mini_text) {echo"slayd matnini kiritishni unutdingiz";exit;}
    
    $html = isset($_REQUEST['html']) ? $_REQUEST['html'] : null;
    // if (!$html) {echo"slaydni kiritishni unutdingiz";exit;}

    $background_color = isset($_REQUEST['background_color']) ? $_REQUEST['background_color'] : null;

    $image_id = null;
    $background_image_id = null;

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/home_slides/";

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
            echo "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk = 0;
        }
        if ($file["size"] > 5000000) {
            echo "Kechirasiz, sizning faylingiz juda katta.";
            $uploadOk = 0;
        }
        if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg") {
            echo "Kechirasiz, faqat JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_folder = $target_dir . $random_name . ".$file_type";
                
                // 650px ga 500px
                $size = filesize($target_file);
                list($width, $height) = getimagesize($target_file);

                $image_id = $db->insert("images", [
                    "creator_user_id" => $user_id,
                    "width" => $width,
                    "height" => $height,
                    "size" => $size,
                    "file_folder" => $file_folder,
                ]);

                if (!$image_id){
                    echo '<script>alert("xato");</script>';
                    return;
                }
            } else {
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    if ($_FILES['image2']["size"] != 0){
        $target_dir2 = "images/home_slides/backgrounds/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir2)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir2, 0777, true);
        }

        $file = $_FILES['image2'];
        $random_name2 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type2 = basename($file["type"]);
        $target_file2 = $_SERVER["DOCUMENT_ROOT"]."/".$target_dir2 . $random_name2 . ".$file_type2";
        $uploadOk2 = 1;
        $file_type2 = strtolower(pathinfo($target_file2,PATHINFO_EXTENSION));

        if (file_exists(''.$target_file2)) {
            exit("Kechirasiz, fayl allaqachon mavjud.");
            $uploadOk2 = 0;
        }
        if ($file["size"] > 5000000) {
            exit("Kechirasiz, sizning faylingiz juda katta.");
            $uploadOk2 = 0;
        }
        if($file_type2 != "jpg" && $file_type2 != "png" && $file_type2 != "jpeg") {
            exit("Kechirasiz, faqat JPG, JPEG, PNG fayllariga ruxsat berilgan.");
            $uploadOk2 = 0;
        }
        if ($uploadOk2 == 0) {
            exit("Kechirasiz, sizning faylingiz yuklanmadi.");
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file2)) {
                $file_folder = $target_dir2 . $random_name2 . ".$file_type2";
                
                // 650px ga 500px
                $size = filesize($target_file2);
                list($width, $height) = getimagesize($target_file2);

                $background_image_id = $db->insert("images", [
                    "creator_user_id" => $user_id,
                    "width" => $width,
                    "height" => $height,
                    "size" => $size,
                    "file_folder" => $file_folder,
                ]);

                if (!$background_image_id){
                    echo '<script>alert("xato");</script>';
                    exit;;
                }
            } else {
                exit("Kechirasiz, faylingizni yuklashda xatolik yuz berdi.");
            }
        }
    }
    
    
    $home_slides_id = $db->insert("home_slides", [
        "id_name" => name($name),
        "creator_user_id" => $user_id,
        "name" => $name,
        "mini_text" => $mini_text,
        "image_id" => $image_id,
        "background_image_id" => $background_image_id,
        "background_color" => $background_color,
        "html" => $html
    ]);

    header('Location: home_slides_list.php');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Slayd qo'shish</h4>
                            
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
                                <form action="add_home_slide.php" method="POST" class="form" enctype="multipart/form-data">
                                    <script src="https://cdn.ckeditor.com/4.14.1/full-all/ckeditor.js"></script>

                                    <input type="hidden" name="type" value="add_home_slide" required>

                                    <div class="form-group">
                                        <label>kichik ustgi matn</span></label>
                                        <textarea name="name" class="form-control border-primary" placeholder="kichik ustgi matn"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>slayd matni (qisqa)</span></label>
                                        <textarea name="mini_text" class="form-control border-primary" placeholder="slayd matni"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>slayd to'liq matni</span></label>
                                        <textarea cols="80" id="editor" name="html" rows="10" class="form-control border-primary"></textarea>
                                    </div>

                                    <script>
                                        CKEDITOR.replace("editor", {
                                            // Pressing Enter will create a new <div> element.
                                            enterMode: CKEDITOR.ENTER_DIV,
                                            // Pressing Shift+Enter will create a new <p> element.
                                            shiftEnterMode: CKEDITOR.ENTER_P,
                                            
                                            extraPlugins: 'uploadimage,image2',

                                            filebrowserUploadMethod: 'form',
                                            filebrowserUploadUrl: "ck_upload_image.php",
                                        });
                                    </script>

                                    <div class="form-group">
                                        <label>rasmni yuklash (650px 500px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>Fon rasmini yuklash (ihtiyoriy)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image2" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-1">
                                            <div class="form-group">
                                                <label for="background_color">fon rangi</span></label>
                                                <input type="color" name="background_color" class="form-control border-primary" placeholder="fon rangi" id="background_color">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions right">
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

<? include('end.php'); ?>