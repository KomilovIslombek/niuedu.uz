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

$quiz_user_block_id = isset($_REQUEST['quiz_user_block_id']) ? $_REQUEST['quiz_user_block_id'] : null;
if (!$quiz_user_block_id) {echo"error";return;}

$quiz_user_block = $db->assoc("SELECT * FROM quiz_user_block WHERE id = ?", [$_REQUEST['quiz_user_block_id']]);
if (!$quiz_user_block["id"]) {echo"error (test natijasi topilmadi!)";exit;}

if ($_REQUEST['type'] == "edit_quiz_user_block"){
    $ball = isset($_REQUEST['ball']) ? $_REQUEST['ball'] : null;
    $answers_count = isset($_REQUEST['answers_count']) ? $_REQUEST['answers_count'] : null;
    $correct_answers_count = isset($_REQUEST['correct_answers_count']) ? $_REQUEST['correct_answers_count'] : null;
    $start_date = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : null;
    $stop_date = isset($_REQUEST['stop_date']) ? $_REQUEST['stop_date'] : null;

    $db->update("quiz_user_block", [
        "ball" => $ball,
        "answers_count" => $answers_count,
        "correct_answers_count" => $correct_answers_count,
        "start_date" => $start_date,
        "stop_date" => $stop_date
    ], [
        "id" => $quiz_user_block["id"]
    ]);

    $db->update("requests", [ "ball" => $ball ], ["code" => $quiz_user_block["student_code"]]);

    header("Location: block_tests_results_list.php?status=".$quiz_user_block["status"]."&page=".($_GET["page"] ? $_GET["page"] : 1));
}

if ($_REQUEST['type'] == "delete_quiz_user_block"){
    $db->delete("quiz_user_block", $quiz_user_block["id"]);
    $db->query("DELETE FROM quiz_user_options WHERE student_code = ".(int)$quiz_user_block["student_code"]." AND block_id = " . (int)$quiz_user_block["block_id"]);
    // $db->delete("quiz_user_options", $quiz_user_block["id"], "block_id");
    // delete_image($quiz_user_block["image_id"]);

    $db->update("requests", [ "ball" => NULL ], ["code" => $quiz_user_block["student_code"]]);

    header("Location: block_tests_results_list.php?status=".$quiz_user_block["status"]."&page=".($_GET["page"] ? $_GET["page"] : 1));
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Talaba test natijasini tahrirlash</h4>
                            
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
                                <form action="edit_quiz_user_block.php" method="quiz_user_block" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_quiz_user_block" required>
                                    <input type="hidden" name="quiz_user_block_id" value="<?=$_REQUEST['quiz_user_block_id']?>" required>
                                    <input type="hidden" name="page" value="<?=$_REQUEST["page"]?>">

                                    <div class="form-group">
                                        <label>ball</label>
                                        <input type="text" name="ball" class="form-control border-primary" value="<?=$quiz_user_block["ball"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label>testlar soni</label>
                                        <input type="text" name="answers_count" class="form-control border-primary" value="<?=$quiz_user_block["answers_count"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label>to'g'ri javoblar soni</label>
                                        <input type="text" name="correct_answers_count" class="form-control border-primary" value="<?=$quiz_user_block["correct_answers_count"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label>boshlangan vaqt</label>
                                        <input type="datetime-local" name="start_date" class="form-control border-primary" value="<?=$quiz_user_block["start_date"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label>tugallangan vaqt</label>
                                        <input type="datetime-local" name="stop_date" class="form-control border-primary" value="<?=$quiz_user_block["stop_date"]?>">
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

<? include('end.php'); ?>