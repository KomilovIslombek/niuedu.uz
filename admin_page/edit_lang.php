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

$lang_id = isset($_REQUEST['lang_id']) ? $_REQUEST['lang_id'] : null;
if (!$lang_id) {echo"error";return;}

$lang = $db->assoc("SELECT * FROM langs_list WHERE id = ?", [$_REQUEST['lang_id']]);
if (!$lang["id"]) {echo"error (lang not found)";exit;}

if ($_REQUEST['type'] == "edit_lang"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}

    $flag_icon = isset($_REQUEST['flag_icon']) ? $_REQUEST['flag_icon'] : null;
    if (!$flag_icon) {echo"error [flag_icon]";exit;}

    $db->update("langs_list", [
        "name" => $name,
        "flag_icon" => $flag_icon
    ], [
        "id" => $lang["id"]
    ]);

    $db->query("ALTER TABLE `words` CHANGE ".$lang["flag_icon"]." $flag_icon TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL");

    header("Location: langs_list.php?page=$page");
}

if ($_REQUEST["type"] == "delete_lang"){
    $db->delete("langs_list", $lang["id"]);
    $db->query("ALTER TABLE words DROP " . $lang["flag_icon"]);

    header("Location: langs_list.php?page=$page");
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Tilni tahrirlash</h4>
                            
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
                                <form action="edit_lang.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_lang" required>
                                    <input type="hidden" name="page" value="<?=$page?>" required>
                                    <input type="hidden" name="lang_id" value="<?=$_REQUEST['lang_id']?>" required>

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
    $("#flag-icon").change(function(){
        $("#lang_icon").attr("class", "flag-icon flag-icon-"+$(this).find(":selected").val());
    });

    $("*[select-form-lang]").click(function(){
        var lang = $(this).attr('select-form-lang');
        console.log(lang);
        $("*[form-lang]").hide();
        $("*[form-lang='"+lang+"']").show();
    });
</script>

<? include('end.php'); ?>