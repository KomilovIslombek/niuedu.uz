<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}


include('filter.php');

include('head.php');
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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Blok testlar ro'yxati</h4>
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
                                            <th>rasmi</th>
                                            <th>nomi</th>
                                            <th>fanlari</th>
                                            <th>test davomiyligi</th>
                                            <th>tavsifi</th>
                                            <!-- <th>Bepul yoki pullik</th> -->
                                            <th>holati</th>
                                            <th>qo'shilgan sana</th>
                                            <th>taxirlash</th>
                                            <th>o'chirish</th>
                                            <!-- <th>Vaqtinchalik natijalar</th> -->
                                            <!-- <th>natijalarni tozalash</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $page = (int)$_GET['page'];
                                        if (empty($page)){$page = 1;}
                                    
                                        $page_count = 12;
                                        $page_end = $page * $page_count;
                                        $page_start = $page_end - $page_count;
                                    
                                        $quiz_blocks = $db->in_array("SELECT * FROM quiz_block ORDER BY id DESC LIMIT $page_start, $page_count");

                                            foreach ($quiz_blocks as $quiz_block){
                                                $image = $db->assoc("SELECT * FROM images WHERE id = ?", [ $quiz_block["image_id"] ]);

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$quiz_block["id"].'</th>';
                                                    echo '<td>
                                                            <img src="../'.$image["file_folder"].'" width="80px" title="../'.$image["file_folder"].'">
                                                        </td>';
                                                    echo '<td>'.$quiz_block['name'].'</td>';

                                                    echo '<td width="200px">';
                                                        $sciences = $db->in_array("SELECT * FROM quiz_block_sciences WHERE block_id = ?", [ $quiz_block["id"] ]);
                                                        $fan_num = 0;
                                                        foreach ($sciences as $science_arr) {
                                                            $fan_num++;
                                                            $science = $db->assoc("SELECT * FROM quiz_sciences WHERE id = ?", [ $science_arr["science_id"] ]);
                                                            echo "$fan_num) ".$science["name"] . "<br>";
                                                        }
                                                    echo '</td>';

                                                    echo '<th>'.$quiz_block['max_timeout'].' daqiqa</th>';

                                                    echo '<td>'.$quiz_block['description'].'</td>';
                                                    // echo '<td>'.($quiz_block['free'] == 1 ? "Bepul" : "pullik").'</td>';
                                                    echo '<td>'.($quiz_block['status'] == 1 ? "faol" : "faol emas").'</td>';
                                                    echo '<td>'.$quiz_block['created_date'].'</td>';
                                                    echo '<td>';
                                                        echo '<a href="edit_block_test.php?block_id='.$quiz_block['id'].'&page='.$page.'" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                        echo '<a href="edit_block_test.php?type=delete_block&block_id='.$quiz_block['id'].'&page='.$page.'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
                                                    echo '</td>';

                                                    // echo '<td>';
                                                    //     echo '<a href="../education/vaqtinchalik_natijalar.php?block_id='.$quiz_block['id'].'" target="_blank" class="tag tag-default tag-info text-white bg-info">yuklab olish</a>';
                                                    // echo '</td>';

                                                    // echo '<td>';
                                                    //     echo '<a href="edit_block_test.php?block_id='.$quiz_block['id'].'&page='.$page.'&type=clear" class="tag tag-danger tag-warning text-white bg-danger">tozalash</a>';
                                                    // echo '</td>';
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
                                        $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_block")["COUNT(*)"] / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="'.$url2[1].'?page='.($page-1).'">
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
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a href="'.$url2[1].'?page='.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="'.$url2[1].'?page='.($page+1).'">
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

<? include("end.php"); ?>