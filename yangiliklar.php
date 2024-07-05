<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

include "system/head.php";

$page = (int)$url[1];
if (empty($page)){$page = 1;}

$page_count = 12;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

$news = $db->in_array("SELECT * FROM news ORDER BY id DESC LIMIT $page_start, $page_count");
$new_count = $db->assoc("SELECT COUNT(*) FROM news")["COUNT(*)"];
?>

<main>
    <!-- blog area start -->
    <section class="blog__area pt-160 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12">
                    <div class="row">
                        <h1 class="text-center mb-4 text-dark"><?=translate("Barcha yangiliklar")?></h1>

                        <? foreach ($news as $post) { ?>
                            <?
                            // $author = $db->assoc("SELECT id, first_name, last_name FROM users WHERE id = ?", [ $post["creator_user_id"] ]);
                            $image = image($post["image_id"]);
                            ?>

                            <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                                <div class="blog__wrapper">
                                    <div class="blog__item white-bg mb-30 transition-3 fix">
                                        <div class="blog__thumb w-img fix">
                                        <a href="/yangilik/<?=$post["id"]?>">
                                            <img src="<?=$image["file_folder"]?>" alt="">
                                        </a>
                                        </div>
                                        <div class="blog__content">
                                        <h3 class="blog__title"><a href="/yangilik/<?=$post["id"]?>"><?=lng($post["name"])?></a></h3>
                
                                        <div class="blog__meta d-flex align-items-center justify-content-between">
                                            <div class="blog__author d-flex align-items-center">
                                                <!-- <div class="blog__author-thumb mr-10">
                                                    <img src="assets/img/blog/author/author-1.jpg" alt="">
                                                </div> -->
                                                <div class="blog__author-info">
                                                    <h5><?=translate("Admin")?></h5>
                                                </div>
                                            </div>
                                            <div class="blog__date d-flex align-items-center">
                                                <i class="fal fa-clock"></i>
                                                <span><?=date("Y-m-d", strtotime($post["created_date"]))?></span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>

                    <? if ($new_count > $page_count) { ?>
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="basic-pagination wow fadeInUp mt-30" data-wow-delay=".2s">
                                    <ul class="d-flex align-items-center">
                                    <?
                                        $count = $new_count / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);

                                        if ($page != 1){
                                            echo '<li class="prev">
                                                    <a href="/news/'.($page-1).'" class="link-btn link-prev">
                                                        Oldingi
                                                        <i class="arrow_left"></i>
                                                        <i class="arrow_left"></i>
                                                    </a>
                                                </li>';
                                        } else {
                                            echo '<li class="prev">
                                                    <a style="cursor:no-drop" class="link-btn link-prev">
                                                        Oldingi
                                                        <i class="arrow_left"></i>
                                                        <i class="arrow_left"></i>
                                                    </a>
                                                </li>';
                                        }

                                        $max = 4;
                                        for ($i = 0; $i <= $count; $i++) {
                                            if ($i == 1 || $i == $count || $i >= $page && $i <= $page + ($max - 1)) {
                                                if ($page == $i) {
                                                    echo '<li class="active">
                                                            <a>
                                                                <span>'.$i.'</span>
                                                            </a>
                                                        </li>';
                                                } else {
                                                    echo '<li>
                                                            <a href="/news/'.$i.'">
                                                                <span>'.$i.'</span>
                                                            </a>
                                                        </li>';
                                                }
                                            }
                                        }

                                        if ($page != $count){
                                            echo '<li class="prev">
                                                <a href="/news/'.($page+1).'" class="link-btn link-prev">
                                                    Oldingi
                                                    <i class="arrow_right"></i>
                                                    <i class="arrow_right"></i>
                                                </a>
                                            </li>';
                                        } else {
                                            echo '<li class="prev">
                                                    <a style="cursor:no-drop" class="link-btn link-prev">
                                                        Oldingi
                                                        <i class="arrow_right"></i>
                                                        <i class="arrow_right"></i>
                                                    </a>
                                                </li>';
                                        }
                                    ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </section>
    <!-- blog area end -->

</main>

<? include "system/scripts.php"; ?>

<? include "system/end.php"; ?>