<?php
$user_id = $systemUser->id;
$rights = $systemUser->rights;
// tekshiruv
if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

include('filter.php');

if ($_REQUEST['type'] == "add_direction"){
    $active = isset($_REQUEST['active']) ? $_REQUEST['active'] : 0;

    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"yo'nalish nomini kiritishni unutdingiz!";exit;}

    $short_name = isset($_REQUEST['short_name']) ? $_REQUEST['short_name'] : null;
    if (!$short_name) {echo"yo'nalish qisqa nomini kiritishni unutdingiz!";exit;}

    $academic_level = isset($_REQUEST["academic_level"]) ? $_REQUEST["academic_level"] : null;
    $learning_type = isset($_REQUEST["learning_type"]) ? $_REQUEST["learning_type"] : null;

    $kunduzgi_oqish_muddati = isset($_REQUEST["kunduzgi_oqish_muddati"]) ? $_REQUEST["kunduzgi_oqish_muddati"] : null;
    $kunduzgi_bir_semestr = isset($_REQUEST["kunduzgi_bir_semestr"]) ? $_REQUEST["kunduzgi_bir_semestr"] : null;
    $kunduzgi_haftalik_oquv_yuklamasi = isset($_REQUEST["kunduzgi_haftalik_oquv_yuklamasi"]) ? $_REQUEST["kunduzgi_haftalik_oquv_yuklamasi"] : null;

    $kechki_oqish_muddati = isset($_REQUEST["kechki_oqish_muddati"]) ? $_REQUEST["kechki_oqish_muddati"] : null;
    $kechki_bir_semestr = isset($_REQUEST["kechki_bir_semestr"]) ? $_REQUEST["kechki_bir_semestr"] : null;
    $kechki_haftalik_oquv_yuklamasi = isset($_REQUEST["kechki_haftalik_oquv_yuklamasi"]) ? $_REQUEST["kechki_haftalik_oquv_yuklamasi"] : null;

    $sirtqi_oqish_muddati = isset($_REQUEST["sirtqi_oqish_muddati"]) ? $_REQUEST["sirtqi_oqish_muddati"] : null;
    $sirtqi_bir_semestr = isset($_REQUEST["sirtqi_bir_semestr"]) ? $_REQUEST["sirtqi_bir_semestr"] : null;
    $sirtqi_haftalik_oquv_yuklamasi = isset($_REQUEST["sirtqi_haftalik_oquv_yuklamasi"]) ? $_REQUEST["sirtqi_haftalik_oquv_yuklamasi"] : null;

    $kunduzgi_narx = isset($_REQUEST["kunduzgi_narx"]) ? $_REQUEST["kunduzgi_narx"] : null;
    $kechki_narx = isset($_REQUEST["kechki_narx"]) ? $_REQUEST["kechki_narx"] : null;
    $sirtqi_narx = isset($_REQUEST["sirtqi_narx"]) ? $_REQUEST["sirtqi_narx"] : null;

    $kunduzgi_narx_int = isset($_REQUEST["kunduzgi_narx_int"]) ? $_REQUEST["kunduzgi_narx_int"] : null;
    $kechki_narx_int = isset($_REQUEST["kechki_narx_int"]) ? $_REQUEST["kechki_narx_int"] : null;
    $sirtqi_narx_int = isset($_REQUEST["sirtqi_narx_int"]) ? $_REQUEST["sirtqi_narx_int"] : null;

    $sirtqi_staj = isset($_REQUEST["sirtqi_staj"]) ? $_REQUEST["sirtqi_staj"] : null;
    
    $html = isset($_REQUEST['html']) ? $_REQUEST['html'] : null;
    $svg = isset($_REQUEST['svg']) ? $_REQUEST['svg'] : null;

    if ($_FILES['image']["size"] != 0){
        $target_dir = "images/directions/";

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
    
    
    $direction_id = $db->insert("directions", [
        "active" => $active,
        "id_name" => name($name["uz"]),
        "creator_user_id" => $user_id,
        "name" => json_encode($name, JSON_UNESCAPED_UNICODE),
        "short_name" => json_encode($short_name, JSON_UNESCAPED_UNICODE),
        "image_id" => $image_id,

        "academic_level" => json_encode($academic_level, JSON_UNESCAPED_UNICODE),
        "learning_type" => json_encode($learning_type, JSON_UNESCAPED_UNICODE),

        "kunduzgi_oqish_muddati" => json_encode($kunduzgi_oqish_muddati, JSON_UNESCAPED_UNICODE),
        "kunduzgi_bir_semestr" => json_encode($kunduzgi_bir_semestr, JSON_UNESCAPED_UNICODE),
        "kunduzgi_haftalik_oquv_yuklamasi" => json_encode($kunduzgi_haftalik_oquv_yuklamasi, JSON_UNESCAPED_UNICODE),

        "kechki_oqish_muddati" => json_encode($kechki_oqish_muddati, JSON_UNESCAPED_UNICODE),
        "kechki_bir_semestr" => json_encode($kechki_bir_semestr, JSON_UNESCAPED_UNICODE),
        "kechki_haftalik_oquv_yuklamasi" => json_encode($kechki_haftalik_oquv_yuklamasi, JSON_UNESCAPED_UNICODE),

        "sirtqi_oqish_muddati" => json_encode($sirtqi_oqish_muddati, JSON_UNESCAPED_UNICODE),
        "sirtqi_bir_semestr" => json_encode($sirtqi_bir_semestr, JSON_UNESCAPED_UNICODE),
        "sirtqi_haftalik_oquv_yuklamasi" => json_encode($sirtqi_haftalik_oquv_yuklamasi, JSON_UNESCAPED_UNICODE),

        "kunduzgi_narx" => json_encode($kunduzgi_narx, JSON_UNESCAPED_UNICODE),
        "kechki_narx" => json_encode($kechki_narx, JSON_UNESCAPED_UNICODE),
        "sirtqi_narx" => json_encode($sirtqi_narx, JSON_UNESCAPED_UNICODE),

        "kunduzgi_narx_int" => $kunduzgi_narx_int,
        "kechki_narx_int" => $kechki_narx_int,
        "sirtqi_narx_int" => $sirtqi_narx_int,

        "html" => json_encode($html, JSON_UNESCAPED_UNICODE),
        "svg" => $svg,

        "sirtqi_staj" => json_encode($sirtqi_staj, JSON_UNESCAPED_UNICODE),
    ]);

    if ($direction_id > 0) {
        foreach ($_REQUEST["learn_type"] as $learn_type_id => $val) {
            $db->insert("direction_learn_types", [
                "creator_user_id" => $user_id,
                "direction_id" => $direction_id,
                "learn_type_id" => $learn_type_id
            ]);
        }
    }

    header("Location: directions_list.php?page=1");
}

$svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
<g>
<path class="st0" d="m178.7 492h-58.7c-55.2 0-100-44.8-100-100v-272c0-55.2 44.8-100 100-100h58.7c-55 0-97.7 44.8-97.7 100v272c0 55.2 42.7 100 97.7 100zm176.8-287.2 18.9-85.5c4.8-24.1 16.7-46.3 34.1-63.7l35.4-35.4c-15.1-1.4-30.7 3.7-42.3 15.3l-61.1 61.1c-17.4 17.4-29.3 39.6-34.1 63.7l-11.4 56.7 56.7-11.3c1.2-0.3 2.5-0.6 3.8-0.9z"/>
<path class="st1" d="m299 512h-179c-66.2 0-120-53.8-120-120v-272c0-66.2 53.8-120 120-120h183c11 0 20 9 20 20s-9 20-20 20h-183c-44.1 0-80 35.9-80 80v272c0 44.1 35.9 80 80 80h179c44.1 0 80-35.9 80-80v-120c0-11 9-20 20-20s20 9 20 20v120c0 66.2-53.8 120-120 120zm-0.1-275.4 56.7-11.3c28.1-5.6 53.7-19.3 73.9-39.6l61.1-61.1c28.5-28.5 28.5-74.8 0-103.2-28.5-28.5-74.8-28.5-103.2 0l-61.1 61.1c-20.3 20.3-33.9 45.8-39.6 73.9l-11.3 56.7c-1.3 6.6 0.7 13.3 5.5 18.1 3.8 3.8 8.9 5.9 14.1 5.9 1.3-0.1 2.6-0.2 3.9-0.5zm163.5-186.9c6.2 6.2 9.7 14.5 9.7 23.3s-3.4 17.1-9.7 23.3l-61.1 61.1c-14.7 14.7-33.2 24.6-53.5 28.6l-27.3 5.4 5.4-27.3c4.1-20.3 14-38.8 28.6-53.5l61.1-61.1c6.2-6.2 14.5-9.7 23.3-9.7s17.2 3.6 23.5 9.9z"/>
<path class="st2" d="m319 352h-218c-11 0-20-9-20-20s9-20 20-20h218c11 0 20 9 20 20s-8.9 20-20 20zm-108 35c-13.8 0-25 11.2-25 25s11.2 25 25 25 25-11.2 25-25-11.2-25-25-25z"/>
</g>
</svg>';

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">yo'nalish qo'shish</h4>

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
                                <form action="add_direction.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_direction" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish nomi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="yo'nalish nomi"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish qisaqa nomi (ariza bo'limida ishlatiladi) <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="short_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="yo'nalish qisqa nomi"></textarea>
                                        </div>
                                    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="academic_level">Akademik daraja <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="academic_level[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Akademik daraja" id="academic_level"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="learning_type">O‘qish tizimi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="learning_type[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="O‘qish tizimi" id="learning_type"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_oqish_muddati">Kunduzgi o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi o'qish muddati" id="kunduzgi_oqish_muddati"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_bir_semestr">kunduzgi bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="kunduzgi bir semestr" id="kunduzgi_bir_semestr"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_haftalik_oquv_yuklamasi">Kunduzgi haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi haftalik o'quv yuklamasi" id="kunduzgi_haftalik_oquv_yuklamasi"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_oqish_muddati">Kechki o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki o'qish muddati" id="kechki_oqish_muddati"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_bir_semestr">Kechki bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki bir semestr" id="kechki_bir_semestr"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_haftalik_oquv_yuklamasi">Kechki haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki haftalik o'quv yuklamasi" id="kechki_haftalik_oquv_yuklamasi"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_oqish_muddati">Sirtqi o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi o'qish muddati" id="sirtqi_oqish_muddati"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_bir_semestr">Sirtqi bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi bir semestr" id="sirtqi_bir_semestr"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_haftalik_oquv_yuklamasi">Sirtqi haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi haftalik o'quv yuklamasi" id="sirtqi_haftalik_oquv_yuklamasi"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_narx">Kunduzgi narx <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_narx[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi narx" id="kunduzgi_narx"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_narx">Kechki narx <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_narx[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi narx" id="kechki_narx"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_narx">Sirtqi narx <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_narx[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi narx" id="sirtqi_narx"></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish to'liq matni (alohida sahifada ochiladigon ma'lumot uchun) <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="html[<?=$lang["flag_icon"]?>]" rows="10" class="form-control border-primary" editor=""></textarea>
                                        </div>

                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_staj">Sirtqi staj <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_staj[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="sirtqi_staj" id="sirtqi_staj"></textarea>
                                        </div>
                                    <? } ?>

                                    <div class="form-group">
                                        <label for="kunduzgi_narx_int">Kunduzgi narx</label>
                                        <input type="number" name="kunduzgi_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="kunduzgi_narx_int">
                                    </div>

                                    <div class="form-group">
                                        <label for="kechki_narx_int">Kechki narx</label>
                                        <input type="number" name="kechki_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="kechki_narx_int">
                                    </div>

                                    <div class="form-group">
                                        <label for="sirtqi_narx_int">Sirtqi narx</label>
                                        <input type="number" name="sirtqi_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="sirtqi_narx_int">
                                    </div>

                                    <div class="form-group">
                                        <label>rasmni yuklash (500px 500px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*" required>
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>Holat</label>
                                        <select name="active" class="form-control">
                                            <option value="1" selected>Faol</option>
                                            <option value="0">Faol emas</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Ta'lim shakli</label>
                                        
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>faollashtirilgan</th>
                                                        <th>ta'lim shakli</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $learn_types = $db->in_array("SELECT * FROM learn_types");

                                                        foreach ($learn_types as $learn_type) {
                                                            $direction_learn_type = $db->assoc("SELECT * FROM direction_learn_types WHERE direction_id = ? AND learn_type_id = ?", [ $direction["id"], $learn_type["id"] ]);

                                                            echo '<tr>';
                                                                echo '<td><input type="checkbox" name="learn_type['.$learn_type["id"].']"></td>';
                                                                echo '<td>'.$learn_type["name"].'</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>svg yoki base64 png (bosh sahifadagi ikonka)</span></label>
                                        <textarea name="svg" class="form-control border-primary"><?=$svg?></textarea>
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