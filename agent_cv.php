<?
date_default_timezone_set("Asia/Tashkent");

$is_config = true;

if (empty($load_defined)) include 'load.php';

$request_ok = false;
$error = false;
$file_1_error = false;
$file_2_error = false;
$file_staj_error = false;


if ($_POST["submit"]) {
    // echo $url2[2];
    // exit;
    // header("Content-type: text/plain");
    // print_r([
    //     "POST" => $_POST,
    //     "FILES" => $_FILES
    // ]);
    // exit;

    if (!empty($_POST["passport_serial"]) && !empty($_POST["passport_number"])) {
        $_POST["passport_serial_number"] = str_replace(" ", "", $_POST["passport_serial"] . " " . $_POST["passport_number"]);
    }

    if ($_FILES['file_1']["size"] != 0){
        $target_dir_1 = "files/upload/passport/";

        if (!file_exists($target_dir_1)) {
            mkdir($target_dir_1, 0777, true);
        }

        $file_1 = $_FILES['file_1'];
        $random_name_1 = date('Y-m-d-H-i-s').'_'.md5(time().rand(0, 10000000000000000));
        $file_type_1 = basename($file_1["type"]);
        $target_file_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
        $uploadOk_1 = 1;
        $file_type_1 = strtolower(pathinfo($target_file_1,PATHINFO_EXTENSION));
    
        if (file_exists('../'.$target_file_1)) {
            $file_1_error = "Kechirasiz, fayl allaqachon mavjud.";
            $uploadOk_1 = 0;
        }
        if ($file_1["size"] > 5000000) {
            $file_1_error = "Kechirasiz, sizning faylingiz hajmi meyordan ko'p.";
            $uploadOk_1 = 0;
        }
        if($file_type_1 != "jpg" && $file_type_1 != "png" && $file_type_1 != "jpeg" && $file_type_1 != "pdf") {
            $file_1_error = "Kechirasiz, faqat PDF, JPG, JPEG, PNG, SVG fayllariga ruxsat berilgan.";
            $uploadOk_1 = 0;
        }
        if ($uploadOk_1 == 0) {
            $file_1_error = "Kechirasiz, sizning faylingiz yuklanmadi.";
        } else {
            if (move_uploaded_file($file_1["tmp_name"], $target_file_1)) {
                $file_folder_1 = $target_dir_1 . $random_name_1 . ".$file_type_1";
                
                // istalgan o'lchamdagi rasm
                $size_1 = filesize($target_file_1);
                list($width, $height) = getimagesize($target_file_1);

                $file_id_1 = $db->insert("files", [
                    "creator_user_id" => $user_id,
                    "name" => $file_1["name"],
                    "type" => $file_type_1,
                    "size" => $size_1,
                    "file_folder" => $file_folder_1
                ]);

                if (!$file_id_1){
                    $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
                }
            } else {
                $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
            }
        }
    } else {
        $file_1_error = "Kechirasiz, faylingizni yuklashda xatolik yuz berdi.";
    }

    if ($file_1_error) {
        $error = "Fayl yuklashda xatolik!";
    }

    if (!$_POST["phone_1"]) {
        $error = "telefon raqamni kiritishni unutdingiz !!!";
    } else if (strlen($_POST["phone_1"]) != 17 || substr($_POST["phone_1"], 0, 4) != "+998") {
        $error = "telefon raqam noto'g'ri formatda kiritilgan !!!";
    }

    if (!$_POST["first_name"] || !$_POST["last_name"] || !$_POST["father_first_name"] || !$_POST["passport_jshr"] || !$_POST["card_number"] || !$_POST["transit_check"]) {
        $error = "Ma'lumotlarni to'ldirishni unutdingiz!";
    }
    
    if (!$error) {
       
        $agent_id = $db->insert("firms", [
            "first_name" => $_POST["first_name"],
            "last_name" => $_POST["last_name"],
            "father_first_name" => $_POST["father_first_name"],
            "phone_1" => $_POST["phone_1"],
            "phone_2" => $_POST["phone_2"],
            "passport_serial_number" => $_POST["passport_serial_number"],
            "passport_jshr" => $_POST["passport_jshr"],
            "card_number" => $_POST["card_number"],
            "transit_check" => $_POST["transit_check"],
            "passport_id" => $file_id_1,
            // "login" => $_POST["phone_1"],
            // "password" => md5(md5(encode($_POST["passport_serial_number"]))), // password uchun
            // "password_encrypted" => encode($_POST["passport_serial_number"]), // password encrypted uchun
        ]);
    }

    if ($agent_id > 0) {
        $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $agent_id ]);

        if($agent["id"]) {
            $insert_user_id = $db->insert("users", [
                "first_name" => $agent["first_name"],
                "last_name" => $agent["last_name"],
                "login" => str_replace(" ", "", $agent["phone_1"]),
                "password" => md5(md5(encode($agent["passport_serial_number"]))),
                "password_encrypted" => encode($agent["passport_serial_number"]),
                "phone" => $agent["phone_1"],
                "code" => null,
                "password_sended_time" => null,
                "datereg" => time(),
                "lastdate" => time(),
                "ip" => $env->getIp(),
                "ip_via_proxy" => $env->getIpViaProxy(),
                "browser" => $env->getUserAgent(),
                "sestime" => time(),
                "request_id" => $agent_id,
            ]);

            if($insert_user_id > 0) {
                $user = $db->assoc("SELECT * FROM users WHERE id = ?", [ $insert_user_id ]);
                $login = $user["login"];
                $pass = $agent["passport_serial_number"];
                
                if(!empty($user["id"])) {
                    $db->update("users", [
                        "failed_login" => 0,
                        "sestime" => time()
                    ], [
                        "id" => $user['id']
                    ]);
            
                    $session = md5($env->getIp() . $env->getIpViaProxy() . $env->getUserAgent());
                    $sessionArr = $db->assoc("SELECT * FROM cms_sessions WHERE session_id = ?", [ $session ]);
            
                    if (!empty($sessionArr["session_id"])) {
                        $sql_arr = [];
                        $movings = ++$user["movings"];
            
                        if ($user["sestime"] < (time() - 300)) {
                            $movings = 1;
                            $sql_arr["sestime"] = time();
                        }
            
                        if ($user["place"] != $headmod) {
                            $sql_arr["place"] = "/id";
                        }
            
                        $sql_arr["movings"] = $movings;
                        $sql_arr["lastdate"] = time();
            
                        $db->update("cms_sessions", $sql_arr, [
                            "session_id" => $session
                        ]);
                    } else {
                        $db->insert("cms_sessions", [
                            "session_id" => $session,
                            "ip" => $env->getIp(),
                            "ip_via_proxy" => $env->getIpViaProxy(),
                            "browser" => $env->getUserAgent(),
                            "lastdate" => time(),
                            "sestime" => time(),
                            "place" => "/id"
                        ]);
                    }
            
                    // exit($session);
                
                    // Cookie ni o'rnatish
                    $cuid = base64_encode($user['id']);
                    $cups = md5(encode($pass));
                    addCookie("cuid", $cuid);
                    addCookie("cups", $cups);
                
                    header("Location: /agent_success/".encode($agent["id"]));
                    // header("Location: /agent_profile/my");
                }
            }

            
        }

    }

}

if ($user_id) $qabul_off = false; // admin uchun ariza topshirishni ochish
if (!$no_header) include "system/head.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=1.0.2">

<main>
    <? if (!$qabul_off) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3 text-center">
                    <h2 id="heading"><?=t("RO'YXATDAN O'TISH")?></h2>
                    <!-- <p>Fill all form field to go to next step</p> -->
                    <form action="/<?=$url2[0]?>/<?=$url2[1]?>?>" method="POST" id="msform" enctype="multipart/form-data">
                        <div class="form-card">
                            <div class="row">
                                <h2 class="fs-title text-danger text-center mb-4"><?=t("Shaxsiy ma'lumotlar")?></h2>
                            </div>
    
                            <? if ($error) { ?>
                                <h3 class="text-center text-danger"><?=$error?></h3>
                            <? } ?>
    
                            <div class="row">
                                
                                <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="first_name"><?=t("Ismingiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="first_name" placeholder="<?=t("Ismingiz (Lotin alifbosida)")?>" id="first_name" required="" value="<?=htmlspecialchars($_POST["first_name"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="last_name"><?=t("Familiyangiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="last_name" placeholder="<?=t("Familiyangiz (Lotin alifbosida)")?>" id="last_name" required="" value="<?=htmlspecialchars($_POST["last_name"])?>">
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="father_first_name"><?=t("Otangizninig ismi (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="father_first_name" placeholder="<?=t("Otangizninig ismi (Lotin alifbosida)")?>" id="father_first_name" required="" value="<?=htmlspecialchars($_POST["father_first_name"])?>">
                                        </div>
                                        
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="phone_1"><?=t("Telefon raqamingiz")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                                        </div>

                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="phone_2"><?=t("Qo'shimcha telefon raqam")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="phone_2" placeholder="+998" id="phone_2" minlength="17" required="" value="<?=($_POST["phone_2"] ? htmlspecialchars($_POST["phone_2"]) : "+998")?>">
                                        </div>
                                        
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <label class="fieldlabels" for="card_number"><?=t("Karta raqami")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="card_number" placeholder="8600"  required="" value="<?=$_POST["card_number"]?>">
                                        </div>
                                        
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <label class="fieldlabels" for="transit_check"><?=t("Karta egasi (to’liq va xatosiz yozing)")?> <label class="text-danger">*</label></label>
                                            <input type="text" name="transit_check" placeholder=""  required="" value="<?=$_POST["transit_check"]?>">
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 ">
                                    <!-- <h2 class="fs-title text-danger text-center mb-4"><?=t("Passport ma'lumotlar")?></h2> -->
    
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="row no-gutters">
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label class="fieldlabels" for="passport_serial"><?=t("Passport seriyasi")?> <label class="text-danger">*</label></label>
                                                    <input type="text" name="passport_serial" placeholder="- -" id="passport_serial" required="" value="<?=htmlspecialchars($_POST["passport_serial"])?>">
                                                </div>
            
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label class="fieldlabels" for="passport_number"><?=t("Passport raqami")?> <label class="text-danger">*</label></label>
                                                    <input type="text" name="passport_number" placeholder="- - - - - - -" id="passport_number" required="" value="<?=htmlspecialchars($_POST["passport_number"])?>">
                                                </div>
    
                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <label class="fieldlabels" for="passport_jshr"><?=t("Passport jshr")?> <label class="text-danger">*</label></label>
                                                    <input type="text" name="passport_jshr" id="passport_jshr" placeholder="- - - - - - - - - - - - - -"  required="" value="<?=htmlspecialchars($_POST["passport_jshr"])?>">
                                                </div>

                                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <? if ($file_1_error) { ?>
                                                        <h5 class="text-danger"><?=$file_1_error?></h5>
                                                    <? } ?>
                                                    <label class="fieldlabels" for="file_1"><?=t("Passport nusxasi")?> <label class="text-danger">*</label></label>
                                                    <input type="file" name="file_1" id="file_1" required="">
                                                </div>
                                                
                                            </div>
                                        </div>
    
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <img src="images/jshr.jpg" alt="jshr" width="100%">
                                        </div>
                                    </div>
                                </div>
    
                                <div class="col-12" id="staj" style="display:none;">
                                    <? if ($file_staj_error) { ?>
                                        <h5 class="text-danger"><?=$file_staj_error?></h5>
                                    <? } ?>
                                    <label class="fieldlabels" for="file_staj"><?=t("Ushbu yo'nalishnining sirtqi ta'lim shaklida o'qishingiz uchun 5 yillik ish stajingiz bo'lishligi talab etiladi, iltimos ish stajingiz bo'yicha hujjatni yuklang")?> </label>
                                    <input type="file" name="file_staj" id="file_staj">
                                </div>
        
                            </div>
                        </div>
                                
                        <input type="submit" name="submit" class="next action-button" value="<?=t("Jo'natish")?> »" style="width:150px;">
                    </form>
                </div>
            </div>
        </div>
    <? }?>
</main>

<? include "system/scripts.php"; ?>

<script>
    $("#input-image").click(function(){
        $("#file_1").click();
    });

    $("#file_1").on("change", function(){
        // $('#image').change(function(){
            $("#input-image").html('');
            $("#input-image").append('<img src="'+window.URL.createObjectURL(this.files[0])+'" width="100%" />');
            // for (var i = 0; i < $(this)[0].files.length; i++) {
            // }
        // });
    });
    
    $("#region_id").on("change", function(){
        var region_id = $(this).val();
        $.ajax({
            url: 'api',
            type: 'POST',
            data: {
                method: 'getDistricts',
                region_id: region_id
            },
            dataType: 'json',
            success: function(data) {
                $("#district_id").html("");

                for (var i in data.districts) {
                    var district = data.districts[i];
                    var name = district["name"];
                    var id = district["id"];
                    $("#district_id").append('<option value="'+id+'">'+name+'</option>');
                }
            }
        });
    }).change();
</script>

<script>
    $(document).ready(function(){
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        $(document).on("change, input", ".error-form", function(){
            if ($(this).val().length == 0) {
                $(this).addClass("error-form");
            } else {
                $(this).removeClass("error-form");
            }
        });

        <?
        
        $direction_learn_types_arr = [];
        $direction_learn_types_perevod_arr = [];

        if ($directions) {
            foreach ($directions as $direction) {
                $direction_learn_types = $db->in_array("SELECT learn_type_id FROM direction_learn_types WHERE direction_id = ?", [
                    $direction["id"]
                ]);
    
                $learn_type_id_arr = [];
                foreach ($direction_learn_types as $direction_learn_type) {
                    array_push($learn_type_id_arr, $direction_learn_type["learn_type_id"]);
                }
                $direction_learn_types_arr[$direction["id"]] = $learn_type_id_arr;

                // 

                $direction_learn_types_perevod = $db->in_array("SELECT learn_type_id FROM direction_learn_types_perevod WHERE direction_id = ?", [
                    $direction["id"]
                ]);

                $learn_type_perevod_id_arr = [];
                foreach ($direction_learn_types_perevod as $direction_learn_type) {
                    array_push($learn_type_perevod_id_arr, $direction_learn_type["learn_type_id"]);
                }
                $direction_learn_types_perevod_arr[$direction["id"]] = $learn_type_perevod_id_arr;
            }
        }

        echo "var direction_learn_types = ".json_encode($direction_learn_types_arr).";";
        echo "var direction_learn_types_perevod = ".json_encode($direction_learn_types_perevod_arr).";";
        // 
        $learn_types = $db->in_array("SELECT id, name FROM learn_types");
        $learn_types_arr = [];

        foreach ($learn_types as $learn_type) {
            $learn_types_arr[$learn_type["id"]] = $learn_type;
            $learn_types_arr[$learn_type["id"]]["name_t"] = t($learn_type["name"]);
        }
        echo "var learn_types = ".json_encode($learn_types_arr).";";
        ?>
        var selected_learn_type = "<?=$_POST["learn_type"]?>";

        function directionChange() {
            $("#learn_type").html("");

            var direction_id = $("#direction").find("option:selected").attr("data-direction-id");
            
            for (key in <?=($url[1] == "firma-agenti" ? "direction_learn_types_perevod" : "direction_learn_types")?>[direction_id]) {
                var learn_type_id = <?=($url[1] == "firma-agenti" ? "direction_learn_types_perevod" : "direction_learn_types")?>[direction_id][key];
                var learn_type = learn_types[learn_type_id];

                var html = '<option value="'+learn_type["name"]+'" '+(selected_learn_type == learn_type["name"] ? 'selected=""' : '')+'>'+learn_type["name_t"]+'</option>';
                $("#learn_type").append(html);
            }

            learnTypeChange();
        }

        function learnTypeChange() {
            var learn_type = $("#learn_type").val();
            var direction_id = $("#direction").find("option:selected").attr("data-direction-id");

            if (learn_type == "Sirtqi") {
                console.log("Sirti ta'lim yo'nalishini tanladi");
                if (direction_id == "1" || direction_id == "101" || direction_id == "102") {
                    console.log("3 ta yo'nalishga to'g'ri keldi");
                    $("#staj").show();
                    $("#file_staj").attr("required", "");
                } else {
                    $("#file_staj").removeAttr("required");
                    $("#staj").hide();
                    console.log("3 ta yo'nalishga to'g'ri kelmadi");
                }
            } else {
                $("#file_staj").removeAttr("required");
                $("#staj").hide();
                console.log("Sirtqi emas");
            }
        }

        directionChange();
        learnTypeChange();

        $("#direction").on("change", function(){
            directionChange();
        });

        $("#learn_type").on("change", function(){
            learnTypeChange();
        });

        $("input[type='file']").on("change", function() {
            console.log(this.files[0]);
            if ((this.files[0].size / 1024 / 1024) > 10) {
                $(this).attr("type", "text");
                $(this).attr("type", "file");
                alert("Fayl hajmi 10MB dan oshlmasligi kerak! Iltimos faylni qayta yuklang!");
            }
        });

        $("#phone_1").on('input keyup', function(e){
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
            // console.log(x);
            e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
        });

        $("#phone_2").on('input keyup', function(e){
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
            // console.log(x);
            e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
        });

        function changeAcceptOfferta() {
            if ($("#accept_offerta:checked").val() == "ha") {
                $("#payment_link").show();
                $("#payment_link_false").hide();
            } else {
                $("#payment_link").hide();
                $("#payment_link_false").show();
            }
        }

        $("input[name='accept_offerta']").click(function(){
            changeAcceptOfferta();
        });

        $("#payment_link_false").click(function(){
            alert("Tolovni amalga oshirish uchun avval ommaviy offerta shartlariga rozilik bildiring!");
        })

        $("#passport_serial").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 2));
        });

        $("#passport_number").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 7));
        });
       
        $("#passport_jshr").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 14));
        });

        $("#passport_jshr").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 14));
        });
    });
</script>

<script>
    /* Helper function */
    // function download_file(fileURL, fileName) {
    //     // for non-IE
    //     if (!window.ActiveXObject) {
    //         var save = document.createElement('a');
    //         save.href = fileURL;
    //         save.target = '_blank';
    //         var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
    //         save.download = fileName || filename;
    //         if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
    //                 document.location = save.href; 
    //     // window event not working here
    //             }else{
    //                 var evt = new MouseEvent('click', {
    //                     'view': window,
    //                     'bubbles': true,
    //                     'cancelable': false
    //                 });
    //                 save.dispatchEvent(evt);
    //                 (window.URL || window.webkitURL).revokeObjectURL(save.href);
    //             }   
    //     }

    //     // for IE < 11
    //     else if ( !! window.ActiveXObject && document.execCommand)     {
    //         var _window = window.open(fileURL, '_blank');
    //         _window.document.close();
    //         _window.document.execCommand('SaveAs', true, fileName || fileURL)
    //         _window.close();
    //     }
    // }
</script>

<?
if (!$no_footer) include 'system/end.php';
?>