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

if ($_REQUEST['type'] == "add_information_center"){
    $bio = isset($_REQUEST['bio']) ? $_REQUEST['bio'] : null;
    if (!$bio) {echo"markaz haqida ma'lumot kiritishni unutdingiz!";exit;}
    
    $terms_use = isset($_REQUEST['terms_use']) ? $_REQUEST['terms_use'] : null;
    if (!$terms_use) {echo"foydalanish qoidalari kiritishni unutdingiz!";exit;}
    
    $site = isset($_REQUEST['site']) ? $_REQUEST['site'] : null;
    if (!$site) {echo"website linkini kiritishni unutdingiz!";exit;}

    $work_time = isset($_REQUEST['work_time']) ? $_REQUEST['work_time'] : null;
    if (!$work_time) {echo"ish vaqtini kiritishni unutdingiz";exit;}
    
    $information_center_id = $db->insert("information_center", [
        "creator_user_id" => $user_id,
        "bio" => json_encode($bio, JSON_UNESCAPED_UNICODE),
        "terms_use" => json_encode($terms_use, JSON_UNESCAPED_UNICODE),
        "site" => $site,
        "library" => $_POST["library"],
        "education_site" => $_POST['education_site'],
        "education_site2" => $_POST['education_site2'],
        "education_site3" => $_POST['education_site3'],
        "education_site4" => $_POST['education_site4'],
        "work_time" => json_encode($work_time, JSON_UNESCAPED_UNICODE),
    ]);

    header('Location: information_center.php?page=1');
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Axborot resusrs markaziga ma'lumot qo'shish</h4>

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
                                <form action="add_information_center.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_information_center" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Markaz haqida <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="bio[<?=$lang["flag_icon"]?>]" class="form-control border-primary" rows="10" editor=""></textarea>
                                        </div>
                                        
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Foydalanish qoidalari <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="terms_use[<?=$lang["flag_icon"]?>]" class="form-control border-primary" rows="10" editor=""></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Ish vaqti <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="work_time[<?=$lang["flag_icon"]?>]" rows="10" class="form-control border-primary" editor=""></textarea>
                                        </div>
                                    <? } ?>

                                    <div class="form-group">
                                        <label>Resurslar vebsite linki(https://niuedu.uz)</label>
                                        <input name="site" class="form-control border-primary" placeholder="vebsite"/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Elektron kutubxona linki(https://niuedu.uz)</label>
                                        <input name="library" class="form-control border-primary" placeholder="kutubxona"/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Jahon ilmiy ta’limiy elektron axborot resurslari: </label>
                                    </div>

                                    <div class="form-group">
                                        <label>1 linki(https://niuedu.uz)</label>
                                        <input name="education_site" class="form-control border-primary"/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>2 linki(https://niuedu.uz)</label>
                                        <input name="education_site2" class="form-control border-primary"/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>3 linki(https://niuedu.uz)</label>
                                        <input name="education_site3" class="form-control border-primary"/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>4 linki(https://niuedu.uz)</label>
                                        <input name="education_site4" class="form-control border-primary"/>
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