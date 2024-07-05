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
?>

<style>
    .reverse {
        flex-direction: row-reverse;
        justify-content: center;
    }

    @media (max-width: 575px) {
        .why__thumb {
            margin-left: 0 !important;
        }
    }

    @media only screen and (min-width: 768px) and (max-width: 991px) {
        .why__thumb {
            margin-left: 0 !important;
        }
    }

    @media only screen and (min-width: 576px) and (max-width: 767px) {
        .why__thumb {
            margin-left: 0 !important;
        }
    }

    .bg-1 {
        background-color: #198A29 !important;
    }

    /* .bg-2 {
        background-color: #DBA074 !important;
    } */

    /* .why__area {
        border-radius: 40px;
        margin-left: 115px;
        margin-right: 115px;
    } */

    .bg-1 p,
    .bg-1 div,
    .bg-1 h2,
    .bg-1 th,
    .bg-1 td {
        color: #fff !important;
    }

    .bg-1 .e-btn {
        background-color: #ffffff;
        color: #000000;
    }

    .bg-1 .e-btn:hover {
        color: #517039;
    }

    .bg-2 .e-btn:hover {
        color: #DBA074;
    }

    .social-fixed {
        position: fixed;
        top: 40%;
        right: 0;
        z-index: 100;
        color: #fff;
        text-align: center;
    }

    .social-fixed li {
        padding: 10px;
    }

    .social-fixed .tg {
        background-color: #1CA1F0;
    }

    .social-fixed .fb {
        background-color: #4167B0;
    }

    .social-fixed .yt {
        background-color: #FF0101;
    }

    .social-fixed .in {
        background-color: #E2306C;
    }

    .social-fixed .te {
        background-color: #16a52b;
    }

    .social-fixed .te i {
        transform: rotate(90deg);
    }
</style>

<ul class="social-fixed">
    <li class="tg">
        <a href="https://t.me/nii_uz">
            <i class="fab fa-telegram"></i>
        </a>
    </li>

    <li class="fb">
        <a href="https://m.facebook.com/profile.php?id=100085299348203&eav=AfY_o2UlUooxGiBBtLxPchUXF9u9FAk03ar7W7qZ-lgdACDthE-maEBcmwf-bf_bZWo&tsid=0.6047059993881825&source=result">
            <i class="fab fa-facebook-f"></i>
        </a>
    </li>

    <li class="yt">
        <a href="https://youtu.be/YU0sVLVtUX0">
            <i class="fab fa-youtube"></i>
        </a>
    </li>

    <li class="in">
        <a href="https://www.instagram.com/nii.uz/">
            <i class="fab fa-instagram"></i>
        </a>
    </li>

    <li class="te">
        <a href="tel:998555000043">
            <i class="fa fa-phone"></i>
        </a>
    </li>
</ul>

<main style="overflow:hidden;">
    <div data-elementor-type="wp-page" data-elementor-id="15" class="elementor elementor-15" style="padding:200px 0;">
        <section class="elementor-section elementor-top-section elementor-element elementor-element-53ca114 elementor-section-full_width elementor-section-height-default elementor-section-height-default" data-id="53ca114" data-element_type="section">
            <div class="elementor-container elementor-column-gap-no">
                <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-db71462"
                    data-id="db71462" data-element_type="column">
                    <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-element-2ac2459 elementor-widget elementor-widget-cf7 bdevs-el cf7"
                            data-id="2ac2459" data-element_type="widget"
                            data-settings="{&quot;design_style&quot;:&quot;style_1&quot;}" data-widget_type="cf7.default">
                            <div class="elementor-widget-container">

                                <section class="contact__area">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xxl-7 col-xl-7 col-lg-6">
                                                <div class="contact__wrapper">
                                                    <div class="section__title-wrapper mb-40">
                                                        <h2 class="section__title">Navoiy Innovatsiyalar Institutiga xush kelibsiz, sizning tashrifingizdan minnadormiz.</h2>
                                                        <h3 class="mt-2">Saytimizga kirish uchun ismingiz va telefon raqamingizni qoldiring</h3>
                                                    </div>
                                                    <div class="contact__form">
                                                        <div role="form" class="wpcf7" id="wpcf7-f350-p15-o1" lang="en-US"
                                                            dir="ltr">
                                                            <div class="screen-reader-response">
                                                                <p role="status" aria-live="polite" aria-atomic="true"></p>
                                                                <ul></ul>
                                                            </div>
                                                            <form action="/wp/educal/contact/#wpcf7-f350-p15-o1" method="post" class="wpcf7-form init bdevs-cf7-form"
                                                                novalidate="novalidate" data-status="init">

                                                                <div class="row">
                                                                    <div class="col-xxl-6 col-xl-6 col-md-6">
                                                                        <div class="contact__form-input">
                                                                            <span class="wpcf7-form-control-wrap">
                                                                                <input
                                                                                    type="text"
                                                                                    name="first_name"
                                                                                    value=""
                                                                                    class="wpcf7-form-control wpcf7-text"
                                                                                    aria-invalid="false"
                                                                                    placeholder="Ismingiz"
                                                                                >
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-xxl-6 col-xl-6 col-md-6">
                                                                        <div class="contact__form-input">
                                                                            <span class="wpcf7-form-control-wrap">
                                                                                <input
                                                                                    type="text"
                                                                                    name="phone"
                                                                                    value="+998"
                                                                                    class="wpcf7-form-control wpcf7-text"
                                                                                    aria-invalid="false"
                                                                                    placeholder="Telefon raqamingiz"
                                                                                    id="phone_1"
                                                                                >
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-xxl-12">
                                                                        <div class="contact__btn">
                                                                            <input type="submit" value="Kirish" class="wpcf7-form-control has-spinner wpcf7-submit e-btn" style="line-height: 0;border:none;">
                                                                            <span class="wpcf7-spinner"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="wpcf7-response-output" aria-hidden="true"></div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xxl-4 offset-xxl-1 col-xl-4 offset-xl-1 col-lg-5 offset-lg-1">
                                                <div class="contact__info white-bg p-relative z-index-1">
                                                    <div class="contact__info-inner white-bg">
                                                        <ul>
                                                            <li>
                                                                <div
                                                                    class="contact__info-item d-flex align-items-start mb-35">
                                                                    <div class="contact__info-icon mr-15">
                                                                        <svg class="map" viewBox="0 0 24 24">
                                                                            <path class="st0"
                                                                                d="M21,10c0,7-9,13-9,13s-9-6-9-13c0-5,4-9,9-9S21,5,21,10z">
                                                                            </path>
                                                                            <circle class="st0" cx="12" cy="10" r="3">
                                                                            </circle>
                                                                        </svg>
                                                                    </div>

                                                                    <div class="contact__info-text">
                                                                        <h4>Manzil</h4>

                                                                        <p>Navoiy viloyati Karmana tumani Toshkent ko'chasi 39-uy</p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div
                                                                    class="contact__info-item d-flex align-items-start mb-35">
                                                                    <div class="contact__info-icon mr-15">
                                                                        <svg class="mail" viewBox="0 0 24 24">
                                                                            <path class="st0"
                                                                                d="M4,4h16c1.1,0,2,0.9,2,2v12c0,1.1-0.9,2-2,2H4c-1.1,0-2-0.9-2-2V6C2,4.9,2.9,4,4,4z">
                                                                            </path>
                                                                            <polyline class="st0" points="22,6 12,13 2,6 ">
                                                                            </polyline>
                                                                        </svg>
                                                                    </div>

                                                                    <div class="contact__info-text">
                                                                        <h4>Elektron pochta</h4>

                                                                        <p>
                                                                            <a href="mailto:navoiyinnovatsiyalaruniversiteti@gmail.com">navoiyinnovatsiyalaruniversiteti@gmail.com</a>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div
                                                                    class="contact__info-item d-flex align-items-start mb-35">
                                                                    <div class="contact__info-icon mr-15">
                                                                        <svg class="call" viewBox="0 0 24 24">
                                                                            <path class="st0"
                                                                                d="M22,16.9v3c0,1.1-0.9,2-2,2c-0.1,0-0.1,0-0.2,0c-3.1-0.3-6-1.4-8.6-3.1c-2.4-1.5-4.5-3.6-6-6  c-1.7-2.6-2.7-5.6-3.1-8.7C2,3.1,2.8,2.1,3.9,2C4,2,4.1,2,4.1,2h3c1,0,1.9,0.7,2,1.7c0.1,1,0.4,1.9,0.7,2.8c0.3,0.7,0.1,1.6-0.4,2.1  L8.1,9.9c1.4,2.5,3.5,4.6,6,6l1.3-1.3c0.6-0.5,1.4-0.7,2.1-0.4c0.9,0.3,1.8,0.6,2.8,0.7C21.3,15,22,15.9,22,16.9z">
                                                                            </path>
                                                                        </svg>
                                                                    </div>

                                                                    <div class="contact__info-text">
                                                                        <h4>Telefon</h4>

                                                                        <p>
                                                                            <a href="tel:998555000043">(+998-55) 500-00-43</a>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section
            class="elementor-section elementor-top-section elementor-element elementor-element-c04531e elementor-section-full_width elementor-section-height-default elementor-section-height-default"
            data-id="c04531e" data-element_type="section">
            <div class="elementor-container elementor-column-gap-no">

            </div>
        </section>
    </div>
</main>

<?
include "system/scripts.php";
?>

<script>
     $("#phone_1").on('input keyup', function(e){
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
        // console.log(x);
        e.target.value = !x[2] ? '+' + (x[1].length == 3 ? x[1] : '998') : '+' + x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });
</script>

<?
include "system/end.php";
?>

