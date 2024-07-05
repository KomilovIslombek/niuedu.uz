<?
date_default_timezone_set("Asia/Tashkent");

$is_config = true;

if (empty($load_defined)) include 'load.php';

// ECHO "<pre>";
// print_r($_FILES);
// exit;
if (empty($url[1])) exit(http_response_code(404));


if (!empty($url[1])) {
    $agent_id = decode($url[1]);
    $agent = $db->assoc("SELECT * FROM firms WHERE id = ?", [ $agent_id ]);
}

if ($user_id) $qabul_off = false; // admin uchun ariza topshirishni ochish
if (!$no_header) include "system/head.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">
<link rel="stylesheet" href="theme/main/assets/css/cv.css?v=1.0.2">

<main>
        <div class="container" style="padding-top:110px;">
            <div class="row justify-content-center cv-row">
                <div class="col-11 col-sm-10 col-md-8 col-lg-7 col-xl-6 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-2 mb-3">
                        <h3 class="mb-4" id="heading"><?=t("Siz ro'yxatdan o'tdingiz, shartnomani yuklab olib, shartnoma bilan tanishib chiqing")?>.</h3>
                        <!-- <p>Fill all form field to go to next step</p> -->
                        
                        <fieldset>
                            <div class="form-card">
                                <div class="row justify-content-center">
                                    <div class="col-7 text-center">
    
                                        <!-- <h3>
                                            yuklab olish uchun
                                            <a href="agent-shartnoma/<?=encode(json_encode([
                                                        "s" => "a", // zapros
                                                        "c" => $agent["id"] // code
                                                    ]))?>" class="text-info"><?=t("link")?></a>
                                        </h3> -->
                                        <h3><?=t("Shaxsiy kabinetingizga kirib shartnomaningizni yuklab oling")?></h3>

                                        <h5>   
                                            <a href="<?=$url2[0]?>/agent_profile/" class="btn btn-success text-white my-3 btn-lg"><?=t("Akkauntga kirish")?></a>
                                        </h5>

                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
</main>

<? include "system/scripts.php"; ?>

<?
if (!$no_footer) include 'system/end.php';
?>