<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

include('filter.php');

$science = $db->assoc("SELECT * FROM quiz_sciences WHERE id = ?", [$_REQUEST["science_id"]]);
if (!$science["id"]) exit(http_response_code(404));

// echo "<pre>";
// print_r($science);
// echo "</pre>";


if ($_REQUEST['type'] == "edit_science"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}

    $db->update("quiz_sciences", [
        "name" => $name
    ], [
        "id" => $science["id"]
    ]);

    header('Location: block_test_sciences_list.php?page' . $_REQUEST["page"]);
}

if ($_REQUEST["type"] == "delete_science") {
    $db->delete("quiz_sciences", $science["id"]);
    $db->delete("quiz_block_sciences", $science["id"], "science_id");
    $db->delete("quiz_options", $science["id"], "science_id");

    header('Location: block_test_sciences_list.php?page' . $_REQUEST["page"]);
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Blok test fanni tahrirlash</h4>
                            
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
                                    <input type="hidden" name="type" value="edit_science" required="">
                                    <input type="hidden" name="science_id" value="<?=$science["id"]?>" required="">

                                    <div class="form-group">
                                        <label>fan nomi</label>
                                        <textarea name="name" rows="5" class="form-control" placeholder="fan nomi" required><?=$science["name"]?></textarea>
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

<style>
    .js .input--file {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .no-js .input--file + label {
        display: none;
    }
    .js .input--file + label {
        display: inline-block;
        cursor: pointer;
        background: orange;
        color: #fff;
        padding: 10px;
    }

    .js .input--file:focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }
</style>

<? include "scripts.php"; ?>

<? include("end.php"); ?>