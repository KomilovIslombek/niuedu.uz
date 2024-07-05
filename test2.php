<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

header("Content-type: text/plain");

$requests = $db3->in_array("SELECT id, code, first_name, last_name, father_first_name, created_date FROM requests");

foreach ($requests as $request) {
    $request2 = $db->assoc("SELECT id, code, first_name, last_name, father_first_name, created_date FROM requests WHERE code = ?", [ $request["code"] ]);
    if (!empty($request2["id"])) {
        echo "niiedu_uz: ";
        print_r($request) . "\n";
        echo "niiedu_uz_2: ";
        print_r($request2) . "\n\n";
    }
}
?>