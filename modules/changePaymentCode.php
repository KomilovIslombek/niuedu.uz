<?php
function changePaymentId($old_code, $new_code) {
    global $db, $user_id;

    // transactions changes [payme]
    $transactions = $db->in_array("SELECT * FROM transactions WHERE order_id = ?", [
        $old_code
    ]);

    foreach ($transactions as $transaction) {
        $change_arr = ["order_id" => $new_code];

        $db->insert("transaction_changes", [
            "table2" => "transactions",
            "creator_user_id" => (int)$user_id,
            "change_from" => json_encode($transaction, JSON_UNESCAPED_UNICODE),
            "change_to" =>  json_encode($change_arr, JSON_UNESCAPED_UNICODE),
            "request_id" => $request["id"]
        ]);

        $db->update("transactions", $change_arr, ["id" => $transaction["id"]]);
    }

    // orders changes [payme]
    $orders = $db->in_array("SELECT * FROM orders WHERE user_id = ?", [
        $old_code
    ]);
    foreach ($orders as $order) {
        $change_arr = ["id" => $new_code, "product_ids" => $new_code, "user_id" => $new_code];

        $db->insert("transaction_changes", [
            "table2" => "orders",
            "creator_user_id" => (int)$user_id,
            "change_from" => json_encode($order, JSON_UNESCAPED_UNICODE),
            "change_to" =>  json_encode($change_arr, JSON_UNESCAPED_UNICODE),
            "request_id" => $request["id"]
        ]);

        $db->update("orders", $change_arr, ["id" => $order["id"]]);
    }

    // payments changes [click]
    $payments = $db->in_array("SELECT * FROM payments WHERE user_id = ?", [
        $old_code
    ]);
    foreach ($payments as $payment) {
        $change_arr = ["product_id" => $new_code, "user_id" => $new_code, "merchant_trans_id" => $new_code];

        $db->insert("transaction_changes", [
            "table2" => "payments",
            "creator_user_id" => (int)$user_id,
            "change_from" => json_encode($payment, JSON_UNESCAPED_UNICODE),
            "change_to" =>  json_encode($change_arr, JSON_UNESCAPED_UNICODE),
            "request_id" => $request["id"]
        ]);

        $db->update("payments", $change_arr, ["id" => $payment["id"]]);
    }
}
?>