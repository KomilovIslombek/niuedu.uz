<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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

if (isset($_REQUEST["lang_id"])) {
    $lang_id = isset($_REQUEST['lang_id']) ? $_REQUEST['lang_id'] : null;
    if (!$lang_id) {echo"error: lang_id";exit;}

    $lang = $db->assoc("SELECT * FROM langs_list WHERE id = ?", [ $lang_id ]);

    $flag_icon = isset($lang['flag_icon']) ? $lang['flag_icon'] : null;
    if ($flag_icon == "uz") exit("Ushbu tilning tarjimasi yo'q !!!");
    
    if ($_POST['type'] == "add_word"){
        $lang_word_1 = isset($_POST['lang_word_1']) ? $_POST['lang_word_1'] : null;
        if (!$lang_word_1) {echo"error: lang_word_1";exit;}
    
        $lang_word_2 = isset($_POST['lang_word_2']) ? $_POST['lang_word_2'] : null;
        if (!$lang_word_2) {echo"error: lang_word_2";exit;}

        if ($db->assoc("SELECT COUNT(*) FROM words WHERE uz = ?", [ $lang_word_1 ])["COUNT(*)"] != 0) {
            exit("Bunday so'z bazada mavjud !!!");
        }
        
        if (!$flag_icon) {echo"error: flag_icon";exit;}
        
        $db->insert("words", [
            "uz" => $lang_word_1,
            "$flag_icon" => $lang_word_2
        ]);

        header("Location: words.php?lang_id=".$lang_id);
    }
    
    if ($_POST["type"] == "delete_word") {
        if (!is_array($lang) || count($lang) == 0) exit("Bunday so'z bazada mavjud emas");

        $word_id = isset($_POST["word_id"]) ? $_POST["word_id"] : null;
        if (!$word_id) {echo"error: word_id";exit;}
        
        $db->delete("words", $word_id);
        header("Location: words.php?lang_id=".$lang_id);
    }

    if ($_POST["type"] == "update_words") {
        foreach ($_POST["words"] as $word_id => $words) {
            foreach ($words as $key2 => $value2) {
                if ($key2 == "uz" && $db->assoc('SELECT COUNT(*) FROM words WHERE uz = "'.$value2.'" ')["COUNT(*)"] == 0) {
                    exit("Kechirasiz <b>O'zbek</b> tilidagi barcha so'zlarni tahrirlash mumkin emas !!!<br>sababi o'zbek tilidagi so'zlar saytga indenfikator sifatida berilgan.");  
                } 
            }
            foreach ($words as $key2 => $value2) {
                $db->update("words", [
                    "$key2" => $value2
                ], [
                    "id" => $word_id
                ]);
            }
        }
        header("Location: words.php?lang_id=".$lang_id);
    }
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">
                <form action="words.php" method="POST" class="form col-md-12" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="update_words">
                    <input type="hidden" name="lang_id" value="<?=$lang['id']?>">
                    <div class="card">
                        <div class="card-body collapse in">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>O'zbekcha <span class="flag-icon flag-icon-uz" id="lang_icon"></span></th>
                                            <th><?=$lang['name']?> <span class="flag-icon flag-icon-<?=$lang['flag_icon']?>" id="lang_icon"></th>
                                            <th width="50px">o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <? foreach($db->in_array("SELECT * FROM words") as $word) { ?>
                                        <tr>
                                            <td>
                                              <input type="text" name="words[<?=$word['id']?>][uz]" class="form-control border-primary" placeholder="so'z" id="userinput5" value="<?=$word['uz']?>" readonly="" style="background-color:#fff;">
                                            </td>
                                            <td>
                                              <input type="text" name="words[<?=$word['id']?>][<?=$lang['flag_icon']?>]" class="form-control border-primary" placeholder="so'z" id="userinput5" value="<?=$word[$lang['flag_icon']]?>">
                                            </td>
                                            <td>
                                              <div class="text-center"><a href="words.php?type=delete_word&word_id=<?=$word['id']?>&lang_id=<?=$_GET['lang_id']?>" class="tag tag-default tag-danger text-white bg-danger"><i class="icon-cross2"></i></a></div>
                                            </td>
                                        </tr>
                                      <? } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-block">
                                <div class="form-actions" style="text-align:right">
                                    <button type="submit" name="submit_update" class="btn btn-primary">
                                        <i class="icon-check2"></i> Barchas so'zlarni yangilash
                                    </button>
                                </div>  
                            </div>
                        </div>
                    </div>
                  </form>

                <!--  -->

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control" style="text-transform:none">Yangi so'z qo'shish</h4>
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
                                <form action="words.php" method="POST" class="form col-md-12" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_word">
                                    <input type="hidden" name="lang_id" value="<?=$lang['id']?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="userinput5">O'zbekcha <span class="flag-icon flag-icon-uz" id="lang_icon"></span></label>
                                                <input type="text" name="lang_word_1" class="form-control border-primary" placeholder="so'z" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="userinput5"><?=$lang['name']?> <span class="flag-icon flag-icon-<?=$lang['flag_icon']?>" id="lang_icon"></label>
                                                <input type="text" name="lang_word_2" class="form-control border-primary" placeholder="so'z" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions right">
                                        <button type="button" class="btn btn-warning mr-1">
                                            <i class="icon-cross2"></i>bekor qilish
                                        </button>
                                        <button type="submit" name="submit_add" class="btn btn-primary">
                                            <i class="icon-check2"></i> Qo'shish
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  -->


            </div>
        </div>
    </div>
</div>

<? include "scripts.php"; ?>

<? include('end.php'); ?>