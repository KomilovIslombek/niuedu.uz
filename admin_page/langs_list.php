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

include('head.php')
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tillar ro'yxati</h4>
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
                                            <th>#</th>
                                            <th>nomi</th>
                                            <th>rasmi</th>
                                            <th>qo'shilgan sana</th>
                                            <th>barcha so'zlar</th>
                                            <th>tahrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                          $num = 0;
                                            foreach ($db->in_array("SELECT * FROM langs_list") as $lang){
                                                $num++;

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$num.'</th>';
                                                    echo '<td>'.$lang['name'].'</td>';
                                                    echo '<td><span class="flag-icon flag-icon-'.$lang['flag_icon'].'"></span></td>';
                                                    echo '<td>'.$lang['created_date'].'</td>';
                                                    echo '<td>';
                                                        echo '<a href="words.php?&lang_id='.$lang['id'].'" class="tag tag-default tag-warning text-white bg-warning">ko\'rish</a>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                        echo '<a href="edit_lang.php?lang_id='.$lang['id'].'" class="tag tag-default tag-warning text-white bg-success">tahrirlash</a>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                        echo '<a href="edit_lang.php?type=delete_lang&lang_id='.$lang['id'].'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
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