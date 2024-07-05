<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$mission = $db->assoc("SELECT * FROM mission");
if (empty($mission["id"])) exit(http_response_code(404));

$mission["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', $mission["html"]);
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
                        <?=lng($mission["html"])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- page title area end -->

</main>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>