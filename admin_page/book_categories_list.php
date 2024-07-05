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
                            <h4 class="card-title">Kitob turlari ro'yxati</h4>
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
                                            <th>nomi</th>
                                            <th>kitoblar</th>
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
                                        
                                          $book_categories = $db->in_array("SELECT * FROM book_categories ORDER BY id ASC LIMIT $page_start, $page_count");

                                            foreach ($book_categories as $book_category){
                                                $book_category_image = image($book_category["image_id"]);

                                                $books_count = $db->assoc("SELECT COUNT(id) FROM books WHERE book_category_id = ?", [ $book_category["id"] ])["COUNT(id)"];

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$book_category["id"].'</th>';
                                                    echo '<td><b>'.lng($book_category["name"], "uz").'</b></td>';

                                                    echo '<td>';
                                                        echo '<a href="books_list.php?book_category_id='.$book_category['id'].'&page=1" class="tag tag-default tag-warning text-white bg-success">kitoblar ('.$books_count.')</a>';
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                        echo '<a href="edit_book_category.php?book_category_id='.$book_category['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-success">tahrirlash</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_book_category.php?type=delete_book_category&book_category_id='.$book_category['id'].'&page='.$_REQUEST["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
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
                                        $count = (int)$db->assoc("SELECT COUNT(*) FROM book_categories")["COUNT(*)"] / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="book_categories_list.php?page='.($page-1).'">
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
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a href="book_categories_list.php?page='.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="book_categories_list.php?page='.($page+1).'">
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