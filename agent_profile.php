<?
$is_config = true;
if (empty($load_defined)) include 'load.php';

if (!$user_id || $user_id == 0){
    header("Location: /agent_auth");
    exit;
} else if (empty($systemUser["request_id"])) {
    if ($systemUser["admin"] == 1) {
        echo 'siz admin lavozimidasiz profile bo\'limiga o\'tish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing va ariza topshirilgan akkauntga kiring';
        exit;
    } else {
        header("Location: /agent_cv");
        exit;
    }
} else if (!$url[1]) {
    header("Location: /$url2[0]/agent_profile/my");
    exit;
}

$agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $systemUser["request_id"] ]);
if (empty($agent["id"])) {
    header("Location: /agent_cv");
    exit;
}

// $agent_ok = false;
$error = false;
$file_1_error = false;
$file_2_error = false;
$file_3_error = false;
// echo $url
if($url[1] != 'my' && $url[1] != 'payments' && $url[1] != 'withdraw_money' && $url[1] != 'studentsList' && $url[1] != 'referal_link' && $url[1] != 'shartnoma') {
    $addStdunt = true;
}

if (!empty($agent["passport_id"])) $file_1_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $agent["passport_id"] ]);
if (!empty($agent["firma_guvohnoma_id"])) $file_2_arr = $db->assoc("SELECT * FROM files WHERE id = ?", [ $agent["firma_guvohnoma_id"] ]);

if (!empty($file_1_arr["id"])) {
    if (file_exists($file_1_arr["file_folder"]  . "_thumb.jpg")) {
        $file_1_arr["file_folder"] = $file_1_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_1_arr["type"] == "pdf") {
            $file_1_arr["file_folder"] = "pdf/" . encode($file_1_arr["file_folder"]);
        } else {
            $file_1_arr["file_folder"] = $file_1_arr["file_folder"];
        }
    }

}

if (!empty($file_2_arr["id"])) {
    if (file_exists($file_2_arr["file_folder"]  . "_thumb.jpg")) {
        $file_2_arr["file_folder"] = $file_2_arr["file_folder"]  . "_thumb.jpg";
    } else {
        if ($file_2_arr["type"] == "pdf") {
            $file_2_arr["file_folder"] = "pdf/" . encode($file_2_arr["file_folder"]);
        } else {
            $file_2_arr["file_folder"] = $file_2_arr["file_folder"];
        }
    }
}


if ($_REQUEST["type"] == "withdraw_money") {
    validate(["amount"]);
    $agent_moneys = $db->in_array("SELECT * FROM withdrawal_of_money_agents WHERE agent_id = ? AND status = 0", [ $agent["id"] ]);
    $money;
    if($agent_moneys) {
        foreach ($agent_moneys as $agent_money) {
            $money += $agent_money["amount"];
        }
    }
    $result_money = $agent["balance"] - $money;
    $money += str_replace(",", "", $_POST["amount"]);
    if($agent["balance"] >= $money) {
        $withdraw_money_id = $db->insert("withdrawal_of_money_agents", [
            "creator_user_id" => $user_id,
            "agent_id" => $agent["id"],
            "amount" => str_replace(",", "", $_POST["amount"]),
            "status" => 0,
        ]);
    } else {
        $error = t("Maksimal").' '. str_replace("-", "", number_format($result_money)) .' so\'m '.t("kiritishingiz mumkin").'! '.t("sizning balansingiz").' '.number_format($agent["balance"]). " so'm";
    }
}

$profile_image = $file_1_arr["file_folder"];

$agent_balances = $db->in_array("SELECT * FROM agent_balances WHERE firm_id = ?", [ $agent["id"] ]);
// Talaba qo'shish
// $direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);


include "system/head.php";
?>

<link rel="stylesheet" href="theme/main/assets/css/profile.css?v=<?=filemtime("theme/main/assets/css/profile.css")?>">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=<?=filemtime("theme/main/assets/css/cv.css")?>">

<div class="container-fluid pt-120 mb-4">
    <div class="row justify-content-center">
        <!-- LEFT MENUS START -->
        <div class="left-menu-wrapper">
            <a href="agent_profile/my" class="menu-item <?=($url[1] == "my" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <?=t("Profil")?>
            </a>

            <a href="agent_profile/addStudent" class="menu-item <?=($addStdunt ? "active" : "")?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="bi bi-plus" viewBox="0 0 24 24"> 
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> 
                </svg>
                <?=t("Talaba qo'shish")?>
            </a>
            
            <a href="agent_profile/studentsList" class="menu-item <?=($url[1] == "studentsList" ? "active" : "")?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="bi bi-card-checklist" viewBox="0 0 24 24"> 
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/> <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/> 
                </svg>
                <?=t("Qo'shilgan talabalar")?>
            </a>

            <a href="agent_profile/payments" class="menu-item <?=($url[1] == "payments" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <?=t("To'lovlar")?>
            </a>

            <a href="agent_profile/withdraw_money" class="menu-item <?=($url[1] == "withdraw_money" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                    <line x1="12" y1="1" x2="12" y2="23"></line>                    
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <?=t("Balans dan pul chiqarish")?>
            </a>

            <a href="agent-shartnoma/<?=encode(json_encode([
                    "s" => "a", // zapros
                    "c" => $agent["id"] // code
                ]))?>" target="_blank" class="menu-item <?=($url[1] == "shartnoma" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <?=t("Shartnomani yuklab olish")?>
            </a>
            
            <a href="agent_profile/referal_link" class="menu-item <?=($url[1] == "referal_link" ? "active" : "")?>">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link">
                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1.002 1.002 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4.018 4.018 0 0 1-.128-1.287z"/> <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243L6.586 4.672z"/> 
                </svg>
                <?=t("Yo'naltiruvchi havolangiz")?>
            </a>

            <a href="/exit" class="menu-item">
                <svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <?=t("Akkauntdan chiqish")?>
            </a>
        </div>
        <!-- LEFT MENUS - END -->
        
        <? if ($url[1] == "my") { ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper">
                <div class="profile-information-section">
                    <div style="position:relative; bottom: -10px;" class="profile-image">
                        <img src="https://niuedu.uz/profileimg.php?n=<?=mb_substr(mb_strtoupper($agent["last_name"]), 0, 1).mb_substr(mb_strtoupper($agent["first_name"]), 0, 1)?>&c=green" id="avatar" style="border-radius: 50%; width:100px;"  alt="avatar" >
                    </div>
                    <div style="margin-top: 26px;" class="full-name text-dark">
                        <?=$agent["first_name"]." ".$agent["last_name"]." ".$agent["father_first_name"]?>
                    </div>
                    <div class="profile-type text-uppercase text-dark">
                        <?=t("Telefon")?>: <b><?=$systemUser["login"]?></b>
                    </div>
                    <div class="profile-type text-uppercase text-dark">
                        <?=t("Karta raqami")?>: <b><?=$agent["card_number"]?></b>
                    </div>
                    <div class="profile-type text-uppercase text-dark">
                        <?=t("KARTA EGASI")?>: <b><?=$agent["transit_check"]?></b>
                    </div>
                    <div class="profile-type text-uppercase text-dark">
                        <?=t("PASSPORT JSHR")?>: <b><?=$agent["passport_jshr"]?></b>
                    </div>
                    <div class="profile-type text-dark">
                        <?=t("Balans")?>: <b><?=number_format($agent["balance"])?> so'm</b>
                    </div>
                    <div class="profile-type text-dark">
                        <?=t("ID")?>: <b><?=$agent["id"]?></b>
                    </div>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>

        <? if ($addStdunt) { ?>

            <?
                // $url[0] = "cv"; $url2[1] = "cv";
                // $url[1] = "check"; $url2[2] = "check";
                // $url[2] = "shartnoma"; $url2[3] = "shartnoma";  
                $no_header = true;
                $no_footer = true;  
                $agent_id = $agent["id"];
            ?>

            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2">
                    <?
                    if ($url2[2] == "oqishni-kochirish") {
                        echo '<h1 class="text-danger text-center mt-4 pt-4">Agentlar uchun o\'qishni ko\'chirish bo\'yicha talaba qo\'shish mavjud emas</h1>';
                    } else {
                        include "cv.php";
                    }
                    ?>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>
        
        <? if ($url[1] == "studentsList") { ?>
             <!-- studentsList -->
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3">
                    <div class="table-responsive table-bordered table-striped p-0 col-12 tableWide-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?=t("ID")?></th>
                                    <th><?=t("F.I.SH")?></th>
                                    <th><?=t("Jinsi")?></th>
                                    <th><?=t("Raqami")?></th>
                                    <th><?=t("Abituriyent")?></th>
                                    <th><?=t("O'qishni ko'chirish")?></th>
                                    <th><?=t("Imtihon uchun to'lov")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $talaba_soni_umumiy = 0;
                                    $oddiy = 0;
                                    $oqishni_kochirish = 0;
                                    $agent_requests = $db->in_array("SELECT * FROM requests WHERE firm_id = ?", [ $agent["id"] ]);
                                ?>
                                <? foreach ($agent_requests as $request) { 
                                    // $direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $request["direction_id"] ]);
                                    $oddiy += $request["reg_type"] == "oddiy" ?? 1;
                                    $oqishni_kochirish += $request["reg_type"] == "oqishni-kochirish" ?? 1;
                                    $talaba_soni_umumiy += 1;

                                    $request["payme"] = $db->in_array("SELECT * FROM transactions WHERE order_id = ? AND state = 2 OR order_id = ? AND state = 2", [
                                        $request["id"],
                                        $request["code"]
                                    ]);
                                ?>
                                    <tr>
                                        <td><?=$request["code"]?></td>
                                        <td><?=$request["last_name"].' '.$request["first_name"].' '.$request["father_first_name"]?></td>
                                        <td><?=t($request["sex"])?></td>
                                        <td><?=$request["phone_1"]?></td>
                                        <td><?=($request["reg_type"] == "oddiy" ? 1 : "")?></td>
                                        <td><?=($request["reg_type"] == "oqishni-kochirish" ? 1 : "")?></td>
                                        <td>
                                            <?
                                            if (!empty($request["payme"][0]["id"])) {
                                                foreach ($request["payme"] as $payme) {
                                                    echo '<b class="text-success"> qilingan ('.number_format(($payme["amount"] / 100), 0).' UZS)</b>';
                                                }
                                            } else {
                                                echo '<b class="text-danger">qilinmagan!</b>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <? } ?>
                                    <tr class="btn-reveal-trigger">
                                        <th class="py-2"><?=t("Jami")?>:</th>
                                        <th class="py-2"><?=number_format($talaba_soni_umumiy)?></th>
                                        <th class="py-2"></th>
                                        <th class="py-2"></th>
                                        <th class="py-2"><?=number_format($oddiy)?></th>
                                        <th class="py-2"><?=number_format($oqishni_kochirish)?></th>
                                        <th class="py-2"></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th><?=t("Talaba soni")?></th>
                                        <th></th>
                                        <th></th>
                                        <th><?=t("Talaba soni")?></th>
                                        <th><?=t("Talaba soni")?></th>
                                        <th></th>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End studentsList -->
        <? } ?>

        <? if ($url[1] == "payments") { ?>
            <!-- Profile Info -->
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3">
                    <div class="table-responsive p-0 col-12 tableWide-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th><?=t("kim tomondanligi")?></th>
                                    <th><?=t("to'lov miqdori")?></th>
                                    <th><?=t("to'lov sanasi")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $talaba_soni_umumiy = 0;
                                ?>
                                <? foreach ($agent_balances as $agent_balance) { 
                                    $request = $db->assoc("SELECT * FROM requests WHERE code = ?", [ $agent_balance["request_id"] ]);
                                    $tolangan_summa_umumiy = $agent_balance["amount"];
                                    $talaba_soni_umumiy += 1;
    
                                ?>
                                    <tr>
                                        <td><?=$agent_balance["id"]?></td>
                                        <td><?=$request["last_name"].' '.$request["first_name"]?></td>
                                        <td><?=number_format($agent_balance["amount"])?></td>
                                        <td><?=$agent_balance["payment_date"]?></td>
                                    </tr>
                                <? } ?>
                                    <tr class="btn-reveal-trigger">
                                        <th class="py-2"><?=t("Jami")?>:</th>
                                        <th class="py-2"><?=number_format($talaba_soni_umumiy)?></th>
                                        <th class="py-2"><?=number_format($tolangan_summa_umumiy)?></th>
                                        <th class="py-2"></th>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Profile Info -->
        <? } ?>
        
        <? if ($url[1] == "withdraw_money") { ?>
            <? 
                $amount = "hello";
            ?>
            <div class="profile-information-section-wrapper-2">
                <div class="profile-information-section-2 pt-3">
                <form action="/<?=$url2[0]?>/agent_profile/<?=$url[1]?>" method="POST" id="msform" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="withdraw_money" required>
                        <div class="form-card">
                            
                            <?=getError("amount")?>
                            <? if ($error) { ?>
                                <h4 class="text-center text-danger"><?=$error?></h4>
                            <? } ?>
                            <label class="fieldlabels" for="amount"><?=t("Miqdori")?> (<?=t("max")?>: <?=number_format($agent["balance"])?> so'm) <?=t("chiqarish mumkin")?> <label class="text-danger">*</label></label>
                            <div class="input-price">
                                <input type="text" name="amount" max="<?=$agent["balance"]?>" placeholder="<?=t("Miqdori")?> " id="price-input" required="">
                                <span class="currency">SO'M</span>
                            </div>
                            <style>
                                .input-price {
                                    position: relative;
                                }
                                .input-price input {
                                    padding-left: 60px !important;
                                }
                                .input-price .currency {
                                    position: absolute;
                                    top: 10px;
                                    left: 12px;
                                    color: #000;
                                } 
                            </style>

                            <input type="submit" name="submit" class="next action-button" value="<?=t("Saqlash")?>" style="width:130px;">
                        </div>
                    </form>
                </div>
            </div>
        <? } ?>

        <? if ($url[1] == "referal_link") { ?>
            <div class="profile-information-section-wrapper-2 mb-0">
                <div class="profile-information-section-2 pt-3">
                    
                    <h2 class="mt-3"><?=t("Yo'naltiruvchi havolangiz")?></h2>
                    <div class="form mt-3 mb-4">
                        <div class="copy-text">
                            <input type="text" id="referal_link" class="text" value="https://niuedu.uz/<?=$url2[0]?>/cv?agent_id=<?=$agent["id"]?>"  readonly/>
                            <button class="copy"><i class="fa fa-clone"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

    </div>

</div>

<? if ($url[1] == "referal_link") { ?>
<style>
    .copy-text {
        position: relative;
        padding: 10px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        display: flex;
    }
    .copy-text input.text {
        padding: 5px 10px;
        font-size: 18px;
        color: #555;
        border: none;
        outline: none;
        width: 90%;
    }
    .copy-text button {
        padding: 10px 15px;
        background: #198A29;
        color: #fff;
        font-size: 18px;
        border: none;
        outline: none;
        border-radius: 10px;
        cursor: pointer;
    }

    .copy-text button:active {
        background: #809ce2;
    }
    .copy-text button:before {
        content: "<?=t("nusxa olindi")?>";
        position: absolute;
        top: -45px;
        right: 0px;
        background: #198A29;
        padding: 8px 10px;
        border-radius: 20px;
        font-size: 15px;
        display: none;
    }
    .copy-text button:after {
        content: "";
        position: absolute;
        top: -20px;
        right: 25px;
        width: 10px;
        height: 10px;
        background: #198A29;
        transform: rotate(45deg);
        display: none;
    }
    .copy-text.active button:before,
    .copy-text.active button:after {
        display: block;
    }

    .table>:not(caption)>*>*{
        min-width: 100px;
        padding-left: 5px;
        text-align:center;
    }
</style>
<? } ?>

<? include "system/scripts.php"; ?>


<script>

    <? if ($url[1] == "referal_link") { ?>
        let copyText = document.querySelector(".copy-text");
        copyText.querySelector("button").addEventListener("click", function () {
            let input = copyText.querySelector("input.text");
            input.select();
            document.execCommand("copy");
            copyText.classList.add("active");
            window.getSelection().removeAllRanges();
            setTimeout(function () {
                copyText.classList.remove("active");
            }, 2500);
        });
    <? } ?>
    $("#price-input").on("input", function(){
        var val = $(this).val().replaceAll(",", "").replaceAll(" ", "");
        var val2 = Number(val);

        if (val.length > 0) {    
            if(val2 == val) {
                $(this).val(
                    String(val).replace(/(.)(?=(\d{3})+$)/g,'$1,')
                );
            } else {
                $(this).val($(this).val().substring(0, 0));
            }
        }
    });
</script>

<? if ($url[1] == "addStudent") { ?>
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

        foreach ($directions as $direction) {
            $direction_learn_types = $db->in_array("SELECT learn_type_id FROM direction_learn_types WHERE direction_id = ?", [
                $direction["id"]
            ]);

            $learn_type_id_arr = [];
            foreach ($direction_learn_types as $direction_learn_type) {
                array_push($learn_type_id_arr, $direction_learn_type["learn_type_id"]);
            }
            $direction_learn_types_arr[$direction["id"]] = $learn_type_id_arr;
        }

        echo "var direction_learn_types = ".json_encode($direction_learn_types_arr).";";
        // 
        $learn_types = $db->in_array("SELECT id, name FROM learn_types");
        $learn_types_arr = [];

        foreach ($learn_types as $learn_type) {
            $learn_types_arr[$learn_type["id"]] = $learn_type;
        }
        echo "var learn_types = ".json_encode($learn_types_arr).";";
        ?>
        var selected_learn_type = "<?=$_POST["learn_type"]?>";

        function directionChange() {
            $("#learn_type").html("");

            var direction_id = $("#direction").find("option:selected").attr("data-direction-id");
            
            for (key in direction_learn_types[direction_id]) {
                var learn_type_id = direction_learn_types[direction_id][key];
                var learn_type = learn_types[learn_type_id];

                var html = '<option value="'+learn_type["name"]+'" '+(selected_learn_type == learn_type["name"] ? 'selected=""' : '')+'>'+learn_type["name"]+'</option>';
                $("#learn_type").append(html);
            }
        }

        directionChange();

        $("#direction").on("change", function(){
            directionChange();
        });

        $("input[type='file']").on("change", function() {
            console.log(this.files[0]);
            if ((this.files[0].size / 1024 / 1024) > 10) {
                $(this).attr("type", "text");
                $(this).attr("type", "file");
                alert("Fayl hajmi 10MB dan oshlmasligi kerak! Iltimos faylni qayta yuklang!");
            }
        });

        $("#passport_serial_number").on("input keyup", function(e){
            $(this).val($(this).val().substring(0, 9));
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

    });
</script>
<? } ?>

<? include 'system/end.php'; ?>