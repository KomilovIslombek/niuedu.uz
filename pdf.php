<?php
if (empty($url[1])) exit(http_response_code(404));
$url[1] = decode($url[1]);
if (explode("/", $url[1])[0] != "files" || explode("/", $url[1])[1] != "upload") exit(http_response_code(404));

$im = new imagick($_SERVER['DOCUMENT_ROOT'] . '/'.$url[1].'[0]');
$im->setImageFormat('png');
$im->setImageBackgroundColor('white');
header('Content-Type: image/png');
echo $im;
?>