<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$photo = $db->assoc("SELECT * FROM photos WHERE id = ?", [ $url[1] ]);
if (empty($photo["id"])) exit(http_response_code(404));

$photo["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', $photo["html"]);

$images = [];

$html = $photo["html"];
$doc = new DOMDocument();
@$doc->loadHTML($html);
$tags = $doc->getElementsByTagName("img");

foreach ($tags as $tag) {
    array_push($images, $tag->getAttribute("src"));
}
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

    .swiper-container {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .swiper-slide img {
        width: 100%;
    }

    .gallery-thumbs {
        margin-top: 10px;
    }
</style>

<main>

    <!-- page title area start -->
    <section class="page__title-area pt-120 pb-90 mt-40">
        <div class="page__title-shape">
            <img class="page-title-shape-5 d-none d-sm-block" src="theme/main/assets/img/page-title/page-title-shape-1.png" alt="">
            <img class="page-title-shape-7" src="theme/main/assets/img/page-title/page-title-shape-4.png" alt="">
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-8 col-lg-8 mt-4">
                    <div class="course__wrapper">
                        <h2 class="text-dark text-center mb-4"><?=lng($photo["name"])?></h2>
                        <div class="swiper-container gallery-top">
                            <div class="swiper-wrapper">
                                <? foreach ($images as $image) { ?>
                                    <div class="swiper-slide">
                                        <img src="<?=$image?>" alt="">
                                    </div>
                                <? } ?>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                        <div class="swiper-container gallery-thumbs">
                            <div class="swiper-wrapper">
                                <? foreach ($images as $image) { ?>
                                    <div class="swiper-slide">
                                        <img src="<?=$image?>" alt="">
                                    </div>
                                <? } ?>
                            </div>
                        </div>
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

    var galleryThumbs = new Swiper('.gallery-thumbs', {
        autoHeight: true,
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
    });
    var galleryTop = new Swiper('.gallery-top', {
        autoHeight: true,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: galleryThumbs
        }
    });
</script>

<? include 'system/end.php'; ?>