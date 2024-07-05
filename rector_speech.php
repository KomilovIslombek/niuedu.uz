<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

include "system/head.php";

$rector_speech = $db->assoc("SELECT * FROM rector_speech");
$image = image($rector_speech["image_id"]);

?>

<main>
    <!-- blog area start -->
    <section class="blog__area pt-160 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12">
                    <div class="row">                            
                        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6  mt-2">
                            <div class="blog__wrapper">
                                <div class="blog__item white-bg mb-30 transition-3 fix">
                                    <div class="blog__thumb w-img fix">
                                        <a href="javascript:void(0);">
                                            <img src="<?=$image["file_folder"]?>" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-8 col-xl-8 col-lg-12 col-md-12 ps-4 mt-2">
                            <div class="description">
                                <h2 class="title"><?=translate("Rektorning kutib olish nutqi")?>, <br> <?=lng($rector_speech["status"])?> <?=lng($rector_speech["first_name"]).' '.lng($rector_speech["last_name"])?></div>
                                <p class="mt-xxl-5 mt-xl-5">
                                    <?=lng($rector_speech["html"])?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- blog area end -->

</main>

<style>
    p {
        color: #000 !important;
    }
</style>

<? include "system/scripts.php"; ?>

<? include "system/end.php"; ?>