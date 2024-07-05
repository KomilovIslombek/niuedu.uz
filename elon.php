<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$post = $db->assoc("SELECT * FROM ads WHERE id = ?", [ $url[1] ]);
if (empty($post["id"])) exit(http_response_code(404));

$post["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', $post["html"]);
?>

<style>
    .course__wrapper img {
        max-width: 100%;
    }
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
    <div class="page__title-shape">
        <img class="page-title-shape-5 d-none d-sm-block" src="theme/main/assets/img/page-title/page-title-shape-1.png" alt="">
        <img class="page-title-shape-7" src="theme/main/assets/img/page-title/page-title-shape-4.png" alt="">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-8 col-lg-8 mt-4">
                <div class="course__wrapper">
                    <?=lng($post["html"])?>
                </div>
            </div>
        </div>
    </div>
    </section>
    <!-- page title area end -->

</main>

<? include "system/scripts.php"; ?>

<script>
    $(".course__wrapper").find("img").each(function(){
        $(this).removeAttr("width").removeAttr("height");
    });
</script>

<? include 'system/end.php'; ?>