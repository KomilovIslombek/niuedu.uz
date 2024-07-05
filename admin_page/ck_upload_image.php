<?php
header("Content-type: application/json");

if (isset($_FILES['upload']['name'])){
    $file = $_FILES['upload']['tmp_name'];
    $file_name = $_FILES['upload']['name'];
    $file_name_array = explode(".", $file_name);
    $extension = end($file_name_array);
    $new_image_name = md5(rand()) . '.' . $extension;

    $target_dir = "images/upload/";

    if (!file_exists("".$target_dir)) {
        mkdir("".$target_dir, 0777, true);
    }

    $allowed_extension = array("jpg", "jpeg", "gif", "png", "webp");
    if (in_array($extension, $allowed_extension)){
        move_uploaded_file($file, "$target_dir" . $new_image_name);
        $function_number = $_GET['CKEditorFuncNum'];
        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']."/$target_dir" . $new_image_name;
        $message = '';

        echo json_encode([
            "uploaded" => 1,
            "fileName" => $new_image_name,
            "url" => $url
        ]);

        // echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($function_number, '$url', '$message');</script>";
    }
}

?>