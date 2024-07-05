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

<!--  -->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Axborot resurs markazi</h4>
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
                                            <th>Markaz haqida</th>
                                            <th>Foydalanish qoidalari</th>
                                            <th>Ish vaqti</th>
                                            <th>Site linki</th>
                                            <!-- <th>Qo'shilgan sana</th> -->
                                            <th>Tahrirlash</th>
                                            <th>O'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                          $page = (int)$_GET['page'];
                                          if (empty($page)){$page = 1;}
                                        
                                          $page_count = 12;
                                          $page_end = $page * $page_count;
                                          $page_start = $page_end - $page_count;
                                        
                                          $information_center = $db->in_array("SELECT * FROM information_center ORDER BY id DESC LIMIT $page_start, $page_count");

                                          foreach ($information_center as $information_center){

                                              echo '<tr>';
                                                  echo '<th scope="row">'.$information_center["id"].'</th>';
                                                  echo '<td>'.substr(lng($information_center['bio'], "uz"), 0, 500).'</td>';
                                                  echo '<td>'.substr(lng($information_center['terms_use'], "uz"), 0, 500).'</td>';
                                                  echo '<td>'.substr(lng($information_center['work_time'], "uz"), 0, 500).'</td>';
                                                  echo '<td>'.$information_center['site'].'</td>';
                                                //   echo '<td>'.$information_center['created_date'].'</td>';
                                                  echo '<td>';
                                                    echo '<a href="edit_information_center.php?id='.$information_center['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-success">tahrirlash</a>';
                                                  echo '</td>';
                                                  echo '<td>';
                                                    echo '<a href="edit_information_center.php?type=delete_information_center&id='.$information_center['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
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
                                        $count = (int)$db->assoc("SELECT COUNT(*) FROM information_center")["COUNT(*)"] / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="information_center.php?page='.($page-1).'">
                                                        <span aria-hidden="true">«</span>
                                                    </a>
                                                </li>';
                                        } else {
                                          echo '<li style="cursor:no-drop" class="page-item">
                                                    <a style="cursor:no-drop" class="page-link">
                                                        <span aria-hidden="true">«</span>
                                                    </a>
                                                </li>';
                                        }
                                        
                                        $max = 4;
                                        for ($i = 0; $i <= $count; $i++) {
                                            if ($i == 1 || $i == $count || $i >= $page && $i <= $page + ($max - 1)) {
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a href="information_center.php?page='.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="information_center.php?page='.($page+1).'">
                                                        <span aria-hidden="true">»</span>
                                                    </a>
                                                </li>';
                                        } else {
                                            echo '<li style="cursor:no-drop" class="page-item">
                                                      <a style="cursor:no-drop" class="page-link">
                                                        <span aria-hidden="true">»</span>
                                                      </a>
                                                  </li>';
                                        }
                                        ?>
                                        <form action="" method="GET" style="display:inline-block;margin-left:50px">
                                            <input type="number" name="page" min="1" max="<?=$count?>" style="width:auto" class="page-to" placeholder="<?=$_GET['page']?>">
                                            <button type="submit" class="page-to">&raquo;</button>
                                        </form>

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