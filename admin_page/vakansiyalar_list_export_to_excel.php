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
                            <p class="lead ml-2 mt-2">
                                <button id="to_xls" class="btn btn-danger">TO XLS</button>
                            </p>

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
                                            <th>qo'shilgan sana</th>
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
                                                    

                                                    echo '<td>'.$vakansiya['created_date'].'</td>';

                                                echo '</tr>';
                                          }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<? include "scripts.php"; ?>

<script>
    $('#to_xls').on('click',function(){
        exceller();
    });

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
        var toExcel = document.getElementById("natijalar_table").innerHTML;
        var ctx = {
            worksheet: name || '',
            table: toExcel
        };
        var link = document.createElement("a");
        link.download = "arizalar_<?=date("Y_m_d_H_i_s")?>.xls";
        link.href = uri + base64(format(template, ctx))
        link.click();

        window.location = '/<?=$url2[0]?>/requests_list.php<?=($_GET ? "?".http_build_query($_GET) : "")?>';
    }

    $('#to_xls').click();
</script>

<? include('end.php'); ?>