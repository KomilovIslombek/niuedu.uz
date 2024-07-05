<?
$is_config = true;
if (empty($load_defined)) include 'load.php';

include "system/head.php";

// $user = $systemUser;
// $profile_image = getAvatar($user);

$requests = $db->in_array("SELECT file_id_1, file_id_2, file_id_3 FROM requests");
foreach ($requests as $request_key => $request) {
    $file_1 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_1"] ]);
    $file_2 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_2"] ]);
    $file_3 = $db->assoc("SELECT * FROM files WHERE id = ?", [ $request["file_id_3"] ]);
    
    // 
    
    $file_1_real_folder = "files/upload/files/" . explode("/", $file_1["file_folder"])[3];
    if (file_exists($file_1_real_folder)) {
        rename($file_1_real_folder, $file_1["file_folder"]);
        rename($file_1_real_folder . "_thumb.jpg", $file_1["file_folder"] . "_thumb.jpg");
    }

    $file_1_thumb_real_folder = "files/upload/files/" . explode("/", $file_1["file_folder"])[3]  . "_thumb.jpg";
    if (file_exists($file_1_thumb_real_folder)) {
        rename($file_1_thumb_real_folder, $file_1["file_folder"] . "_thumb.jpg");
    }

    // 

    $file_2_real_folder = "files/upload/files/" . explode("/", $file_2["file_folder"])[3];
    if (file_exists($file_2_real_folder)) {
        rename($file_2_real_folder, $file_2["file_folder"]);
    }

    $file_2_thumb_real_folder = "files/upload/files/" . explode("/", $file_2["file_folder"])[3]  . "_thumb.jpg";
    if (file_exists($file_2_thumb_real_folder)) {
        rename($file_2_thumb_real_folder, $file_2["file_folder"] . "_thumb.jpg");
    }

    // 

    $file_3_real_folder = "files/upload/files/" . explode("/", $file_3["file_folder"])[3];
    if (file_exists($file_3_real_folder)) {
        rename($file_3_real_folder, $file_3["file_folder"]);
    }

    $file_3_thumb_real_folder = "files/upload/files/" . explode("/", $file_3["file_folder"])[3]  . "_thumb.jpg";
    if (file_exists($file_3_thumb_real_folder)) {
        rename($file_3_thumb_real_folder, $file_3["file_folder"] . "_thumb.jpg");
    }

    // 

    $request["file_1"] = $file_1;
    $request["file_2"] = $file_2;
    $request["file_3"] = $file_3;

    $requests[$request_key] = $request;
}

header("Content-type: text/plain");
print_r($requests);
exit;

?>