<?php

// if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
//     header('Location: http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'').'://' . substr($_SERVER['HTTP_HOST'], 4).$_SERVER['REQUEST_URI']);
//     exit;
// }

date_default_timezone_set('Asia/Tashkent');

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

defined('_IN_JOHNCMS') or die('Error: restricted access');

/** @var Psr\Container\ContainerInterface $container */
$container = App::getContainer();

/** @var PDO $db2 */
$db2 = $container->get(PDO::class);

/** @var Johncms\Api\ToolsInterface $tools */
$tools = $container->get(Johncms\Api\ToolsInterface::class);

/** @var Johncms\Api\EnvironmentInterface $env */
$env = $container->get(Johncms\Api\EnvironmentInterface::class);

/** @var Johncms\Api\UserInterface $systemUser */
$systemUser = $container->get(Johncms\Api\UserInterface::class);

/** @var Johncms\Api\ConfigInterface $config */
$config = $container->get(Johncms\Api\ConfigInterface::class);

// $act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
// $headmod = isset($headmod) ? $headmod : '';
// $textl = isset($textl) ? $textl : $config['copyright'];
// $keywords = isset($keywords) ? htmlspecialchars($keywords) : $config->meta_key;
// $descriptions = isset($descriptions) ? htmlspecialchars($descriptions) : $config->meta_desc;


// Фиксация местоположений посетителей
$sql = '';
$set_karma = $config['karma'];

$user_id = $systemUser->id;
$rights = $systemUser->rights;

if ($systemUser["id"]) {
    // Фиксируем местоположение авторизованных
    if (!$systemUser->karma_off && $set_karma['on'] && $systemUser->karma_time <= (time() - 86400)) {
        $sql .= " `karma_time` = " . time() . ", ";
    }

    $movings = $systemUser->movings;

    if ($systemUser->lastdate < (time() - 300)) {
        $movings = 0;
        $sql .= " `sestime` = " . time() . ", ";
    }

    if ($systemUser->place != $headmod) {
        ++$movings;
        $sql .= " `place` = " . $db2->quote($headmod) . ", ";
    }

    if ($systemUser->browser != $env->getUserAgent()) {
        $sql .= " `browser` = " . $db2->quote($env->getUserAgent()) . ", ";
    }

    $totalonsite = $systemUser->total_on_site;

    if ($systemUser->lastdate > (time() - 300)) {
        $totalonsite = $totalonsite + time() - $systemUser->lastdate;
    }
    
    $db->update("users", [
        "movings" => $movings,
        "total_on_site" => $totalonsite,
        "lastdate" => time(),
    ], [
        "id" => $systemUser["id"]
    ]);
} else {
    // Фиксируем местоположение гостей
    $movings = 0;
    $session = md5($env->getIp() . $env->getIpViaProxy() . $env->getUserAgent());
    $req = $db2->query("SELECT * FROM `cms_sessions` WHERE `session_id` = " . $db2->quote($session) . " LIMIT 1");

    if ($req->rowCount()) {
        // Если есть в базе, то обновляем данные
        $res = $req->fetch();
        $movings = ++$res['movings'];

        if ($res['sestime'] < (time() - 300)) {
            $movings = 1;
            $sql .= " `sestime` = '" . time() . "', ";
        }

        if ($res['place'] != $headmod) {
            $sql .= " `place` = " . $db2->quote($headmod) . ", ";
        }

        $db2->exec("UPDATE `cms_sessions` SET $sql
            `movings` = '$movings',
            `lastdate` = '" . time() . "'
            WHERE `session_id` = " . $db2->quote($session) . "
        ");
    } else {
        // Если еще небыло в базе, то добавляем запись
        $db2->exec("INSERT INTO `cms_sessions` SET
            `session_id` = '" . $session . "',
            `ip` = '" . $env->getIp() . "',
            `ip_via_proxy` = '" . $env->getIpViaProxy() . "',
            `browser` = " . $db2->quote($env->getUserAgent()) . ",
            `lastdate` = '" . time() . "',
            `sestime` = '" . time() . "',
            `place` = " . $db2->quote($headmod) . "
        ");
    }
}

$full_link = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");


$urls = implode("/", $url);
?>

<!DOCTYPE html>
    <html lang="<?=$lng?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <base href="<?=$domain?>">
        <title>Navoiy innovatsiyalar universiteti</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Place favicon.ico in the root directory -->
        <link rel="shortcut icon" type="image/x-icon" href="theme/main/assets/img/favicon.png">
        <!-- CSS here -->
        
        <link rel="stylesheet" href="theme/main/assets/css/preloader.css">
        <link rel="stylesheet" href="theme/main/assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="theme/main/assets/css/meanmenu.css">
        <link rel="stylesheet" href="theme/main/assets/css/animate.min.css">
        <link rel="stylesheet" href="theme/main/assets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="theme/main/assets/css/swiper-bundle.css">
        <link rel="stylesheet" href="theme/main/assets/css/backToTop.css">
        <link rel="stylesheet" href="theme/main/assets/css/jquery.fancybox.min.css">
        <link rel="stylesheet" href="theme/main/assets/css/fontAwesome5Pro.css">
        <link rel="stylesheet" href="theme/main/assets/css/elegantFont.css">
        <link rel="stylesheet" href="theme/main/assets/css/default.css">
        <link rel="stylesheet" href="theme/main/assets/css/style.css?v=<?=filemtime("theme/main/assets/css/style.css")?>">
        <link rel="stylesheet" href="theme/main/assets/css/header.css?v=<?=filemtime("theme/main/assets/css/header.css")?>">

        <!-- Flag icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">

        <style>
            .dropdown-toggle::after {
                vertical-align: 0.125em !important;
            }
        </style>
        
        <? if ($url[0] == "aloqa") { ?>
            <!-- Meta Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '2899425030192857');
            fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=2899425030192857&ev=PageView&noscript=1"
            /></noscript>
            <!-- End Meta Pixel Code -->
        <? } else { ?>
            <!-- Meta Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '6624048694295003');
            fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=6624048694295003&ev=PageView&noscript=1"
            /></noscript>
            <!-- End Meta Pixel Code -->
        <? } ?>

        <meta name="facebook-domain-verification" content="hc0srvgdfgnkfjledw5fzmvj9545la" />
    </head>
    <body>
        <?
        include "header.php";
        ?>