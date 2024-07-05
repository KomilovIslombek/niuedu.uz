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

$user = $db->assoc("SELECT * FROM teachers WHERE id = ?", [ $_REQUEST["user_id"]]);
if (!$user["id"]) exit(http_response_code(404));

if ($_REQUEST['type'] == "edit_user"){
    $first_name = !empty($_REQUEST['first_name']) ? $_REQUEST['first_name'] : null;
    if (!$first_name) {echo"error1";return;}

    $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
    if (!$last_name) {echo"error1";return;}

    $bio = !empty($_REQUEST['bio']) ? $_REQUEST['bio'] : null;
    $fan = !empty($_REQUEST['fan']) ? $_REQUEST['fan'] : null;
    $tajriba = !empty($_REQUEST['tajriba']) ? $_REQUEST['tajriba'] : null;

    $image_id = $user["big_image_id"];
    $small_image_id = $user["small_image_id"];

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/teachers/";

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
                
                // 1280px 720px
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

                // 480px 272px
                $small_file_folder = $target_dir . $random_name . "2" . ".$file_type";
                $pic = new Imagick($_SERVER['DOCUMENT_ROOT'] . "/$file_folder");
                $pic->resizeImage(600,600,Imagick::FILTER_LANCZOS,1);
                $pic->writeImage($_SERVER['DOCUMENT_ROOT'] . "/$small_file_folder");
                $pic->destroy();
                $small_size = filesize($_SERVER['DOCUMENT_ROOT'] . "/$small_file_folder");
                list($small_width, $small_height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/$small_file_folder");

                $new_small_image_id = $db->insert("images", [
                    "creator_user_id" => $user_id,
                    "width" => $small_width,
                    "height" => $small_height,
                    "size" => $small_size,
                    "file_folder" => $small_file_folder,
                ]);

                if (!$new_small_image_id){
                    echo '<script>alert("rasmni kichik o\'lchamdagini nusxasini bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_image_id && $new_small_image_id) {
                    delete_image($image_id);
                    delete_image($small_image_id);

                    $image_id = $new_image_id;
                    $small_image_id = $new_small_image_id;
                }
            } else {
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    $db->update("teachers", [
        "first_name" => $first_name,
        "last_name" => $last_name,
        "bio" => $bio,
        "fan" => $fan,
        "tajriba" => $tajriba,
        "big_image_id" => $image_id,
        "small_image_id" => $small_image_id
    ], [
        "id" => $_REQUEST['user_id']
    ]);

    header('Location: teachers_list.php');
}

if ($_REQUEST['type'] == "delete_user"){
    $db->delete("teachers", $user["id"]);

    header('Location: teachers_list.php?page=1');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">O'qituvchini tahrirlash</h4>
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
                                    <input type="hidden" name="type" value="edit_user">
                                    <input type="hidden" name="user_id" value="<?=$_REQUEST['user_id']?>">
                                    
                                    <div class="form-group">
                                        <label for="first_name">Ism</label>
                                        <input type="text" name="first_name" class="form-control border-primary" placeholder="ismi" id="first_name" value="<?=$user['first_name']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="last_name">Familiya</label>
                                        <input type="text" name="last_name" class="form-control border-primary" placeholder="familiya" id="last_name" value="<?=$user['last_name']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="fan">Fan (Misol uchun: Matematika)</label>
                                        <input type="text" name="fan" class="form-control border-primary" placeholder="Fan" id="fan" value="<?=$user['fan']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="tajriba">Tajriba (Misol uchun: 10 yil)</label>
                                        <input type="text" name="tajriba" class="form-control border-primary" placeholder="Fan" id="tajriba" value="<?=$user['tajriba']?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="created_date">Tug'ilgan sana</label>
                                        <input type="date" name="created_date" class="form-control border-primary" placeholder="Tug'ilgan sana" id="created_date" value="<?=(date("Y-m-d", strtotime($user['created_date'])))?>">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="bio">Foydalanuvchi haqida</label>
                                                <textarea type="text" name="bio" class="form-control border-primary" placeholder="Foydalanuvchi haqida" id="bio"><?=$user["bio"]?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?
                                    if ($user["big_image_id"]) {
                                        $image = image($user["big_image_id"]);
                                        if ($image["file_folder"]) {
                                            echo '<image src="../'.$image["file_folder"].'" width="250px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>rasmni yuklash (1024px ga 1024px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
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
