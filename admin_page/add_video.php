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

if ($_REQUEST['type'] == "add_home_video"){
    $name = !empty($_REQUEST['name']) ? $_REQUEST['name'] : null;
    // if (!$name) {echo"error [name]";exit;}

    $mini_text = !empty($_REQUEST['mini_text']) ? $_REQUEST['mini_text'] : null;
    // if (!$mini_text) {echo"error [mini_text]";exit;}

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/videos/posters/";

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
            exit("Kechirasiz, sizning faylingiz hajmi meyordan ko'p.");
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
                exit("Kechirasiz, faylingizni yuklashda xatolik yuz berdi.");
            }
        }
    } else {
    //   exit("\n\nXatolik, rasm yuklash kerak!");
    }

    if ($_FILES['video']["size"] != 0){
        $target_dir = "videos/home_videos/";
        
        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir, 0777, true);
        }

        $file = $_FILES['video'];
        $random_name = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type = basename($file["type"]);
        $uploadOk = 1;
        
        $file_type = strtr($file_type, array(
            "video/mp4" => "mp4",
            "video/mpg" => "mpg",
            "video/mpeg" => "mpeg",
            "video/mov" => "mov",
            "video/avi" => "avi",
            "video/flv" => "flv",
            "video/wmv" => "wmv",
            "video/3gp" => "3gp",
            "x-matroska" => "mkv",
        ));

        $target_file = $_SERVER["DOCUMENT_ROOT"]."/".$target_dir . $random_name . ".$file_type";
    
        if (file_exists(''.$target_file)) {
            echo "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk = 0;
        }
        if ($file["size"] > 500000000) {
            echo "Kechirasiz, sizning faylingiz juda katta. max 500Mb";
            $uploadOk = 0;
        }
        if (
            $file_type != "mp4" &&
            $file_type != "3gp" &&
            $file_type != "mpeg" &&
            $file_type != "avi" &&
            $file_type != "mkv" &&
            $file_type != "mov" &&
            $file_type != "wmv" &&
            $file_type != "flv"
        ) {
            echo "Kechirasiz, faqat MP4, MPEG, 3GP, AVI, MKV, MOV, WMV, FLV fayllariga ruxsat berilgan.<br>siz yuklamoqchi bo'lgan fayl format ($file_type)";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_folder = $target_dir . $random_name . ".$file_type";

                include("getid3/getid3/getid3.php");
                
                $file_size = filesize($_SERVER["DOCUMENT_ROOT"]."/".$file_folder);
                $getID3 = new getID3;
                $file_info = $getID3->analyze($_SERVER["DOCUMENT_ROOT"]."/".$file_folder);
                $duration = $file_info['playtime_seconds']; // fayl davomiylik vaqtini olish
                $width  =  $file_info['video']['resolution_x'];  // video fayl razmerlarni olish bo'yi
                $height =  $file_info['video']['resolution_y'];  // video fayl razmerlarni olish eni

                // $file_info = json_encode($file_info, JSON_INVALID_UTF8_SUBSTITUTE);
                // if (json_last_error_msg() != "No error") exit(json_last_error_msg());

                $video_id = $db->insert("videos", [
                    "creator_user_id" => $user_id,
                    "width" => $width,
                    "height" => $height,
                    "size" => $file_size,
                    "duration" => $duration,
                    "file_folder" => $file_folder
                ]);

                if (!$video_id){
                    exit('<script>alert("videoni bazaga yozishda xatolik yuz berdi");</script>');
                    return;
                }
            } else {
                echo "Kechirasiz, video faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }

    $home_video_id = $db->insert("home_videos", [
        "name" => $name,
        "mini_text" => $mini_text,
        "image_id" => $image_id,
        "video_id" => $video_id
    ]);
  
    header('Location: videos_list.php');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Yangi video qo'shish</h4>
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
                                <form action="" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_home_video">

                                    <div class="form-group">
                                        <label>video rasmi(poster)ni yuklash (576px ga 324px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*" required="">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>videoni yuklash</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="video" accept="video/*,.mkv" required="">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Nomi</label>
                                        <input type="text" name="name" class="form-control border-primary" placeholder="Nomi" id="name" required="">
                                    </div>

                                    <div class="form-group">
                                        <label for="mini_text">Tavsif</label>
                                        <input type="text" name="mini_text" class="form-control border-primary" placeholder="Tavsif" id="mini_text" required="">
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Qo'shish
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
