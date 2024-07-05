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

$page = (int)$_GET['page'];
$code = (int)$_REQUEST['code'];
if (empty($page)) $page = 1;

$page_count = $_GET["page_count"] ? $_GET["page_count"] : 20;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

$query = "";

if (!empty($_GET["from_date"])) {
    $from_date = date("Y-m-d H:i:s", strtotime($_GET["from_date"]." 00:00:00"));
    $query .= " AND payment_date >= '" . $from_date . "'";
}

if (!empty($_GET["to_date"])) {
    $to_date = date("Y-m-d H:i:s", strtotime($_GET["to_date"]." 23:59:59"));
    $query .= " AND payment_date <= '" . $to_date . "'";
}

if (!empty($_GET["payment_method"])) {
    $query .= " AND payment_method_id = '" . $_GET["payment_method"] . "'";
}

if (!empty($_GET["code"])) {
    $query .= " AND code = '" . $_GET["code"] . "'";
}

if ($_GET["export"] == "excel") {
    $sql = "SELECT * FROM payments_contract WHERE id > 0$query ORDER BY id DESC";                               
} else {
    $sql = "SELECT * FROM payments_contract WHERE id > 0$query ORDER BY id DESC LIMIT $page_start, $page_count";
}

$count = $db->assoc("SELECT COUNT(id) FROM payments_contract WHERE id > 0$query")["COUNT(id)"];
$payments = $db->in_array($sql);

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
                            <h4 class="card-title">To'lovlar ro'yxati (<?=$count?> ta)</h4>
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
                            <div class="container-fluid" style="padding-left:25px;margin-top:15px;margin-bottom:15px;">
                                <div class="row">
                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="from_date" title="<?=($from_date ? "[$from_date]" : "")?>">Dan (sana)</label>
                                        
                                        <input type="date" name="from_date" value="<?=$_GET["from_date"]?>" class="form-control" id="from_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="to_date" title="<?=($to_date ? "[$to_date]" : "")?>">Gacha (sana)</label>
                                        
                                        <input type="date" name="to_date" value="<?=$_GET["to_date"]?>" class="form-control" id="to_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;">
                                        <button class="btn btn-info" id="submit-date"><i class="icon-clock5"></i> Sana bo'yicha olish</button>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label>To'lov uslubi</label>
                                        <select name="payment_method" class="form-control" id="payment_method">
                                            <option value="" <?=(!$_GET["payment_method"] || "" == $_GET["payment_method"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <? foreach ($db->in_array("SELECT * FROM payment_methods") as $payment_method) { ?>
                                                <option value="<?=$payment_method["id"]?>" <?=($_GET["payment_method"] && $payment_method["id"] == $_GET["payment_method"] ? 'selected=""' : '')?>><?=$payment_method["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Talaba (ID)</label>
                                        <select name="code" id="single-select" class="form-control">
                                            <? foreach ($db->in_array("SELECT * FROM requests ORDER BY last_name ASC") as $student) { ?>
                                                <option value="<?=$student["code"]?>"><?=$student["last_name"] . " " . $student["first_name"]?> <?=$student["father_first_name"]?> (<?=$student["code"]?>)</option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;margin-left:25px;">
                                        <a href="/<?=$url2[0]?>/payments_list.php<?=($_GET ? "?".htmlspecialchars(http_build_query($_GET))."&export=excel" : "?export=excel")?>" class="btn btn-success" id="submit-date"><i class="icon-file5"></i> Barcha to'lovlarni olish (EXCEL)</a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table" id="table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>Talaba (ID)</th>
                                            <th>F.I.SH</th>
                                            <th>To'lagan summasi</th>
                                            <th>To'lov uslubi</th>
                                            <th>To'lov sanasi</th>
                                            <th>Shartnoma kodi</th>
                                            <? if (!$_GET["export"]) { ?>
                                                <th>tahrirlash</th>
                                                <th>o'chirish</th>
                                            <? } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? foreach ($payments as $payment){ ?>
                                            <?
                                            $payment_method = $db->assoc("SELECT * FROM payment_methods WHERE id = ?", [ $payment["payment_method_id"] ]);

                                            $student = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $payment["code"] ]);
                                            ?>
                                            <tr class="btn-reveal-trigger">
                                                <td><?=$student["code"]?></td>
                                                <td>
                                                    <?=$student["last_name"] . " " . $student["first_name"] . " " . $student["father_first_name"]?>
                                                </td>
                                                <td><?=number_format($payment["amount"])?></td>
                                                <td><?=$payment_method["name"]?></td>
                                                <td><?=$payment["payment_date"]?></td>
                                                <td><?=$student["shartnoma_code"]?></td>
                                                
                                                <? if (!$_GET["export"]) { ?>
                                                    <td>
                                                        <a href="edit_payment.php?payment_id=<?=$payment["id"]?>&page=<?=$page?>" class="tag tag-default tag-warning text-white bg-success">taxrirlash</a>
                                                    </td>
                                                    <td>
                                                        <a href="edit_payment.php?type=delete_payment&payment_id=<?=$payment["id"]?>&page=<?=$page?>" class="tag tag-default tag-warning text-white bg-danger">o'chirish</a>
                                                    </td>
                                                <? } ?>
                                            </tr>
                                        <? } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <? if (!$_GET["page_count"]) { ?>
                                <!-- Pagination -->
                                <div class="text-xs-center mb-3">
                                    <nav aria-label="Page navigation">
                                    
                                        <button class="btn btn-success" style="margin-bottom: 35px;margin-right: 45px;" id="show-all">Barchasini ko'rsatish</button>

                                        <ul class="pagination">
                                            <?
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

<? if ($_GET["export"] == "excel") { ?>
<script>
    function exceller() {
        var uri = 'data:application/vnd.ms-excel;base64,',
        template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
        base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        },
        format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        }
        var toExcel = document.getElementById("table").innerHTML;
        var ctx = {
            worksheet: name || '',
            table: toExcel
        };
        var link = document.createElement("a");
        link.download = "arizalar_<?=date("Y_m_d_H_i_s")?>.xls";
        link.href = uri + base64(format(template, ctx))
        link.click();

        <?
        unset($_GET["export"]);
        ?>
        window.location = '/<?=$url2[0]?>/payments_list.php<?=($_GET ? "?".http_build_query($_GET) : "")?>';
    }

    exceller();
</script>
<? } ?>

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

    $("#show-all").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&payment_method=" + findGetParameter("payment_method");
        url = url + "&code=" + findGetParameter("code");
        url = url + "&page=" + findGetParameter("page");
        url = url + "&page_count=1000000";
        window.location = url;
    });

    $("*[data-page], #page-to").on("click", function(){
        var page = $(this).attr("data-page");

        if ($(this).attr("id") == "page-to") {
            page = $("#page-to-input").val();
        }

        var url = '<?=$url2[1]?>?';
        url = url + "from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&payment_method=" + findGetParameter("payment_method");
        url = url + "&code=" + findGetParameter("code");
        url = url + "&page=" + page;
        window.location = url;
    });

    $("#payment_method").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&payment_method=" + $("#payment_method").val();
        url = url + "&code=" + findGetParameter("code");
        url = url + "&page=" + findGetParameter("page");
        window.location = url;
    });

    $("#submit-date").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "from_date=" + $("#from_date").val();
        url = url + "&to_date=" + $("#to_date").val();
        url = url + "&payment_method=" + findGetParameter("payment_method");
        url = url + "&code=" + findGetParameter("code");
        url = url + "&page=" + findGetParameter("page");
        // console.log(url);
        window.location = url;
    });

    $("#single-select").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&payment_method=" + findGetParameter("payment_method");
        url = url + "&code=" + $("#single-select").val();
        url = url + "&page=" + findGetParameter("page");
        window.location = url;
    });
</script>

<!-- Select2 -->
<script src="../modules/select2/select2.full.min.js"></script>
<script src="../modules/select2/select2-init.js"></script>

<? include('end.php'); ?>