<?php
date_default_timezone_set("Asia/Tashkent");

$is_config = true;
if (empty($load_defined)) include "load.php";

$json = decode($url[1]);
$json = json_decode($json, true);
$id = $json["id"];
if (empty($id)) exit(http_response_code(404));

$agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $id ]);

if (empty($agent["id"])) {
    exit(http_response_code(404));
}

$url = "https://niuedu.uz:4499/" . encode(json_encode([
    "s" => "a", // zapros
    "c" => $agent["id"] // code
]));
exit($url);
header("Location: $url");
exit;

$html = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/files/agent-shartnoma-2.html");

$months = [
    "yanvar", "fеvral", "mart", "aprеl", "may", "iyun", "iyul", "avgust", "sеntyabr", "oktyabr", "noyabr", "dеkabr"
];


$res = strtr($html, [
    "#STUDENT_ID" => $agent["id"],
    "#FIRST_NAME" => $agent["first_name"],
    "#LAST_NAME" => $agent["last_name"],
    "#FATHER_FIRST_NAME" => $agent["father_first_name"],
    "#DAY" => date("d", strtotime($agent["created_date"])),
    "#MONTH_NAME" => $months[date("m", strtotime($agent["created_date"]))-1],
    "#YEAR" => date("Y", strtotime($agent["created_date"])),
]);

echo $res;
?>