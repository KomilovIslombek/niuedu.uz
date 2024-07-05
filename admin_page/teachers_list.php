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

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <!-- Search bar -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control"
                                placeholder="O'qituvchining ma'lumotini yozing:" id="search_input_jquery">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="list-group" id="search_result" style="display:none"></ul>
                </div>
            </div>
            <!-- Search bar -->

            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">O'qituvchilar ro'yxati</h4>
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
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#id</th>
                                            <th>rasm</th>
                                            <th>ism / familiya</th>
                                            <th>fan</th>
                                            <th>qo'shilgan sana</th>
                                            <!-- <th>natija</th> -->
                                            <!-- <th>natijalar</th> -->
                                            <th>tahrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $teachers = $db->in_array("SELECT * FROM teachers ORDER BY id ASC");

                                        foreach ($teachers as $teacher){
                                            if ($teacher["small_image_id"]) $profileimg = image($teacher["small_image_id"]);

                                            echo '<tr>';
                                                echo '<th scope="row">'.$teacher['id'].'</th>';
                                                echo '<td>';
                                                    echo '<a href="../'.$profileimg["file_folder"].'">';
                                                        echo '<img src="../'.$profileimg["file_folder"].'" alt="avatar" width="80px">';
                                                    echo '</a>';
                                                echo '</td>';
                                                echo '<td>'.$teacher['first_name'].' '.$teacher['last_name'].'</td>';
                                                echo '<td>'.$teacher['fan'].'</td>';
                                                // echo '<td>'.$teacher['phone'].'</td>';
                                                echo '<td>'.date('Y.m.d h:i:s', strtotime($teacher['created_date'])).'</td>';

                                                echo '<td>';
                                                    echo '<a href="edit_teacher.php?user_id='.$teacher['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>';
                                                echo '</td>';

                                                echo '<td>';
                                                    echo '<a href="edit_teacher.php?type=delete_user&user_id='.$teacher['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
                                                echo '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
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