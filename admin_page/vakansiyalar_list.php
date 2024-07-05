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

$disable_pagination = false;

$page = (int)$_GET['page'];
if (empty($page)){
    $page = 1;
    $_GET["page"] = 1;
}

$page_count = $_GET["page_count"] ? $_GET["page_count"] : 20;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

$query = "";

if (!empty($_GET["q"])) {
    $q = mb_strtolower(trim($_GET["q"]));
    $q = str_replace("'", "", $q);
    // $q = str_replace("'", "\\"."'"."\\", $q);

    $pq = "";
    $pq .= "REPLACE(phone_1, '+', ''), ";
    $pq .= "REPLACE(phone_1, '-', ''), ";
    $pq .= "REPLACE(REPLACE(phone_1, '+', ''), '-', ''), ";

    $q = str_replace(" ", "", $q);

    $query .= " AND REPLACE(CONCAT(first_name,last_name,father_first_name,last_name,first_name), '\'', '') LIKE '%".$q."%' OR REPLACE(REPLACE(phone_1, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%'";
}

$sql = "SELECT * FROM vakansiyalar WHERE id > 0$query ORDER BY id ASC";
$sql .= " LIMIT $page_start, $page_count";

$vakansiyalar = $db->in_array($sql);

$vakansiyalar_count = $db->assoc("SELECT COUNT(id) FROM vakansiyalar WHERE id > 0$query")["COUNT(id)"];
// exit($sql);

$ariza_topshirganlar_soni = $db->assoc("SELECT COUNT(*) FROM vakansiyalar")["COUNT(*)"];

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
                            <h4 class="card-title" title="<?=$sql?>">Ishga arizalar ro'yxati (<?=$ariza_topshirganlar_soni?> ta)</h4>
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
                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;margin-left:25px;">
                                        <a href="/<?=$url2[0]?>/vakansiyalar_list_export_to_excel.php<?=($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "")?>" class="btn btn-success" id="submit-date"><i class="icon-file5"></i> Barcha arizalarni olish (EXCEL)</a>
                                    </div>
                                </div>
                                
                                <!-- Search -->
                                <form action="vakansiyalar_list.php" method="GET" class="form-group position-relative" style="margin-top:25px;margin-bottom:25px;" id="search_form">
                                    <input type="search" class="form-control form-control-lg input-lg" id="input-search" placeholder="Qidirish..." name="q" value="<?=$_GET["q"]?>">
                                    <input type="hidden" name="page" value="1">
                                    <div class="form-control-position" onclick="$('#search_form').submit()" style="cursor:pointer;">
                                        <i class="icon-search7 font-medium-4"></i>
                                    </div>
                                </form>
                                <!-- /Search -->
                            </div>

                            <!-- table-responsive EDI CLASS-->
                            <div class="table bg-white"> 
                                <table class="table" id="natijalar_table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#id</th>
                                            <th>F.I.O</th>
                                            <th>telefon raqam</th>
                                            <th>Mutaxassisligi</th>
                                            <th>OTM</th>
                                            <th>Ilmiy daraja</th>
                                            <th>Tug'ilgan sanasi</th>
                                            <th>Ish tajribasi</th>
                                            <th>Passport seriya hamda raqami</th>
                                            <th>fayllar</th>
                                            <th>qo'shilgan sana</th>
                                            <th>tahrirlash</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($vakansiyalar as $vakansiya){
                                                $vakansiya["phone_1_formatted"] = str_replace("+", "", str_replace("-", "", $vakansiya['phone_1']));

                                                echo '<tr class="bg-white">';
                                                    echo '<th scope="row"><a href="'.$edit_vakansiya.'?vakansiya_id='.$vakansiya['id'].'&page='.$_GET["page"].'">'.$vakansiya["id"].'</a></th>';
                                                    echo '<th>'.$vakansiya["last_name"].' '.$vakansiya["first_name"].' '.$vakansiya["father_first_name"].'</th>';
                                                    echo '<td><a href="tel:'.$vakansiya["phone_1_formatted"].'">('.$vakansiya['phone_1'].')</a></td>';
                                                    echo '<td>'.$vakansiya['mutaxassisligi'].'</td>';
                                                    echo '<td>'.$vakansiya['otm'].'</td>';
                                                    echo '<td>'.$vakansiya['ilmiy_daraja'].'</td>';
                                                    echo '<td>'.$vakansiya['birth_date'].'</td>';
                                                    echo '<td>'.$vakansiya['ish_tajribasi'].'</td>';
                                                    echo '<td>'.$vakansiya['passport_serial_number'].'</td>';
                                                    
                                                    // Fayllar
                                                    $files = [];
                                                    $files_arr = [];
                                                    if ($vakansiya["diplom_file_id_1"] > 0) array_push($files_arr, $vakansiya["diplom_file_id_1"]);
                                                    if ($vakansiya["diplom_file_id_2"] > 0) array_push($files_arr, $vakansiya["diplom_file_id_2"]);
                                                    if ($vakansiya["diplom_file_id_3"] > 0) array_push($files_arr, $vakansiya["diplom_file_id_3"]);
                                                    if ($vakansiya["tarjimai_xol_file_id"] > 0) array_push($files_arr, $vakansiya["tarjimai_xol_file_id"]);

                                                    if (count($files_arr) > 0) {
                                                        $files = $db->in_array("SELECT * FROM files WHERE id IN(".implode(", ", $files_arr).")");
                                                    }

                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn btn-info btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">fayllar</button>';

                                                            echo '<div class="dropdown-menu">';
                                                            foreach ($files as $file) {
                                                                echo '<a href="../'.$file["file_folder"].'" class="dropdown-item">'.$file["file_folder"].'</a>';
                                                                echo '<div class="dropdown-divider"></div>';
                                                            }
                                                            echo '</div>';

                                                        echo '</div>';
                                                    echo '</td>';
                                                    // End Fayllar

                                                    echo '<td>'.$vakansiya['created_date'].'</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_vakansiya.php?vakansiya_id='.$vakansiya['id'].'&page='.$_GET["page"].'" target="_blank" class="tag tag-default tag-warning text-white bg-info">tahrirlash</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a data-ajax-href="edit_vakansiya.php?type=delete_vakansiya&vakansiya_id='.$vakansiya['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
                                                    echo '</td>';
                                                echo '</tr>';
                                          }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <? if (!$disable_pagination && !$_GET["page_count"]) { ?>
                                <!-- Pagination -->
                                <div class="text-xs-center mb-3" id="pagination-wrapper">
                                    <nav aria-label="Page navigation">

                                        <button class="btn btn-success" style="margin-bottom: 35px;margin-right: 45px;" id="show-all">Barchasini ko'rsatish</button>

                                        <ul class="pagination">
                                            <?
                                            $count = (int)$db->assoc("SELECT COUNT(*) FROM vakansiyalar WHERE id > 0$query ORDER BY id ASC")["COUNT(*)"] / $page_count;
                                            if (gettype($count) == "double") $count = (int)($count + 1);
                            
                                            if ($page != 1){
                                            echo '<li class="page-item">
                                                        <button class="page-link" data-page="'.($page-1).'">
                                                            <span aria-hidden="true">«</span>
                                                        </button>
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
                                                    echo '<li class="page-item '.($page == $i ? "active" : "").'">
                                                            <button data-page="'.$i.'" class="page-link">'.$i.'</button>
                                                        </li>';
                                                }
                                            }
                            
                                            if ($page != $count){
                                            echo '<li class="page-item">
                                                        <button class="page-link" data-page="'.($page+1).'">
                                                            <span aria-hidden="true">»</span>
                                                        </button>
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
                                                <input type="number" name="page" min="1" max="<?=$count?>" style="width:auto" class="page-to" placeholder="<?=$_GET['page']?>" id="page-to-input">
                                                <?
                                                if ($_GET["direction_id"]) {
                                                    echo '<input type="hidden" name="direction_id" value='.$_GET["direction_id"].'>';
                                                }
                                                ?>
                                                <button type="button" class="page-to" id="page-to">&raquo;</button>
                                            </form>

                                            <style>
                                                button.page-link,
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
                            <? } ?>
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
        var url = '<?=$url2[1]?>?' + q;
        $.ajax({
            url: url,
            type: "GET",
            dataType: "html",
            success: function(data) {
                // console.log(data);
                $("#header").html($(data).find("#header").html());
                $("#natijalar_table").html($(data).find("#natijalar_table").html());
                $("#pagination-wrapper").html($(data).find("#pagination-wrapper").html());
            }
        })
    }

    $("#input-search").on("input", function(){
        updateTable();
    });

    $(document).on("click", "*[data-ajax-href]", function(){
        var ajax_url = $(this).attr("data-ajax-href");

        $.ajax({
            url: ajax_url,
            type: "GET",
            dataType: "html",
            success: function(data) {
                updateTable();
            },
            error: function(data) {
                alert("Siz ushbu imkoniyatni amalga oshira olmaysiz!!!");
            }
        })
    });

    $("#show-all").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "q=" + findGetParameter("q");
        url = url + "&page=" + findGetParameter("page");
        url = url + "&page_count=1000000";
        window.location = url;
    });

    $(document).on("click", "*[data-page], #page-to", function(){
        var page = $(this).attr("data-page");

        if ($(this).attr("id") == "page-to") {
            page = $("#page-to-input").val();
        }

        var url = '<?=$url2[1]?>?';
        url = url + "q=" + findGetParameter("q");
        url = url + "&page=" + page;
        // console.log(url);
        window.location = url;
    });
</script>

<? include('end.php'); ?>