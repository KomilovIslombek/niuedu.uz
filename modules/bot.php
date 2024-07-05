<?php
function sendDocument($telegram_id, $file_path, $caption, $reply_to_message_id = false) {
    global $db, $request_id;
    $token = "5471695430:AAFEEeYwt3dw_IQIklxYOVyB9trVAewBncc";
    // Create CURL object
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$token."/sendDocument");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Create CURLFile
    $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);
    $cFile = new CURLFile($file_path, $finfo);

    $post_data = [
        "document" => $cFile,
        "chat_id" => $telegram_id,
        "caption" => $caption,
        "parse_mode" => "html",
    ];

    if ($reply_to_message_id) {
        $post_data["reply_to_message_id"] = $reply_to_message_id;
    }

    // Add CURLFile to CURL request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    // Call
    $res = curl_exec($ch);
    $res_arr = json_decode($res, true);
    
    $db->insert("bot_messages", [
        "user_id" => $telegram_id,
        "method" => "sendDocument",
        "callback_datas" => json_encode($post_data, JSON_UNESCAPED_UNICODE),
        "res" => $res,
        "message_id" => $res_arr["result"]["message_id"],
        "limit_count" => 0,
        "time" => time(),
        "request_id" => $request_id
    ]);

    return json_decode($res, true);

    // Show result and close curl
    // var_dump($result);
    curl_close($ch);
}

function bot($method, $callback_datas=[]){
    global $db, $request_id;
    
    define("api_key", "5471695430:AAFEEeYwt3dw_IQIklxYOVyB9trVAewBncc");

    $url = "https://api.telegram.org/bot".api_key."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $callback_datas);

    $res = curl_exec($ch);
    $res_arr = json_decode($res, true);

    $db->insert("bot_messages", [
        "user_id" => $callback_datas["chat_id"],
        "method" => $method,
        "callback_datas" => json_encode($callback_datas, JSON_UNESCAPED_UNICODE),
        "res" => $res,
        "message_id" => $res_arr["result"]["message_id"],
        "limit_count" => 0,
        "time" => time(),
        "request_id" => $request_id
    ]);

    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        $res_arr = json_decode($res, true);
    }
    return $res_arr;
}
?>