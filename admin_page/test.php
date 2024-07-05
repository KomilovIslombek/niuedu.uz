<?php

// tekshiruv
if (!$user_id || $user_id == 0) {
    header('Location:/login');
    exit;
} else if ($systemUser->admin != 1){
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        siz admin lavozimida emassiz!<br>admin lavozimidagi akkauntga kirish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing.
    </body>
    </html>';
    exit;
}

include('filter.php');

header("Content-type: text/plain");

exit("OK");

$requests = $db->in_array("SELECT * FROM requests");

foreach ($requests as $request) {
    $request2 = $db->assoc("SELECT id, code, first_name, last_name, father_first_name FROM requests WHERE id = ?", [ $request["id"] ]);
    if (!empty($request2["id"])) {
    }
    print_r($request) . "\n\n";
}
?>