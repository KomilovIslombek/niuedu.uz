<?php

if ($systemUser->admin!=1 || !$user_id || $user_id == 0){
    header('Location:/login');
    exit;
}

include('filter.php');

if ($_REQUEST['type'] == "add_block_quiz"){

    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
    if (!$name) {echo"error [name]";exit;}

    $description = isset($_REQUEST['description']) ? $_REQUEST['description'] : null;
    if (!$description) {echo"error [description]";exit;}

    $direction_id = isset($_REQUEST['direction_id']) ? $_REQUEST['direction_id'] : null;

    $max_timeout = isset($_REQUEST['max_timeout']) ? $_REQUEST['max_timeout'] : 60;

    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;

    if ($_FILES["image"]["size"] != 0){
        $target_dir = "images/block_test/";

        if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_dir)) {
            mkdir($_SERVER["DOCUMENT_ROOT"]."/".$target_dir, 0777, true);
        }

        $file = $_FILES['image'];
        $random_name = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type = basename($file["type"]);
        $target_file = $_SERVER["DOCUMENT_ROOT"]."/".$target_dir . $random_name . ".$file_type";
        $uploadOk = 1;
        $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$target_file)) {
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
                
                // 250px 250px
                $size = filesize($target_file);
                list($width, $height) = getimagesize($target_file);

                $image_id = $db->insert("images", [
                    "creator_user_id" => $user_id,
                    "width" => $width,
                    "height" => $height,
                    "size" => $size,
                    "file_folder" => $file_folder,
                ]);

                if (!$image_id){
                    echo '<script>alert("xato");</script>';
                    return;
                }
            } else {
                echo "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        return;
    }

    $block_id = $db->insert("quiz_block", [
        "creator_user_id" => $user_id,
        "name" => $name,
        "description" => $description,
        "status" => $status,
        "max_timeout" => $max_timeout,
        "image_id" => $image_id,
        "direction_id" => $direction_id
    ]);

    if ($_POST["sciences"]) {
        foreach ($_POST["sciences"] as $science_id => $val) {
            $db->insert("quiz_block_sciences", [
                "creator_user_id" => $user_id,
                "block_id" => $block_id,
                "science_id" => $science_id,
                "ball" => trim($val["ball"]),
                "tests_count" => trim($val["tests_count"])
            ]);
        }
    }
    
    header('Location: block_tests_list.php?page=1');
}

include('head.php');
?>

<script src="https://cdn.ckeditor.com/4.15.1/full-all/ckeditor.js"></script>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Yangi blok quiz qo'shish</h4>
                            
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
                                <form action="" method="POST" class="form" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="add_block_quiz" required>

                                    <div class="form-group">
                                        <label>blok nomi</label>
                                        <textarea name="name" rows="5" class="form-control" placeholder="blok nomi" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>blok izohi</label>
                                        <textarea name="description" rows="5" class="form-control" placeholder="blok izohi" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="direction_id">Ta'lim yo'nalishiga biriktirish</label>

                                        <select name="direction_id" class="form-control border-primary">
                                            <? foreach ($db->in_array("SELECT * FROM directions") as $direction) { ?>
                                                <option value="<?=$direction["id"]?>"><?=lng($direction["name"], "uz")?></option>
                                            <? } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Testning umumiy davomiyligi (daqiqa)<br>misol uchun: <b>60</b></label>
                                        <input type="text" name="max_timeout" rows="5" class="form-control" required placeholder="60">
                                    </div>

                                    <div class="form-group">
                                        <label>rasmni yuklash (250px 250px)</label>
                                        <label id="projectinput7" class="file center-block">
                                            <input type="file" name="image" accept="image/*">
                                            <span class="file-custom"></span>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Holatni tanlang</label>

                                        <select name="status" class="form-control border-primary">
                                            <option value="0">faol emas</option>
                                            <option value="1" selected="">faol</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="science_id">Fanlarni tanlang</label>

                                        <div class="input-group">
                                            <?
                                            $sciences = $db->in_array("SELECT * FROM quiz_sciences");
                                            
                                            foreach ($sciences as $science) {
                                                $quiz_block_science = $db->assoc("SELECT * FROM quiz_block_sciences WHERE block_id = ? AND science_id = ?", [
                                                    $block["id"],
                                                    $science["id"]
                                                ]);

                                                echo '<fieldset style="display:inline-block;margin-right:15px;">
                                                        <input type="checkbox" name="science_id" id="science_id_'.$science["id"].'" class="chk-remember" value="'.$science["id"].'">
                                                        <label for="science_id_'.$science["id"].'"> '.$science["name"].'</label>
                                                    </fieldset>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div id="sciences" class="row col-12"></div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> saqlash
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

<style>
    .js .input--file {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }

    .no-js .input--file + label {
        display: none;
    }
    .js .input--file + label {
        display: inline-block;
        cursor: pointer;
        background: orange;
        color: #fff;
        padding: 10px;
    }

    .js .input--file:focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }
</style>

<script>
    [].slice.call(document.querySelectorAll(".input--file")).forEach(function(el,i){
		el.addEventListener( 'change', function( e ){
			var fileName = '';
			var label = document.querySelectorAll('label[for="' + el.getAttribute('id') + '"]')[0];
			var currentVal = label.innerHTML;
			if (this.files && this.files.length > 1)
				fileName = this.files.length + ' ta fayl';
			else
				fileName = e.target.value.split('\\').pop();

			if (fileName)
				label.innerHTML = fileName;
			else
				label.innerHTML = currentVal;
		});
	});
</script>

<? include "scripts.php"; ?>

<?
$sciences = [];
$sciencesArr = $db->in_array("SELECT * FROM quiz_sciences");
foreach ($sciencesArr as $scienceArr) { $sciences[$scienceArr["id"]] = $scienceArr; }
?>

<script>
    var sciences;
    <? if ($sciences) { ?>
        sciences = JSON.parse('<?=strtr(
            json_encode($sciences),
            [
                "'" => "\'",
                '\r' => "",
                '\n' => ""
            ]
        )?>');
    <? } ?>

    $("input[name='science_id']").on("change", function(){
        var values = [];
    
        for (var i in sciences) {
            var  science = sciences[i];
            var science_id = science.id;
    
            // console.warn(science_id);
            values[science_id] = {
                ball: $(document).find("#sciences_"+science_id+"_ball").val(),
                tests_count: $(document).find("#sciences_"+science_id+"_tests_count").val(),
            };
        }
    
        // console.log(values);
    
        $("#sciences").html("");
        
        $("input[name='science_id']:checked").each(function(){
            var science_id = $(this).val();
            var science_name = sciences[science_id]["name"];

            var ball = values[science_id]["ball"];
            ball = ball ? ball : "";
    
            var tests_count = values[science_id]["tests_count"];
            tests_count = tests_count ? tests_count : "";
    
            var ball_elm = '<div class="form-group col-lg-6 col-sm-12">'
                +'<label>ball <b>('+science_name+')</b></label>'
                +'<input type="text" name="sciences['+science_id+'][ball]" class="form-control" placeholder="ball" data-number-input id="sciences_'+science_id+'_ball" value="'+ball+'" required="">'
            +'</div>';

            var tests_count_elm = '<div class="form-group col-lg-6 col-sm-12">'
                +'<label>Testlar soni <b>('+science_name+')</b></label>'
                +'<input type="text" name="sciences['+science_id+'][tests_count]" class="form-control" placeholder="Testlar soni" data-number-input id="sciences_'+science_id+'_tests_count" value="'+tests_count+'" required="">'
            +'</div>';
    
            $("#sciences").append(ball_elm);
            $("#sciences").append(tests_count_elm);
        });
    })
</script>

<? include("end.php"); ?>