<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$post = $db->assoc("SELECT * FROM posts WHERE id_name = ?", [ $url[1] ]);
if (empty($post["id"])) exit(http_response_code(404));

$post["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', $post["html"]);
?>

<style>
    p, span {
        color: #000;
    }

    td {
        padding-left: 10px;
    }
</style>

<main>

<!-- page title area start -->
<section class="page__title-area pt-120 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-8 col-lg-8 mt-4">
                <div class="course__wrapper">
                    <div class="course__wrapper">
                        <?=lng($post["html"])?>
                    </div>

                    <? if (!$no_cv_button) { ?>
                        <a href="/cv" class="e-btn mt-4"><?=t("ARIZA TOPSHIRISH")?></a>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- page title area end -->

</main>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>