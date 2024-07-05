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

$page = (int)$_REQUEST["page"];
if (empty($page)) $page = 1;

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
if (!$id) {echo"error";return;}

$scientific_council = $db->assoc("SELECT * FROM scientific_council WHERE id = ?", [$_REQUEST['id']]);
if (!$scientific_council["id"]) {echo"error (scientific_council is not found)";exit;}

$councils = $db->in_array("SELECT * FROM council");

if ($_REQUEST['type'] == "edit_scientific_council"){
    $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
    if (!$id) {echo"error [id]";exit;}
    
    $council_id = isset($_REQUEST['council_id']) ? $_REQUEST['council_id'] : null;
    if (!$council_id) {echo"qaysi ilmiy kengashligini kiritishni unutdingiz!";exit;}

    $first_name = isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : null;
    if (!$first_name) {echo"ismini ismi kiritishni unutdingiz!";exit;}
    
    $last_name = isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
    if (!$last_name) {echo"familyasini familyasi kiritishni unutdingiz!";exit;}
    
    $father_first_name = isset($_REQUEST['father_first_name']) ? $_REQUEST['father_first_name'] : null;
    if (!$father_first_name) {echo"otasini ismini ismi kiritishni unutdingiz!";exit;}

    $bio = isset($_REQUEST['bio']) ? $_REQUEST['bio'] : null;
    if (!$bio) {echo"biografiyasini kiritishni unutdingiz";exit;}

    $image_id = $scientific_council["image_id"];
    
    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/scientific_council/";

        if (!file_exists("".$target_dir)) {
            mkdir("".$target_dir, 0777, true);
        }

        $file = $_FILES['image'];
        $random_name = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type = basename($file["type"]);
        $target_file = "".$target_dir . $random_name . ".$file_type";
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
                
                // 576px 324px
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
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }
    
    
    $db->update("scientific_council", [
        "council_id" => $council_id,
        "first_name" => json_encode($first_name, JSON_UNESCAPED_UNICODE),
        "last_name" => json_encode($last_name, JSON_UNESCAPED_UNICODE),
        "father_first_name" => json_encode($father_first_name, JSON_UNESCAPED_UNICODE),
        "image_id" => $image_id,
        "bio" => json_encode($bio, JSON_UNESCAPED_UNICODE),
    ],
    [
        "id" => $scientific_council["id"]
    ]);

    header('Location: scientific_council.php?page='.$page);
}

if ($_REQUEST['type'] == "delete_scientific_council"){
    $db->delete("scientific_council", $scientific_council["id"]);
    delete_image($scientific_council["image_id"]);
    header('Location: scientific_council.php?page='.$page);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">ilmiy kengashga odamni tahrirlash</h4>
                            
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
                                <form action="edit_scientific_council.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_scientific_council" required>
                                    <input type="hidden" name="page" value="<?=$page?>" required>
                                    <input type="hidden" name="id" value="<?=$_REQUEST['id']?>" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>ismi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="first_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="ismi"><?=lng($scientific_council["first_name"], $lang["flag_icon"])?></textarea>
                                        </div>
                                        
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>familyasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="last_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="familyasi"><?=lng($scientific_council["last_name"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>otasini ismi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="father_first_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary"><?=lng($scientific_council["father_first_name"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Bio <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="bio[<?=$lang["flag_icon"]?>]" class="form-control border-primary"><?=lng($scientific_council["bio"], $lang["flag_icon"])?></textarea>
                                        </div>
                                    <? } ?>

                                    <?
                                    if ($scientific_council["image_id"]) {
                                        $image = image($scientific_council["image_id"]);
                                        if ($image["file_folder"]) {
                                            echo '<image src="../'.$image["file_folder"].'" width="400px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>rasmni yuklash </label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    

                                    <div class="form-group">
                                        <label>ilmiy kengashlar</label>
                                        <select name="council_id" class="form-control border-primary">
                                            <? foreach ($councils as $council) { ?>
                                                <option value="<?=$council["id"]?>" <?=($scientific_council["council_id"] == $council["id"] ? 'selected=""' : '')?>><?=lng($council["name"], "uz")?></option>
                                            <? } ?>
                                        </select>
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