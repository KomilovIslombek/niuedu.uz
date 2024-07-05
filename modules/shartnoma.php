<?
$transaction_id = $old_request["id"];
$payment_confirmed = false;

$confirmed_payment_click = $db->assoc("SELECT * FROM payments WHERE product_id = ? AND status = 'confirmed' OR product_id = ? AND status = 'confirmed'", [
    $transaction_id,
    $old_request["code"]
]);

if (empty($confirmed_payment_click["id"])) {
    $confirmed_payment_payme = $db->assoc("SELECT * FROM transactions WHERE hide = 0 AND order_id = ? AND state = 2 OR order_id = ? AND state = 2 AND hide = 0", [
        $transaction_id,
        $old_request["code"]
    ]);
}

if (empty($confirmed_payment_payme["id"])) {
    $confirmed_payment_kassa = $db->assoc("SELECT * FROM payments_kassa_aparat WHERE order_id = ?", [
        $old_request["id"]
    ]);
}

if (in_array($direction["id"], ["101", "102", "1"]) && $old_request["learn_type"] == "Sirtqi" || $old_request["reg_type"] == "ikkinchi-mutaxassislik") {
    if (!empty($confirmed_payment_payme["id"]) && $old_request["suhbat"] != 1) {
        $db->update("requests", ["suhbat" => 1], ["id" => $old_request["id"]]);
        $old_request["suhbat"] = 1;
    }
}
?>

<h2 id="heading"><?=t("NAVOIY INNOVATSIYALAR UNIVERSITETI")?></h2>
<!-- <p>Fill all form field to go to next step</p> -->
<? if ($old_request["javob"]) { ?>
    <p>Javob:</p>
    <h3><?=$old_request["javob"]?></h3>
<? } else { ?>
    <? if ($old_request["suhbat"] == 1) { ?>
        <h3 class="mt-4 mb-4 text-success"><?=t("Tabriklaymiz siz talabalikka tavsiya etildingiz")?></h3>
    <? } else if ($old_request["suhbat"] == 2) { ?>
        <h3 class="mt-4 mb-4 text-danger"><?=t("Kechirasiz siz talabalikka tavsiya etilmadingiz")?></h3>

        <h3 class="mt-4 mb-4 text-success"><?=t("Sizda 165,000 so'm to'lov evaziga qayta imtihon topshirish imkoniyati mavjud. Qayta imtihon topshirish uchun «Qayta imtihon topshirish» tugmasini bosing")?></h3>

        <a href="<?=$url2[0]?>/cv/qayta-imtihon-topshirish?id=<?=$old_request["code"]?>&submit=Tekshirish" class="btn btn-success text-white mb-4 btn-lg"><?=t("Qayta imtihon topshirish")?></a>
    <? } else {        
        if (!empty($confirmed_payment_payme["id"])) {
            echo "<hr>";
            echo '<h3 class="text-success">'.t("Siz imtihon uchun to'lovni payme orqali amalga oshirgansiz").'</h3>';
            $payment_confirmed = true;
        } else if (!empty($confirmed_payment_click["id"])) {
            echo "<hr>";
            echo '<h3 class="text-success">'.t("Siz imtihon uchun to'lovni click orqali amalga oshirgansiz").'</h3>';
            $payment_confirmed = true;
        } else if (!empty($confirmed_payment_kassa["id"])) {
            echo "<hr>";
            echo '<h3 class="text-success">'.t("Siz imtihon uchun to'lovni amalga oshirgansiz").'</h3>';
            $payment_confirmed = true;
        } else {
            echo "<hr>";

            if (in_array($direction["id"], ["101", "102", "1"]) && $old_request["learn_type"] == "Sirtqi" || $old_request["reg_type"] == "ikkinchi-mutaxassislik") {
                echo "<h3>".t("Ariza topshirish uchun quyidagi link orqali o'tib, 165.000 so'm miqdorda PAYME dasturi orqali pul o'tkazishingiz talab etiladi.")."</h3>";
            } else {
                echo "<h3>".t("Imtihon topshirish uchun quyidagi link orqali o'tib, 165.000 so'm miqdorda PAYME dasturi orqali pul o'tkazishingiz talab etiladi.")."</h3>";
            }

            ?>
            <hr>

            <?

            echo "<br>";
            ?>
            <div class="btn-group" id="payment_link">
                <!-- <a href="/payment/create/click/<?=encode($old_request["code"])?>" class="btn btn-default">
                    <img src="images/payment_systems/click_logo.png" alt="click logo" style="width:200px;">
                </a> -->

                <a href="/payment/create/payme/<?=encode($old_request["code"])?>" class="btn btn-default">
                    <img src="images/payment_systems/payme_logo.png" alt="payme logo" style="width:200px;margin-top:10px;">
                </a>
            </div>

            <hr>

            <h5 class="purple-text text-center mb-2"><?=t("To'lovni amalga oshirib bo'lgan bo'lsangiz «Imtihon topshirish» tugmasini bosing")?></h5>

            <h3>
                <? if ($url[0] == "profile") { ?>
                    <a href="<?=$url2[0]?>/profile/shartnoma/download" class="btn btn-success text-white mb-2 btn-lg"><?=t("Imtihon topshirish")?></a>
                <? } else { ?>
                    <a href="<?=$url2[0]?>/cv/check?id=<?=$old_request["code"]?>&submit=Tekshirish" class="btn btn-success text-white mb-2 btn-lg"><?=t("Imtihon topshirish")?></a>
                <? } ?>
            </h3>
            <?

            // echo '<h3><a href="'.$payment_url.'" class="text-success" style="display:none;" id="payment_link">To\'lovni amalga oshirish</a></h3>';

            // echo '<h3><a class="text-secondary" id="payment_link_false">To\'lovni amalga oshirish</a></h3>';
        }
    }

    $direction = $db->assoc("SELECT * FROM directions WHERE id = ?", [ $old_request["direction_id"] ]);
    $course_arr = $db->assoc("SELECT * FROM courses WHERE id = ?", [ $old_request["course_id"] ]);
    $course = $course_arr["name"] ? $course_arr["name"] : ($old_request["to_course"] ? $old_request["to_course"] : "1-kurs");
    ?>

    <? if ($payment_confirmed) { ?>
        <?
        if (in_array($direction["id"], ["101", "102", "1"]) && $old_request["learn_type"] == "Sirtqi" || $old_request["reg_type"] == "ikkinchi-mutaxassislik") {
            
        } else {
        ?>
            <h3 class="text-center text-dark"><?=t("Imtihon topshirish uchun")?></h3>
            
            <table class="table table-striped table-bordered mt-3 mb-3">
                <tr>
                    <th><?=t("Sayt")?></th>
                    <td><b><a href="https://test.niuedu.uz" class="text-info">https://test.niuedu.uz</a></b></td>
                </tr>
                <tr>
                    <th><?=t("Talaba ID")?></th>
                    <td><b><?=$old_request["code"]?></b></td>
                </tr>
                <tr>
                    <th><?=t("Passport seriyasi hamda raqami")?></th>
                    <td><b><?=$old_request["passport_serial_number"]?></b></td>
                </tr>
            </table>
        <? } ?>
    <? } ?>

    <h3 class="text-center text-dark"><?=t("Talaba ma'lumotlari")?></h3>

    <table class="table table-striped table-bordered mt-3 mb-3">
        <tr>
            <th><?=t("Talaba F.I.SH.")?></th>
            <td><b><?=$old_request["last_name"]?> <?=$old_request["first_name"]?> <?=$old_request["father_first_name"]?></b></td>
        </tr>
        <tr>
            <th><?=t("Ta'lim turi")?></th>
            <td><b><?=lng($direction["academic_level"])?></b></td>
        </tr>
        <tr>
            <th><?=t("Ta'lim shakli")?></th>
            <td><b><?=t($old_request["learn_type"])?></b></td>
        </tr>
        <tr>
            <th><?=t("O'quv yili")?></th>
            <td><b>2023-2024</b></td>
        </tr>
        <tr>
            <th><?=t("O'quv kursi")?></th>
            <td><b><?=t($course)?></b></td>
        </tr>
        <tr>
            <th><?=t("ID raqami")?></th>
            <td><b><?=(idCode($direction["code"], $old_request["id"]))?></b></td>
        </tr>
        <tr>
            <th><?=t("Ta'lim yo'nalishi")?></th>
            <td><b><?=lng($direction["name"])?></b></td>
        </tr>
        <tr>
            <th><?=t("Ta'lim muassasasi")?></th>
            <td><b><?=t("NAVOIY INNOVATSIYALAR UNIVERSITETI")?></b></td>
        </tr>
        <tr>
            <th><?=t("Tug'ilgan sanasi")?></th>
            <td><b><?=$old_request["birth_date"]?></b></td>
        </tr>
    </table>

    <? if ($old_request["suhbat"] == 1) { ?>
        <?
        if ($old_request["suhbat"] == 1) {
            $shartnoma_date = ($old_request["shartnoma_date"] ? $old_request["shartnoma_date"] : date("Y-m-d"));
            if (empty($old_request["shartnoma_date"])) {
                $db->update("requests", ["shartnoma_date" => date("Y-m-d")], ["code" => $old_request["code"]]);
                $db->update("requests", ["shartnoma_date" => date("Y-m-d")], ["code" => $old_request["code"]]);
            }

            if ($old_request["learn_type"] == "Kunduzgi") {
                $oqish_davomiyligi_yili = lng($direction["kunduzgi_oqish_muddati"]);
                $contract_amount = $direction["kunduzgi_narx_int"];
            } else if ($old_request["learn_type"] == "Kechki") {
                $oqish_davomiyligi_yili = lng($direction["kechki_oqish_muddati"]);
                $contract_amount = $direction["kechki_narx_int"];
            } else if ($old_request["learn_type"] == "Sirtqi") {
                $oqish_davomiyligi_yili = lng($direction["sirtqi_oqish_muddati"]);
                $contract_amount = $direction["sirtqi_narx_int"];
            }

            if ($course == "1-kursning 2-semestri" || $course == "2-kursning 4-semestri") {
                if ($direction["sirtqi_narx_int"] == 13000000) {
                    $contract_amount = 6500000;
                } else if ($direction["sirtqi_narx_int"] == 11000000) {
                    $contract_amount = 5500000;
                } else if ($direction["sirtqi_narx_int"] == 15000000) {
                    $contract_amount = 7500000;
                }
            }

            if (!empty($old_request["shartnoma_amount"])) $contract_amount = $old_request["shartnoma_amount"];
        }
        ?>
        <h3 class="text-center text-dark mt-3"><?=t("Shartnoma ma'lumotlari")?></h3>

        <table class="table table-striped table-bordered mt-3 mb-3">
            <tr>
                <th><?=t("Shartnoma raqami")?></th>
                <td><b><?=$direction["prefix"]?>-<?=(idCode($direction["code"], $old_request["id"]))?>/<?=($old_request["learn_type"] == "Sirtqi" ? "S" : "K")?></b></td>
            </tr>
            <tr>
                <th><?=t("Shartnoma sanasi")?></th>
                <td><b><?=$shartnoma_date?></b></td>
            </tr>
            <tr>
                <th><?=t("Shartnoma turi")?></th>
                <td><b><?=t("Bazaviy kontrakt")?></b></td>
            </tr>
            <tr>
                <th><?=t("Shartnoma shakli")?></th>
                <? if ($url[3] == "3-tomonlama" || $shartnoma == 3) { ?>
                    <td><b><?=t("3 tomonlama shartnoma")?></b></td>
                <? } else { ?>
                    <td><b><?=t("2 tomonlama shartnoma")?></b></td>
                <? } ?>
            </tr>
            <tr>
                <th><?=t("Shartnoma summasi turi")?></th>
                <td><b><?=t("Stipendiyasiz")?></b></td>
            </tr>
            <tr>
                <th><?=t("Shartnoma summasi")?></th>
                <td><b><?=(number_format($contract_amount, 0, "", " "))?> so'm</b></td>
            </tr>
            <tr>
                <th><?=t("Chegirma")?></th>
                <td><b>0.0</b></td>
            </tr>
            <tr>
                <th colspan="2">
                    <h3 class="text-info">
                        <a href="https://niuedu.uz:4499/<?=encode(json_encode([
                            "s" => ($url[3] == "3-tomonlama" || $shartnoma == 3 ? 3 : 2),
                            "c" => $old_request["code"] // code
                        ]))?>" download="shartnoma.pdf" target="_blank"><?=t("Shartnomani pdf shaklda yuklab olish")?> <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/PDF_icon.svg" width="25px" style="margin-left:15px;"></a>
                    </h3>
                </th>
            </tr>
        </table>
    <? } ?>

    <h4><?=str_replace("https://t.me/niuedu_uz", '<a href="https://t.me/niuedu_uz" class="text-info">https://t.me/niuedu_uz</a>', t("Bizni https://t.me/niuedu_uz orqali kuzatib boring"))?></h4>
<? } ?>