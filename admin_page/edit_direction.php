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

$direction_id = isset($_REQUEST['direction_id']) ? $_REQUEST['direction_id'] : null;
if (!$direction_id) {echo"error";return;}

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [$_REQUEST['direction_id']]);
if (!$direction["id"]) {echo"error (direction not found)";exit;}

if ($_REQUEST['type'] == "edit_direction"){
    $active = isset($_REQUEST['active']) ? $_REQUEST['active'] : 0;

    $direction_id = isset($_REQUEST['direction_id']) ? $_REQUEST['direction_id'] : null;
    if (!$direction_id) {echo"error [direction_id]";exit;}
    
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

    $kunduzgi_narx_int = isset($_REQUEST["kunduzgi_narx_int"]) ? $_REQUEST["kunduzgi_narx_int"] : null;
    $kechki_narx_int = isset($_REQUEST["kechki_narx_int"]) ? $_REQUEST["kechki_narx_int"] : null;
    $sirtqi_narx_int = isset($_REQUEST["sirtqi_narx_int"]) ? $_REQUEST["sirtqi_narx_int"] : null;
    
    $sirtqi_staj = isset($_REQUEST["sirtqi_staj"]) ? $_REQUEST["sirtqi_staj"] : null;
    
    $sciences = isset($_REQUEST["sciences"]) ? json_encode($_REQUEST["sciences"], JSON_UNESCAPED_UNICODE) : null;

    $html = isset($_REQUEST['html']) ? $_REQUEST['html'] : null;
    $svg = isset($_REQUEST['svg']) ? $_REQUEST['svg'] : null;
    // if (!$html) {echo"error [html]";exit;}

    $image_id = $direction["image_id"];
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

    $db->delete("direction_learn_types", $direction["id"], "direction_id");
    foreach ($_REQUEST["learn_type"] as $learn_type_id => $val) {
        $db->insert("direction_learn_types", [
            "creator_user_id" => $user_id,
            "direction_id" => $direction["id"],
            "learn_type_id" => $learn_type_id
        ]);
    }

    $db->delete("direction_learn_types_perevod", $direction["id"], "direction_id");
    foreach ($_REQUEST["learn_type_perevod"] as $learn_type_id => $val) {
        $db->insert("direction_learn_types_perevod", [
            "creator_user_id" => $user_id,
            "direction_id" => $direction["id"],
            "learn_type_id" => $learn_type_id
        ]);
    }

    $db->delete("direction_learn_types_ikkinchi_mutaxassislik", $direction["id"], "direction_id");
    foreach ($_REQUEST["learn_type_ikkinchi_mutaxassislik"] as $learn_type_id => $val) {
        $db->insert("direction_learn_types_ikkinchi_mutaxassislik", [
            "creator_user_id" => $user_id,
            "direction_id" => $direction["id"],
            "learn_type_id" => $learn_type_id
        ]);
    }

    

    $db->update("directions", [
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

        "kunduzgi_narx_int" => $kunduzgi_narx_int,
        "kechki_narx_int" => $kechki_narx_int,
        "sirtqi_narx_int" => $sirtqi_narx_int,

        "sciences" => json_encode($sciences, JSON_UNESCAPED_UNICODE),

        "html" => json_encode($html, JSON_UNESCAPED_UNICODE),
        "svg" => $svg,

        "sirtqi_staj" => json_encode($sirtqi_staj, JSON_UNESCAPED_UNICODE),
    ], [
        "id" => $direction["id"]
    ]);

    header('Location: directions_list.php?page=' . $_REQUEST["page"]);
}

if ($_REQUEST['type'] == "delete_direction"){
    $db->delete("direction", $direction["id"]);
    delete_image($direction["image_id"]);
    header('Location: directions_list.php?page=' . $_REQUEST["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">yo'nalishni taxrirlash</h4>
                            
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
                                <form action="edit_direction.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_direction" required>
                                    <input type="hidden" name="page" value="<?=$_REQUEST["page"]?>" required>
                                    <input type="hidden" name="direction_id" value="<?=$_REQUEST['direction_id']?>" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish nomi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="yo'nalish nomi"><?=lng($direction["name"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish qisaqa nomi (ariza bo'limida ishlatiladi) <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="short_name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="yo'nalish qisqa nomi"><?=lng($direction["short_name"], $lang["flag_icon"])?></textarea>
                                        </div>
                                    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="academic_level">Akademik daraja <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="academic_level[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Akademik daraja" id="academic_level"><?=lng($direction["academic_level"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="learning_type">O‘qish tizimi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="learning_type[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="O‘qish tizimi" id="learning_type"><?=lng($direction["learning_type"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_oqish_muddati">Kunduzgi o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi o'qish muddati" id="kunduzgi_oqish_muddati"><?=lng($direction["kunduzgi_oqish_muddati"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_bir_semestr">kunduzgi bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="kunduzgi bir semestr" id="kunduzgi_bir_semestr"><?=lng($direction["kunduzgi_bir_semestr"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kunduzgi_haftalik_oquv_yuklamasi">Kunduzgi haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kunduzgi_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kunduzgi haftalik o'quv yuklamasi" id="kunduzgi_haftalik_oquv_yuklamasi"><?=lng($direction["kunduzgi_haftalik_oquv_yuklamasi"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_oqish_muddati">Kechki o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki o'qish muddati" id="kechki_oqish_muddati"><?=lng($direction["kechki_oqish_muddati"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_bir_semestr">Kechki bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki bir semestr" id="kechki_bir_semestr"><?=lng($direction["kechki_bir_semestr"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="kechki_haftalik_oquv_yuklamasi">Kechki haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="kechki_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Kechki haftalik o'quv yuklamasi" id="kechki_haftalik_oquv_yuklamasi"><?=lng($direction["kechki_haftalik_oquv_yuklamasi"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_oqish_muddati">Sirtqi o'qish muddati <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_oqish_muddati[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi o'qish muddati" id="sirtqi_oqish_muddati"><?=lng($direction["sirtqi_oqish_muddati"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_bir_semestr">Sirtqi bir semestr <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_bir_semestr[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi bir semestr" id="sirtqi_bir_semestr"><?=lng($direction["sirtqi_bir_semestr"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label for="sirtqi_haftalik_oquv_yuklamasi">Sirtqi haftalik o'quv yuklamasi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_haftalik_oquv_yuklamasi[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="Sirtqi haftalik o'quv yuklamasi" id="sirtqi_haftalik_oquv_yuklamasi"><?=lng($direction["sirtqi_haftalik_oquv_yuklamasi"], $lang["flag_icon"])?></textarea>
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>yo'nalish to'liq matni (alohida sahifada ochiladigon ma'lumot uchun) <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea cols="80" id="editor" name="html[<?=$lang["flag_icon"]?>]" rows="10" class="form-control border-primary" editor=""><?=lng($direction["html"], $lang["flag_icon"])?></textarea>
                                        </div>

                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>Sirtqi staj <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <textarea name="sirtqi_staj[<?=$lang["flag_icon"]?>]" class="form-control border-primary"><?=lng($direction["sirtqi_staj"], $lang["flag_icon"])?></textarea>
                                        </div>
                                    <? } ?>

                                    <div class="form-group">
                                        <label for="kunduzgi_narx_int">Kunduzgi narx</label>
                                        <input type="number" name="kunduzgi_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="kunduzgi_narx_int" value="<?=$direction["kunduzgi_narx_int"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="kechki_narx_int">Kechki narx</label>
                                        <input type="number" name="kechki_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="kechki_narx_int" value="<?=$direction["kechki_narx_int"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="sirtqi_narx_int">Sirtqi narx</label>
                                        <input type="number" name="sirtqi_narx_int" class="form-control border-primary" placeholder="Kunduzgi narx" id="sirtqi_narx_int" value="<?=$direction["sirtqi_narx_int"]?>">
                                    </div>

                                    <?
                                    if ($direction["image_id"]) {
                                        $image = image($direction["image_id"]);
                                        if ($image["file_folder"]) {
                                            echo '<image src="../'.$image["file_folder"].'" width="400px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>rasmni yuklash (500px 500px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>Holat</label>
                                        <select name="active" class="form-control">
                                            <option value="1" <?=($direction["active"] == 1 ? 'selected=""' : '')?>>Faol</option>
                                            <option value="0" <?=($direction["active"] == 0 ? 'selected=""' : '')?>>Faol emas</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Ta'lim shakli (Oddiy)</label>
                                        
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
                                                                echo '<td><input type="checkbox" name="learn_type['.$learn_type["id"].']"'.($direction_learn_type["id"] ? ' checked=""' : '').'></td>';
                                                                echo '<td>'.$learn_type["name"].'</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Ta'lim shakli O'qishni ko'chirish -->
                                    <div class="form-group">
                                        <label>Ta'lim shakli (O'qishni ko'chirish)</label>
                                        
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
                                                            $direction_learn_type_perevod = $db->assoc("SELECT * FROM direction_learn_types_perevod WHERE direction_id = ? AND learn_type_id = ?", [ $direction["id"], $learn_type["id"] ]);

                                                            echo '<tr>';
                                                                echo '<td><input type="checkbox" name="learn_type_perevod['.$learn_type["id"].']"'.($direction_learn_type_perevod["id"] ? ' checked=""' : '').'></td>';
                                                                echo '<td>'.$learn_type["name"].'</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Ta'lim shakli Ikkinchi mutaxassislik -->
                                    <div class="form-group">
                                        <label>Ta'lim shakli (Ikkinchi mutaxassislik)</label>
                                        
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
                                                            $direction_learn_type_ikkinchi_mutaxassislik = $db->assoc("SELECT * FROM direction_learn_types_ikkinchi_mutaxassislik WHERE direction_id = ? AND learn_type_id = ?", [ $direction["id"], $learn_type["id"] ]);

                                                            echo '<tr>';
                                                                echo '<td><input type="checkbox" name="learn_type_ikkinchi_mutaxassislik['.$learn_type["id"].']"'.($direction_learn_type_ikkinchi_mutaxassislik["id"] ? ' checked=""' : '').'></td>';
                                                                echo '<td>'.$learn_type["name"].'</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Test topshiriladigon fanlar: (O'ZBEK)</label>
                                        
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>№</th>
                                                        <th>fan nomi</th>
                                                        <th>o'chirish</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fanlar">
                                                    <?php
                                                        $sciences = $direction["sciences"] ? json_decode($direction["sciences"], true) : ["uz" => [""]];
                                                        $sciences = empty($sciences["uz"]) || !is_array($sciences["uz"]) || count($sciences["uz"]) == 0 ? ["uz" => [""]] : $sciences;

                                                        foreach ($sciences["uz"] as $num => $science_name) {
                                                            echo '<tr>';
                                                                echo '<td style="width:0;">';
                                                                    echo $num + 1;
                                                                echo '</td>';

                                                                echo '<td>';
                                                                    echo '<input type="text" name="sciences[uz][]" value="'.$science_name.'" style="width:100%;" class="form-control border-primary">';
                                                                echo '</td>';

                                                                echo '<td style="width:0;">';
                                                                    echo '<button class="tag tag-default tag-danger text-white bg-danger" id="remove-fan">o\'chirish</button>';
                                                                echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>

                                            <button type="button" class="btn btn-success" id="add-fan">
                                                <i class="icon-plus2"></i> Fan qo'shish
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Test topshiriladigon fanlar: (RUS)</label>
                                        
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th>№</th>
                                                        <th>fan nomi</th>
                                                        <th>o'chirish</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fanlar">
                                                    <?php
                                                        $sciences = $direction["sciences"] ? json_decode($direction["sciences"], true) : ["ru" => [""]];

                                                        $sciences = empty($sciences["ru"]) || !is_array($sciences["ru"]) || count($sciences["ru"]) == 0 ? ["ru" => [""]] : $sciences;

                                                        foreach ($sciences["ru"] as $num => $science_name) {
                                                            echo '<tr>';
                                                                echo '<td style="width:0;">';
                                                                    echo $num + 1;
                                                                echo '</td>';

                                                                echo '<td>';
                                                                    echo '<input type="text" name="sciences[ru][]" value="'.$science_name.'" style="width:100%;" class="form-control border-primary">';
                                                                echo '</td>';

                                                                echo '<td style="width:0;">';
                                                                    echo '<button class="tag tag-default tag-danger text-white bg-danger" id="remove-fan">o\'chirish</button>';
                                                                echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    ?>

                                                    
                                                </tbody>
                                            </table>

                                            <button type="button" class="btn btn-success" id="add-fan">
                                                <i class="icon-plus2"></i> Fan qo'shish
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>svg yoki base64 png (bosh sahifadagi ikonka)</span></label>
                                        <textarea name="svg" class="form-control border-primary"><?=$direction["svg"]?></textarea>
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

<script>
    function renameTable() {
        $(".fanlar").find("tr").each(function(){
            var num = parseInt($(this).index()) + 1;
            $(this).find("td").eq(0).text(num);
        })
    }

    setInterval(() => {
        renameTable();
    }, 1000);

    $(document).on("click", "#add-fan", function(){
        var table = $(this).parents(".table-responsive").find("table");
        var tbody = $(table).find("tbody");
        var last_tr = $(table).find("tr").last();
        var last_tr_html = $(last_tr).html();
        $(tbody).append("<tr>" + last_tr_html + "</tr>");
        var last_tr = $(table).find("tr").last();
        $(last_tr).find("td").eq(1).find("input").val("");
        renameTable();
    });

    $(document).on("click", "#remove-fan", function(){
        $(this).parents("tr").remove();
        renameTable();
    });
</script>

<? include('end.php'); ?>