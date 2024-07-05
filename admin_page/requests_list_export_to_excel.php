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

$query = "";

if (!empty($_GET["q"])) {
    $q = mb_strtolower(trim($_GET["q"]));
    $q = str_replace("'", "", $q);
    // $q = str_replace("'", "\\"."'"."\\", $q);

    $pq = "";
    $pq .= "REPLACE(phone_1, '+', ''), ";
    $pq .= "REPLACE(phone_1, '-', ''), ";
    $pq .= "REPLACE(REPLACE(phone_1, '+', ''), '-', ''), ";

    $pq .= "REPLACE(phone_2, '+', ''), ";
    $pq .= "REPLACE(phone_2, '-', ''), ";
    $pq .= "REPLACE(REPLACE(phone_2, '+', ''), '-', '')";

    $q = str_replace(" ", "", $q);

    $query .= " AND REPLACE(CONCAT(code,first_name,last_name,father_first_name,last_name,first_name), '\'', '') LIKE '%".$q."%' OR REPLACE(REPLACE(phone_1, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%' OR REPLACE(REPLACE(phone_2, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%'";
}

if (!empty($_GET["staj"])) {
    $query .= " AND learn_type = 'Sirtqi'";
    $query .= " AND (direction_id = 1 OR direction_id = 101 OR direction_id = 102)";
} else {
    if (!empty($_GET["direction_id"])) {
        $query .= " AND direction_id = " . $_GET["direction_id"];
    }
    
    if (!empty($_GET["learn_type"])) {
        $query .= " AND learn_type = '" . $_GET["learn_type"] . "'";
    }
}

if (!empty($_GET["firm_id"])) {
    $query .= " AND firm_id = " . $_GET["firm_id"];
}

if (!empty($_GET["course_id"])) {
    $query .= " AND course_id = " . $_GET["course_id"];
}

if (!empty($_GET["document_type_id"])) {
    $query .= " AND document_type_id = " . $_GET["document_type_id"];
}

if (!empty($_GET["contract_type_id"])) {
    $query .= " AND contract_type_id = " . $_GET["contract_type_id"];
}

if (!empty($_GET["reg_type"])) {
    $query .= " AND reg_type = '" . $_GET["reg_type"] . "'";
}

if (strlen($_GET["suhbat"]) > 0) {
    $query .= " AND suhbat = '" . $_GET["suhbat"] . "'";
}

if (!empty($_GET["from_date"])) {
    $from_date = date("Y-m-d H:i:s", strtotime($_GET["from_date"]));
    $query .= " AND created_date >= '" . $from_date . "'";
}

if (!empty($_GET["to_date"])) {
    $to_date = date("Y-m-d H:i:s", strtotime($_GET["to_date"]));
    $query .= " AND created_date <= '" . $to_date . "'";
}

if (!empty($_GET["contract_payment"])) {
    $query .= " AND shartnoma_amount IS NOT NULL";
}

if (!empty($_GET["shartnoma_date"]) && $_GET["shartnoma_date"] == "yuklab-olgan") {
    $query .= " AND shartnoma_date IS NOT NULL";
} else if (!empty($_GET["shartnoma_date"]) && $_GET["shartnoma_date"] == "yuklab-olmagan") {
    $query .= " AND shartnoma_date IS NULL";
}

$sql = "SELECT * FROM requests WHERE id > 0$query ORDER BY id ASC";

$requests = $db->in_array($sql);
// exit($sql);

if (!empty($_GET["payment_method"])) {
    $_GET["payment"] = "qilgan";
}

foreach ($requests as $request_key => $request) {
    $request["click"] = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
        $request["id"],
        $request["code"],
    ]);

    if (empty($request["click"]["id"])) {
        $request["payme"] = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
            $request["id"],
            $request["code"]
        ]);
    }

    if (empty($request["payme"]["id"])) {
        $request["kassa"] = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
            $request["id"]
        ]);
    }

    $requests[$request_key] = $request;
}

if (!empty($_GET["payment"])) {
    $requests2 = [];

    foreach ($requests as $request) {
        $tolov_qilgan = false;

        if (!empty($request["click"]["id"])) {
            $tolov_qilgan = true;
        } else if (!empty($request["payme"]["id"])) {
            $tolov_qilgan = true;
        } else if (!empty($request["kassa"]["id"])) {
            $tolov_qilgan = true;
        }

        if ($_GET["payment"] == "qilgan" && $tolov_qilgan) {
            array_push($requests2, $request);
        } else if ($_GET["payment"] == "qilmagan" && !$tolov_qilgan) {
            array_push($requests2, $request);
        }
    }

    $requests = $requests2;
    $disable_pagination = true;
    $requests_count = count($requests);
}

if (!empty($_GET["payment_method"])) {
    $requests2 = [];

    foreach ($requests as $request) {
        if ($_GET["payment_method"] == "payme" && !empty($request["payme"]["id"])) {
            array_push($requests2, $request);
        } else if ($_GET["payment_method"] == "click" && !empty($request["click"]["id"])) {
            array_push($requests2, $request);
        } else if ($_GET["payment_method"] == "kassa" && !empty($request["kassa"]["id"])) {
            array_push($requests2, $request);
        }
    }

    $requests = $requests2;
    $disable_pagination = true;
    $requests_count = count($requests);
}

foreach ($requests as $request_key => $request) {
    $payed_amount = $db->assoc("SELECT SUM(amount) FROM payments_contract WHERE code = ?", [ $request["code"] ])["SUM(amount)"];
    $requests[$request_key]["payed_amount"] = $payed_amount;
}

// Kontraktga to'lov qilgan va qilmaganlarni filterlash
if (!empty($_GET["contract_payment"])) {
    $requests3 = [];

    foreach ($requests as $request) {
        if ($_GET["contract_payment"] == "toliq-tolagan") {
            if ($request["payed_amount"] > 0 && $request["shartnoma_amount"] == $request["payed_amount"]) {
                array_push($requests3, $request);
            }
        } else if ($_GET["contract_payment"] == "toliq-tolamagan") {
            if ($request["payed_amount"] > 0 && $request["shartnoma_amount"] != $request["payed_amount"]) {
                array_push($requests3, $request);
            }
        } else if ($_GET["contract_payment"] == "umuman-tolamagan") {
            if ($request["payed_amount"] == 0 && $request["shartnoma_amount"]) {
                array_push($requests3, $request);
            }
        }
    }

    $requests = $requests3;
    $disable_pagination = true;
    $requests_count = count($requests);
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
                        <div class="card-header">
                            <h4 class="card-title">Arizalar ro'yxati (Barchasi)</h4>
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

                            <div class="table-responsive">
                                <table class="table" id="natijalar_table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>id</th>
                                            <th>#code</th>
                                            <th>ism</th>
                                            <th>familiya</th>
                                            <th>Otasining ismi</th>
                                            <th>Passport seriya hamda raqami</th>
                                            <th>Jinsi</th>
                                            <th>Tug'ilgan sanasi</th>
                                            <th>telefon raqam (1)</th>
                                            <th>telefon raqam (2)</th>
                                            <th>Ta'lim yo'nalishi</th>
                                            <th>Ta'lim shakli</th>
                                            <th>qo'shilgan sana</th>
                                            <th>ariza to'lov</th>
                                            <th>ariza to'lov miqdori</th>
                                            <th>ariza to'lov turi</th>
                                            <th>ball</th>
                                            <th>status</th>
                                            <th>agent</th>
                                            <th>kurs</th>
                                            <th>xujjat</th>
                                            <th>kontrakt turi</th>
                                            <th>shartnoma summasi</th>
                                            <th>to'lagan kontrakt to'lovi</th>
                                            <!-- <th>shartnoma raqami</th> -->
                                            <th>shartnoma kodi</th>
                                            <th>qaysi kursga o'tayotgani</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $status_options = [
                                                [
                                                    "name" => "Ariza topshirilgan",
                                                    "bg" => "btn-secondary"
                                                ],
                                                [
                                                    "name" => "Imtihondan o'tgan",
                                                    "bg" => "btn-success"
                                                ],
                                                [
                                                    "name" => "Imtihondan o'tmagan",
                                                    "bg" => "btn-danger"
                                                ],
                                                [
                                                    "name" => "Kelmagan",
                                                    "bg" => "btn-danger"
                                                ],
                                                [
                                                    "name" => "Onlayn",
                                                    "bg" => "btn-info"
                                                ],
                                                [
                                                    "name" => "O'qimaydi",
                                                    "bg" => "btn-danger"
                                                ]
                                            ];

                                            foreach ($requests as $request){
                                                $request["phone_1_formatted"] = str_replace("+", "", str_replace("-", "", $request['phone_1']));
                                                $request["phone_2_formatted"] = str_replace("+", "", str_replace("-", "", $request['phone_2']));

                                                $confirmed_payment_click = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
                                                    $request["id"],
                                                    $request["code"],
                                                ]);

                                                if (empty($confirmed_payment_click["id"])) {
                                                    $confirmed_payment_payme = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
                                                        $request["id"],
                                                        $request["code"]
                                                    ]);
                                                }

                                                if (empty($confirmed_payment_payme["id"])) {
                                                    $confirmed_payment_kassa = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
                                                        $request["id"]
                                                    ]);
                                                }

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$request["id"].'</th>';
                                                    echo '<th scope="row">'.$request["code"].'</th>';
                                                    echo '<th>'.$request['first_name'].'</th>';
                                                    echo '<th>'.$request["last_name"].'</th>';
                                                    echo '<th>'.$request['father_first_name'].'</th>';
                                                    echo '<th>'.$request['passport_serial_number'].'</th>';
                                                    echo '<td>'.$request['sex'].'</td>';
                                                    echo '<td>'.$request['birth_date'].'</td>';
                                                    echo '<td><a href="tel:'.$request["phone_1_formatted"].'">('.$request['phone_1'].')</a></td>';
                                                    echo '<td><a href="tel:'.$request["phone_2_formatted"].'">('.$request['phone_2'].')</a></td>';
                                                    echo '<td>'.$request['direction'].'</td>';
                                                    echo '<td>'.$request['learn_type'].'</td>';
                                                    echo '<td>'.$request['created_date'].'</td>';

                                                    
                                                    if (!empty($confirmed_payment_click["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-success">qabul qilingan</a>';
                                                        echo '</td>';
                                                        echo '<td>'.(number_format($confirmed_payment_click["amount"], 0)).' UZS</td>';
                                                        echo '<td>click</td>';
                                                    } else if (!empty($confirmed_payment_payme["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-success">qabul qilingan</small></a>';
                                                        echo '</td>';
                                                        echo '<td>'.(number_format(($confirmed_payment_payme["amount"] / 100), 0)).' UZS</td>';
                                                        echo '<td>payme</td>';
                                                    } else if (!empty($confirmed_payment_kassa["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-success">qabul qilingan</small></a>';
                                                        echo '</td>';
                                                        echo '<td>'.(number_format($confirmed_payment_kassa["amount"], 0)).' UZS</td>';
                                                        echo '<td>kassa aparat orqali</td>';
                                                    } else {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-danger">qilinmagan</a>';
                                                        echo '</td>';

                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-danger">qilinmagan</a>';
                                                        echo '</td>';

                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-danger">qilinmagan</a>';
                                                        echo '</td>';
                                                    }

                                                    echo '<td>'.($request["ball"] ? $request["ball"] . " ball" : "kiritilmagan").'</td>';

                                                    echo '<td>'.$status_options[$request["suhbat"]]["name"].'</td>';

                                                    echo '<th>'.$firms[$request["firm_id"]]["name"].'</th>';
                                                    echo '<th>'.$courses[$request["course_id"]]["name"].'</th>';
                                                    echo '<th>'.$document_types[$request["document_type_id"]]["name"].'</th>';

                                                    echo '<th>'.$contract_types[$request["contract_type_id"]]["name"].'</th>';

                                                    echo '<td>'.($request["shartnoma_amount"] ? '<b>'.number_format($request["shartnoma_amount"]).' </b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>'.($request["payed_amount"] ? '<b>'.number_format($request["payed_amount"]).' </b>' : '<b class="text-danger">to\'lamagan</b>').'</td>';

                                                    echo '<td>'.($request["shartnoma_code"] ? '<b>'.$request["shartnoma_code"].'</b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>'.($request["to_course"] ? $request["to_course"] : "kiritilmagan").'</td>';
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