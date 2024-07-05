<?php
$domain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");

function sendSms($phone, $message) {
    global $db, $domain;

    $phone = str_replace("+", "", $phone);
    $phone = str_replace("-", "", $phone);

    $today_sended_messages_count = (int)$db->assoc("SELECT COUNT(*) FROM sms WHERE DATE(created_date) = '2022-08-15' AND res = 'Request is received'")["COUNT(*)"];

    if ($today_sended_messages_count == 10) return;

    $message_id = $db->insert("sms", [
        "phone" => $phone,
        "message" => $message
    ]);

    $json_request = [
        "messages" => [
            [
                "recipient" => $phone,
                "message-id" => $message_id,
                "sms" => [
                    "originator" => "3700",
                    "content" => [
                        "text" => $message
                    ]
                ]
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://91.204.239.44/broker-api/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($json_request, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic aW5ub3ZhdHNpeWFsYXJpbnN0aXR1dDpMbUc3QjdtTjR5YzY='
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $sms_id = $db->update("sms", [
        "req" => json_encode($json_request, JSON_UNESCAPED_UNICODE),
        "res" => $response
    ], [
        "id" => $message_id
    ]);

    return $sms_id;
}

function bot($method, $callback_datas=[]){
    define("key", "5471695430:AAFEEeYwt3dw_IQIklxYOVyB9trVAewBncc");
    $url = "https://api.telegram.org/bot".key."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $callback_datas);
    $res = curl_exec($ch);

    if (curl_error($ch)) {
      var_dump(curl_error($ch));
    } else {
      $res_arr = json_decode($res, true);
      return $res_arr;
    }
}

function sendMessage($phone, $message) {
    bot("sendMessage", [
        "chat_id" => 166975358,
        "text" => $message,
        "parse_mode" => "html"
    ]);
}
?>