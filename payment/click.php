<?php
if ($_REQUEST) {
    $db->insert("texts", [
        "url" => implode("/", $url),
        "json" => json_encode($_REQUEST, JSON_UNESCAPED_UNICODE)
    ]);

    if ($_REQUEST["merchant_trans_id"]) {
        $request = $db->assoc("SELECT * FROM requests WHERE code = ? AND suhbat = 1", [ $_REQUEST["merchant_trans_id"] ]);

        if (!empty($request["id"])) {
            $order_id = $_REQUEST["merchant_trans_id"];
            $AMOUNT = "250000.00";
            
            $db->insert("payments", [
                "product_id" => $order_id,
                "status" => "created",
                "currency" => "uzs",
                "total" => number_format( $AMOUNT, 0, '.', '' ),
                "amount" => $AMOUNT,
                "user_id" => $order_id,
                "merchant_trans_id" => $order_id,
                "is_app" => 1
            ]);
        }
    }
}

require($_SERVER['DOCUMENT_ROOT'] . '/modules/payments/click-integration-php/vendor/autoload.php');

use click\applications\Application;
use click\models\Payments;

Application::session('JKhkjANmjHAJjbnKAhA', ['/payment/click/prepare', '/payment/click/complete'], function(){
    $payments = new Payments([
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=niiedu_uz_2',
            'username' => 'niiedu_uz',
            'password' => 'yzxR5f9dbU7IOsrO'
        ]
    ]);
    $application = new Application([
        'type' => 'json',
        'model' => $payments,
        'configs' => [
            'click' => [
                'merchant_id' => '17367',
                'service_id' => '24952',
                'user_id' => '28061',
                'secret_key' => 'KysCAZ0PbWykdgQ'
            ]
        ]
    ]);
    $application->run();
});
?>