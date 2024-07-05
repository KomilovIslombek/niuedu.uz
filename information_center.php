<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$information_center = $db->assoc("SELECT * FROM information_center");
$team = $db->in_array("SELECT * FROM team WHERE team_type = ?", [ 'axborot-resurs-markazi' ]);
// $team = $db->in_array("SELECT * FROM team");

?>

<main>

<!-- page title area start -->
<section class="page__title-area pt-120 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div style="margin-top: 3rem; padding: 0;" class="col-12 change-col">
                <div class="course__wrapper">
                    <div class="course__wrapper">
                        
                        <div class="d-flex align-items-start change-wrap">
                            <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true"><?=translate("Markaz haqida")?></button>
                                <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false"><?=translate("Foydalanish qoidalari")?></button>
                                <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false"><?=translate("Resurslar")?></button>
                                <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings" aria-selected="false"><?=translate("Ish vaqti")?></button>
                            </div>
                            <div class="tab-content" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab" tabindex="0">
                                    <?=lng($information_center["bio"])?>

                                    <h3 class="text-center my-3"><?=translate("Bizning jamoa")?></h3>

                                    <div class="row mt-3">
                                        <? foreach ($team as $team) { ?>
                                            <?
                                                $image = image($team["image_id"]);
                                            ?>
                                            
                                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 team-card ">
                                                <div class="blog__wrapper">
                                                    <div class="blog__item white-bg mb-30 transition-3 fix">
                                                        <div class="blog__thumb w-img fix">
                                                        <a href="javascript:void(0);">
                                                            <img style="height: 300px;object-fit:cover;" class="change_img" src="<?=$image["file_folder"]?>" alt="">
                                                        </a>
                                                        </div>
                                                        <div class="blog__content">
                                                        <h3 class="blog__title mb-3 text-center"><a href="javascript:void(0)"><?=lng($team["last_name"]).' '.lng($team["first_name"]).' '.lng($team["father_first_name"])?></a></h3>
                                
                                                        <div class="blog__meta d-flex align-items-center justify-content-center">
                                                            <div class="blog__author-info">
                                                                <h5 class="text-center text-muted"><?=lng($team["role"])?></h5>
                                                                <h5 class="text-center text-muted mt-2"><a href="tel:<?=$team["phone"]?>"><?=$team["phone"]?></a></h5>
                                                            </div>
                                                        </div>
                                                        <hr >
                                                        <div class="d-flex align-items-center justify-content-center gap-4">

                                                            <?if($team["email_link"]) {?>
                                                                <a href="mailto:<?=$team["email_link"]?>" class="icon-wrapper" target="_blank">
                                                                    <i class="fa fa-envelope custom-icon">
                                                                        <span class="fix-editor">&nbsp;</span>
                                                                    </i>
                                                                </a>
                                                            <? } ?>
                                                            
                                                            <?if($team["telegram_link"]) {?>
                                                                <a href="https://t.me/<?=$team["telegram_link"]?>" class="icon-wrapper" target="_blank">
                                                                    <i class="fa-brands fa-telegram custom-icon">
                                                                        <span class="fix-editor">&nbsp;</span>
                                                                    </i>
                                                                </a>
                                                            <? } ?>
                                                            
                                                            <?if($team["linkedin_link"]) {?>
                                                                <a href="<?=$team["linkedin_link"]?>" class="icon-wrapper" target="_blank">
                                                                    <i class="fa-brands fa-linkedin custom-icon">
                                                                        <span class="fix-editor">&nbsp;</span>
                                                                    </i>
                                                                </a>
                                                            <? } ?>
                                                        </div>
                
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
                                    <?=lng($information_center["terms_use"])?>
                                </div>
                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab" tabindex="0">
                                    <h4><?=translate("Resurslar")?></h4>
                                    <div class="mt-2"><span class="text-dark"><?=translate("Veb sayt")?></span> - <a href="<?=$information_center["site"]?>"><?=$information_center["site"]?></a></div>
                                    <div class="mt-1"><span class="text-dark"><?=translate("Elektron kutubxona")?></span> - <a href="<?=$information_center["library"]?>"><?=$information_center["library"]?></a></div>
                                    <br>
                                    <h4><?=translate("Jahon ilmiy ta’limiy elektron axborot resurslari")?>:</h4>
                                    <div class="mt-1"><a href="<?=$information_center["education_site"]?>"><?=$information_center["education_site"]?></a></div>
                                    <div class="mt-1"><a href="<?=$information_center["education_site2"]?>"><?=$information_center["education_site2"]?></a></div>
                                    <div class="mt-1"><a href="<?=$information_center["education_site3"]?>"><?=$information_center["education_site3"]?></a></div>
                                    <div class="mt-1"><a href="<?=$information_center["education_site4"]?>"><?=$information_center["education_site4"]?></a></div>
                                </div>
                                <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-messages-tab" tabindex="0">
                                    <?=lng($information_center["work_time"])?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- page title area end -->

</main>


<style>

.blog__content{
        padding: 25px 13px !important;
    }
    .team-card{
        padding: 0 10px !important;
    }
    .blog__title{
        font-size: 1.1rem;
    }
    .icon-wrapper{
        padding:7px;
        width:33px;
        height:33px;
        display: grid;
        place-items: center;
        border-radius:100%;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.46);
        /* border:6px solid #ccc; */
        background: rgba(0,0,0, 0.2);
        transition: all .2s ease;
    }
    .custom-icon {
        font-size:15px;
        transition: all .2s ease;
    }
    .fa-telegram, .fa-linkedin{
        font-size: 16px !important;
    }
    .icon-wrapper:hover {
        background: #198A29;
    }
    .icon-wrapper:hover .custom-icon {
        color: #fff;
    }
    .fix-editor {
        display:none;
    }


    .nav-link{
        width: 300px;   
    }
    .nav-link.active{
        background-color: #198A29 !important;
    }
    .tab-content{
        /* border: 1px solid #f0f0f0;
        border-radius: .25rem; */
        padding-left: 10px;
    }
    @media (max-width: 780px) {
        .nav{
            width: 100%;
        }
        .nav-link{
            width: 100%;
        }
        .change-wrap{
            flex-wrap: wrap;
            gap: 20px;
        }
        .change-col{
            margin-top: 1.5rem !important;
        }
    }

    @media (min-width: 1400px) {
        .container{
            max-width: 1330px !important;
        }
    }
</style>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>