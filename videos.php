<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

include "system/head.php";

$page = (int)$url[1];
if (empty($page)){$page = 1;}

$page_count = 12;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

$medias = $db->in_array("SELECT * FROM medias ORDER BY id DESC LIMIT $page_start, $page_count");
$count = $db->assoc("SELECT COUNT(*) FROM medias")["COUNT(*)"];
?>
<style>
    .embed-youtube {
        position: relative;
        padding-bottom: 56.25%; /* - 16:9 aspect ratio (most common) */
        /* padding-bottom: 62.5%; - 16:10 aspect ratio */
        /* padding-bottom: 75%; - 4:3 aspect ratio */
        padding-top: 30px;
        height: 0;
        overflow: hidden;
    }

    .embed-youtube iframe,
    .embed-youtube object,
    .embed-youtube embed {
        border: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<main>
    <!-- blog area start -->
    <section class="blog__area pt-120 pb-120 mt-40">
        <div class="container">
            <div class="row">
                <h1 class="text-center mb-4 text-dark"><?=translate("Videolar")?></h1>

                <div class="row">
                    <? foreach ($medias as $media) { ?>
                        <?
                        $author = $db->assoc("SELECT id, first_name, last_name FROM users WHERE id = ?", [ $media["creator_user_id"] ]);
                        ?>

                        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                            <div class="blog__wrapper">
                                <div class="blog__item white-bg mb-30 transition-3 fix">
                                    <div class="blog__thumb w-img fix">
                                        <div class="embed-youtube">
                                            <?=$media["iframe"]?>
                                        </div>
                                    </div>
                                    <div class="blog__content">
                                    <h3 class="blog__title mb-b pb-4"><?=(mb_strlen(lng($media["name"])) > 60 ? mb_substr(lng($media["name"]), 0, 60)."..." : lng($media["name"]))?></h3>
        
                                    <div class="blog__meta d-flex align-items-center justify-content-between mt-4 pt-4">
                                        <div class="blog__author d-flex align-items-center">
                                            <div class="blog__author-info">
                                                <h5>NIU</h5>
                                            </div>
                                        </div>
                                        <div class="blog__date d-flex align-items-center">
                                            <i class="fal fa-clock"></i>
                                            <span><?=date("Y-m-d", strtotime($media["created_date"]))?></span>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>

                <!-- Pagination -->
                <?
                include "modules/pagination.php";
                echo pagination($count, $url[0]."/", $page_count); 
                ?>
                <!-- End Pagination -->
            </div>
        </div>
    </section>
    <!-- blog area end -->

</main>

<? include "system/scripts.php"; ?>

<? include "system/end.php"; ?>