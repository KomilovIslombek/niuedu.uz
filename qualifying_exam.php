<?
$is_config = true;

if (empty($load_defined)) include 'load.php';

include "system/head.php";

$qualifying_exam = $db->in_array("SELECT * FROM qualifying_exam");

// $qualifying_exam["html"] = str_replace('style="font-family:"Times New Roman",serif"', '', $qualifying_exam["html"]);
?>

<main>

<!-- page title area start -->
<section style="margin-top: 2rem;" class="page__title-area pt-120 pb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-8 col-lg-8 mt-4">
                <div class="course__wrapper">
                    <div class="course__wrapper">
                        <? foreach ($qualifying_exam as $qualifying_exam) {
                            $file = fileArr($qualifying_exam["file_id"]);
                        ?>
                            <p><a class="text-primary" target="_blank" href="<?=$file["file_folder"]?>"> <?=lng($qualifying_exam["file_name"])?> </a> </p>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- page title area end -->

</main>

<? include "system/scripts.php"; ?>

<? include 'system/end.php'; ?>