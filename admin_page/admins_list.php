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
            <!-- <div class="row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control" placeholder="Adminning ma'lumotini yozing:" id="search_input" search-in="search_admin">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="list-group" id="search_result" style="display:none"></ul>
                </div>
            </div> -->
            <!-- Search bar -->

            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Adminlar ro'yxati</h4>
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
                                            <th>ism / familiya</th>
                                            <th>lavozim</th>
                                            <th>ro'yxatdan o'tgan</th>
                                            <th>tahrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                          $page = (int)$_GET['page'];
                                          if (empty($page)) $page = 1;
                                        
                                          $page_count = 12;
                                          $page_end = $page * $page_count;
                                          $page_start = $page_end - $page_count;

                                          $users = $db->in_array("SELECT * FROM users WHERE admin = 1 ORDER BY id ASC LIMIT $page_start, $page_count");
                                          $num = 0;
                                          foreach ($users as $user){
                                              $num++;
                                              echo '<tr>';
                                                  echo '<th scope="row">'.$user['id'].'</th>';
                                                  echo '<td>'.$user['first_name'].' '.$user['last_name'].'</td>';
                                                  echo '<td><b>'.$user['role'].'</b></td>';
                                                  echo '<td>'.gmdate('Y.m.d h:i:s', $user['datereg']).'</td>';
                                                  echo '<td>';
                                                    echo '<a href="edit_admin.php?user_id='.$user['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>';
                                                  echo '</td>';
                                                  echo '<td>';
                                                    echo '<a href="edit_admin.php?type=delete_user&user_id='.$user['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
                                                  echo '</td>';
                                              echo '</tr>';
                                          }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="text-xs-center mb-3">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?
                                        // count
                                        $count = (int)$db->assoc("SELECT COUNT(*) FROM users WHERE admin = 1")["COUNT(*)"];
                                        if (gettype($count) == "double") $count = (int)($count + 1);

                                        // qurl
                                        $queries = array();
                                        parse_str($_SERVER['QUERY_STRING'], $queries);
                                        unset($queries["page"]);
                                        $qurl = http_build_query($queries);

                                        echo pagination($count, basename($_SERVER["SCRIPT_NAME"]), $qurl, 12); 
                                        ?>

                                        <style>
                                            button.page-to {
                                                cursor: pointer;
                                            }
                                            button.page-to:hover {
                                                background-color: #ddd;
                                            }
                                            input.page-to {
                                                width: 70px;
                                                padding-right: 0;
                                            }
                                            input.page-to:focus,
                                            button.page-to:focus {
                                                outline: none;
                                            }
                                            .page-to {
                                                position: relative;
                                                float: left;
                                                padding: 0.5rem 0.75rem;
                                                margin-left: -1px;
                                                color: #7A54D8;
                                                text-decoration: none;
                                                background-color: #fff;
                                                border: 1px solid #ddd;
                                                line-height: 1.8;
                                            }
                                        </style>
                                    </ul>
                                </nav>
                            </div>
                            <!-- End Pagination -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<? include "scripts.php"; ?>

<? include('end.php'); ?>
