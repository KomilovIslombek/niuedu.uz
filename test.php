<?php
exit;
$is_config = true;
if (empty($load_defined)) include 'load.php';

header("Content-type: text/plain");

$directions_arr = $db->in_array("SELECT * FROM directions");
$directions = [];
foreach ($directions_arr as $direction_arr) {
    $directions[$direction_arr["id"]] = $direction_arr;
}

$requests = $db3->in_array("SELECT * FROM requests");
foreach ($requests as $request) {
    if ($request["birth_date"] == "0000-00-00") $request["birth_date"] = date("Y-m-d H:i:s");
    $db->insert("requests2", $request);
}

$requests = $db3->in_array("SELECT * FROM requests");
foreach ($requests as $request) {
    if ($request["birth_date"] == "0000-00-00") $request["birth_date"] = date("Y-m-d H:i:s");
    $request2 = $db->assoc("SELECT * FROM requests WHERE id = ?", [ $request["id"] ]);

    if (!empty($request2["id"])) {
        if ($request2["birth_date"] == "0000-00-00") $request2["birth_date"] = date("Y-m-d H:i:s");

        $request_id = $request2["id"];
        // $db->delete("requests", $request_id);
        unset($request2["id"]);
        unset($request2["code"]);
        // print_r($request2);
        // exit;
        $new_request_id = $db->insert("requests2", $request2);
        $direction = $directions[$request2["direction_id"]];
        $new_code = idCode($direction["code"], $new_request_id);
        $db->update("requests2", ["code" => $new_code], ["id" => $new_request_id]);

        // $request2["click"] = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
        //     $request_id,
        //     $request2["code"],
        // ]);

        // if (!empty($request2["click"]["id"])) {
        //     $request2["click"]["product_id"] = $new_code;
        //     $request2["click"]["user_id"] = $new_code;
        //     $request2["click"]["merchant_trans_id"] = $new_code;
        //     $db->insert("payments", $request2["click"]);
        // }
    
        // if (empty($request2["click"]["id"])) {
        //     $request2["payme"] = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
        //         $request_id,
        //         $request2["code"]
        //     ]);

        //     if (!empty($request2["payme"]["id"])) {
        //         $request2["payme"]["order_id"] = $new_code;
        //         $db->insert("transactions", $request2["payme"]);

        //         $order = $db->assoc("SELECT * FROM orders WHERE id = ?", [
        //             $request2["code"]
        //         ]);
        //         if (!empty($order["id"])) {
        //             $order["id"] = $new_code;
        //             $order["product_ids"] = $new_code;
        //             $order["user_id"] = $new_code;
        //             $db->insert("orders", $order);
        //         }
        //     }
        // }
    
        // if (empty($request2["payme"]["id"])) {
        //     $request2["kassa"] = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
        //         $request2["id"]
        //     ]);

        //     if (!empty($request2["kassa"]["id"])) {
        //         $request2["kassa"]["order_id"] = $new_code;
        //         $db->insert("payments_kassa_aparat", $request2["kassa"]);
        //     }
        // }



        // 

        // $request2["click"] = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
        //     $request2["id"],
        //     $request2["code"],
        // ]);
    
        // if (empty($request2["click"]["id"])) {
        //     $request2["payme"] = $db->assoc("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
        //         $request2["id"],
        //         $request2["code"]
        //     ]);
        // }
    
        // if (empty($request2["payme"]["id"])) {
        //     $request2["kassa"] = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
        //         $request2["id"]
        //     ]);
        // }

        // 

        // echo "niiedu_uz: ";
        // print_r($request) . "\n";
        // echo "niiedu_uz_2: ";
        // print_r($request2) . "\n\n";
    }
}
?>