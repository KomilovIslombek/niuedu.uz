<?
$is_config = true;
if (empty($load_defined)) include 'load.php';

// if (empty($systemUser["request_id"])) {
//     if ($systemUser["admin"] == 1) {
//         echo 'siz admin lavozimidasiz profile bo\'limiga o\'tish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing va ariza topshirilgan akkauntga kiring';
//         exit;
//     } else {
//         header("Location: /agent_cv");
//         exit;
//     }
// } else if (!$url[1]) {
//     header("Location: /$url2[0]/agent_profile/my");
//     exit;
// }

$setting = $db->assoc("SELECT * FROM settings");

include "system/head.php";
?>
<style>
    .information * {
        color: #000 !important;
    }
</style>

<div class="container-fluid pt-100 mb-4 mt-4">
    <div class="row justify-content-center align-items-center text-center text-dark">
        
        <div class="information">
            <p class="lead"><?=lng($setting["about_system"])?></p>
        </div>
        <p class="mt-4 lead">
            <a href="<?=$url2[0]?>/agent_cv" class="btn-sm e-btn bdevs-el-btn site-btn"><?=t("Davom etish")?></a>
        </p>

    </div>

</div>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>