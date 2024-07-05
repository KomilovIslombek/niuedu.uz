<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$councils = $db->in_array("SELECT * FROM council");

?>

<main>

<!-- page title area start -->
<section class="page__title-area pt-120 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div style="padding: 0;" class="col-12">
                <div class="course__wrapper">
                    <div class="course__wrapper">

                            <? foreach ($councils as $council) { ?>
                                <?
                                    $scientific_councils = $db->in_array("SELECT * FROM scientific_council WHERE council_id = ?", [ $council["id"] ]);
                                ?>
                                <h3 style="margin: 2.5rem 0 !important; font-size: 30px;" class="text-center "><?=lng($council["name"])?></h3>
                                    
                                    <div class="row">
                                        <? foreach ($scientific_councils as $scientific_council) { ?>
                                            <? $image = image($scientific_council["image_id"]) ?>
                                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 team-card">
                                                <div class="blog__wrapper">
                                                    <div class="blog__item white-bg mb-30 transition-3 fix">
                                                        <div class="blog__thumb w-img fix">
                                                        <a href="javascript:void(0);">
                                                            <img style="height: 300px;object-fit:cover;" class="change_img" src="<?=$image["file_folder"]?>" alt="">
                                                        </a>
                                                        </div>
                                                        <div class="blog__content">
                                                        <h3 class="blog__title mb-3 text-center"><a href="javascript:void(0)"><?=lng($scientific_council["first_name"]).' '.lng($scientific_council["last_name"]).' '.lng($scientific_council["father_first_name"])?></a></h3>

                                                        <div class="blog__meta d-flex align-items-center justify-content-center">
                                                            <div class="blog__author-info">
                                                                <h5 class="text-center text-muted"><?=lng($scientific_council["bio"])?></h5>
                                                            </div>
                                                        </div>
                                                        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                            <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- page title area end -->

</main>


<style>
    /* Team Card */
    /* .team-card:not(:last-of-type) {
        margin-right: 20px;
    } */
    .swiper-pagination{
        bottom: -5px !important;
    }
    .blog__content{
        padding: 25px 13px !important;
    }   
    .blog__title{
        font-size: 1.1rem;
    }

    .team-card{
        padding: 0 10px !important;
    }
    
    

    @media (max-width: 770px) {
        .change_img{
            height: 400px !important;
        }
        .tabs{
            flex-wrap: wrap !important;
        }
        .tabs li{
            flex: 1 1 100% !important;
        }
    }
    @media (max-width: 590px) {
        .team-card{
            padding: 0 !important;
        }
        .change-row{
            margin: 0 !important;
        }
    }
</style>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>