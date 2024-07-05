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

    $pq .= "REPLACE(phone_2, '+', ''), ";
    $pq .= "REPLACE(phone_2, '-', ''), ";
    $pq .= "REPLACE(REPLACE(phone_2, '+', ''), '-', '')";

    $q = str_replace(" ", "", $q);

    $query .= " AND REPLACE(CONCAT(code,first_name,last_name,father_first_name,last_name,first_name), '\'', '') LIKE '%".$q."%' OR REPLACE(REPLACE(phone_1, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%' OR REPLACE(REPLACE(phone_2, '+', ''), '-', '') LIKE '%".str_replace("-", "", $q)."%'";
}

if (!empty($_GET["direction_id"])) {
    $query .= " AND direction_id = " . $_GET["direction_id"];
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

if (!empty($_GET["learn_type"])) {
    $query .= " AND learn_type = '" . $_GET["learn_type"] . "'";
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

if (!empty($_GET["code"])) {
    $query .= " AND code = '" . $_GET["code"] . "'";
}

if (!empty($_GET["contract_payment"])) {
    $query .= " AND shartnoma_amount IS NOT NULL AND shartnoma_amount > 0";
}

if (!empty($_GET["shartnoma_date"]) && $_GET["shartnoma_date"] == "yuklab-olgan") {
    $query .= " AND shartnoma_date IS NOT NULL";
} else if (!empty($_GET["shartnoma_date"]) && $_GET["shartnoma_date"] == "yuklab-olmagan") {
    $query .= " AND shartnoma_date IS NULL";
}

$sql = "SELECT * FROM requests_1 WHERE id > 0$query ORDER BY id ASC";

if (empty($_GET["payment"]) && empty($_GET["payment_method"]) && empty($_GET["send_sms"]) && empty($_GET["contract_payment"])) {
    $sql .= " LIMIT $page_start, $page_count";
}

$requests_1 = $db->in_array($sql);

$requests_1_count = $db->assoc("SELECT COUNT(id) FROM requests_1 WHERE id > 0$query")["COUNT(id)"];
// exit($sql);

if (!empty($_GET["payment_method"])) {
    $_GET["payment"] = "qilgan";
}

foreach ($requests_1 as $request_key => $request) {
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

    $requests_1[$request_key] = $request;
}

if (!empty($_GET["payment"])) {
    $requests_12 = [];

    foreach ($requests_1 as $request) {
        $tolov_qilgan = false;

        if (!empty($request["click"]["id"])) {
            $tolov_qilgan = true;
        } else if (!empty($request["payme"]["id"])) {
            $tolov_qilgan = true;
        } else if (!empty($request["kassa"]["id"])) {
            $tolov_qilgan = true;
        }

        if ($_GET["payment"] == "qilgan" && $tolov_qilgan) {
            array_push($requests_12, $request);
        } else if ($_GET["payment"] == "qilmagan" && !$tolov_qilgan) {
            array_push($requests_12, $request);
        }
    }

    $requests_1 = $requests_12;
    $disable_pagination = true;
    $requests_1_count = count($requests_1);
}

if (!empty($_GET["payment_method"])) {
    $requests_12 = [];

    foreach ($requests_1 as $request) {
        if ($_GET["payment_method"] == "payme" && !empty($request["payme"]["id"])) {
            array_push($requests_12, $request);
        } else if ($_GET["payment_method"] == "click" && !empty($request["click"]["id"])) {
            array_push($requests_12, $request);
        } else if ($_GET["payment_method"] == "kassa" && !empty($request["kassa"]["id"])) {
            array_push($requests_12, $request);
        }
    }

    $requests_1 = $requests_12;
    $disable_pagination = true;
    $requests_1_count = count($requests_1);
}

foreach ($requests_1 as $request_key => $request) {
    $payed_amount = $db->assoc("SELECT SUM(amount) FROM payments_contract WHERE code = ?", [ $request["code"] ])["SUM(amount)"];
    $requests_1[$request_key]["payed_amount"] = $payed_amount;
}

// Kontraktga to'lov qilgan va qilmaganlarni filterlash
if (!empty($_GET["contract_payment"])) {
    $requests_13 = [];

    foreach ($requests_1 as $request) {
        if ($_GET["contract_payment"] == "toliq-tolagan") {
            if ($request["payed_amount"] > 0 && $request["shartnoma_amount"] == $request["payed_amount"]) {
                array_push($requests_13, $request);
            }
        } else if ($_GET["contract_payment"] == "toliq-tolamagan") {
            if ($request["payed_amount"] > 0 && $request["shartnoma_amount"] != $request["payed_amount"]) {
                array_push($requests_13, $request);
            }
        } else if ($_GET["contract_payment"] == "umuman-tolamagan") {
            if ($request["payed_amount"] == 0 && $request["shartnoma_amount"]) {
                array_push($requests_13, $request);
            }
        }
    }

    $requests_1 = $requests_13;
    $disable_pagination = true;
    $requests_1_count = count($requests_1);
}

$directions_arr = $db->in_array("SELECT id, code, name FROM directions");
$directions = [];
foreach ($directions_arr as $direction) {
    $directions[$direction["id"]] = $direction;
}

// suhbat uchun to'lovlar soni
$payments_click = $db->assoc("SELECT COUNT(*) FROM payments WHERE status = 'confirmed'")["COUNT(*)"];
$payments_payme = $db->assoc("SELECT COUNT(*) FROM transactions WHERE state = 2")["COUNT(*)"];
$payments_kassa = $db->assoc("SELECT COUNT(*) FROM payments_kassa_aparat")["COUNT(*)"];
$payments_count = $payments_click + $payments_payme + $payments_kassa;

// suhbat uchun to'lovlar summasi
$payments_click_amount = $db->assoc("SELECT SUM(amount) FROM payments WHERE status = 'confirmed'")["SUM(amount)"];
$payments_payme_amount = $db->assoc("SELECT SUM(amount) FROM transactions WHERE state = 2")["SUM(amount)"] / 100;
$payments_kassa_amount = (int)$db->assoc("SELECT SUM(amount) FROM payments_kassa_aparat")["SUM(amount)"];
$payments_amount = $payments_click_amount + $payments_payme_amount + $payments_kassa_amount;


if (empty($_GET["reg_type"])) {
    $suhbatdan_otganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1 WHERE suhbat = 1")["COUNT(*)"];

    $suhbatdan_otmaganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1 WHERE suhbat = 2")["COUNT(*)"];

    $ariza_topshirganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1")["COUNT(*)"];
} else {
    $suhbatdan_otganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1 WHERE suhbat = 1 AND reg_type = ?", [ $_GET["reg_type"] ])["COUNT(*)"];

    $suhbatdan_otmaganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1 WHERE suhbat = 2 AND reg_type = ?", [ $_GET["reg_type"] ])["COUNT(*)"];

    $ariza_topshirganlar_soni = $db->assoc("SELECT COUNT(*) FROM requests_1 WHERE reg_type = ?", [ $_GET["reg_type"] ])["COUNT(*)"];
}

// $yuborilgan_smslar_soni = $db->assoc("SELECT COUNT(*) FROM sms WHERE res = ?", [ "Request is received" ])["COUNT(*)"];
// $ruxsatnomani_yuklab_olganlar_soni = $db->assoc("SELECT COUNT(DISTINCT(user_id)) FROM ruxsatnoma_logs")["COUNT(DISTINCT(user_id))"];

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

if (!empty($_GET["send_sms"])) {
    header("Content-type: text/plain");
    $phones = [];

    foreach ($requests_1 as $request) {
        $request["phone_1"] = str_replace("+", "", $request["phone_1"]);
        $request["phone_2"] = str_replace("+", "", $request["phone_2"]);

        $request["phone_1"] = str_replace("-", "", $request["phone_1"]);
        $request["phone_2"] = str_replace("-", "", $request["phone_2"]);

        if (mb_strlen($request["phone_1"]) == 12) {
            array_push($phones, $request["phone_1"]);
        }
        
        
        if (mb_strlen($request["phone_2"]) == 12) {
            array_push($phones, $request["phone_2"]);
        }
    }

    if (count($phones > 0)) {
        $sms_id = $db->insert("sms_send", [
            "creator_user_id" => $user_id,
            "phones" => json_encode($phones, JSON_UNESCAPED_UNICODE)
        ]);

        if ($sms_id > 0) {
            header("Location: /$url2[0]/send_sms.php?sms_id=$sms_id");
            exit;
        }
    }
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
                            <h4 class="card-title" title="<?=$sql?>">Arizalar ro'yxati (<?=$requests_1_count?> ta)</h4>
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
                            <!-- Statistics -->
                            <div class="row">
                                <div class="col-xl-3 col-lg-6 col-xs-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="media">
                                                <div class="p-2 text-xs-center bg-deep-orange bg-darken-2 media-left media-middle" style="height:160px;">
                                                    <i class="icon-user1 font-large-2 white"></i>
                                                </div>
                                                <div class="p-2 bg-deep-orange white media-body" style="height:160px;">
                                                    <h5>Jami ariza topshirganlar soni</h5>
                                                    <h5 class="text-bold-400"><?=$ariza_topshirganlar_soni?> ta</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-xl-3 col-lg-6 col-xs-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="media">
                                                <div class="p-2 text-xs-center bg-success bg-darken-2 media-left media-middle" style="height:160px;">
                                                    <i class="icon-android-checkmark-circle font-large-2 white"></i>
                                                </div>
                                                <div class="p-2 bg-success white media-body" style="height:160px;">
                                                    <h5>Imtihondan o'tganlar soni</h5>
                                                    <h5 class="text-bold-400"><?=$suhbatdan_otganlar_soni?> ta</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-xl-3 col-lg-6 col-xs-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="media">
                                                <div class="p-2 text-xs-center bg-danger bg-darken-2 media-left media-middle" style="height:160px;">
                                                    <i class="icon-circle-cross font-large-2 white"></i>
                                                </div>
                                                <div class="p-2 bg-danger white media-body" style="height:160px;">
                                                    <h5>Imtihondan o'tmaganlar soni</h5>
                                                    <h5 class="text-bold-400"><?=$suhbatdan_otmaganlar_soni?> ta</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="col-xl-3 col-lg-6 col-xs-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="media">
                                                <div class="p-2 text-xs-center bg-info bg-darken-2 media-left media-middle" style="height:160px;">
                                                    <i class="icon-check2 font-large-2 white"></i>
                                                </div>
                                                <div class="p-2 bg-info white media-body" style="height:160px;">
                                                    <h5>Arizaga to'lov qilganlar soni</h5>
                                                    <h5 class="text-bold-400"><?=$payments_count?> ta</h5>
                                                    <h5 class="text-bold-400"><?=number_format($payments_amount, 0)?> uzs</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                            <!--/ Statistics -->

                            <div class="container-fluid" style="padding-left:25px;">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Ta'lim yo'nalishi</label>
                                        <select name="direction_id" class="form-control" id="direction_id">
                                            <option value="" <?=(!$_GET["direction_id"] || "" == $_GET["direction_id"] ? 'selected=""' : '')?>>Barcha yo'nalishlar</option>

                                            <? foreach ($db->in_array("SELECT * FROM directions") as $direction) { ?>
                                                <option value="<?=$direction["id"]?>" <?=($_GET["direction_id"] && $direction["id"] == $_GET["direction_id"] ? 'selected=""' : '')?>><?=lng($direction["short_name"], "uz")?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label>Ta'lim shakli</label>
                                        <select name="learn_type" class="form-control" id="learn_type">
                                            <option value="" <?=(!$_GET["learn_type"] || "" == $_GET["learn_type"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="Kunduzgi" <?=($_GET["learn_type"] && $_GET["learn_type"] == "Kunduzgi" ? 'selected=""' : '')?>>Kunduzgi</option>

                                            <option value="Sirtqi" <?=($_GET["learn_type"] && $_GET["learn_type"] == "Sirtqi" ? 'selected=""' : '')?>>Sirtqi</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label>ariza turi</label>
                                        <select name="reg_type" class="form-control" id="reg_type">
                                            <option value="" <?=(!$_GET["reg_type"] || "" == $_GET["reg_type"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="oqishni-kochirish" <?=($_GET["reg_type"] && "oqishni-kochirish" == $_GET["reg_type"] ? 'selected=""' : '')?>>O'qishni ko'chirish</option>
                                            <option value="oddiy" <?=($_GET["reg_type"] && "oddiy" == $_GET["reg_type"] ? 'selected=""' : '')?>>Abituriyent</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label>Ariza to'lovi</label>
                                        <select name="payment" class="form-control" id="payment">
                                            <option value="" <?=(!$_GET["payment"] || "" == $_GET["payment"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="qilgan" <?=($_GET["payment"] && "qilgan" == $_GET["payment"] ? 'selected=""' : '')?>>Ariza to'lovini qilgan</option>
                                            <option value="qilmagan" <?=($_GET["payment"] && "qilmagan" == $_GET["payment"] ? 'selected=""' : '')?>>Ariza to'lovini qilmagan</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label>Status</label>
                                        <select name="suhbat" class="form-control" id="suhbat">
                                            <option value="" <?=(!$_GET["suhbat"] || "" == $_GET["suhbat"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <?
                                            foreach ($status_options as $status_key => $status_option) {
                                                echo '<option value="'.$status_key.'" '.(strlen($_GET["suhbat"]) > 0 && $status_key == $_GET["suhbat"] ? 'selected=""' : '').'>'.$status_option["name"].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="from_date" title="<?=($from_date ? "[$from_date]" : "")?>">Dan (sana)</label>
                                        
                                        <input type="datetime-local" name="from_date" value="<?=$_GET["from_date"]?>" class="form-control" id="from_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12">
                                        <label for="to_date" title="<?=($to_date ? "[$to_date]" : "")?>">Gacha (sana)</label>
                                        
                                        <input type="datetime-local" name="to_date" value="<?=$_GET["to_date"]?>" class="form-control" id="to_date">
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;">
                                        <button class="btn btn-info" id="submit-date"><i class="icon-clock5"></i> Sana bo'yicha olish</button>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;">
                                        <button class="btn btn-primary" id="send-sms"><i class="icon-mail5"></i> Barchaga sms yuborish</button>
                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-sm-6 col-12" style="margin-top:32px;margin-left:25px;">
                                        <a href="/<?=$url2[0]?>/requests_1_list_export_to_excel.php<?=($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "")?>" class="btn btn-success" id="submit-date"><i class="icon-file5"></i> Barcha arizalarni olish (EXCEL)</a>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Agentlar</label>
                                        <select name="firm_id" class="form-control" id="firm_id">
                                            <option value="" <?=(!$_GET["firm_id"] || "" == $_GET["firm_id"] ? 'selected=""' : '')?>>Barcha agentlar</option>

                                            <? foreach ($firms as $firm) { ?>
                                                <option value="<?=$firm["id"]?>" <?=($_GET["firm_id"] && $firm["id"] == $_GET["firm_id"] ? 'selected=""' : '')?>><?=$firm["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Kurslar</label>
                                        <select name="course_id" class="form-control" id="course_id">
                                            <option value="" <?=(!$_GET["course_id"] || "" == $_GET["course_id"] ? 'selected=""' : '')?>>Barcha kurslar</option>

                                            <? foreach ($courses as $course) { ?>
                                                <option value="<?=$course["id"]?>" <?=($_GET["course_id"] && $course["id"] == $_GET["course_id"] ? 'selected=""' : '')?>><?=$course["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Xujjatlar</label>
                                        <select name="document_type_id" class="form-control" id="document_type_id">
                                            <option value="" <?=(!$_GET["document_type_id"] || "" == $_GET["document_type_id"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <? foreach ($document_types as $document_type) { ?>
                                                <option value="<?=$document_type["id"]?>" <?=($_GET["document_type_id"] && $document_type["id"] == $_GET["document_type_id"] ? 'selected=""' : '')?>><?=$document_type["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Kontrakt turlari</label>
                                        <select name="contract_type_id" class="form-control" id="contract_type_id">
                                            <option value="" <?=(!$_GET["contract_type_id"] || "" == $_GET["contract_type_id"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <? foreach ($contract_types as $contract_type) { ?>
                                                <option value="<?=$contract_type["id"]?>" <?=($_GET["contract_type_id"] && $contract_type["id"] == $_GET["contract_type_id"] ? 'selected=""' : '')?>><?=$contract_type["name"]?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Kontrakt to'lovi</label>
                                        <select name="contract_payment" class="form-control" id="contract_payment">
                                            <option value="" <?=(!$_GET["contract_payment"] || "" == $_GET["contract_payment"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="toliq-tolagan" <?=($_REQUEST["contract_payment"] == "toliq-tolagan" ? 'selected=""' : "")?>>To'liq to'lagan</option>
                                            <option value="toliq-tolamagan" <?=($_REQUEST["contract_payment"] == "toliq-tolamagan" ? 'selected=""' : "")?>>To'liq to'lamagan</option>
                                            <option value="umuman-tolamagan" <?=($_REQUEST["contract_payment"] == "umuman-tolamagan" ? 'selected=""' : "")?>>Umuman to'lamagan</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Ariza to'lov turi</label>
                                        <select name="payment_method" class="form-control" id="payment_method">
                                            <option value="" <?=(!$_GET["payment_method"] || "" == $_GET["payment_method"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="payme" <?=($_REQUEST["payment_method"] == "payme" ? 'selected=""' : "")?>>Payme</option>
                                            <option value="click" <?=($_REQUEST["payment_method"] == "click" ? 'selected=""' : "")?>>Click</option>
                                            <option value="kassa" <?=($_REQUEST["payment_method"] == "kassa" ? 'selected=""' : "")?>>kassa</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-sm-6 col-12">
                                        <label>Shartnoma</label>
                                        <select name="shartnoma_date" class="form-control" id="shartnoma_date">
                                            <option value="" <?=(!$_GET["shartnoma_date"] || "" == $_GET["shartnoma_date"] ? 'selected=""' : '')?>>Barchasi</option>

                                            <option value="yuklab-olgan" <?=($_REQUEST["shartnoma_date"] == "yuklab-olgan" ? 'selected=""' : "")?>>Yuklab olgan</option>
                                            <option value="yuklab-olmagan" <?=($_REQUEST["shartnoma_date"] == "yuklab-olmagan" ? 'selected=""' : "")?>>Yuklab olmagan</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Search -->
                                <form action="requests_1_list.php" method="GET" class="form-group position-relative" style="margin-top:25px;margin-bottom:25px;" id="search_form">
                                    <input type="hidden" name="direction_id" value="<?=$_GET["direction_id"]?>">
                                    <input type="hidden" name="reg_type" value="<?=$_GET["reg_type"]?>">
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
                                            <th>Ta'lim yo'nalishi</th>
                                            <th>Ta'lim shakli</th>
                                            <th>telefon raqam</th>
                                            <th>qo'shilgan sana</th>
                                            <th>qo'shimcha</th>
                                            <th>test tashkillashtirish</th>
                                            <th>ariza to'lovi</th>
                                            <th>agent</th>
                                            <th>kurs</th>
                                            <th>xujjat</th>
                                            <th>kontrakt turi</th>
                                            <th>fayllar</th>
                                            <th>ball</th>
                                            <th>shartnoma summasi</th>
                                            <th>to'lagan summasi</th>
                                            <!-- <th>shartnoma raqami</th> -->
                                            <th>shartnoma kodi</th>
                                            <th>tahrirlash</th>
                                            <th>sms yuborish</th>
                                            <th>zaprosni yuklab olish</th>
                                            <th>o'chirish</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($requests_1 as $request){
                                                $request["phone_1_formatted"] = str_replace("+", "", str_replace("-", "", $request['phone_1']));

                                                $direction = $directions[$request["direction_id"]];

                                                if ($_GET["reg_type"] == "oqishni-kochirish") {
                                                    $edit_request = "edit_request_oqishni_kochirish.php";
                                                } else {
                                                    $edit_request = "edit_request.php";
                                                }

                                                echo '<tr class="bg-white">';
                                                    echo '<th scope="row"><a href="'.$edit_request.'?request_id='.$request['id'].'&page='.$_GET["page"].'">'.idCode($direction["code"], $request["id"]).'</a></th>';
                                                    echo '<th>'.$request["last_name"].' '.$request["first_name"].' '.$request["father_first_name"].'</th>';
                                                    echo '<td>'.$request['direction'].'</td>';
                                                    echo '<td>'.$request['learn_type'].'</td>';
                                                    echo '<td><a href="tel:'.$request["phone_1_formatted"].'">('.$request['phone_1'].')</a></td>';
                                                    echo '<td>'.$request['created_date'].'</td>';
                                                    
                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn '.$status_options[$request["suhbat"]]["bg"].' btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$status_options[$request["suhbat"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            foreach ($status_options as $status_key => $status_option) {
                                                                echo '<a data-ajax-href="'.$edit_request.'?type=change_suhbat&suhbat='.$status_key.'&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">'.($request["suhbat"] == $status_key ? "✅" : "").' '.$status_option["name"].'</a>';

                                                                if ($status_key + 1 != count($status_options)) {
                                                                    echo '<div class="dropdown-divider"></div>';
                                                                }
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    $quiz_user_block = $db->assoc("SELECT * FROM quiz_user_block WHERE student_code = ?", [ $request["code"] ]);

                                                    if (!empty($quiz_user_block["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="add_block_test_student.php?code='.$request['code'].'" target="_blank" class="tag tag-default tag-warning text-white bg-success">'.$quiz_user_block["ball"].'-ball</a>';
                                                        echo '</td>';
                                                    } else {
                                                        echo '<td>';
                                                            echo '<a href="add_block_test_student.php?code='.$request['code'].'" target="_blank" class="tag tag-default tag-warning text-white bg-success">test tashkillashtirish</a>';
                                                        echo '</td>';
                                                    }

                                                    if (!empty($request["click"]["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-success">qabul qilingan<br><small>('.number_format($request["click"]["amount"], 0).' UZS)</small><br><small>click</small></a>';
                                                        echo '</td>';
                                                    } else if (!empty($request["payme"]["id"])) {
                                                        echo '<td>';
                                                            echo '<a href="javascript:void(0)" class="tag tag-default tag-warning text-white bg-success">qabul qilingan<br><small>('.number_format(($request["payme"]["amount"] / 100), 0).' UZS)</small><br><small>payme</small></a>';
                                                        echo '</td>';
                                                    } else if (!empty($request["kassa"]["id"])) {
                                                        // echo '<td>';
                                                            
                                                        // echo '</td>';

                                                        echo '<td>';
                                                            echo '<div class="btn-group mr-1 mb-1">';
                                                                echo '<button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">qabul qilingan<br><small>('.(number_format($request["kassa"]["amount"], 0)).' UZS)</small><br><small>kassa aparat orqali</small></button>';

                                                                echo '<div class="dropdown-menu">';
                                                                    echo '<a data-ajax-href="/'.$url2[0].'/'.$edit_request.''.($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "").'&request_id='.$request["id"].'&remove_payment=1" class="dropdown-item">to\'lovni bekor qilish</a>';
                                                                echo '</div>';

                                                            echo '</div>';
                                                        echo '</td>';
                                                    } else {
                                                        echo '<td>';
                                                            echo '<div class="btn-group mr-1 mb-1">';
                                                                echo '<button type="button" class="btn btn-danger btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">qilinmagan</button>';

                                                                echo '<div class="dropdown-menu">';
                                                                    echo '<a data-ajax-href="/'.$url2[0].'/'.$edit_request.''.($_GET ? "?".htmlspecialchars(http_build_query($_GET)) : "").'&request_id='.$request["id"].'&accept_payment=1" class="dropdown-item">Kassa aparat orqali to\'langan</a>';
                                                                echo '</div>';

                                                            echo '</div>';
                                                        echo '</td>';
                                                    }

                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$firms[$request["firm_id"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            echo '<a data-ajax-href="'.$edit_request.'?type=change_firm&firm_id=&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">Agentdan chiqarish</a>';
                                                            echo '<div class="dropdown-divider"></div>';

                                                            $key = 0;
                                                            foreach ($firms as $firm) {
                                                                echo '<a data-ajax-href="'.$edit_request.'?type=change_firm&firm_id='.$firm["id"].'&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">'.($request["firm_id"] == $firm["id"] ? "✅" : "").' '.$firm["name"].'</a>';

                                                                if ($key + 1 != count($firms)) {
                                                                    echo '<div class="dropdown-divider"></div>';
                                                                } else {
                                                                    $key++;
                                                                }
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$courses[$request["course_id"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            echo '<a data-ajax-href="'.$edit_request.'?type=change_course&course_id=&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">Kursdan chiqarish</a>';
                                                            echo '<div class="dropdown-divider"></div>';

                                                            $key = 0;
                                                            foreach ($courses as $course) {
                                                                echo '<a data-ajax-href="'.$edit_request.'?type=change_course&course_id='.$course["id"].'&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">'.($request["course_id"] == $course["id"] ? "✅" : "").' '.$course["name"].'</a>';

                                                                if ($key + 1 != count($courses)) {
                                                                    echo '<div class="dropdown-divider"></div>';
                                                                } else {
                                                                    $key++;
                                                                }
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$document_types[$request["document_type_id"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            // echo '<a data-ajax-href="'.$edit_request.'?type=change_document_type&document_type_id=&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">Kursdan chiqarish</a>';
                                                            // echo '<div class="dropdown-divider"></div>';

                                                            $key = 0;
                                                            foreach ($document_types as $document_type) {
                                                                echo '<a data-ajax-href="'.$edit_request.'?type=change_document_type&document_type_id='.$document_type["id"].'&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">'.($request["document_type_id"] == $document_type["id"] ? "✅" : "").' '.$document_type["name"].'</a>';

                                                                if ($key + 1 != count($document_types)) {
                                                                    echo '<div class="dropdown-divider"></div>';
                                                                } else {
                                                                    $key++;
                                                                }
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    // Kontrakt turlari td
                                                    echo '<td>';
                                                        echo '<div class="btn-group mr-1 mb-1">';
                                                            echo '<button type="button" class="btn btn-success btn-min-width dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$contract_types[$request["contract_type_id"]]["name"].'</button>';
                                                            echo '<div class="dropdown-menu">';

                                                            // echo '<a data-ajax-href="'.$edit_request.'?type=change_contract_type&contract_type_id=&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">Kursdan chiqarish</a>';
                                                            // echo '<div class="dropdown-divider"></div>';

                                                            $key = 0;
                                                            foreach ($contract_types as $contract_type) {
                                                                echo '<a data-ajax-href="'.$edit_request.'?type=change_contract_type&contract_type_id='.$contract_type["id"].'&request_id='.$request["id"].'&page='.$_GET["page"].'" class="dropdown-item">'.($request["contract_type_id"] == $contract_type["id"] ? "✅" : "").' '.$contract_type["name"].'</a>';

                                                                if ($key + 1 != count($contract_types)) {
                                                                    echo '<div class="dropdown-divider"></div>';
                                                                } else {
                                                                    $key++;
                                                                }
                                                            }
                                                        echo '</div>';
                                                    echo '</td>';

                                                    // Fayllar
                                                    $files = [];
                                                    $files_arr = [];
                                                    if ($request["file_id_1"] > 0) array_push($files_arr, $request["file_id_1"]);
                                                    if ($request["file_id_2"] > 0) array_push($files_arr, $request["file_id_2"]);
                                                    if ($request["file_id_3"] > 0) array_push($files_arr, $request["file_id_3"]);
                                                    if ($request["file_id_4"] > 0) array_push($files_arr, $request["file_id_4"]);

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

                                                    echo '<td>'.($request["ball"] ? '<b>'.$request["ball"].' ball</b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>'.($request["shartnoma_amount"] ? '<b>'.number_format($request["shartnoma_amount"]).' </b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>'.($request["payed_amount"] ? '<b>'.number_format($request["payed_amount"]).' </b>' : '<b class="text-danger">to\'lamagan</b>').'</td>';

                                                    // echo '<td>'.($request["shartnoma_number"] ? '<b>'.$request["shartnoma_number"].'</b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>'.($request["shartnoma_code"] ? '<b>'.$request["shartnoma_code"].'</b>' : '<b class="text-danger">kiritilmagan</b>').'</td>';

                                                    echo '<td>';
                                                        echo '<a href="'.$edit_request.'?request_id='.$request['id'].'&page='.$_GET["page"].'" target="_blank" class="tag tag-default tag-warning text-white bg-info">tahrirlash</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a href="requests_1_list.php?code='.$request['code'].'&send_sms=1" target="_blank" class="tag tag-default tag-warning text-white bg-success">sms yuborish</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a href="edit_request.php?request_id='.$request['id'].'&download_zapros=1" target="_blank" class="tag tag-default tag-warning text-white bg-success">zapros</a>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                        echo '<a data-ajax-href="'.$edit_request.'?type=delete_request&request_id='.$request['id'].'&page='.$_GET["page"].'" class="tag tag-default tag-warning text-white bg-danger">o`chirish</a>';
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
                                            $count = (int)$db->assoc("SELECT COUNT(*) FROM requests_1 WHERE id > 0$query ORDER BY id ASC")["COUNT(*)"] / $page_count;
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
        url = url + "direction_id=" + findGetParameter("direction_id")
        url = url + "&learn_type=" + findGetParameter("learn_type");;
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
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
        url = url + "direction_id=" + findGetParameter("direction_id")
        url = url + "&learn_type=" + findGetParameter("learn_type");;
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=" + page;
        // console.log(url);
        window.location = url;
    });

    $("#direction_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + $(this).val();
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#learn_type").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + $(this).val();
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#reg_type").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + $(this).val();
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#payment").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + $(this).val();
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#suhbat").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + $(this).val();
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#submit-date").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + $("#from_date").val();
        url = url + "&to_date=" + $("#to_date").val();
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#send-sms").on("click", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&send_sms=1";
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#firm_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + $("#firm_id").val();
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#course_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + $("#course_id").val();
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#document_type_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + $("#document_type_id").val();
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#contract_type_id").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + $("#contract_type_id").val();
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#contract_payment").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + $("#contract_payment").val();
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#payment_method").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&payment_method=" + $("#payment_method").val();
        url = url + "&shartnoma_date=" + findGetParameter("shartnoma_date");
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });

    $("#shartnoma_date").on("change", function(){
        var url = '<?=$url2[1]?>?';
        url = url + "direction_id=" + findGetParameter("direction_id");
        url = url + "&learn_type=" + findGetParameter("learn_type");
        url = url + "&reg_type=" + findGetParameter("reg_type");
        url = url + "&q=" + findGetParameter("q");
        url = url + "&payment=" + findGetParameter("payment");
        url = url + "&suhbat=" + findGetParameter("suhbat");
        url = url + "&from_date=" + findGetParameter("from_date");
        url = url + "&to_date=" + findGetParameter("to_date");
        url = url + "&firm_id=" + findGetParameter("firm_id");
        url = url + "&course_id=" + findGetParameter("course_id");
        url = url + "&document_type_id=" + findGetParameter("document_type_id");
        url = url + "&contract_type_id=" + findGetParameter("contract_type_id");
        url = url + "&contract_payment=" + findGetParameter("contract_payment");
        url = url + "&payment_method=" + findGetParameter("payment_method");
        url = url + "&shartnoma_date=" + $("#shartnoma_date").val();
        url = url + "&page=1";
        // console.log(url);
        window.location = url;
    });
</script>

<? include('end.php'); ?>