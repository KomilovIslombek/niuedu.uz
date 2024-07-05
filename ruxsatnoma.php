<?
// html to pdf module
include "modules/wkhtmltopdf/vendor/autoload.php";
use mikehaertl\wkhtmlto\Pdf;
// 

$is_config = true;
if (empty($load_defined)) include 'load.php';

$html = file_get_contents("files/ruxsatnoma-2.html");

$request_id = $systemUser["request_id"];
if (empty($request_id)) exit(http_response_code(404));

$request = $db->assoc("SELECT * FROM requests WHERE id = ?", [ $request_id ]);
if (empty($request["id"])) exit(http_response_code(404));

// to'lovni tekshirish
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

if (!empty($confirmed_payment_click["id"])) {
    $is_paid = true;
} else if (!empty($confirmed_payment_payme["id"])) {
    $is_paid = true;
} else {
    echo "to'lovni amalga oshirmagansiz!";
    http_response_code(404);
    exit;
}
// 

$direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);
if (empty($direction["id"])) exit(http_response_code(404));

$ruxsatnoma_html = $db->assoc("SELECT * FROM ruxsatnoma_html");
if ($request["exam_lang"] == "uz") {
    $html = $ruxsatnoma_html["html_uz"];
} else if ($request["exam_lang"] == "ru") {
    $html = $ruxsatnoma_html["html_ru"];
}

if (!empty($request["file_id_1"])) $file_1_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_1"] ]);

if (!empty($file_1_arr["id"])) {
    if (file_exists($file_1_arr["file_folder"]  . "_thumb.jpg")) {
        $file_1_arr["file_folder"] = $file_1_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_1_arr["type"] == "pdf") {
            $file_1_arr["file_folder"] = "pdf/" . encode($file_1_arr["file_folder"]);
        } else {
            $file_1_arr["file_folder"] = $file_1_arr["file_folder"];
        }
    }
}

$type = pathinfo($file_1_arr["file_folder"], PATHINFO_EXTENSION);
$data = file_get_contents($file_1_arr["file_folder"]);
$base64_image = 'data:image/' . $type . ';base64,' . base64_encode($data);

$YEAR = "2022/2023-o‘quv yili uchun qabul";
$ID = $request["code"];
$FIRST_NAME = $request["first_name"];
$LAST_NAME = $request["last_name"];
$FATHER_FIRST_NAME = $request["father_first_name"];
$PASSORT_SERIYA = mb_substr($request["passport_serial_number"], 0, 2);
$PASSPORT_NUMBER = mb_substr($request["passport_serial_number"], 2);
$BIRTH_DATE = date("d.m.Y", strtotime($request["birth_date"]));

$TEST_REGION_AND_VISIT_TIME = "Toshkent shahri, 15-avgust, soat 06:00";
$TEST_REGION_AND_DISTRICT = "Toshkent shahri, Shayxontohur tumani";
$TEST_LOCATION = "Toshkent, Paxtakor futbol klubi 4-sektor";
$TEST_GROUP = "1 - smena, 67 - guruh.";

$sciences = json_decode($direction["sciences"], true);

if ($request["exam_lang"] == "uz") {
    $TALIM_TILI = "o‘zbekcha.";

    if ($sciences["uz"] && count($sciences["uz"]) > 1) {
        $fanlar = $sciences["uz"];
    } else {
        $fanlar = $sciences["ru"];
    }
} else if ($request["exam_lang"] == "ru") {
    $TALIM_TILI = "ruscha.";

    if ($sciences["ru"] && count($sciences["ru"]) > 1) {
        $fanlar = $sciences["ru"];
    } else {
        $fanlar = $sciences["uz"];
    }
}

$TEST_ALIFBO = "lotin";

if ($request["exam_foreign_lang"] == "ar") {
    $CHET_TILI = "arab";
} else if ($request["exam_foreign_lang"] == "en") {
    $CHET_TILI = "ingiliz";
}

$FAN_1 = $fanlar[0];
$FAN_2 = $fanlar[1];
$FAN_3 = $fanlar[2];
$FAN_4 = $fanlar[3];
$FAN_5 = $fanlar[4];

$html = strtr($html, [
    "#BASE_64_IMAGE" => $base64_image,

    "#YEAR" => $YEAR,

    "#ID" => $ID,
    "#LAST_NAME" => $LAST_NAME,
    "#FIRST_NAME" => $FIRST_NAME,
    "#FATHER_FIRST_NAME" => $FATHER_FIRST_NAME,
    "#PASSPORT_SERIYA" => $PASSORT_SERIYA,
    "#PASSPORT_NUMBER" => $PASSPORT_NUMBER,
    "#BIRTH_DATE" => $BIRTH_DATE,

    "#FAN_1" => $FAN_1,
    "#FAN_2" => $FAN_2,
    "#FAN_3" => $FAN_3,
    "#FAN_4" => $FAN_4,
    "#FAN_5" => $FAN_5,

    "#TEST_REGION_AND_VISIT_TIME" => $TEST_REGION_AND_VISIT_TIME,
    "#TEST_REGION_AND_DISTRICT" => $TEST_REGION_AND_DISTRICT,
    "#TEST_LOCATION" => $TEST_LOCATION,
    "#TEST_GROUP" => $TEST_GROUP,

    "#TALIM_TILI" => $TALIM_TILI,
    "#TEST_ALIFBO" => $TEST_ALIFBO,
    "#CHET_TILI" => $CHET_TILI,

    "#LEARN_TYPE" => $request["learn_type"],
    "#UNERSITY_NAME" => "Navoiy innovatsiyalar universiteti",
    "#DIRECTION_NAME" => $direction["name"]
]);

$db->insert("ruxsatnoma_logs", [
    "request_id" => $request["id"],
    "method" => $url[1],
    "user_id" => $user_id
]);

if ($url[1] == "view") {
    echo $html;
} else if ($url[1] == "download") {
    // You can pass a filename, a HTML string, an URL or an options array to the constructor
    $pdf = new Pdf($html);

    // On some systems you may have to set the path to the wkhtmltopdf executable
    // $pdf->binary = 'C:\...';

    // if (!$pdf->saveAs('/path/to/page.pdf')) {
    //     $error = $pdf->getError();
    //     // ... handle error here
    // }

    if (!$pdf->send()) {
        $error = $pdf->getError();
    }
}
?>