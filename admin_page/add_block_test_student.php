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

if ($_REQUEST['type'] == "add_block_test_student"){
    $student_code = isset($_REQUEST['student_code']) ? $_REQUEST['student_code'] : null;
    if (!$student_code) {echo"talabani tanlashni unutdingiz!";exit;}
    
    $student = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $student_code ]);

    $ball = isset($_REQUEST['ball']) ? $_REQUEST['ball'] : null;

    if (!empty($student["code"])) {
        $course_id = $db->insert("quiz_user_block", [
            "creator_user_id" => $user_id,
            "ball" => $ball,
            "student_code" => $student["code"],
            "block_id" => $_POST["quiz_block_id"],
            "status" => 0
        ]);
    } else {
        echo"talaba bazadan topilmadi!";exit;
    }

    header("Location: block_tests_results_list.php?status=0");
    exit;
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
                            <h4 class="card-title" id="basic-layout-colored-form-control">Test tashkil qilish</h4>
                            
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
                                <form action="add_block_test_student.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_block_test_student" required>

                                    <div class="form-group">
                                        <label>Talaba ID</span></label>
                                        <input type="text" name="student_code" class="form-control border-primary" placeholder="Talaba ID" value="<?=$_REQUEST["code"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Blok testni tanlang</label>
                                        <select name="quiz_block_id" class="form-control" id="quiz_block">
                                            <?
                                            $quiz_blocks = $db->in_array("SELECT * FROM quiz_block WHERE status = 1");
                                            foreach ($quiz_blocks as $quiz_block) {
                                                echo '<option value="'.$quiz_block["id"].'" '.($_REQUEST["quiz_block_id"] == $quiz_block["id"] ? 'selected=""' : '').'>'.$quiz_block["name"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>ball</span></label>
                                        <input type="text" name="ball" class="form-control border-primary" placeholder="ball" value="<?=$_REQUEST["ball"]?>">
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