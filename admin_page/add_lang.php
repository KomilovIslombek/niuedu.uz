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

// echo "<pre>";
// print_r($_POST);
// exit;

if ($_REQUEST['type'] == "add_lang"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error: name";exit;}

    $flag_icon = isset($_REQUEST['flag_icon']) ? $_REQUEST['flag_icon'] : null;
    if (!$flag_icon) {echo"error: flag_icon";exit;}

    foreach ($db2->query("SHOW columns FROM words") as $column) {
        $prev_name = $column['Field'];
        if ($prev_name == $flag_icon) exit("Ushbu til bazada mavjud !!!");
    } 
    
    $db2->query("ALTER TABLE words ADD $flag_icon TEXT DEFAULT NULL AFTER $prev_name");

    $db->insert("langs_list", [
        "creator_user_id" => $user_id,
        "name" => $name,
        "flag_icon" => $flag_icon
    ]);

    header("Location: langs_list.php?page=1");
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
                            <h4 class="card-title" id="basic-layout-colored-form-control"><?=$_REQUEST['type'] == "edit_lang" ? "Tilni tahrirlash" : "Yangi til qo'shish" ?></h4>
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
                                <form action="add_lang.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_lang">

                                    <? $langs_icons = ["ad","ae","af","ag","ai","al","am","ao","aq","ar","as","at","au","aw","ax","az","ba","bb","bd","be","bf","bg","bh","bi","bj","bl","bm","bn","bo","bq","br","bs","bt","bv","bw","by","bz","ca","cc","cd","cf","cg","ch","ci","ck","cl","cm","cn","co","cr","cu","cv","cw","cx","cy","cz","de","dj","dk","dm","do","dz","ec","ee","eg","eh","er","es","es-ca","es-ga","et","eu","fi","fj","fk","fm","fo","fr","ga","gb","gb-eng","gb-nir","gb-sct","gb-wls","gd","ge","gf","gg","gh","gi","gl","gm","gn","gp","gq","gr","gs","gt","gu","gw","gy","hk","hm","hn","hr","ht","hu","id","ie","il","im","in","io","iq","ir","is","it","je","jm","jo","jp","ke","kg","kh","ki","km","kn","kp","kr","kw","ky","kz","la","lb","lc","li","lk","lr","ls","lt","lu","lv","ly","ma","mc","md","me","mf","mg","mh","mk","ml","mm","mn","mo","mp","mq","mr","ms","mt","mu","mv","mw","mx","my","mz","na","nc","ne","nf","ng","ni","nl","no","np","nr","nu","nz","om","pa","pe","pf","pg","ph","pk","pl","pm","pn","pr","ps","pt","pw","py","qa","re","ro","rs","ru","rw","sa","sb","sc","sd","se","sg","sh","si","sj","sk","sl","sm","sn","so","sr","ss","st","sv","sx","sy","sz","tc","td","tf","tg","th","tj","tk","tl","tm","tn","to","tr","tt","tv","tw","tz","ua","ug","um","un","us","uy","uz","va","vc","ve","vg","vi","vn","vu","wf","ws","xk","ye","yt","za","zm","zw"]; ?>

                                    <div class="form-group">
                                        <label for="userinput5">til nomi</label>
                                        <input type="text" name="name" class="form-control border-primary" placeholder="til nomi" id="userinput5" value="<?=$lang['name']?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Tilni tanlang <span class="flag-icon flag-icon-<?=$lang['flag_icon']?>" id="lang_icon"></span></label>

                                        <select name="flag_icon" class="form-control" id="flag-icon" required>
                                            <?
                                            foreach ($langs_icons as $lang_name) {
                                                echo '<option value="'.$lang_name.'" '.($lang['flag_icon'] == $lang_name ? "selected" : "").'>'.$lang_name.'</option>';
                                            }
                                            ?>
                                        </select>
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