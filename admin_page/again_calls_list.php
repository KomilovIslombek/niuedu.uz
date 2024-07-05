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

if ($_GET["page_count"] == 1000000) {
    $page = 1;
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

    $query .= " AND REPLACE(CONCAT(first_name), '\'', '') LIKE '%".$q."%' OR REPLACE(REPLACE(phone_1, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%'";
}

if ($_GET["desc_id"] === "0" || !empty($_GET["desc_id"])) {
    $query .= " AND description_id = '" . (int)$_GET["desc_id"] . "'";
}

if (!empty($_GET["from_date"])) {
    $from_date = date("Y-m-d H:i:s", strtotime($_GET["from_date"]));
    $query .= " AND created_date >= '" . $from_date . "'";
}

if (!empty($_GET["to_date"])) {
    $to_date = date("Y-m-d H:i:s", strtotime($_GET["to_date"]));
    $query .= " AND created_date <= '" . $to_date . "'";
}


$sql = "SELECT * FROM again_calls WHERE id > 0$query ORDER BY id ASC";
$sql .= " LIMIT $page_start, $page_count";

$again_calls = $db->in_array($sql);

$again_calls_count = $db->assoc("SELECT COUNT(id) FROM again_calls WHERE id > 0$query")["COUNT(id)"];

$descriptions_arr = $db->in_array("SELECT * FROM descriptions");
$descriptions = [];
foreach ($descriptions_arr as $description) {
    $descriptions[$description["id"]] = $description; 
}

include('head.php');
?>

<!--  -->
<div class="app-content content container-fluid" title="<?=$sql?>">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header" id="header">
                            <h4 class="card-title" title="<?=$sql?>">Arizalar ro'yxati (<?=$again_calls_count?> ta)</h4>
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
                            <form action="again_calls_list.php" method="GET" class="container-fluid" style="padding-left:25px;" id="search_form" >
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="from_date" title="<?=($from_date ? "[$from_date]" : "")?>">Dan (sana)</label>
                                        
                                        <input type="datetime-local" name="from_date" value="<?=$_GET["from_date"]?>" class="form-control" id="from_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="to_date" title="<?=($to_date ? "[$to_date]" : "")?>">Gacha (sana)</label>
                                        
                                        <input type="datetime-local" name="to_date" value="<?=$_GET["to_date"]?>" class="form-control" id="to_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;margin-bottom:32px;">
                                        <button class="btn btn-info" id="submit-date"><i class="icon-clock5"></i> Sana bo'yicha olish</button>
                                    </div>

                                    <div class="col-xl-3 col-lg-3 col-sm-6 col-12">
                                        <label>Izoh</label>
                                        <select name="desc_id" class="form-control" id="desc_id">
                                            <option value="" >Barchasi</option>

                                            <? foreach ($db->in_array("SELECT * FROM descriptions") as $desc) { ?>
                                                <option value="<?=$desc["id"]?>" <?=(($_GET["desc_id"] || $_GET["desc_id"] === "0") && $desc["id"] == $_GET["desc_id"] ? 'selected=""' : '')?>><?=$desc["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;margin-left:25px;">
                                        <a href="/<?=$url2[0]?>/again_calls_export_to_excel.php<?=($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "")?>" class="btn btn-success" id="submit-date"><i class="icon-file5"></i> Barcha arizalarni olish (EXCEL)</a>
                                    </div>
                                </div>

                                <!-- Search -->
                                <div>
                                    <input type="search" class="form-control form-control-lg input-lg" id="input-search" placeholder="Qidirish..." name="q" value="<?=$_GET["q"]?>">
                                    <input type="hidden" name="page" value="1">
                                    <div class="form-control-position" onclick="$('#search_form').submit()" style="cursor:pointer;">
                                        <i class="icon-search7 font-medium-4"></i>
                                    </div>
                                </div>
                                <!-- /Search -->

                                <div class="form-group col-12">
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d")?>&page=1">bugungi</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d", strtotime(date("Y-m-d") . " - 1 days"))?>&page=1">kechagi</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d", strtotime("monday this week"))?>&to_date=<?=date("Y-m-d", strtotime("sunday this week"))?>&page=1">shu hafta</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d", strtotime("monday previous week"))?>&to_date=<?=date("Y-m-d", strtotime("sunday previous week"))?>&page=1">o'tkan hafta</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d", strtotime("first day of this month"))?>&to_date=<?=date("Y-m-d", strtotime("last day of this month"))?>&page=1">shu oy</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-m-d", strtotime("first day of previous month"))?>&to_date=<?=date("Y-m-d", strtotime("last day of previous month"))?>&page=1">o'tkan oy</a> |
                                    <a href="again_calls_list.php?&desc_id=<?=$_GET["desc_id"]?>&from_date=<?=date("Y-01-01")?>&to_date=<?=date("Y-12-31")?>&page=1">shu yil</a>
                                </div>
                            </form>

                            <!-- table-responsive EDI CLASS-->
                            <div class="table bg-white"> 
                                <table class="table" id="natijalar_table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#id</th>
                                            <th>F.I.O</th>
                                            <th>telefon raqam</th>
                                            <th>izoh</th>
                                            <th>suhbat sanasi</th>
                                            <th>qo'shilgan sana</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($again_calls as $again_call){
                                                $again_call["phone_1_formatted"] = str_replace("+", "", str_replace("-", "", $again_call['phone_1']));

                                                echo '<tr class="bg-white">';
                                                    echo '<th scope="row">'.$again_call["id"].'</th>';
                                                    echo '<th>'.$again_call["first_name"].'</th>';
                                                    echo '<td><a href="tel:'.$again_call["phone_1_formatted"].'">('.$again_call['phone_1'].')</a></td>';
                                                    
                                                    echo '<td title="'.count($descriptions).'">';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn bg-success btn-min-width dropdown-toggle text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$descriptions[$again_call["description_id"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            foreach ($descriptions as $key => $description) {
                                                                echo '<a data-ajax-href="/admin/edit_again_call.php'.($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "").'&again_call_id='.$again_call["id"].'&type=change_description&description_id='.$description["id"].'" class="dropdown-item">'.($again_call["description_id"] == $description["id"] ? "✅" : "").' '.$description["name"].'</a>';

                                                                echo '<div class="dropdown-divider"></div>';
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    echo '<td id="interview_date_'.$again_call["id"].'">'.$again_call["interview_date"].'</td>';

                                                    echo '<td>'.$again_call['created_date'].'</td>';
                                                    
                                                    echo '<td>';
                                                        echo '<a data-ajax-href="edit_again_call.php?type=delete_again_call&again_call_id='.$again_call['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
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
                                            $count = (int)$db->assoc("SELECT COUNT(*) FROM again_calls WHERE id > 0$query ORDER BY id ASC")["COUNT(*)"] / $page_count;
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
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&page=" + findGetParameter("page");
        url = url + "&description_id=" + findGetParameter("description_id");
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
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&desc_id=" + findGetParameter("desc_id");
        url = url + "&page=" + page;
        // console.log(url);
        window.location = url;
    });

    $("#desc_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "q=" + findGetParameter("q");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&desc_id=" + $(this).val();
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#submit-date").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "q=" + findGetParameter("q");
        url = url + "&from_date=" + $("#from_date").val();
        url = url + "&to_date=" + $("#to_date").val();
        url = url + "&desc_id=" + findGetParameter("desc_id");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });
</script>

<? include('end.php'); ?>