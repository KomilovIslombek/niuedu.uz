<?
date_default_timezone_set("Asia/Tashkent");

$is_config = true;

if (empty($load_defined)) include 'load.php';

if ($_POST["submit"] || $_GET["submit"]) {
    if (!$_POST["phone_1"]) {
        $error = "telefon raqamni kiritishni unutdingiz !!!";
    } else if (strlen($_POST["phone_1"]) != 17 || substr($_POST["phone_1"], 0, 4) != "+998") {
        $error = "telefon raqam noto'g'ri formatda kiritilgan !!!";
    }

    if (!$_POST["first_name"]) {
        $error = "Ismingizni to'ldirishni unutdingiz!";
    }
    
    if (!$error) {
        $request_id = $db->insert("again_calls", [
            "first_name" => $_POST["first_name"],
            "phone_1" => $_POST["phone_1"]
        ]);
    }

    if ($request_id > 0) {
        include "modules/bot.php";

        $text = "";
        $text .= "\n#ariza_".$request_id . "";
        $text .= "\n\nIsm: <b>".$_POST["first_name"]."</b>";
        $text .= "\nTelefon 1: <b>".$_POST["phone_1"]."</b>";

        $groups = ["-1001694124015"];

        foreach ($groups as $admin_id) {
            $res_msg = bot("sendMessage", [
                "chat_id" => $admin_id,
                "text" => "<b>".$_SERVER['HTTP_HOST']."\n\nSayt orqali ariza qoldirishdi!</b>\n$text",
                "parse_mode" => "html"
            ]);
        }

        if ($error == false) {
            $request_ok = true;

            $old_request = $db->assoc("SELECT * FROM again_calls WHERE id = ?", [ $request_id ]);
        }
    }

}

if (!$no_header) include "system/head.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=1.0.2">

<main>
    <? if (empty($old_request["id"])) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <H2 id="heading">NAVOIY INNOVATSIYALAR UNIVERSITETI</h2>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        <form action="/<?=$url2[0]?>/<?=$url2[1]?>" method="POST" id="msform" enctype="multipart/form-data">
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="fs-title text-danger"><?=t("Ma'lumotlaringizni qoldiring, biz siz bilan tez orada bog'lanamiz")?></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="fieldlabels" for="first_name"><?=t("Ismingiz (Lotin alifbosida)")?> <label class="text-danger">*</label></label>
                                        <input type="text" name="first_name" placeholder="<?=t("Ismingiz (Lotin alifbosida)")?>" id="first_name" required="" value="<?=htmlspecialchars($_POST["first_name"])?>">
                                    </div>

                                    <div class="col-12">
                                        <label class="fieldlabels" for="phone_1"><?=t("Telefon raqamingiz")?> <label class="text-danger">*</label></label>
                                        <input type="text" name="phone_1" placeholder="+998" id="phone_1" minlength="17" required="" value="<?=($_POST["phone_1"] ? htmlspecialchars($_POST["phone_1"]) : "+998")?>">
                                    </div>
                                </div>
                            </div>
                                    
                            <input type="submit" name="submit" class="next action-button" value="<?=t("Yuborish")?> »" style="width: 145px;">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <? } else if ($request_ok == true) { ?>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <h2 id="heading"><?=t("NAVOIY INNOVATSIYALAR UNIVERSITETIGA TOPSHIRGAN ARIZANGIZ")?></h2>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        
                        <fieldset>
                            <div class="form-card">
                                <h2 class="purple-text text-center"><strong><?=t("YUBORILDI !")?></strong></h2> <br>
                                <div class="row justify-content-center">
                                    <div class="col-3"> <img src="images/check.png" class="fit-image"> </div>
                                </div> <br><br>
                                <div class="row justify-content-center">
                                    <div class="col-7 text-center">
                                        <h5 class="purple-text text-center mb-2"><?=t("Bizning xodimlarimiz tez orada siz bilan bog'lanishadi")?></h5>

                                        <h5><?=str_replace("https://t.me/niuedu_uz", '<a href="https://t.me/niuedu_uz" class="text-info">https://t.me/niuedu_uz</a>', t("Bizni https://t.me/niuedu_uz orqali kuzatib boring"))?></h5>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                            
                        <table class="table table-hover mt-3 mb-3">
                            <tr>
                                <th><?=t("ID raqami")?></th>
                                <td><b><?=(idCode($direction["code"], $old_request["id"]))?></b></td>
                            </tr>
                            <tr>
                                <th><?=t("Ismingiz")?></th>
                                <td><b><?=$old_request["first_name"]?></b></td>
                            </tr>
                            <tr>
                                <th><?=t("Telefon")?></th>
                                <td><b><?=$old_request["phone_1"]?></b></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</main>

<? include "system/scripts.php"; ?>

<script>
    $(document).ready(function(){
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
    });
</script>

<?
if (!$no_footer) include 'system/end.php';
?>