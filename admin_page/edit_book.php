<?php
$user_id = $systemUser->id;
$rights = $systemUser->rights;
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

$book_id = isset($_REQUEST['book_id']) ? $_REQUEST['book_id'] : null;
if (!$book_id) {echo"error";return;}

$book = $db->assoc("SELECT * FROM books WHERE id = ?", [ $_REQUEST['book_id'] ]);
if (!$book["id"]) {echo"error (book not found)";exit;}

if ($_REQUEST['type'] == "edit_book"){
    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}

    $book_category_id = isset($_REQUEST["book_category_id"]) ? $_REQUEST["book_category_id"] : null;
    if (!$book_category_id) {echo"kitob turini tanlashni unutdingiz!";exit;}

    $author = isset($_REQUEST["author"]) ? $_REQUEST["author"] : null;
    $year = isset($_REQUEST["year"]) ? $_REQUEST["year"] : null;
    $lang = isset($_REQUEST["lang"]) ? $_REQUEST["lang"] : null;
    
    $image_id = $book["image_id"];
    if ($_FILES["image"]["size"] != 0){
        $target_dir = "images/books/";

        if (!file_exists("".$target_dir)) {
            mkdir("".$target_dir, 0777, true);
        }

        $file = $_FILES["image"];
        $random_name = date("Y-m-d-H-i-s").'_'.md5(time().rand(0, 10000000000000000));
        $file_type = basename($file["type"]);
        $target_file = "".$target_dir . $random_name . ".$file_type";
        $uploadOk = 1;
        $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
        if (file_exists("".$target_file)) {
            echo "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk = 0;
        }
        if ($file["size"] > 5000000) {
            echo "Kechirasiz, sizning faylingiz juda katta.";
            $uploadOk = 0;
        }
        if($file_type != "jpg" && $file_type != "png" && $file_type != "jpeg") {
            echo "Kechirasiz, faqat JPG, JPEG, PNG fayllariga ruxsat berilgan.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_folder = $target_dir . $random_name . ".$file_type";
                
                // 576px 324px
                $size = filesize($target_file);
                list($width, $height) = getimagesize($target_file);

                $new_image_id = $db->insert("images", [
                    "creator_user_id" => $user_id,
                    "width" => $width,
                    "height" => $height,
                    "size" => $size,
                    "file_folder" => $file_folder,
                ]);

                if (!$new_image_id){
                    echo '<script>alert("rasmni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_image_id) {
                    delete_image($image_id);
                    $image_id = $new_image_id;
                }
            } else {
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }
    
    $file_id = $book["file_id"];
    if ($_FILES["file"]["size"] != 0){
        $target_dir = "files/books/";

        if (!file_exists("".$target_dir)) {
            mkdir("".$target_dir, 0777, true);
        }

        $file = $_FILES["file"];
        $random_name = date("Y-m-d-H-i-s").'_'.md5(time().rand(0, 10000000000000000));
        $file_type_2 = basename($file["type"]);
        $target_file = "".$target_dir . $random_name . ".$file_type_2";
        $uploadOk = 1;
        $file_type_2 = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
        if (file_exists("".$target_file)) {
            echo "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk = 0;
        }
        if($file_type_2 == "php") {
            echo "Kechirasiz, faqat fayllariga ruxsat berilgan.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_folder_2 = $target_dir . $random_name . ".$file_type_2";
                $size_2 = filesize($target_file);

                $new_file_id = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file["name"],
                    "type" => $file_type_2,
                    "size" => $size_2,
                    "file_folder" => $file_folder_2
                ]);

                if (!$new_file_id){
                    echo '<script>alert("rasmni bazaga yozishda xatolik yuzaga keldi!!!");</script>';
                    return;
                }

                if ($new_file_id) {
                    delete_file($file_id);
                    $file_id = $new_file_id;
                }
            } else {
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    }
    
    $db->update("books", [
        "name" => json_encode($name, JSON_UNESCAPED_UNICODE),
        "book_category_id" => $book_category_id,
        "author" => json_encode($author, JSON_UNESCAPED_UNICODE),
        "year" => $year,
        "lang" => $lang,
        "image_id" => $image_id,
        "file_id" => $file_id
    ], [
        "id" => $book["id"]
    ]);

    header("Location: books_list.php?page=$page");
}

if ($_REQUEST["type"] == "delete_book"){
    $db->delete("books", $book["id"]);
    delete_image($book["image_id"]);
    delete_file($book["file_id"]);
    header("Location: books_list.php?page=$page");
}

include("head.php");
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Kitobni tahrirlash</h4>
                            
                            <?
                            foreach ($db->in_array("SELECT * FROM langs_list") as $lang) {
                                echo'<span select-form-lang="'.$lang["flag_icon"].'" style="cursor:pointer;font-size:30px;border:1px solid #f1f1f1;margin:15px 10px" class="flag-icon flag-icon-'.$lang["flag_icon"].'" id="form_lang_select"></span> ';
                            }
                            ?>

                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                    <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form action="edit_book.php" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="edit_book" required>
                                    <input type="hidden" name="page" value="<?=$page?>" required>
                                    <input type="hidden" name="book_id" value="<?=$_REQUEST["book_id"]?>" required>

                                    <? foreach ($db->in_array("SELECT * FROM langs_list") as $lang) { ?>
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>kitob nomi <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <input type="text" name="name[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="kitob nomi" value="<?=lng($book["name"], $lang["flag_icon"])?>">
                                        </div>
    
                                        <div class="form-group" form-lang="<?=$lang["flag_icon"]?>" style="<?=($lang["flag_icon"] != "uz" ? "display:none" : "")?>">
                                            <label>muallif(lari) <span class="flag-icon flag-icon-<?=$lang["flag_icon"]?>"></span></label>
                                            <input type="text" name="author[<?=$lang["flag_icon"]?>]" class="form-control border-primary" placeholder="muallif(lari)" value="<?=lng($book["author"], $lang["flag_icon"])?>">
                                        </div>
                                    <? } ?>

                                    <div class="form-group">
                                        <label>kitob turi</span></label>

                                        <select name="book_category_id" id="book_category_id" class="form-control border-primary">
                                            <?
                                            foreach ($db->in_array("SELECT * FROM book_categories") as $book_category) {
                                                echo '<option value="'.$book_category["id"].'" '.($book_category["id"] == $book["book_category_id"] ? 'selected=""' : 's').'>'.lng($book_category["name"]).'</option>';  
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="year">yil</span></label>
                                        <input type="text" name="year" class="form-control border-primary" placeholder="yil" value="<?=$book["year"]?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="lang">til</span></label>

                                        <select name="lang" class="form-control border-primary">
                                            <option <?=($book["lang"] == "o'zbek" ? 'selected=""' : '')?> value="o'zbek">o'zbek</option>
                                            <option <?=($book["lang"] == "rus" ? 'selected=""' : '')?> value="rus">rus</option>
                                            <option <?=($book["lang"] == "ingliz" ? 'selected=""' : '')?> value="ingliz">ingliz</option>
                                        </select>
                                    </div>

                                    <?
                                    if ($book["image_id"]) {
                                        $image = image($book["image_id"]);
                                        if ($image["file_folder"]) {
                                            echo '<image src="'.$image["file_folder"].'" width="400px">';
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label>rasmni yuklash (316px 447px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>kitobni yuklash (pdf)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="file">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Saqlash
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<? include "scripts.php"; ?>

<? include('end.php'); ?>