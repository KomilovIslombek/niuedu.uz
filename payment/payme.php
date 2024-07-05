<?php
// require($_SERVER['DOCUMENT_ROOT'] . '/modules/payments/paycom-integration-php-template/vendor/autoload.php');
$php_input = file_get_contents('php://input');
if (!empty($php_input)) {
    $php_input = json_decode($php_input, true);

    $db->insert("texts", [
        "url" => implode("/", $url),
        "json" => json_encode($php_input, JSON_UNESCAPED_UNICODE)
    ]);


    $order_id = $php_input["params"]["account"]["order_id"];
    if (!empty($order_id)) {
        $request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $order_id ]);

        $db->insert("texts", [
            "url" => $order_id,
            "json" => json_encode($request, JSON_UNESCAPED_UNICODE)
        ]);

        if (!empty($request["id"])) {
            $amount = 250000;

            $order = $db->assoc("SELECT * FROM orders WHERE id = ?", [ $order_id ]);

            if (empty($order["id"])) {
                $id = $db->insert("orders", [
                    "id" => $order_id,
                    "product_ids" => $order_id,
                    "amount" => number_format($amount, 2, ".", ""),
                    "state" => 1,
                    "user_id" => $order_id,
                    "is_app" => 1
                ]);
            }
        }
    }
}

require($_SERVER['DOCUMENT_ROOT'] . '/modules/payments/paycom-integration-php-template/index.php');
?>