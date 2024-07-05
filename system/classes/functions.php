<?php
function error($code) {
    switch ($code) {
        case "404":
            // include realpath("404-error.php");
            http_response_code(404);
            exit();
    }
}

function addApi($method, $method_name, $id = null, $page = '', $token = null) {
    global $db;
    if($token === null) {
        $other_site_res = $db->assoc("SELECT * FROM other_site_res");
        $token = $other_site_res["token"];
    }
    $curl = curl_init();

    curl_setopt_array($curl, array(
    // CURLOPT_URL => 'https://api.mentalaba.uz/v1/university-dashboard/accepted-applications',
    CURLOPT_URL => 'https://api.mentalaba.uz/v1/'.$method_name. ($id ? '/'. $id : ''),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => array(
        'page: '.$page,
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

function getTokenApi() {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.mentalaba.uz/v1/auth/admin/login',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"Origin": "nii.mentalaba.uz", "email": "info@nii.uz", "password": "niu13332308"}',
      CURLOPT_HTTPHEADER => array(
        'Origin: nii.mentalaba.uz',
        'Content-Type: application/json',
        'login: info@nii.uz',
        'password: niu13332308',
        'Authorization: Basic aW5mb0BuaWkudXo6bml1MTMzMzIzMDg='
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
}



function image($image_id) {
    global $db;
    $image_arr = [
        "id" => 0,
        "width" => 404,
        "height" => 404,
        "size" => 404,
        "file_folder" => "images/404.jpg",
        "created_date" => date("Y-m-d H:i:s"),
        "updated_date" => date("Y-m-d H:i:s"),
    ];

    $image_id = (int)$image_id;
    if ($image_id) {
        $image = $db->assoc("SELECT * FROM `images` WHERE id = $image_id LIMIT 1");
        if (file_exists($image["file_folder"]) || file_exists("../".$image["file_folder"])) {
            $image_arr = $image;
        }
    }

    return $image_arr;
}


function delete_file($file_id) {
    global $db;
    if (!$file_id) return;
    $file = fileArr($file_id);
    if ($file["id"] > 0) {
        if ($file["thumb_image_id"] > 0) {
            delete_image($file["thumb_image_id"]);
        }
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/". $file["file_folder"])) {
            unlink($_SERVER["DOCUMENT_ROOT"] . "/".$file["file_folder"]);
        }
        $db->delete("files", $file_id);
    }
}

function video($video_id) {
    global $db;
    $video_arr = [
        "id" => 0,
        "creator_user_id" => 0,
        "width" => 0,
        "height" => 0,
        "size" => 0,
        "duration" => 0,
        "file_folder" => "",
        "file_info" => json_encode(array()),
        "created_date" => date("Y-m-d H:i:s"),
        "updated_date" => date("Y-m-d H:i:s"),
    ];

    $video_id = (int)$video_id;
    if ($video_id) {
        $video = $db->assoc("SELECT * FROM `videos` WHERE id = $video_id LIMIT 1");
        if (file_exists($video["file_folder"]) || file_exists("../".$video["file_folder"])) {
            $video_arr = $video;
        }
    }
    return $video_arr;
}

function audio($audio_id) {
    global $db;
    $audio_arr = [];

    $audio_id = (int)$audio_id;
    if ($audio_id) {
        $audio = $db->assoc("SELECT * FROM audios WHERE id = $audio_id");
        if (file_exists($audio["file_folder"]) || file_exists("../".$audio["file_folder"])) {
            $audio_arr = $audio;
        }
    }
    return $audio_arr;
}

/** 
 * Convert number of seconds into hours, minutes and seconds 
 * and return an array containing those values 
 * 
 * @param integer $inputSeconds Number of seconds to parse 
 * @return array 
 */ 

function secondsToTime($inputSeconds, $no_seconds = false) {
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;

    // Extract days
    $days = floor($inputSeconds / $secondsInADay);

    // Extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // Extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // Extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // Format and return
    $timeParts = [];
    $sections = [
        'kun' => (int)$days,
        'soat' => (int)$hours,
        'daqiqa' => (int)$minutes
    ];

    if (!$no_seconds) {
        $sections['soniya'] = (int)$seconds;
    }

    foreach ($sections as $name => $value){
        if ($value > 0){
            $timeParts[] = $value. ' '.$name.($value == 1 ? '' : '');
        }
    }

    return implode(', ', $timeParts);
}

function getAvatar($user) {
    if ($user["id"]) {
        if ($user['profileimg'] && file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $user['profileimg'])) {
            return $user['profileimg'];
        } else {
            $nm = mb_strtoupper($user["first_name"][0].$user["last_name"][0]);
            return "profileimg.php?n=$nm&c=blue";
        }
    } else {
        return "theme/main/assets/img/logo/logo-icon.png";
    }
}

function encrypt_decrypt($action, $string) {
    if (!$string) return;
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'YWd#chtYWRqb25=';
    $secret_iv = 'QpWFkeade2GZ3F6ahlldg==';
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function encode($str) {
    return encrypt_decrypt("encrypt", $str);
}

function decode($str) {
    return encrypt_decrypt("decrypt", $str);
}

$domain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");

function addCookie($key, $val) {
    global $domain;
    $_COOKIE[$key] = $val;
    setcookie($key, $val, time() + (3600 * 24 * 365 * 5), "/");
}

function removeCookie($key, $id = 0) {
    // if ($key == "phone") exit($id);
    unset($_COOKIE[$key]);
    setcookie($key, '', time()-100000);
    setcookie($key, '', time()-100000, '/');
}

function getUser($user_id) {
    global $db;
    $user = $db->assoc("SELECT * FROM users WHERE id = ?", [ $user_id ]);

    if ($user["id"]) {
        $user["full_name"] = $user["first_name"] . " " . $user["last_name"];
        return $user;
    } else {
        return false;
    }
}

function isAuth() {
    global $user_id;

    if (!empty($user_id) && $user_id > 0) {
        return true;
    } else {
        return false;
    }
}

function multiexplode($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return array_map(function($v){return trim($v);}, $launch);
}

function idCode($direction_id, $request_id) {
    $direction_id = (string)$direction_id;
    $request_id = (string)$request_id;

    if (strlen($request_id) == 3) {
        $direction_id = substr($direction_id, 0, 4);
    } else if (strlen($request_id) == 4) {
        $direction_id = substr($direction_id, 0, 3);
    } else if (strlen($request_id) == 5) {
        $direction_id = substr($direction_id, 0, 3);
    }

    return $direction_id . $request_id;
}

function parseID($id) {
    $id = (string)$id;
    if (strlen($id) == 8) {
        return [
            "direction_code" => substr($id, 0, 5),
            "request_id" => substr($id, 5)
        ];
    } else if (strlen($id) == 7) {
        return [
            "direction_code" => substr($id, 0, 4) . "0",
            "request_id" => substr($id, 5)
        ];
    }
}

$errors = [];
function validate($forms) {
    global $errors;
    foreach ($forms as $form) {
        if (empty($_POST[$form])) {
            $errors["forms"][$form][] = "$form ni kiritishni unutdingiz!";
        }
    }
}

function getError($formName) {
    global $errors;

    if ($errors["forms"][$formName] && count($errors["forms"][$formName]) > 0) {
        foreach ($errors["forms"][$formName] as $errorText) {
            echo '<h4 class="text-danger">'.$errorText.'</h4>';
        }
    }
}

function formatBytes($size, $precision = 2){
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   
    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

function fileArr($file_id) {
    global $db;
    $file_arr = [];

    $file_id = (int)$file_id;
    if ($file_id) {
        $file = $db->assoc("SELECT * FROM files WHERE id = $file_id");
        if (file_exists($file["file_folder"]) || file_exists("../".$file["file_folder"])) {
            $file_arr = $file;
        }
    }
    return $file_arr;
}

// 3010 544
?>