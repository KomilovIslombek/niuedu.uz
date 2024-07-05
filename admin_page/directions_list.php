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
            <!-- Search bar -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <div class="input-group">
                            <input name="search" type="text" class="form-control"
                                placeholder="Qidirish:" id="search_input_jquery">
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
                            <h4 class="card-title">Yo'nalishlar ro'yxati</h4>
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
                                            <th>kod</th>
                                            <th>rasm</th>
                                            <th>yo'nalish nomi</th>
                                            <th>qo'shilgan sana</th>
                                            <th>arizalar</th>
                                            <th>taxrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                          $page = (int)$_GET['page'];
                                          if (empty($page)){$page = 1;}
                                        
                                          $page_count = 12;
                                          $page_end = $page * $page_count;
                                          $page_start = $page_end - $page_count;
                                        
                                          $directions = $db->in_array("SELECT * FROM directions ORDER BY id ASC LIMIT $page_start, $page_count");

                                            foreach ($directions as $direction){
                                                $direction_image = image($direction["image_id"]);

                                                $requests_count = $db->assoc("SELECT COUNT(id) FROM requests WHERE direction_id = ?", [ $direction["id"] ])["COUNT(id)"];

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$direction["id"].'</th>';
                                                    echo '<td>'.$direction["code"].'</td>';
                                                    echo '<td><img src="../'.$direction_image["file_folder"].'" width="125px"></td>';
                                                    echo '<td><b class="'.($direction["active"] == 1 ? "text-success" : "text-danger").'">'.lng($direction["name"], "uz").'</b></td>';
                                                    echo '<td>'.$direction["created_date"].'</td>';

                                                    echo '<td>';
                                                        echo '<a href="requests_list.php?direction_id='.$direction['id'].'&page=1" class="tag tag-default tag-warning text-white bg-success">arizalar ('.$requests_count.')</a>';
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                        echo '<a href="edit_direction.php?direction_id='.$direction['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_direction.php?type=delete_direction&direction_id='.$direction['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
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
                                        $count = (int)$db->assoc("SELECT COUNT(*) FROM directions")["COUNT(*)"] / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="directions_list.php?page='.($page-1).'">
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
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a href="directions_list.php?page='.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="directions_list.php?page='.($page+1).'">
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