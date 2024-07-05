<?php
// if ($_SERVER['REMOTE_ADDR'] != "84.54.76.243" && $_SERVER['REMOTE_ADDR'] != "84.54.76.126" && $_SERVER['REMOTE_ADDR'] != "213.230.80.161") {
//     // exit($_SERVER['REMOTE_ADDR']);
//     exit(http_response_code(404));
// }
/*
 * JohnCMS NEXT Mobile Content Management System (http://johncms.com)
 *
 * For copyright and license information, please see the LICENSE.md
 * Installing the system or redistributions of files must retain the above copyright notice.
 *
 * @link        http://johncms.com JohnCMS Project
 * @copyright   Copyright (C) JohnCMS Community
 * @license     GPL-3
 */

define('_IN_JOHNCMS', 1);

require('system/bootstrap.php');

$id = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : 0;

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var PDO $db2 */
$db2 = $container->get(PDO::class);

/** @var Johncms\Api\UserInterface $systemUser */
$systemUser = $container->get(Johncms\Api\UserInterface::class);

/** @var Johncms\Api\ToolsInterface $tools */
$tools = $container->get(Johncms\Api\ToolsInterface::class);

/** @var Johncms\Api\ConfigInterface $config */
$config = $container->get(Johncms\Api\ConfigInterface::class);

$user_id = $systemUser->id;
$rights = $systemUser->rights;

$langs_list = $db->in_array("SELECT * FROM langs_list");
$langs_arr = [];
foreach ($langs_list as $lang_arr) {
    array_push($langs_arr, $lang_arr["flag_icon"]);
}

$REQUEST_URI = $_SERVER["REQUEST_URI"];
if ($_SERVER["QUERY_STRING"]) {
    $REQUEST_URI = explode("?", $REQUEST_URI)[0];
}
  
$url = [];
$fr2url = explode('/', mb_substr(urldecode($REQUEST_URI), 1, mb_strlen(urldecode($REQUEST_URI))));
if ($fr2url){
    foreach($fr2url as $frurl){
        if ($frurl) $url[] = $frurl;
    }
}

$url2 = $url;
array_shift($url);

if (!function_exists("name")) {
    function name($str) {
        return mb_strtolower(
            str_replace(" ", "-", $str)
        );
    }
}

if ($_COOKIE["lang"] != $url2[0] && in_array($url2[0], $langs_arr) || $url2[0] == "en") {
    $lng = $url2[0];
    addCookie("lang", $url2[0]);
}

$all_words = $db->in_array("SELECT * FROM words");
function translate($string){
    global $db, $all_words, $lng;

    $lng2 = $lng == "en" ? "gb" : $lng;

    if ($lng == "uz") {
        return $string;
    } else {
        foreach ($all_words as $word) {
            if (mb_strtolower($word["uz"]) == mb_strtolower($string)) {
                return $word[$lng2];
            }
        }
        return $string;
    }
}

function t($string) {
    return translate($string);
}

function isJson($string) {
    return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
}

function lng($json, $lng_param = false) {
    global $lng;
    $lng2 = $lng == "en" ? "gb" : $lng;

    if ($lng_param) $lng2 = $lng_param;

    if (isJson($json)){
        $json = json_decode($json, true);
        if ($json[$lng2]) {
            return $json[$lng2];
        } else {
            return "";
        }
    } else {
        return $json;
    } 
}

if ($url2[0] == "api") {
    include "api.php";
    exit;
}

// Jamoa turlari 

$team_types = [
    "rektorat" => "Rektorat",
    "yonalish-rahbarlari" => "Yo'nalish rahbarlari",
    "kafedra-mudirlari" => "Kafedra mudirlari", 
    "bolim-markaz-rahbarlari" => "Bolim va Markaz rahbarlari",
    "axborot-resurs-markazi" => "Axborot resurs markazi",
];

// tilni o'zgaruvchiga olib aniq qilib olish

$lng = !empty($_COOKIE["lang"]) ? $_COOKIE["lang"] : "uz";

if (!$url2[0]) {
    header("Location: /$lng/");
}

if (
    !empty($url2[0]) && 
    !in_array($url2[0], $langs_arr) && 
    $url2[0] != "admin" && 
    $url2[0] != "natija_html" && 
    $url2[0] != "natija" && 
    $url2[0] != "login" &&
    $url2[0] != "en" &&
    $url2[0] != "api" &&
    $url2[0] != "shartnoma_html" &&
    $url2[0] != "shartnoma" &&
    $url2[0] != "payment"
) {
    header("Location: "."/$lng".urldecode($_SERVER["REQUEST_URI"]));
    exit;
}

$page = explode(".", $url2[1]);

$permissions = false;
if ($systemUser["permissions"]) {
    $permissions = json_decode($systemUser["permissions"], true);
}

// print_r($permissions);
// exit;

if (
    ($url2[0] == "admin" || $url2[0] == "admin2") && 
    $url2[0] != "login" && $url2[0] != "exit"
) {
    if($permissions && $url2[1]){
        if ($page[0] == "ck_upload_image" && $user_id > 0) {

        } else if (!$permissions || !in_array($page[0], $permissions)) {
            echo "Sizda ushbu sahifaga kirish uchun huquqlar yetarli emas!";
            http_response_code(404);
            exit;
        }
    }
}


if ($url2[0] == "admin2" || $url2[0] == "admin") {
    if (!$url2[1]) {
        $file = "admin_page/index.php";
    } else {
        $file = "admin_page/$url2[1]";
    }
    if (file_exists($file)) {
        // $db2 = $db;

        // if ($url2[0] == "admin") {
        //     $db = $db3; // niiedu_uz
        // } else if ($url2[0] == "admin2") {
        //     $db = $db; // niiedu_uz_2
        // }
         
        include $file;
    }
    exit;
}

$load_defined = true;
if ($url2[0] == "natija_html") {
    include "natija_html.php";
} else if ($url2[0] == "natija") {
    include "natija.php";
} else if ($url2[0] == "shartnoma_html") {
    include "shartnoma_html.php";
} else if ($url2[0] == "login") {
    include "login.php";
} else if ($url[0]) {
    if (!$is_config && file_exists($url[0].".php")) {
        include $url[0].".php";
    } else if ($url2[0] == "payment") {
        if ($url2[1] == "click") {
            include "payment/click.php";
        } else if ($url2[1] == "payme") {
            include "payment/payme.php";
        } else if ($url2[1] == "create") {
            include "payment/create.php";
        }
        // if ($url[1] && file_exists(implode("/", $url).".php")) {
        //     include implode("/", $url) . ".php";
        // }
        // include "system/head.php";
        // include "404-error.php";
    } else if ($url[1]) {
        $post = $db->assoc("SELECT * FROM posts WHERE id_name = ?", [ $url[0] ]);
        if (!empty($post["id"])) {
            $url[1] = $post["id_name"];
            $no_cv_button = true;
            include "post.php";
        }
    }  else {
        include "index.php";
    }
} else {
    include "index.php";
}
?>