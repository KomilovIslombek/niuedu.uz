<?
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$is_config = true;

if (empty($load_defined)) include 'load.php';

if (isAuth() === false) {
    // header("Location: /login");
}

include "system/head.php";

$video = $db->assoc("SELECT * FROM videos WHERE id = 6");
$post = $db->assoc("SELECT * FROM posts WHERE id = 1");
$savollar = $db->in_array("SELECT * FROM savol_javoblar");

$home_images_arr = $db->in_array("SELECT * FROM home_images");
$home_images = [];
foreach ($home_images_arr as $key => $home_image) {
    $home_images[$key] = image($home_image["image_id"]);
}

$directions = $db->in_array("SELECT * FROM directions WHERE active = 1");

$newses = $db->in_array("SELECT * FROM news ORDER BY id DESC LIMIT 7");
?>

<link rel="stylesheet" href="theme/main/assets/css/index.css">
<!-- <link rel="stylesheet" href="../modules/plyr/plyr.css"> -->

<main style="overflow:hidden;">

    <!-- hero area start -->
    <section class="hero__area hero__height d-flex align-items-center white-bg p-relative">
        <div class="video-shadow-bottom"></div>
        <div class="video-shadow-top"></div>

        <div class="container">
            <div class="hero__content-wrapper mt-0">
                <div class="row align-items-center">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-8">
                        <div class="hero__content p-relative z-index-1">
                            <h3 class="hero__title">
                                <span><?=t("Navoiy innovatsiyalar universitetiga xush kelibsiz")?></span>
                            </h3>
                            <p><?=t("Yangi olam, yangi jamiyatda ilg'or texnologiyalar innovatsiyalar asosida yetuk mutaxassislarni tayyorlash maskani.")?></p>
                            
                            <div class="buttons">
                                <a class="btn-sm e-btn bdevs-el-btn site-btn" href="/cv"><?=t("Hujjat topshirish")?></a>
                                
                                <a class="btn-sm e-btn slider__btn bdevs-el-btn bg-white text-dark" href="/profile/my"> <?=t("Shaxsiy kabinet")?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="video-wrapper">
            <video id="bannerVideo" autoplay="autoplay" loop="loop" muted="muted" playsinline=""><source src="videos/bg-video-compressed.mp4" type="video/mp4"></video>
        </div>
    </section>
    <!-- hero area end -->

    <!-- Ta'lim yo'nalishlari -->
    <section class="category__area pt-50 pb-0" id="talim-yonalishlari">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                <div class="section__title-wrapper mb-45">
                    <h2 class="section__title text-center"><?=t("Ta'lim yo'nalishlari")?></h2>
                </div>
                </div>
            </div>
            <div class="row">
                <? foreach ($directions as $direction) { ?>
                    <?
                    $direction_image = image($direction["image_id"]);
                    ?>
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6">
                        <div class="category__item mb-30 transition-3 d-flex align-items-center" style="background-image: url('<?=$direction_image["file_folder"]?>');">
                            <div class="category__content">
                                <h4 class="category__title"><a href="/<?=$url2[0]?>/direction/<?=$direction["id_name"]?>"><?=lng($direction["name"])?></a></h4>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </section>
    <!-- end Ta'lim yo'nalishlari -->

    <!-- Yangiliklar -->
    <section class="course__area grey-bg pt-115 pb-120">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-8">
                    <div class="section__title-wrapper mb-60">
                    <h2 class="section__title text-dark"><?=translate("Yangiliklar")?></h2>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-4">
                    <div class="category__more mb-60 float-md-end fix">
                    <a href="/<?=$lng?>/yangiliklar/1" class="link-btn">
                        <?=translate("Barcha yangiliklar")?>
                        <i class="far fa-arrow-right"></i>
                        <i class="far fa-arrow-right"></i>
                    </a>
                    </div>
                </div>
            </div>
            <div class="news__slider swiper-container">
                <div class="swiper-wrapper">
                    <? foreach ($newses as $news) { ?>
                        <?
                        $author = $db->assoc("SELECT id, first_name, last_name FROM users WHERE id = ?", [ $news["creator_user_id"] ]);
                        $image = image($news["image_id"]);
                        ?>
        
                        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 swiper-slide">
                            <div class="blog__wrapper">
                                <div class="blog__item white-bg mb-30 transition-3 fix">
                                    <div class="blog__thumb w-img fix">
                                    <a href="/<?=$lng?>/yangilik/<?=$news["id"]?>">
                                        <img src="<?=$image["file_folder"]?>" alt="">
                                    </a>
                                    </div>
                                    <div class="blog__content">
                                    <h3 class="blog__title"><a href="/<?=$lng?>/yangilik/<?=$news["id"]?>"><?=(mb_strlen(lng($news["name"])) > 60 ? mb_substr(lng($news["name"]), 0, 60)."..." : lng($news["name"]))?></a></h3>
        
                                    <div class="blog__meta d-flex align-items-center justify-content-between">
                                        <div class="blog__author d-flex align-items-center">
                                            <!-- <div class="blog__author-thumb mr-10">
                                                <img src="assets/img/blog/author/author-1.jpg" alt="">
                                            </div> -->
                                            <div class="blog__author-info">
                                                <h5>NIU</h5>
                                            </div>
                                        </div>
                                        <div class="blog__date d-flex align-items-center">
                                            <i class="fal fa-clock"></i>
                                            <span><?=date("Y-m-d", strtotime($news["created_date"]))?></span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>

                <!-- next and prev buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
    <!-- end Yangiliklar -->

    <link rel="stylesheet" href="theme/main/assets/css/accordion.css?v=1.0.1">

    <section class="mt-0 pt-80 pb-80 accordion-section" style="background-color:#198A29;">
        <div class="container accordion-container" id="savol-javoblar">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <h1 class="text-center mb-4 text-white"><?=t("ENG KO'P SO'RALADIGAN SAVOLLAR")?></h2>
                    <div class="accordion">
                        <? foreach ($savollar as $savol) { ?>
                            <div class="accordion-item">
                                <button id="accordion-button-1" aria-expanded="false"><span class="accordion-title"><?=lng($savol["savol"])?></span><span class="icon" aria-hidden="true"></span></button>
                                <div class="accordion-content">
                                    <p class="text-white"><?=lng($savol["javob"])?></p>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?
include "system/scripts.php";
?>

<script>
    const items = document.querySelectorAll(".accordion button");

    function toggleAccordion() {
        const itemToggle = this.getAttribute('aria-expanded');
        
        for (i = 0; i < items.length; i++) {
            items[i].setAttribute('aria-expanded', 'false');
        }
        
        if (itemToggle == 'false') {
            this.setAttribute('aria-expanded', 'true');
        }
    }

    items.forEach(item => item.addEventListener('click', toggleAccordion));
</script>

<?
include "system/end.php";
?>

