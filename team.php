<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

if($url2[2]) {
    $query .= " AND team_type = '" . $url2[2] . "'";
} else {
    $query .= " AND team_type = 'rektorat'";
    $active = 'rektorat';
}

$team = $db->in_array("SELECT * FROM team WHERE 1=1 $query");
// if (empty($team)) exit(http_response_code(404));

?>

<main>

<!-- page title area start -->
<section class="page__title-area pt-120 pb-90">
    <div class="container">
        <div class="row justify-content-center">
            <div style="margin-top: 5.5rem!important; padding: 0;" class="col-12">
                <div class="course__wrapper">
                    <div class="course__wrapper">
                        <ul class="tabs d-flex ">
                            <? foreach ($team_types as $key => $value) { ?>
                                <li><a class="tab text-uppercase <?=$url2[2] == $key || $key == $active ? 'active' : ''?>" href="<?=$url2[0]?>/<?=$url[0]?>/<?=$key?>"><?=translate($value)?></a></li>
                            <? } ?>
                        </ul>

                        <div class="row">
                            <? foreach ($team as $team) { ?>
                                <?
                                    $image = image($team["image_id"]);
                                ?>
                                
                                <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 team-card ">
                                    <div class="blog__wrapper">
                                        <div class="blog__item white-bg mb-30 transition-3 fix">
                                            <div class="blog__thumb w-img fix">
                                            <a href="javascript:void(0);">
                                                <img style="height: 300px;object-fit:cover;" class="change_img" src="<?=$image["file_folder"]?>" alt="">
                                            </a>
                                            </div>
                                            <div class="blog__content">
                                            <h3 class="blog__title mb-3 text-center"><a href="javascript:void(0)"><?=lng($team["status"])?> <?=lng($team["first_name"]).' '.lng($team["last_name"])?></a></h3>
                    
                                            <div class="blog__meta d-flex align-items-center justify-content-center">
                                                <div class="blog__author-info">
                                                    <h5 class="text-center text-muted"><?=lng($team["role"])?></h5>
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
                                                <!-- <a href="<?=$team["google_link"]?>" target="_blank">
                                                    <i class="fa-brands fa-google"></i>
                                                </a> -->
                                            </div>
    
                                            </div>
                                        </div>
                                    </div>
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


<style>
    /* Team Card */
    /* .team-card:not(:last-of-type) {
        margin-right: 20px;
    } */
    
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


    /* Tabs */
    .tabs{
        border: 1px solid #ccc;
        margin-bottom: 40px;
    }
    .tabs li {
        flex: 1 1 25%;
    }
    .tabs .tab{
        padding: 12px 0; 
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        text-align: center;
    }
    .tabs .tab:hover{
        background-color: #198A29;
        color: white !important;
    }
    .tabs .tab.active{
        background-color: #198A29;
        color: white !important;
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
</style>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>