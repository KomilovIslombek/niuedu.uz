<?php
date_default_timezone_set("Asia/Tashkent");

$is_config = true;
if (empty($load_defined)) include "load.php";
include "modules/num_to_text.php";
include "modules/phpqrcode/qrlib.php";
$json = decode($url2[1]);
$json = json_decode($json, true);
$code = $json["c"];
$shartnoma = ($json["s"] ? $json["s"] : 2);
if (empty($code)) exit(http_response_code(404));

if ($shartnoma == "a") {
    $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $code ]);

    if (empty($agent["id"])) {
        exit(http_response_code(404));
    }
} else {
    $request = $db->assoc("SELECT * FROM requests WHERE code = ? AND suhbat = 1", [ $code ]);

    if (empty($request["id"])) {
        exit(http_response_code(404));
    }
}
// if (empty($request["id"])) {
//     $request = $db3->assoc("SELECT * FROM requests WHERE code = ? AND suhbat = 1", [ $code ]);
// }

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);

if ($shartnoma == 2) {
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/files/shartnoma4.html");
} else if ($shartnoma == 3) {
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/files/3-tamonlama-shartnoma.html");
} else if ($shartnoma == "z") {
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/files/zapros.html");
} else if ($shartnoma == "a") {
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/files/agent-shartnoma-2.html");
}

$months = [
    "yanvar", "fеvral", "mart", "aprеl", "may", "iyun", "iyul", "avgust", "sеntyabr", "oktyabr", "noyabr", "dеkabr"
];

$months_ru = [
    "январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"
];

// QR CODE
$target_dir = "images/qr/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$qr_file_path = $target_dir.uniqid().".png";

if ($shartnoma == "a") {
    $qr_json = json_encode([
        "s" => $shartnoma,
        "c" => $agent["id"]
    ], JSON_UNESCAPED_UNICODE);
} else {
    $qr_json = json_encode([
        "s" => $shartnoma,
        "c" => $request["code"]
    ], JSON_UNESCAPED_UNICODE);
}

$domain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");

$qr_code = "$domain/shartnoma/" . encode($qr_json);
if ($shartnoma == "a") {
    $qr_code = "$domain/agent-shartnoma/" . encode($qr_json);
}
QrCode::png($qr_code, $qr_file_path, 'L', 18);

$base64_qr = "data:image/png;base64," . base64_encode(file_get_contents($qr_file_path));
if (file_exists($qr_file_path)) unlink($qr_file_path);

// echo $base64_qr;

if ($shartnoma == "a") {
    $qr_html = '<img src="'.$base64_qr.'" style="width: 165px;left: 750px;bottom: 220px;position: absolute;">';
} else if ($shartnoma == 2) {
    $qr_html = '<img src="'.$base64_qr.'" style="width: 160px;left: 510px;bottom: 928px;position: absolute;">';
} else if ($shartnoma == 3) {
    $qr_html = '<img src="'.$base64_qr.'" style="width: 140px;left: 437px;bottom: 28px;position: absolute;">';
}
// END QR CODE

if ($shartnoma == "a") {
    $res = strtr($html, [
        "#STUDENT_ID" => $agent["id"],
        "#FIRST_NAME" => $agent["first_name"],
        "#LAST_NAME" => $agent["last_name"],
        "#FATHER_FIRST_NAME" => $agent["father_first_name"],
        "#DAY" => date("d", strtotime($agent["created_date"])),
        "#MONTH_NAME" => $months[date("m", strtotime($agent["created_date"]))-1],
        "#YEAR" => date("Y", strtotime($agent["created_date"])),
        "#QR_IMAGE" => $qr_html,
    ]);
    
    echo $res;
    exit;
}

$shartnoma_date = ($request["shartnoma_date"] ? $request["shartnoma_date"] : date("Y-m-d"));
if (empty($request["shartnoma_date"])) {
    $db->update("requests", ["shartnoma_date" => date("Y-m-d")], ["code" => $request["code"]]);
    // $db3->update("requests", ["shartnoma_date" => date("Y-m-d")], ["code" => $request["code"]]);
}

// $course_arr = $db->assoc("SELECT * FROM courses WHERE id = ?", [ $request["course_id"] ]);
// $course = $course_arr["name"] ? $course_arr["name"] : ($request["to_course"] ? $request["to_course"] : "1-kurs");

$course = $request["to_course"];

if ($request["learn_type"] == "Kunduzgi") {
    $oqish_davomiyligi_yili = lng($direction["kunduzgi_oqish_muddati"], "uz");
    $contract_amount = $direction["kunduzgi_narx_int"];
} else if ($request["learn_type"] == "Kechki") {
    $oqish_davomiyligi_yili = lng($direction["kechki_oqish_muddati"], "uz");
    $contract_amount = $direction["kechki_narx_int"];
} else if ($request["learn_type"] == "Sirtqi") {
    $oqish_davomiyligi_yili = lng($direction["sirtqi_oqish_muddati"], "uz");
    $contract_amount = $direction["sirtqi_narx_int"];
}

// if ($course == "1-kursning 2-semestri" || $course == "2-kursning 4-semestri") {
//     if ($direction["sirtqi_narx_int"] == 13000000) {
//         $contract_amount = 6500000;
//     } else if ($direction["sirtqi_narx_int"] == 11000000) {
//         $contract_amount = 5500000;
//     } else if ($direction["sirtqi_narx_int"] == 15000000) {
//         $contract_amount = 7500000;
//     }
// }

if (!empty($request["shartnoma_amount"])) $contract_amount = $request["shartnoma_amount"];

$oqish_davomiyligi_yili = str_replace(" ", "", $oqish_davomiyligi_yili);
$oqish_davomiyligi_yili = str_replace("yil", "", $oqish_davomiyligi_yili);

if ($shartnoma == "z") {
    $shartnoma_date = $request["zapros_date"];
}

$course_text = str_replace("kurs", "", str_replace("", "", str_replace("-", "", $course)));

if ($request["to_course"] == "1-kursning 2-semestri" || $request["to_course"] == "2-kursning 4-semestri") {
    $course_text = $request["to_course"];
} else {
    $course_text .= "-kurs";
}

$res = strtr($html, [
    "#SHARTNOMA_CODE" => "$direction[prefix]-$request[code]/" . ($request["learn_type"] == "Sirtqi" ? $course_num."S" : $course_num."K"),
    "#DIRECTION_PREFIX" => $direction["prefix"],
    "#STUDENT_CODE" => $request["code"],
    "#LEARN_TYPE_SHORT" => $request["learn_type"] == "Sirtqi" ? "S" : "K",
    "#DAY" => date("d", strtotime($shartnoma_date)),
    "#MONTH_NAME" => $months[date("m", strtotime($shartnoma_date))-1],
    "#YEAR" => date("Y", strtotime($shartnoma_date)),
    "#NEXT_YEAR" => date("Y", strtotime($shartnoma_date)) + 1,
    "#FIRST_NAME" => $request["first_name"],
    "#LAST_NAME" => $request["last_name"],
    "#FATHER_FIRST_NAME" => $request["father_first_name"],
    "#COURSE" =>  $course_text,
    "#PASSPORT_SERIAL_NUMBER" => mb_strtoupper($request["passport_serial_number"]),
    "#ACADEMIC_LEVEL" => lng($direction["academic_level"], "uz"),
    "#LEARN_TYPE" => $request["learn_type"],
    "#LEARN_YEAR" => $oqish_davomiyligi_yili,
    "#LEARN_YEAR_TEXT" => num_to_text($oqish_davomiyligi_yili),
    "#DIRECTION_NAME" => lng($direction["name"], "uz"),
    "#CONTRACT_AMOUNT_IN_WORDS" => num_to_text($contract_amount),
    "#CONTRACT_AMOUNT" => number_format($contract_amount, 0, "", " "),
    "#QR_IMAGE" => $qr_html,

    // zapros uchun
    "#ZAPROS_YEAR" => date("Y", strtotime($request["zapros_date"])),
    "#ZAPROS_MONTH_NAME" => $months_ru[date("m", strtotime($request["zapros_date"]))-1],
    "#ZAPROS_DAY" => date("d", strtotime($request["zapros_date"])),
    "#ZAPROS_UNIVERSITY_NAME" => $request["zapros_university_name"],
    "#ZAPROS_REKTOR_FULL_NAME" => $request["zapros_rektor_full_name"]
]);

echo $res;
?>