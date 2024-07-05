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

function uploadFileFromURL($fileURL, $fileFolder, $allowedFileTypes, $additionalFileSize = false, $required = true) {
    global $errors, $db, $user_id;

    $res = [];
    $errorsArr = [];

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $fileFolder)) {
        mkdir($_SERVER["DOCUMENT_ROOT"] . "/" . $fileFolder, 0777, true);
    }

    $file_type = pathinfo($fileURL, PATHINFO_EXTENSION);
    $random_name = date("Y-m-d-H-i-s") . '_' . md5(time() . rand(0, 10000000000000000));
    $file_folder = $fileFolder . "/" . $random_name . "." . $file_type;
    $file_path = $_SERVER["DOCUMENT_ROOT"] . "/" . $file_folder;
    $uploadOk = 1;

    if (file_exists($file_path)) {
        array_push($errorsArr, "Kechirasiz, fayl allaqachon mavjud.");
        $uploadOk = 0;
    }

    // Загрузка файла по ссылке
    $fileContents = file_get_contents($fileURL);
    $filename = basename($fileURL);
    if ($fileContents === false) {
        array_push($errorsArr, "Kechirasiz, faylni yuklashda xatolik yuz berdi.");
    }

    if (file_put_contents($file_path, $fileContents) === false) {
        array_push($errorsArr, "Kechirasiz, faylni yuklashda xatolik yuz berdi.");
    }

    // $size = filesize($file_path);
    $size = filesize($file_folder);

    if (!in_array($file_type, $allowedFileTypes)) {
        array_push(
            $errorsArr,
            "Siz yuklamoqchi bo'lgan format '$file_type' Kechirasiz, faqat " . implode(" ", $allowedFileTypes) . " fayllarni yuklashga ruxsat berilgan."
        );
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        // array_push($errorsArr, "Kechirasiz, sizning faylingiz yuklanmadi.");
    } else {
        $file_id = $db->insert("files", [
            "creator_user_id" => $user_id,
            "type" => $file_type,
            "name" => $filename,
            "size" => $size,
            "file_folder" => $file_folder,
        ]);

        if ($file_id > 0) {
            // $res["file_id"] = $file_id;
            $res = $file_id;
        } else {
            array_push(
                $errorsArr,
                "Faylni bazaga yozishda xatolik yuzaga keldi"
            );
            return;
        }
    }

    if (count($errorsArr) > 0) {
        $errors["forms"][$fileKey] = $errorsArr;
    }
    return $res;
}

function uploadImageFromUrl($imageURL) {
    global $db, $user_id;

    $target_dir = "images/other_site_requests/";

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $target_dir)) {
        mkdir($_SERVER["DOCUMENT_ROOT"] . "/" . $target_dir, 0777, true);
    }

    $file_type = pathinfo($imageURL, PATHINFO_EXTENSION);
    $random_name = date('Y-m-d-H-i-s') . '_' . md5(time() . rand(0, 10000000000000000));
    $target_file = $_SERVER["DOCUMENT_ROOT"] . "/" . $target_dir . $random_name . "." . $file_type;
    $uploadOk = 1;

    if (file_exists($target_file)) {
        exit("Kechirasiz, fayl allaqachon mavjud.");
        $uploadOk = 0;
    }

    // Загрузка изображения по ссылке
    $imageContents = file_get_contents($imageURL);
    if ($imageContents === false) {
        exit("Kechirasiz, faylni yuklashda xatolik yuz berdi.");
    }

    if (file_put_contents($target_file, $imageContents) === false) {
        exit("Kechirasiz, faylni yuklashda xatolik yuz berdi.");
    }

    $size = filesize($target_file);
    list($width, $height) = getimagesize($target_file);

    $image_id = $db->insert("images", [
        "creator_user_id" => $user_id,
        "width" => $width,
        "height" => $height,
        "size" => $size,
        "file_folder" => $target_dir . $random_name . "." . $file_type,
    ]);

    if (!$image_id) {
        echo '<script>alert("xato");</script>';
        return;
    }

    return $image_id;
}

include('filter.php');

$page = $_REQUEST["page"];
$id = $_REQUEST["id"];
$direction_name = $_REQUEST["direction_name"];
$direction_id = $_REQUEST["direction_id"];
$learn_type = $_REQUEST["learn_type"];
if (!$id) exit(http_response_code(404));

$method_name = "application-form";
$other_site_student = addApi("GET", $method_name, $id, '');
$student = json_decode($other_site_student, true);


// echo "<pre>";
// print_r($student);
// exit;
$allowedFileTypes = ["docx", "doc", "pdf", "txt", "png", "jpeg", "jpg"]; 


if ($student['applicant_id']){
    
    // echo "<pre>";
    // print_r($student);

    // echo "<br><br><br>";
    $first_name =  $student["first_name"];
    $last_name =  $student["last_name"];
    $father_first_name =  $student["third_name"];
    $birth_date = explode("T", $student["birth_date"]);
    $phone_1 = $student["phone"];
    $phone_2 = $student["extra_phone"];
    $region_id = $student["region_id"];
    $passport_serial_number = $student["serial_number"];
    $district_id = $student["district_id"];
    $address = $student["user_address"]["address"];
    $photo = "https://api.mentalaba.uz/".$student["photo"];
    $passport_photo = "https://api.mentalaba.uz/".$student["passport"][0];
    $diplom_photo = "https://api.mentalaba.uz/".$student["user_educations"]["file"][0];
    $dtm_photo = "https://api.mentalaba.uz/".$student["dtm_result"]["dtm_exam_result_document"];

    if($student["gender"] == 'female') {
        $sex = 'ayol';
    } else if($student["gender"] == 'male') {
        $sex = 'erkak';
    }

    $file_id_1 = uploadFileFromURL($photo, "files/upload/3x4", $allowedFileTypes, false, true);
    $file_id_2 = uploadFileFromURL($passport_photo, "files/upload/passport", $allowedFileTypes, false, true);
    $file_id_3 = uploadFileFromURL($diplom_photo, "files/upload/diplom", $allowedFileTypes, false, true);
    $file_id_4 = uploadFileFromURL($dtm_photo, "files/upload/dtm_natija", $allowedFileTypes, false, true);

    // echo "id: ". $id . "<br>";
    // echo "first_name: ". $first_name. "<br>";
    // echo "last_name: ". $last_name. "<br>";
    // echo "father_first_name: ". $father_first_name. "<br>";
    // echo "birth_date: ". $birth_date[0]. "<br>";
    // echo "phone_1: ". $phone_1 . "<br>";
    // echo "phone_2: ". $phone_2. "<br>";
    // echo "region_id: ". $region_id. "<br>";
    // echo "passport_serial_number: ". $passport_serial_number. "<br>";
    // echo "district_id: ". $district_id. "<br>";
    // echo "direction_name: ". $direction_name. "<br>";
    // echo "direction_id: ". $direction_id. "<br>";
    // echo "learn_type: ". $learn_type. "<br>";
    // echo "address: ". $address. "<br>";
    // echo "sex: ". $sex. "<br>";
    // echo "file_id_1: ". $file_id_1. "<br>";
    // echo "file_id_2: ". $file_id_2. "<br>";
    // echo "file_id_3: ". $file_id_3. "<br>";
    // echo "imported: ". "Men talabaman". "<br>";
    // echo "imported_id: " . $id. "<br>";
    // echo "imported_for_delete: ". "delete";
    
    // exit;
    $request_id = $db->insert("requests", [
        "first_name" => $first_name,
        "last_name" => $last_name,
        "father_first_name" => $father_first_name,
        "birth_date" => $birth_date[0],
        "phone_1" => $phone_1,
        "phone_2" => $phone_2,
        "region_id" => $region_id,
        "passport_serial_number" => $passport_serial_number,
        "district_id" => $district_id,
        "direction" => $direction_name,
        "direction_id" => $direction_id,
        "learn_type" => $learn_type,
        "adress" => $address,
        "sex" => $sex,
        "file_id_1" => $file_id_1,
        "file_id_2" => $file_id_2,
        "file_id_3" => $file_id_3,
        "file_id_4" => $file_id_4,
        "imported" => "Men talabaman",
        "imported_id" => $id,
        "imported_for_delete" => "delete",
    ]);

    if ($request_id > 0) {
        $direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $direction_id ]);
        $new_code = idCode($direction["code"], $request_id);

        $update_arr = [
            "code" => $new_code,
        ];
        
        $db->update("requests", $update_arr, [
            "id" => $request_id
        ]);
    } else {
        echo exit("request_id not found");
    }
    
    header('Location: other_site_requests.php?page='.$page);
}

include('head.php');
?>


<? include "scripts.php"; ?>


<? include('end.php'); ?>
