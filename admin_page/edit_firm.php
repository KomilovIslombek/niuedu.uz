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

$agent_id = isset($_REQUEST['firm_id']) ? $_REQUEST['firm_id'] : null;
if (!$agent_id) {echo"error";return;}

$agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [$_REQUEST['firm_id']]);
if (!$agent["id"]) {echo"error (firm not found)";exit;}

if ($_REQUEST['type'] == "edit_firm"){

    include "modules/uploadFile.php";
    
    $uploadedFilePassport = uploadFileWithUpdate("file_1", "files/upload/passport", ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "png", "jpg", "jpeg", "mp4"], false, false, $agent["passport_id"]);

    if (!empty($_POST["passport_serial"]) && !empty($_POST["passport_number"])) {
        $_POST["passport_serial_number"] = str_replace(" ", "", $_POST["passport_serial"] . " " . $_POST["passport_number"]);
    }
    
    if (!$_POST["phone_1"]) {
        $error = "telefon raqamni kiritishni unutdingiz !!!";
    } else if (strlen($_POST["phone_1"]) != 17 || substr($_POST["phone_1"], 0, 4) != "+998") {
        $error = "telefon raqam noto'g'ri formatda kiritilgan !!!";
    }

    if (!$_POST["first_name"] || !$_POST["last_name"] || !$_POST["father_first_name"] || !$_POST["passport_jshr"] || !$_POST["card_number"] || !$_POST["transit_check"]) {
        $error = "Ma'lumotlarni to'ldirishni unutdingiz!";
    }

    if (!$error) {
        $user = $db->assoc("SELECT * FROM users WHERE login = ?", [ $agent["phone_1"] ]);
        $db->update("firms", [
            "creator_user_id" => $user_id,
            "first_name" => $_POST["first_name"],
            "last_name" => $_POST["last_name"],
            "father_first_name" => $_POST["father_first_name"],
            "phone_1" => $_POST["phone_1"],
            "phone_2" => $_POST["phone_2"],
            "passport_serial_number" => $_POST["passport_serial_number"],
            "passport_jshr" => $_POST["passport_jshr"],
            "card_number" => $_POST["card_number"],
            "transit_check" => $_POST["transit_check"],
            "passport_id" => $uploadedFilePassport["file_id"],
        ], [
            "id" => $agent["id"]
        ]);
        
        $db->update("users", [ 
            "login" => str_replace(" ", "", $_POST["phone_1"]),
            "password" => md5(md5(encode($_POST["passport_serial_number"]))),
            "password_encrypted" => encode($_POST["passport_serial_number"]),
        ], [
            "id" => $user["id"] 
        ]);
        
        header('Location: firms_list.php?page=' . $_GET["page"]);
    }
    
}

if ($_REQUEST['type'] == "delete_firm"){
    
    $db->delete("firms", $agent["id"], "id");
    $db->delete("users", $agent["phone_1"], "login");
    delete_file($agent["passport_id"]);
    header('Location: firms_list.php?page=' . $_GET["page"]);
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Agentni tahrirlash</h4>
                            
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
                                <form action="edit_firm.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_firm" required>
                                    <input type="hidden" name="page" value="<?=$_GET["page"]?>" required>
                                    <input type="hidden" name="firm_id" value="<?=$_REQUEST['firm_id']?>" required>

                                    <? if ($error) { ?>
                                        <h3 class="text-center text-danger"><?=$error?></h3>
                                    <? } ?>

                                    <div class="form-group">
                                        <label>Agent familyasi</label>
                                        <input name="last_name" value="<?=$agent["last_name"]?>" class="form-control border-primary" placeholder="Agent familyasi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Agent ismi</label>
                                        <input name="first_name" value="<?=$agent["first_name"]?>" class="form-control border-primary" placeholder="Agent ismi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Agent otasini ismi</label>
                                        <input name="father_first_name" value="<?=$agent["father_first_name"]?>" class="form-control border-primary" placeholder="Agent otasini ismi" required/>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="phone_1">Telefon raqamingiz </label>
                                        <input type="text" name="phone_1" value="<?=$agent["phone_1"]?>" class="form-control border-primary" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="phone_2">Qo'shimcha telefon raqam</label>
                                        <input type="text" name="phone_2" value="<?=$agent["phone_2"]?>" class="form-control border-primary"  placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($_POST["phone_2"] ? htmlspecialchars($_POST["phone_2"]) : "+998")?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="card_number">Karta raqami</label>
                                        <input type="text" name="card_number" value="<?=$agent["card_number"]?>" class="form-control border-primary" placeholder="8600"  required="">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="fieldlabels" for="transit_check">Karta egasi</label>
                                        <input type="text" name="transit_check" value="<?=$agent["transit_check"]?>" class="form-control border-primary" placeholder="0000"  required="" >
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_serial">Passport seriyasi</label>
                                        <input type="text" name="passport_serial" value="<?=substr($agent["passport_serial_number"], 0, 2)?>" class="form-control border-primary" placeholder="- -" id="passport_serial" required="">
                                    </div>

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_number">Passport raqami</label>
                                        <input type="text" name="passport_number" value="<?=substr($agent["passport_serial_number"], 2)?>" class="form-control border-primary" placeholder="- - - - - - -" id="passport_number" required="" >
                                    </div>
                                    
                                    
                                    <img src="../images/jshr.jpg" alt="jshr" width="150px">

                                    <div class="form-group">
                                        <label class="fieldlabels" for="passport_jshr">Passport jshr</label>
                                        <input type="text" name="passport_jshr" value="<?=$agent["passport_jshr"]?>" id="passport_jshr" class="form-control border-primary" placeholder="- - - - - - - - - - - - - -"  required="" >
                                    </div>


                                    <?
                                    if ($agent["passport_id"]) {
                                        $passport_file = fileArr($agent["passport_id"]);
                                        if ($passport_file["file_folder"]) {
                                            echo '<img src="../'.$passport_file["file_folder"].'" width="150px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="file_1">Passport nusxasi</label>
                                        <input type="file" name="file_1" class="form-control border-primary" id="file_1" accept="file/*">
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
    $("#phone_1").on('input keyup', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        // console.log(x);
        e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });

    $("#phone_2").on('input keyup', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        // console.log(x);
        e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });


    $("#passport_serial").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 2));
    });

    $("#passport_number").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 7));
    });
    
    $("#passport_jshr").on("input keyup", function(e){
        $(this).val($(this).val().substring(0, 14));
    });

</script>

<? include('end.php'); ?>