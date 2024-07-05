<?
date_default_timezone_set("Asia/Tashkent");

$is_config = true;
if (empty($load_defined)) include 'load.php';

if (!$url[3]) {
    exit(http_response_code(404));
} else {
    $order_id = decode($url[3]);

    $request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $order_id ]);
    if (empty($request["id"])) {exit(http_response_code(404));}
}


if ($url[2] == "click") {
    $order_id = decode($url[3]);
    if ($order_id == "5010350") {
        $AMOUNT = "1000.00";
    } else {
        $AMOUNT = "250000.00";
    }
    $MERCHANT_ID = "17367";
    $SECRET_KEY = "KysCAZ0PbWykdgQ";
    $MERCHANT_USER_ID = "28061";
    $SERVICE_ID = "24952";
    // $transaction_id = "3";
    // $order_id = $old_request["id"];
    $sign_time = date("Y-m-d H:i:s");
    
    $sign_string = md5( $order_id . $SERVICE_ID . $SECRET_KEY . $order_id . $AMOUNT . $action . $sign_time);
    
    $db->insert("payments", [
        "product_id" => $order_id,
        "status" => "created",
        "currency" => "uzs",
        "total" => number_format( $AMOUNT, 0, '.', '' ),
        "amount" => $AMOUNT,
        "user_id" => $order_id,
        "merchant_trans_id" => $order_id
    ]);
    
    $payment_url = "https://my.click.uz/services/pay?amount=$AMOUNT&merchant_id=$MERCHANT_ID&merchant_user_id=$MERCHANT_USER_ID&service_id=$SERVICE_ID&transaction_param=$order_id&return_url=" . urlencode("$domain/cv/success/".encode($order_id)."/");

    header("Location: $payment_url");
} else if ($url[2] == "payme") {
    $checkout_url = "https://checkout.paycom.uz";
    // $checkout_url = "https://checkout.test.paycom.uz"; // test
    $order_id = decode($url[3]);
    if ($order_id == "2010341") {
        $amount = 1000;
    } else {
        $amount = 250000;
    }
    $sum = $amount * 100; // 1,000 so'm
    $merchant_id = "63046b787066d254af79528e"; // 
    // $merchant_id = "62bae86eb2a26248fca75666";
    $callbackUrl = "$domain/cv/success/".encode($order_id)."/";
    $lang = "uz";
    $description = "NAVOIY INNOVATSIYALAR INSTITUTIGA ARIZA UCHUN TO'LOV";

    $order = $db->assoc("SELECT * FROM orders WHERE id = ?", [ 
        $order_id
    ]);


    if (empty($order["id"])) {
        $order_id = $db->insert("orders", [
            "id" => $order_id,
            "product_ids" => $order_id,
            "amount" => number_format($amount, 2, ".", ""),
            "state" => 1,
            "user_id" => $order_id
        ]);
    } else {
        $order_id = $order["id"];
    }

    $form = <<<FORM
    <form action="{$checkout_url}" method="POST" id="payme_form">
    <input type="hidden" name="account[order_id]" value="$order_id">
    <input type="hidden" name="amount" value="$sum">
    <input type="hidden" name="merchant" value="{$merchant_id}">
    <input type="hidden" name="callback" value="{$callbackUrl}">
    <input type="hidden" name="lang" value="$lang">
    <input type="hidden" name="description" value="$description">
    <input type="submit" class="button alt" id="submit_payme_form" value="To'lovni amalga oshirish">
    </form>
    FORM;

    echo $form;

    echo '<script>document.getElementById("payme_form").submit();</script>';
}
?>