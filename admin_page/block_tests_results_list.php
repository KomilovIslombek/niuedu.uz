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

$page = (int)$_GET['page'];
if (empty($page)){
    $page = 1;
    $_GET["page"] = 1;
}

$page_count = 20;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

$q = "%".$_GET["q"]."%";
if (!empty($_GET["q"])) {
    $tests = $db->in_array("SELECT * FROM quiz_user_block WHERE status = ? AND student_code LIKE ? ORDER BY ball ASC LIMIT $page_start, $page_count", [
        (int)$_GET["status"],
        $q
    ]);

    $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_user_block WHERE status = ? AND student_code LIKE ?", [
        (int)$_GET["status"],
        $q
    ])["COUNT(*)"];
} else if (!empty($_GET["block_id"])) {
    $tests = $db->in_array("SELECT * FROM quiz_user_block WHERE status = ? AND block_id = ? ORDER BY ball ASC LIMIT $page_start, $page_count", [
        (int)$_GET["status"],
        $_GET["block_id"]
    ]);

    $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_user_block WHERE status = ? AND block_id = ?", [
        (int)$_GET["status"],
        $_GET["block_id"]
    ])["COUNT(*)"];
} else {
    $tests = $db->in_array("SELECT * FROM quiz_user_block WHERE status = ? ORDER BY ball ASC LIMIT $page_start, $page_count", [
        (int)$_GET["status"]
    ]);

    $count = (int)$db->assoc("SELECT COUNT(*) FROM quiz_user_block WHERE status = ?", [
        (int)$_GET["status"]
    ])["COUNT(*)"];
}

include('head.php');
?>

<!--  -->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header" id="header">
                            <h4 class="card-title">Test natijalari ro'yxati (<?=$count?> ta)</h4>
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
                            <div class="container-fluid" style="padding-left:25px;">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Blok testlar</label>
                                        <select name="block_id" class="form-control" id="block_id">
                                            <option value="" <?=(!$_GET["block_id"] || "" == $_GET["block_id"] ? 'selected=""' : '')?>>Barcha blok testlar</option>

                                            <? foreach ($db->in_array("SELECT * FROM quiz_block") as $quiz_block) { ?>
                                                <option value="<?=$quiz_block["id"]?>" <?=($_GET["block_id"] && $quiz_block["id"] == $_GET["block_id"] ? 'selected=""' : '')?>><?=$quiz_block["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Search -->
                                <form action="requests_list.php" method="GET" class="form-group position-relative" style="margin-top:25px;margin-bottom:25px;" id="search_form">
                                    <input type="hidden" name="block_id" value="<?=$_GET["block_id"]?>">
                                    <input type="hidden" name="status" value="<?=$_GET["status"]?>">
                                    <input type="search" class="form-control form-control-lg input-lg" id="input-search" placeholder="Qidirish..." name="q" value="<?=$_GET["q"]?>">
                                    <input type="hidden" name="page" value="1">
                                    <div class="form-control-position" onclick="$('#search_form').submit()" style="cursor:pointer;">
                                        <i class="icon-search7 font-medium-4"></i>
                                    </div>
                                </form>
                                <!-- /Search -->
                            </div>

                            <div class="table-responsive">
                                <table class="table" id="table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>Talaba ID</th>
                                            <th>parol</th>
                                            <th>F.I.SH</th>
                                            <th>Blok</th>
                                            <th>ball</th>
                                            <th>to'g'ri javoblar soni</th>
                                            <th>noto'g'ri javoblar soni</th>
                                            <!-- <? if ($_GET["status"] == 1) { ?>
                                                <th>natija</th>
                                            <? } ?> -->
                                            <th>test boshlangan vaqt</th>
                                            <th>test tugallangan vaqt</th>
                                            <th>sarflangan vaqt</th>
                                            <th>tahrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($tests as $test){
                                                $request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ 
                                                    $test["student_code"]
                                                ]);

                                                $quiz_block = $db->assoc("SELECT * FROM quiz_block WHERE id = ?", [ $test["block_id"] ]);

                                                echo '<tr>';
                                                    echo '<td scope="row">'.$request["code"].'</td>';
                                                    echo '<td scope="row">'.$request["passport_serial_number"].'</td>';
                                                    echo '<td scope="row">'.$request["last_name"].' '.$request["first_name"].' '.$request["father_first_name"].'</td>';
                                                    echo '<td>'.$quiz_block["name"].'</td>';
                                                    echo '<th>'.($test["stop_date"] ? $test["ball"] . "-ball" : "hali tugatmadi").'</th>';
                                                    echo '<th>'.($test["stop_date"] ? $test["correct_answers_count"] . " ta" : "hali tugatmadi").'</th>';
                                                    echo '<th>'.($test["answers_count"] - $test["correct_answers_count"]).' ta</th>';
                                                    // if ($_GET["status"] == 1) {
                                                    //     echo '<td>';
                                                    //         echo '<a href="../natija/?block_id='.$test['block_id'].'&student_code='.$test["student_code"].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-info">natija (pdf)</a>';
                                                    //         echo '<a href="../natija_html/?block_id='.$test['block_id'].'&student_code='.$test["student_code"].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-primary">natija (html)</a>';
                                                    //     echo '</td>';
                                                    // }
                                                    echo '<td>'.($test["start_date"] ? $test["start_date"] : "boshlanmadi").'</td>';
                                                    echo '<td>'.($test["stop_date"] ? $test["stop_date"] : "tugamadi").'</td>';
                                                    echo '<td>'.($test["stop_date"] && $test["start_date"] ? gmdate("H:i:s", strtotime($test["stop_date"]) - strtotime($test["start_date"])) : "hali aniq emas").'</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_quiz_user_block.php?quiz_user_block_id='.$test['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-success">tahrirlash</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_quiz_user_block.php?type=delete_quiz_user_block&quiz_user_block_id='.$test['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o\'chirish</a>';
                                                    echo '</td>';
                                                echo '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="text-xs-center mb-3" id="pagination-wrapper">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?
                                        $count = $count / $page_count;
                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" data-page="'.($page-1).'">
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
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a data-page="'.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" data-page="'.($page+1).'">
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
                                            <? foreach ($_GET as $key => $val) { ?>
                                                <?
                                                if ($key == "page") continue;
                                                ?>
                                                <input type="hidden" name="<?=$key?>" value="<?=$val?>">
                                            <? } ?>
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

<script>
    function findGetParameter(parameterName) {
        var result = "",
            tmp = [];
        location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
            });
        return result;
    }

    function updateTable() {
        var q = $( "#search_form" ).serialize();
        console.log(q);
        var url = '<?=$url[1]?>?' + q;
        $.ajax({
            url: url,
            type: "GET",
            dataType: "html",
            success: function(data) {
                console.log(data);
                $("#header").html($(data).find("#header").html());
                $("#table").html($(data).find("#table").html());
                $("#pagination-wrapper").html($(data).find("#pagination-wrapper").html());
            }
        })
    }

    $("#input-search").on("input", function(){
        updateTable();
    });

    $("#block_id").on("change", function(){
        var url = '<?=$url[1]?>?';
        url = url + "block_id=" + $(this).val();
        url = url + "&archive=" + findGetParameter("archive");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&status=" + findGetParameter("status");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("*[data-page], #page-to").on("click", function(){
        var page = $(this).attr("data-page");

        if ($(this).attr("id") == "page-to") {
            page = $("#page-to-input").val();
        }

        var url = '<?=$url[1]?>?';
        url = url + "&q=" + findGetParameter("q");
        url = url + "&status=" + findGetParameter("status");
        url = url + "&page="+page;
        // console.log(url);
        window.location = url;
    });
</script>

<? include('end.php'); ?>