<?php
date_default_timezone_set("Asia/Tashkent");

$is_config = true;
if (empty($load_defined)) include "load.php";

$json = decode($url[1]);
$json = json_decode($json, true);
$id = $json["c"];
if (empty($id)) exit(http_response_code(404));

$agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $id ]);

if (empty($agent["id"])) {
    exit(http_response_code(404));
}

$url = "https://niuedu.uz:4499/" . encode(json_encode([
    "s" => "a", // zapros
    "c" => $agent["id"] // code
]));
header("Location: $url");
exit;
?>