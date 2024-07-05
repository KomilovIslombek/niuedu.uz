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


if ($_REQUEST['type'] == "add_team"){
    $team_type = isset($_REQUEST['team_type']) ? $_REQUEST['team_type'] : null;
    if (!$team_type) {echo"jamodagi ornini ismi kiritishni unutdingiz!";exit;}

    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : null;
    if (!$status) {echo"unvonini kiritishni unutdingiz!";exit;}

    $first_name = isset($_REQUEST['first_name']) ? $_REQUEST['first_name'] : null;
    if (!$first_name) {echo"ismini ismi kiritishni unutdingiz!";exit;}
        
    $last_name = isset($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
    if (!$last_name) {echo"familyasini familyasi kiritishni unutdingiz!";exit;}

    $father_first_name = isset($_REQUEST['father_first_name']) ? $_REQUEST['father_first_name'] : null;

    $role = isset($_REQUEST['role']) ? $_REQUEST['role'] : null;
    if (!$role) {echo"lavozimini kiritishni unutdingiz";exit;}

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/team/";

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
                
                // 1280px 720px
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
    } else {
        exit("Rasm yuklashda xatolik!");
    }
    
    
    $team_id = $db->insert("team", [
        "creator_user_id" => $user_id,
        "team_type" => $team_type,
        "status" => json_encode($status, JSON_UNESCAPED_UNICODE),
        "first_name" => json_encode($first_name, JSON_UNESCAPED_UNICODE),
        "last_name" => json_encode($last_name, JSON_UNESCAPED_UNICODE),
        "father_first_name" => json_encode($father_first_name, JSON_UNESCAPED_UNICODE),
        "image_id" => $image_id,
        "phone" => $_POST["phone"],
        "role" => json_encode($role, JSON_UNESCAPED_UNICODE),
        "email_link" => $_POST["email_link"],
        "telegram_link" => $_POST["telegram_link"],
        "linkedin_link" => $_POST["linkedin_link"],
    ]);

    header('Location: team.php?page=1');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Jamoaga odam qo'shish</h4>

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
                                <form action="add_team.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_team" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>unvoni <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="status[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="unvoni"></textarea>
                                        </div>

                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>ismi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="first_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="ismi"></textarea>
                                        </div>
                                        
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>familyasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="last_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="familyasi"></textarea>
                                        </div>
                                        
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>otasini ismi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="father_first_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="otasini ismi"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Lavozimi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="role[<?=$lang["flag_icon"]?>]" class="form-control border-primary"></textarea>
                                        </div>
                                    <? } ?>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="phone">Telefon raqami </label>
                                        <input type="text" name="phone"  class="form-control border-primary" placeholder="+998" id="phone" minlength="17" required="" value="<?=($_POST["phone"] ? htmlspecialchars($_POST["phone"]) : "")?>">
                                    </div>

                                    <div class="form-group">
                                        <label>rasmni yuklash</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*" required>
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>Jamoadagi orni</label>
                                        <select name="team_type" class="form-control border-primary">
                                            <? foreach ($team_types as $key => $type) { ?>
                                                <option value="<?=$key?>"><?=$type?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="email_link">Emil linki</label>
                                        <input type="text" name="email_link" class="form-control border-primary" placeholder="Emil linki">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="telegram_link">Telegram linki (@test_1024)</label>
                                        <input type="text" name="telegram_link" class="form-control border-primary" placeholder="Telegram linki">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="linkedin_link">Linkedin linki</label>
                                        <input type="text" name="linkedin_link" class="form-control border-primary" placeholder="Linkedin linki">
                                    </div>
                                    
                                    <div class="form-actions right">
                                        <button type="button" class="btn btn-warning mr-1">
                                            <i class="icon-cross2"></i> bekor qilish
                                        </button>
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