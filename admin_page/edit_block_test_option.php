<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

$option_id = isset($_REQUEST['option_id']) ? $_REQUEST['option_id'] : null;
if (!$option_id) {echo"error [option_id]";exit;}

$option = $db->assoc("SELECT * FROM quiz_options WHERE id = ?", [$_REQUEST['option_id']]);
if (!$option["id"]) {echo"error (option not found)";exit;}

$variantlar = json_decode($option["variant"], true);

if (!empty($_POST["type"] == "edit_option")) {
    $science_id = isset($_REQUEST['science_id']) ? $_REQUEST['science_id'] : null;
    if (!$science_id) {echo"error [science_id]";exit;}

    $db->update("quiz_options", [
        "creator_user_id" => $user_id,
        "science_id" => $science_id,
        "savol" => $_POST["test_savol"],
        "javob" => $_POST["test_javob"],
        "variant" => json_encode([
            "A" => $_POST["variant_a"],
            "B" => $_POST["variant_b"],
            "C" => $_POST["variant_c"],
            "D" => $_POST["variant_d"],
        ], JSON_UNESCAPED_UNICODE)
    ], [
        "id" => $option["id"]
    ]);

    header("Location: block_test_options_list.php?science_id=$science_id&page=" . ($_POST["page"] ? $_POST["page"] : 1));
    exit;
}


if ($_REQUEST["type"] == "delete_option"){
    $db->delete("quiz_options", $option["id"]);
    header("Location: block_test_options_list.php?science_id=$science_id&page=" . ($_POST["page"] ? $_POST["page"] : 1));
    exit;
}

include("filter.php");

include("head.php");
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <!-- <div class="row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Block testni qidirish</label>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control" placeholder="nomi yoki id raqamini yozing." id="search_input" search-in="search_block_test">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="list-group" id="search_result" style="display:none"></ul>
                </div>
            </div> -->
            <div class="row">
                <div class="col-xs-12">
                    <!--  -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="savol-title">Testni tahrirlash</h4>
                            
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
                                    <input type="hidden" name="type" value="edit_option" required>
                                    <input type="hidden" name="option_id" value="<?=$option["id"]?>" required>
                                    <input type="hidden" name="page" value="<?=$_GET["page"]?>">

                                    <div class="form-group">
                                        <label>Fan</label>
                                        <select name="science_id" class="form-control">
                                            <?
                                            $sciences = $db->in_array("SELECT * FROM quiz_sciences");
                                            foreach ($sciences as $science) {
                                                echo '<option value="'.$science["id"].'" '.($option["science_id"] == $science["id"] ? 'selected=""' : '').'>'.$science["name"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Savol</label>
                                        <textarea rows="5" editor="" name="test_savol" rows="10" class="form-control border-primary" placeholder="Savol"><?=$option["savol"]?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>To'g'ri variant</label>
                                        <select name="test_javob" class="form-control">
                                            <option value="A" <?=($option["javob"] == "A" ? 'selected=""' : '')?>>A</option>
                                            <option value="B" <?=($option["javob"] == "B" ? 'selected=""' : '')?>>B</option>
                                            <option value="C" <?=($option["javob"] == "C" ? 'selected=""' : '')?>>C</option>
                                            <option value="D" <?=($option["javob"] == "D" ? 'selected=""' : '')?>>D</option>
                                        </select>
                                    </div>
    
                                    <!-- Variantlar -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>A-variant</label>
                                                <textarea rows="5" id="variant_a" name="variant_a" rows="10" class="form-control border-primary" placeholder="A-variant" editor=""><?=$variantlar["A"]?></textarea>
                                            </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>B-variant</label>
                                                <textarea rows="5" id="variant_b" name="variant_b" rows="10" class="form-control border-primary" placeholder="B-variant" editor=""><?=$variantlar["B"]?></textarea>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>C-variant</label>
                                                <textarea rows="5" id="variant_c" name="variant_c" rows="10" class="form-control border-primary" placeholder="C-variant" editor=""><?=$variantlar["C"]?></textarea>
                                            </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>D-variant</label>
                                                <textarea rows="5" id="variant_d" name="variant_d" rows="10" class="form-control border-primary" placeholder="D-variant" editor=""><?=$variantlar["D"]?></textarea>
                                            </div>
                                        </div>
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
                    <!--  -->
                </div>
            </div>

        </div>
    </div>
</div>

<? include "scripts.php"; ?>

<? include("end.php"); ?>