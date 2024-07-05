<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$direction = $db->assoc("SELECT * FROM directions WHERE id_name = ?", [ $url[1] ]);
if (empty($direction["id"])) exit(http_response_code(404));

// $direction["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', lng($direction["html"]));
?>

<!-- <style>
    p, span {
        color: #000;
    }

    td {
        padding-left: 10px;
    }
</style> -->

<main class="pt-100 pb-0">
<?
    function directionHtml($arr) {
        $arr["type"] = (gettype($arr["key"] / 2) == "double" ? "image_right" : "image_left");

        $html_1 = '<div class="col-xxl-5 offset-xxl-1 col-xl-5 offset-xl-1 col-lg-6 col-md-8">
            <div class="why__content pr-50 mt-40">
                <div class="section__title-wrapper mb-30">
                    <a href="/direction/'.$arr["id_name"].'">
                        <h2 class="section__title '.($arr["type"] == "image_left" ? "text-white" : "text-dark").'">'.lng($arr["name"]).'</h2>
                    </a>
                    <p>'.t("Akademik daraja").': <b>'.lng($arr["academic_level"]).'</b><br>'.t("O'qish tizimi").': <b>'.lng($arr["learning_type"]).'</b></p>

                    <table class="table table-hover mt-3 mb-3">
                        <thead>
                            <tr>
                                <th>'.t("Ta'lim shakli").'</th>
                                '.(lng($arr["kunduzgi_oqish_muddati"]) || lng($arr["kunduzgi_bir_semestr"]) || lng($arr["kunduzgi_haftalik_oquv_yuklamasi"]) ? '<th>'.t("Kunduzgi").'</th>' : '').'
                                '.(lng($arr["kechki_oqish_muddati"]) || lng($arr["kechki_bir_semestr"]) || lng($arr["kechki_haftalik_oquv_yuklamasi"]) ? '<th>'.t("Kechki").'</th>' : '').'
                                '.(lng($arr["sirtqi_oqish_muddati"]) || lng($arr["sirtqi_bir_semestr"]) || lng($arr["sirtqi_haftalik_oquv_yuklamasi"]) ? '<th>'.t("Sirtqi").'</th>' : '').'
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>'.t("O'qish muddati").'</th>
                                '.(lng($arr["kunduzgi_oqish_muddati"]) ? '<td>'.lng($arr["kunduzgi_oqish_muddati"]).'</td>' : '').'
                                '.(lng($arr["kechki_oqish_muddati"]) ? '<td>'.lng($arr["kechki_oqish_muddati"]).'</td>' : '').'
                                '.(lng($arr["sirtqi_oqish_muddati"]) ? '<td>'.lng($arr["sirtqi_oqish_muddati"]).'</td>' : '').'
                            </tr>
                            <tr>
                                <th>'.t("Bir semestr").'</th>
                                '.(lng($arr["kunduzgi_bir_semestr"]) ? '<td>'.lng($arr["kunduzgi_bir_semestr"]).'</td>' : '').'
                                '.(lng($arr["kechki_bir_semestr"]) ? '<td>'.lng($arr["kechki_bir_semestr"]).'</td>' : '').'
                                '.(lng($arr["sirtqi_bir_semestr"]) ? '<td>'.lng($arr["sirtqi_bir_semestr"]).'</td>' : '').'
                            </tr>
                            <tr>
                                <th>'.t("Haftalik o'quv yuklamasi").'</th>
                                '.(lng($arr["kunduzgi_haftalik_oquv_yuklamasi"]) ? '<td>'.lng($arr["kunduzgi_haftalik_oquv_yuklamasi"]).'</td>' : '').'
                                '.(lng($arr["kechki_haftalik_oquv_yuklamasi"]) ? '<td>'.lng($arr["kechki_haftalik_oquv_yuklamasi"]).'</td>' : '').'
                                '.(lng($arr["sirtqi_haftalik_oquv_yuklamasi"]) ? '<td>'.lng($arr["sirtqi_haftalik_oquv_yuklamasi"]).'</td>' : '').'
                            </tr>
                            <tr>
                                <th>'.t("Narxlar").'</th>
                                '.($arr["kunduzgi_narx_int"] ? '<td>'.($arr["kunduzgi_narx_int"] / 1000000).' '.t("mln").'</td>' : '').'
                                '.($arr["kechki_narx_int"] ? '<td>'.($arr["kechki_narx_int"] / 1000000).' '.t("mln").'</td>' : '').'
                                '.($arr["sirtqi_narx_int"] ? '<td>'.($arr["sirtqi_narx_int"] / 1000000).' '.t("mln").'</td>' : '').'
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';

        $html_image = '<div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8">
            <div class="why__thumb">
                <img src="'.$arr["image"]["file_folder"].'" alt="" style="border-radius:30px;">
            </div>
        </div>';

        echo '<!-- why area start -->
            <section class="why__area '.($arr["key"] == 0 ? "pt-120 pb-70" : "pt-70 pb-70").' '.($arr["type"] == "image_left" ? "bg-1" : "bg-2").'" id="category_'.$arr["id"].'">
                <div class="container">
                    <div class="row align-items-center'.($arr["type"] == "image_left" ? " reverse" : "").'">
                        '.$html_1.'
                        '.$html_image.'
                    </div>
                </div>
            </section>
            <!-- why area end -->';
    }

    // foreach ($directions as $direction_key => $direction) {
    // }
    $direction["key"] = $direction_key + 1;
    $direction["image"] = image($direction["image_id"]);
    directionHtml($direction);
?>
</main>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>