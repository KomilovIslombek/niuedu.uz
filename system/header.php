<div class="top-navbar">
    <div class="container">
        <div class="navbar-phone">
            <img src="images/phone-icon.svg" class="phone-icon">

            <div class="support-phone">
                <h6><a href="tel:998555000043" class="text-white">+998 55 500 00 43</a></h6>
            </div>
        </div>

        <div class="top-right">
            <div class="navbar-social">
                <a href="mailto:ipu@ipu-edu.uz">
                    <img src="images/email.svg" alt="mail icon" width="29px">
                    info@niuedu.uz
                </a>
            </div>
        </div>

    </div>
</div>

<!-- header area start -->
<header>
    <div id="header-sticky" class="header__area header__transparent">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-2 col-sm-2 col-2 left-search-icon">
                    <div class="header__search-2" style="cursor:pointer;">
                        <img src="images/search.svg" alt="search icon" class="search-toggle">
                    </div>
                </div>
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-8 col-sm-8 col-8">
                    <div class="logo d-flex justify-content-center">
                        <a href="/">
                            <img src="../images/logo/niu_logotype_white_2.png" alt="logo" class="logo-only">
                            <img src="../images/logo/niu_logotype_white_2.png" alt="logo" class="logotype">
                        </a>
                    </div>
                </div>
                <div class="col-xxl-10 col-xl-10 col-lg-9 col-md-2 col-sm-2 col-2">
                    <div class="header__right d-flex justify-content-end align-items-center">
                    <div class="main-menu main-menu-2">
                        <nav id="mobile-menu">
                            <ul>
                                <li class="has-dropdown <?=($url[0] == "post" && $url[1] == "biz-haqimizda" ? 'active' : '')?>">
                                    <a href="/<?=$lng?>/post/biz-haqimizda"><?=translate("Biz haqimizda")?></a>
                                    <ul class="submenu">
                                        <li><a href="/<?=$lng?>/post/biz-haqimizda"><?=translate("Biz haqimizda")?></a></li>
                                        <li><a href="<?=(!$url[0] ? "javascript:void(0)" : "$lng/#savol-javoblar")?>" data-scroll-to="#savol-javoblar"><?=translate("Savol-javoblar")?></a></li>
                                        <li><a href="/<?=$lng?>/post/manzil"><?=translate("Manzil (lokatsiya)")?></a></li>
                                        <li><a href="files/certificate-73045_2.pdf"><?=t("Litsenziya")?></a></li>
                                        <li><a href="/<?=$lng?>/rector_speech"><?=t("Rektorning kutib olish nutqi")?></a></li>
                                        <li><a href="/<?=$lng?>/mission"><?=t("Missiyamiz")?></a></li>
                                        <li><a href="/<?=$lng?>/team"><?=t("Bizning jamoamiz")?></a></li>
                                        <li><a href="/<?=$lng?>/scientific_council"><?=t("Ilmiy kengash")?></a></li>
                                        <li><a href="/<?=$lng?>/information_center"><?=t("Axborot resurs markazi")?></a></li>
                                        <li><a href="/<?=$lng?>/qualifying_exam"><?=t("Malakaviy imtihon")?></a></li>
                                    </ul>
                                </li>

                                <li class="has-dropdown">
                                    <a href="<?=$url2[0]?>/rahbariyat"><?=translate("Tuzilma")?></a>
                                    <ul class="submenu">
                                        <!-- <li><a href="<?=$url2[0]?>/rahbariyat"><?=translate("Rahbariyat")?></a></li>
                                        <li><a href="<?=$url2[0]?>/fakultetlar"><?=translate("Fakultetler va Kafedralar")?></a></li>
                                        <li><a href="<?=$url2[0]?>/bolimlar"><?=translate("Bo'limlar")?></a></li>
                                        <li><a href="<?=$url2[0]?>/markazlar"><?=translate("Markazlar")?></a></li> -->
                                        <li>
                                            <a href="<?=(!$url[0] ? "javascript:void(0)" : "/#talim-yonalishlari")?>" data-scroll-to="#talim-yonalishlari"><?=translate("Ta'lim yo'nalishlari")?></a>
                                        </li>
                                       <!-- <li><a href="/struktura">Struktura</a></li> -->
                                    </ul>
                                </li>

                                <!-- <li><a href="<?=(!$url[0] ? "javascript:void(0)" : "/#ustozlar")?>" data-scroll-to="#ustozlar">Ustozlar</a></li> -->
                                <li class="has-dropdown">
                                    <a href="<?=$url2[0]?>/yangiliklar/1"><?=translate("Yangiliklar")?></a>
                                    <ul class="submenu">
                                       <li><a href="<?=$url2[0]?>/yangiliklar/1"><?=translate("Universitet yangiliklari")?></a></li>
                                       <!-- <li><a href="<?=$url2[0]?>/elonlar/1"><?=translate("E'lonlar")?></a></li> -->
                                    </ul>
                                </li>

                                <!-- <li class="has-dropdown">
                                    <a href="/cv/shartnoma"><?=t("Shartnomani yuklab olish")?></a>
                                    <ul class="submenu">
                                       <li><a href="/cv/shartnoma/2-tomonlama"><?=t("2 tomonlama shartnoma")?></a></li>
                                       <li><a href="/cv/shartnoma/3-tomonlama"><?=t("3 tomonlama shartnoma")?></a></li>
                                    </ul>
                                </li> -->

                                <!-- <li>
                                    <a href="<?=(!$url[0] ? "javascript:void(0)" : "/#talim-yonalishlari")?>" data-scroll-to="#talim-yonalishlari"><?=t("Ta'lim yo'nalishlari")?></a>
                                </li>

                                <li>
                                    <a href="<?=(!$url[0] ? "javascript:void(0)" : "/#savol-javoblar")?>" data-scroll-to="#savol-javoblar"><?=t("Savol-javoblar")?></a>
                                </li> -->

                                <li>
                                    <a href="<?=$url2[0]?>/books/1"><?=translate("Elektron kutubxona")?></a>
                                </li>

                                <li class="has-dropdown <?=($url[0] == "photos" || $url[0] == "photo" || $url[0] == "videos" || $url[0] == "video" ? 'active' : '')?>">
                                    <a href="javascript:void(0)"><?=translate("Media")?></a>
                                    <ul class="submenu">
                                        <li><a href="/<?=$url2[0]?>/videos/1"><?=translate("Videolar")?></a></li>
                                        <li><a href="/<?=$url2[0]?>/photos/1"><?=translate("Rasmlar")?></a></li>
                                    </ul>
                                </li>

                                <li class="has-dropdown">
                                    <a href="javascript:void(0)"><?=translate("Ariza")?></a>
                                    <ul class="submenu">
                                        <li><a href="https://test.niuedu.uz"><?=t("Imtihon topshirish")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/oddiy"><?=t("Ariza topshirish")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/oqishni-kochirish"><?=t("O'qishni ko'chirish")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/check"><?=t("Arizani tekshirish")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/check"><?=t("Ariza to'lovi")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/natija"><?=t("Imtihon natijasi")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/shartnoma/2-tomonlama"><?=t("2 tomonlama shartnoma")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/cv/shartnoma/3-tomonlama"><?=t("3 tomonlama shartnoma")?></a></li>
                                        <li><a class="dropdown-item" href="<?=$url2[0]?>/vakansiya"><?=t("ishga ariza")?></a></li>
                                    </ul>
                                </li>

                                <li class="has-dropdown">
                                    <?
                                    foreach ($langs_list as $key => $lang) {
                                        if ($lang["flag_icon"] == $lng || $lang["flag_icon"] == "gb" && $lng == "en") {
                                            echo '<a href="javascript:void(0)"><span class="flag-icon flag-icon-'.$lang["flag_icon"].'"></span> '.$lang["name"].'</a>';
                                        }
                                    }
                                    ?>
                                    <ul class="submenu">
                                        <?
                                        foreach ($langs_list as $key => $lang) {
                                            $lang_link = str_replace(
                                                $url2[0]."/",
                                                str_replace("gb", "en", $lang["flag_icon"])."/",
                                                urldecode($_SERVER["REQUEST_URI"])
                                            );
                            
                                            echo '<li><a href="'.$lang_link.'"><span class="flag-icon flag-icon-'.$lang["flag_icon"].'"></span> '.str_replace("gb"," en", $lang["name"]).'</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </li>

                                <li class="header__search-2" style="cursor:pointer;">
                                    <img src="images/search.svg" alt="search icon" class="search-toggle">
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="sidebar__menu d-xl-none">
                        <div class="sidebar-toggle-btn ml-30" id="sidebar-toggle">
                            <span class="line"></span>
                            <span class="line"></span>
                            <span class="line"></span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header area end -->

<div class="header__search-3 white-bg transition-3">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="header__search-3-inner text-center">
                    <div class="header__search-3-btn">
                        <a href="javascript:void(0);" class="header__search-3-btn-close">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                    <div class="header__search-3-header">
                        <h3><?=t("Qidirish")?></h3>
                    </div>
                    <div class="header__search-3-categories">
                    </div>

                    <form action="/<?=$lng?>/">
                        <div class="header__search-3-input p-relative">
                            <input type="text" name="s" value="" placeholder="<?=t("Qidirish")?>...">
                            <button type="submit"><i class="far fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="body-overlay"></div>
<!-- cart mini area end -->


<!-- sidebar area start -->
<div class="sidebar__area">
    <div class="sidebar__wrapper">
        <div class="sidebar__close">
            <button class="sidebar__close-btn" id="sidebar__close-btn">
            <span><i class="fal fa-times"></i></span>
            <span>Yopish</span>
            </button>
        </div>
        <div class="sidebar__content mt-50">
            <div class="mobile-menu fix"></div>
        </div>
    </div>
</div>
<!-- sidebar area end -->      
<div class="body-overlay"></div>
<!-- sidebar area end -->