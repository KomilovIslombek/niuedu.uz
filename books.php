<?php
$is_config = true;
if (empty($load_defined)) include 'load.php';

$page = ($url[1] ? $url[1] : 1);
$page_count = 8;
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

if (!empty($_GET["category_id"])) {
    $books = $db->in_array("SELECT * FROM books WHERE book_category_id = ? ORDER BY id ASC LIMIT $page_start, $page_count", [ $_GET["category_id"] ]);
    $count = (int)$db->assoc("SELECT COUNT(*) FROM books WHERE book_category_id = ?", [ $_GET["category_id"] ])["COUNT(*)"];
} else if (!empty($_GET["q"])) {
    $books = $db->in_array("SELECT * FROM books WHERE name LIKE ?", [ $_GET["q"] ]);

    $q = "%".$_GET["q"]."%";
    $books  = $db->in_array("SELECT * FROM books WHERE name LIKE ? OR author LIKE ?", [$q, $q]);

    $count = count($books);
} else {
    $books = $db->in_array("SELECT * FROM books ORDER BY id ASC LIMIT $page_start, $page_count");
    $count = (int)$db->assoc("SELECT COUNT(*) FROM books")["COUNT(*)"];
}

$book_categories = $db->in_array("SELECT * FROM book_categories");
include "system/head.php";
?>

<main>
    <section class="course__area pt-160 pb-120">
        <div class="container-fluid" style="padding:0 40px;">
            <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="course__tab-inner grey-bg-2 mb-50 d-sm-flex justify-content-between align-items-center">
                        <div class="course__tab-wrapper d-flex align-items-center">
                            <div class="course__tab-btn">
                                <ul class="nav nav-tabs" id="courseTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="grid-tab" data-bs-toggle="tab"
                                            data-bs-target="#grid" type="button" role="tab" aria-controls="grid"
                                            aria-selected="true">
                                            <svg class="grid" viewBox="0 0 24 24" style="position: relative; height: 0px;">
                                                <rect x="3" y="3" class="st0" width="7" height="7"></rect>
                                                <rect x="14" y="3" class="st0" width="7" height="7"></rect>
                                                <rect x="14" y="14" class="st0" width="7" height="7"></rect>
                                                <rect x="3" y="14" class="st0" width="7" height="7"></rect>
                                            </svg>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="course__view">
                                <? if (!empty($_GET["q"])) { 
                                    if($url2[0] == 'uz'){
                                    ?>
                                        <h4><?=translate("Kitoblar")?> <b>«<?=htmlspecialchars($_GET["q"])?>»</b> <?=translate("qidiruv bo'yicha")?> (<b><?=$count?></b> <?=translate("ta kitob topildi")?>)</h4>
                                    <? } else if($url2[0] == 'ru'){ ?>
                                        <h4>Посик книг по <b>«<?=htmlspecialchars($_GET["q"])?>»</b> (найдено книг <b><?=$count?></b>)</h4>
                                    <? } else if($url2[0] == 'en') {?>
                                        <h4>Books <b>«<?=htmlspecialchars($_GET["q"])?>»</b> search (<b><?=$count?></b> books found)</h4>
                                    <? } ?>
                                <? } else {
                                    if($url2[0] == 'uz'){ ?>
                                    <h4><?=translate("Kitoblar")?> <?=$count?> <?=translate("tadan")?> <?=($page * $page_count - $page_count + 1)?> - <?=($page * $page_count)?> <?=translate("gacha ko'rsatilmoqda")?></h4>
                                <? } else if($url2[0] == 'ru') {?>
                                    <h4>Показаны книги от  <?=$count?> до <?=($page * $page_count - $page_count + 1)?> - <?=($page * $page_count)?></h4>
                                <? } else if($url2[0] == 'en') {?>
                                    <h4>Showing books from  <?=$count?> to <?=($page * $page_count - $page_count + 1)?> - <?=($page * $page_count)?></h4>
                                    <? } ?>
                                <? } ?>
                            </div>
                        </div>
                        <div class="course__sort d-flex justify-content-sm-end">
                            <div class="course__sort-inner">
                                <select id="book-category-select">
                                    <option value=""><?=translate("Barchasi")?></option>
                                    <?
                                    foreach ($book_categories as $book_category) {
                                        echo '<option value="'.$book_category["id"].'" '.($book_category["id"] == $_GET["category_id"] ? 'selected=""' : '').'>'.lng($book_category["name"]).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="course__tab-conent">
                        <div class="tab-content" id="courseTabContent">
                            <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid-tab">
                                <div class="row">
                                    <? foreach ($books as $book) { ?>
                                        <?
                                        $book_image = image($book["image_id"]);
                                        $book_category = $db->assoc("SELECT * FROM book_categories WHERE id = ?", [ $book["book_category_id"] ]);
                                        $book_file = fileArr($book["file_id"]);
                                        ?>

                                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-12">
                                            <div class="course__item white-bg mb-30 fix">
                                                <div class="course__thumb w-img p-relative fix">
                                                    <a href="javascript:void(0)">
                                                        <img src="<?=$book_image["file_folder"]?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="course__content" style="padding-bottom:0;">
                                                    <h3 class="course__title"><a href="javascript:void(0)"><?=lng($book["name"])?></a></h3>

                                                    <p><?=lng($book["author"])?></p>
                                                </div>
                                                <div class="course__more d-flex">
                                                    <div class="course__btn">
                                                        <a href="<?=$book_file["file_folder"]?>" class="text-dark">
                                                            <?=translate("Kitobni yuklab olish")?>
                                                            <i class="far fa-arrow-down"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <?
                        include "modules/pagination.php";
                        echo pagination($count, $url[0]."/", $page_count); 
                        ?>
                        <!-- End Pagination -->
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="course__sidebar pl-40">
                        <div class="course__sidebar-search mb-50">
                            <form action="/books/">
                                <input type="text" name="q" placeholder="<?=translate("Kitoblar ichidan qidirish")?>..." value="<?=htmlspecialchars($_GET["q"])?>">
                                <button type="submit">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 584.4 584.4"
                                        style="enable-background:new 0 0 584.4 584.4;" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path class="st0"
                                                    d="M565.7,474.9l-61.1-61.1c-3.8-3.8-8.8-5.9-13.9-5.9c-6.3,0-12.1,3-15.9,8.3c-16.3,22.4-36,42.1-58.4,58.4    c-4.8,3.5-7.8,8.8-8.3,14.5c-0.4,5.6,1.7,11.3,5.8,15.4l61.1,61.1c12.1,12.1,28.2,18.8,45.4,18.8c17.1,0,33.3-6.7,45.4-18.8    C590.7,540.6,590.7,499.9,565.7,474.9z">
                                                </path>
                                                <path class="st1"
                                                    d="M254.6,509.1c140.4,0,254.5-114.2,254.5-254.5C509.1,114.2,394.9,0,254.6,0C114.2,0,0,114.2,0,254.5    C0,394.9,114.2,509.1,254.6,509.1z M254.6,76.4c98.2,0,178.1,79.9,178.1,178.1s-79.9,178.1-178.1,178.1S76.4,352.8,76.4,254.5    S156.3,76.4,254.6,76.4z">
                                                </path>
                                            </g>
                                        </g>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="course__sidebar-widget grey-bg">
                            <div class="course__sidebar-info">
                                <h3 class="course__sidebar-title"><?=translate("Kitob turlari")?></h3>
                                <ul>
                                    <li>
                                        <a href="/books/1/" class="course__sidebar-check mb-10 d-flex align-items-center">
                                            <input class="m-check-input category-check-input" type="checkbox" id="c-all" <?=(empty($_GET["category_id"]) ? 'checked=""' : '')?>>
                                            <label class="m-check-label" for="c-all"><?=translate("Barchasi")?></label>
                                        </a>
                                    </li>
                                    <? foreach ($book_categories as $book_category) { ?>
                                        <li>
                                            <a href="/books/<?=$page?>/?category_id=<?=$book_category["id"]?>" class="course__sidebar-check mb-10 d-flex align-items-center">
                                                <input class="m-check-input category-check-input" type="checkbox" id="c-<?=$book_category["id"]?>" <?=($book_category["id"] == $_GET["category_id"] ? 'checked=""' : '')?>>
                                                <label class="m-check-label" for="c-<?=$book_category["id"]?>"><?=lng($book_category["name"])?></label>
                                            </a>
                                        </li>
                                    <? } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<? include "system/scripts.php"; ?>

<script>
    $(".category-check-input").on("change", function(){
        window.location.href = $(this).parents("a").attr("href");
    });

    $("#book-category-select").on("change", function(){
        var category_id = $(this).find("option:selected").val();
        if (!category_id) {
            window.location.href = "/books/1/";
        } else {
            window.location.href = "/books/1/?category_id=" + category_id;
        }
    });
</script>

<? include "system/end.php"; ?>