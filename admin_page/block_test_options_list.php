<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

if (!empty($_POST["type"] == "add_option")) {
    $science_id = isset($_REQUEST['science_id']) ? $_REQUEST['science_id'] : null;
    if (!$science_id) {echo"error [science_id]";exit;}

    $db->insert("quiz_options", [
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
    ]);

    header("Location: block_test_options_list.php?science_id=$science_id&page=1");
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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Fan testlari ro'yxati</h4>
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
                                            <th>fan</th>
                                            <th>savol</th>
                                            <th>javob</th>
                                            <th>qo'shilgan sana</th>
                                            <th>tahrirlash</th>
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

                                        if (!empty($_GET["science_id"])) {
                                            $quiz_options = $db->in_array("SELECT * FROM quiz_options WHERE science_id = ? ORDER BY id DESC LIMIT $page_start, $page_count", [ $_GET["science_id"] ]);
                                        } else {
                                            $quiz_options = $db->in_array("SELECT * FROM quiz_options ORDER BY id DESC LIMIT $page_start, $page_count");
                                        }

                                        foreach ($quiz_options as $quiz_option){
                                            $quiz_science = $db->assoc("SELECT * FROM quiz_sciences WHERE id = ?", [ $quiz_option["science_id"] ]);

                                            echo '<tr>';
                                                echo '<th scope="row">'.$quiz_option["id"].'</th>';
                                                echo '<td>'.$quiz_science['name'].'</td>';
                                                echo '<th>'.$quiz_option["savol"].'</th>';
                                                echo '<th>'.$quiz_option["javob"].'</th>';
                                                echo '<td>'.$quiz_option['created_date'].'</td>';
                                                echo '<td>';
                                                    echo '<a href="edit_block_test_option.php?option_id='.$quiz_option['id'].'&page='.$page.'" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>';
                                                echo '</td>';
                                                echo '<td>';
                                                    echo '<a href="edit_block_test_option.php?type=delete_option&option_id='.$quiz_option['id'].'&page='.$page.'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
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
                                        if (!empty($_GET["science_id"])) {
                                            $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_options WHERE science_id = ?", [ $_GET["science_id"] ])["COUNT(*)"] / $page_count;
                                        } else {
                                            $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_options")["COUNT(*)"] / $page_count;
                                        }

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
                    
                    <!--  -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="savol-title">Test qo'shish</h4>
                            
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
                                    <input type="hidden" name="type" value="add_option" required>

                                    <div class="form-group">
                                        <label>Fan</label>
                                        <select name="science_id" class="form-control">
                                            <?
                                            $sciences = $db->in_array("SELECT * FROM quiz_sciences");
                                            foreach ($sciences as $science) {
                                                echo '<option value="'.$science["id"].'" '.($_GET["science_id"] == $science["id"] ? 'selected=""' : '').'>'.$science["name"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Savol</label>
                                        <textarea rows="5" editor="" name="test_savol" rows="10" class="form-control border-primary" placeholder="Savol"></textarea>
                                    </div>
    
                                    <div class="form-group">
                                        <label>To'g'ri variant</label>
                                        <select name="test_javob" class="form-control">
                                            <option value="A" selected="">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
    
                                    <!-- Variantlar -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>A-variant</label>
                                                <textarea rows="5" id="variant_a" name="variant_a" rows="10" class="form-control border-primary" placeholder="A-variant" editor=""></textarea>
                                            </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>B-variant</label>
                                                <textarea rows="5" id="variant_b" name="variant_b" rows="10" class="form-control border-primary" placeholder="B-variant" editor=""></textarea>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>C-variant</label>
                                                <textarea rows="5" id="variant_c" name="variant_c" rows="10" class="form-control border-primary" placeholder="C-variant" editor=""></textarea>
                                            </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>D-variant</label>
                                                <textarea rows="5" id="variant_d" name="variant_d" rows="10" class="form-control border-primary" placeholder="D-variant" editor=""></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Qo'shish
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