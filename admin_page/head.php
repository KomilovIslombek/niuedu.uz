<?
date_default_timezone_set("Asia/Tashkent");
?>
<!DOCTYPE html>
<html lang="uz" data-textdirection="ltr" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Navoiy innovatsiyalar universiteti - Admin</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../theme/main/assets/img/favicon.png">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <!-- BEGIN VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/bootstrap.css">
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/fonts/icomoon.css">
    <!-- <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/fonts/flag-icon-css/css/flag-icon.min.css"> -->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/vendors/css/extensions/pace.css">
    <!-- END VENDOR CSS-->
    <!-- BEGIN ROBUST CSS-->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/bootstrap-extended.css?v=1.0.0">
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/app.css?v=1.1.0">
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/colors.css">
    <!-- END ROBUST CSS-->
    <!-- BEGIN Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css"
        href="../theme/rubust/app-assets/css/core/menu/menu-types/vertical-overlay-menu.css">
    <link rel="stylesheet" type="text/css" href="../theme/rubust/app-assets/css/core/colors/palette-gradient.css">
    <!-- END Page Level CSS-->
    <!-- BEGIN Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../theme/rubust/assets/css/style.css">
    <!-- END Custom CSS-->
    <!-- Flag icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <!-- END Flag icons-->

    <!-- Select2 -->
    <link href="../modules/select2/select2.min.css" rel="stylesheet">

    <!-- Loader -->
    <link rel="stylesheet" href="../theme/rubust/app-assets/css/loader.css">
</head>

<body data-open="click" data-menu="vertical-menu" data-col="2-columns"
    class="vertical-layout vertical-menu 2-columns  fixed-navbar">

    <div class="loader-wrapper" style="display:none">
        <div class="loader-text">Iltimos Kuting ma'lumot yuklanmoqda...</div>
        <div id="loading-indicator" style="width: 60px; height: 60px;" role="progressbar"
            class="MuiCircularProgress-root MuiCircularProgress-colorPrimary MuiCircularProgress-indeterminate"><svg
                viewBox="22 22 44 44" class="MuiCircularProgress-svg">
                <circle cx="44" cy="44" r="20.2" fill="none" stroke-width="3.6"
                    class="MuiCircularProgress-circle MuiCircularProgress-circleIndeterminate"></circle>
            </svg></div>
    </div>

    <nav class="header-navbar navbar navbar-with-menu navbar-fixed-top navbar-semi-dark navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-header">
                <ul class="nav navbar-nav">
                    <li class="nav-item mobile-menu hidden-md-up float-xs-left"><a
                            class="nav-link nav-menu-main menu-toggle hidden-xs is-active"><i
                                class="icon-menu5 font-large-1"></i></a></li>
                                
                    <!-- <a href="index.php" class="brand-logo">
                        <div class="logo-abbr">
                            <img src="../theme/rubust/app-assets/images/logo/logo_frame_white80.png" alt="logo icon" width="50px" height="52px">
                        </div>
                        <img class="logo-compact" src="theme/vora/images/logo-text-dark.png" alt="log text">
                        <div class="brand-title">
                            <img src="../theme/rubust/app-assets/images/logo/logo_frame_white1402.png" width="150px" alt="">
                        </div>
                    </a> -->

                    <li class="nav-item">
                        <a style="padding-top: 10px;" href="index.php" class="navbar-brand nav-link">
                        <img alt="branding logo"
                        src="../theme/rubust/app-assets/images/logo/logo_frame_white.png"
                        data-expand="../theme/rubust/app-assets/images/logo/logo_frame_white.png"
                        data-collapse="../theme/rubust/app-assets/images/logo/logo_frame_white80.png"
                        class="brand-logo" width="42px"></a>
                    </li>

                    <li class="nav-item hidden-md-up float-xs-right"><a data-toggle="collapse"
                            data-target="#navbar-mobile" class="nav-link open-navbar-container"><i
                                class="icon-ellipsis pe-2x icon-icon-rotate-right-right"></i></a></li>
                </ul>
            </div>
            <div class="navbar-container content container-fluid">
                <div id="navbar-mobile" class="collapse navbar-toggleable-sm">
                    <ul class="nav navbar-nav">
                        <li class="nav-item hidden-sm-down"><a
                                class="nav-link nav-menu-main menu-toggle hidden-xs is-active"><i class="icon-menu5">
                                </i></a></li>
                        <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i
                                    class="ficon icon-expand2"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav float-xs-right">
                        <li class="dropdown dropdown-user nav-item">
                            <a href="#" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link">
                                <span class="avatar avatar-online">
                                    <img src="../theme/main/assets/img/favicon.png" alt="favicon">
                                </span>
                                <span class="user-name">
                                    <?=$systemUser->first_name." ".$systemUser->last_name?>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="/" class="dropdown-item"><i class="icon-open"></i> Bosh sahifa</a>
                                <a href="/exit" class="dropdown-item"><i class="icon-power3"></i> Akkauntdan chiqish</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- End Nav -->

    <!-- Left Menus -->

    <!-- main menu-->
    <div data-scroll-to-active="true" class="main-menu menu-fixed menu-dark menu-accordion menu-shadow">

        <div class="main-menu-header">
            <input type="text" placeholder="Qidirish" class="menu-search form-control round" id="search_input_jquery"/>
        </div>

        <div class="main-menu-content">

            <?
            
            function addMenu($icon, $name, $menus_arr) {
                global $url2, $systemUser;
                $has_active = false;
                $allow = false;
    
                $menus = '';

                $permissions = false;
                if ($systemUser["permissions"]) {
                    $permissions = json_decode($systemUser["permissions"]);
                }

                foreach ($menus_arr as $menu) {
                    $res = explode( "/", $menu["href"]);
                    $page = explode(".php", $res[2])[0];
                    $page = explode("?", $page)[0];

                    $is_active = $url2[1] == "$page.php" ? true : false;

                    if ($menu["param"]) {
                        if ($is_active) {
                            $is_active = $_GET[$menu["param"]["name"]] == $menu["param"]["value"] ? true : false;
                        }

                        if ($is_active) $has_active = true;
                    }

                    if (in_array($page, $permissions)) {
                        $allow = true;
                        $menus .= '
                        <li class="'.($is_active ? 'active' : "").'">
                            <a href="'.$menu['href'].'" data-i18n="nav.dash.main" class="menu-item">'.$menu['name'].'</a>
                        </li>
                        ';
                    }
                }

                if ($allow) {
                    echo ('
                        <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                        <li class="nav-item has-sub '.($is_active ? "open" : "").'">
                            <a>
                            <i class="'.$icon.'"></i>
                                <span data-i18n="nav.dash.main" class="menu-title">'.$name.'</span>
                            </a>
                            <ul class="menu-content">
                                '.$menus.'
                            </ul>
                        </li>
                        </ul>
                    ');
                }
            }

            addMenu("icon-image4", "Bosh sahifadagi<br>rasmlar", [
                ["name" => "Rasmlar ro'yxati", "href" => "/$url2[0]/images_list.php"],
                ["name" => "Yangi rasm<br>qo'shish", "href" => "/$url2[0]/add_image.php"]
            ]);

            addMenu("icon-shuffle3", "Yangiliklar", [
                ["name" => "Yangiliklar ro'yxati", "href" => "/$url2[0]/news_list.php?page=1"],
                ["name" => "Yangilik qo'shish", "href" => "/$url2[0]/add_news.php"]
            ]);

            addMenu("icon-shuffle3", "E'lonlar", [
                ["name" => "E'lonlar ro'yxati", "href" => "/$url2[0]/ads_list.php?page=1"],
                ["name" => "E'lon qo'shish", "href" => "/$url2[0]/add_ads.php?page=1"]
            ]);

            addMenu("icon-video2", "Videolar", [
                ["name" => "Videolar ro'yxati", "href" => "/$url2[0]/videos_list.php"],
                ["name" => "Yangi video<br>qo'shish", "href" => "/$url2[0]/add_video.php"]
            ]);

            addMenu("icon-paper", "Ta'lim shakli", [
                ["name" => "Ta'lim shakli ro'yxati", "href" => "/$url2[0]/learn_types_list.php"],
                ["name" => "Ta'lim shakli qo'shish", "href" => "/$url2[0]/add_learn_type.php"]
            ]);

            addMenu("icon-shuffle3", "Yona'lishlar", [
                ["name" => "Yo'nalishlar ro'yxati", "href" => "/$url2[0]/directions_list.php"],
                ["name" => "Yo'nalish qo'shish", "href" => "/$url2[0]/add_direction.php"]
            ]);

            addMenu("icon-content-right", "Postlar", [
                ["name" => "Postlar ro'yxati", "href" => "/$url2[0]/posts_list.php"],
                ["name" => "Post qo'shish", "href" => "/$url2[0]/add_post.php"]
            ]);

            addMenu("icon-layers", "Savol-javoblar", [
                ["name" => "Savol-javoblar ro'yxati", "href" => "/$url2[0]/savol_javoblar_list.php"],
                ["name" => "Savol-javob qo'shish", "href" => "/$url2[0]/add_savol_javob.php"]
            ]);

            addMenu("icon-user-tie", "Adminlar", [
                ["name" => "Adminlar ro'yxati", "href" => "/$url2[0]/admins_list.php"],
                ["name" => "Yangi Admin<br>qo'shish", "href" => "/$url2[0]/add_admin.php"]
            ]);

            addMenu("icon-bell4", "Arizalar", [
                [
                    "name" => "Arizalar ro'yxati<br>(Abituriyent)",
                    "href" => "/$url2[0]/requests_list.php?reg_type=oddiy&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oddiy"
                    ]
                ],
                [
                    "name" => "Arizalar ro'yxati<br>(o'qishni ko'chirish)",
                    "href" => "/$url2[0]/requests_list.php?reg_type=oqishni-kochirish&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oqishni-kochirish"
                    ]
                ],
                [
                    "name" => "Arizalar ro'yxati<br>(Ikkinchi mutaxassislik)",
                    "href" => "/$url2[0]/requests_list.php?reg_type=ikkinchi-mutaxassislik&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "ikkinchi-mutaxassislik"
                    ]
                ],
                [
                    "name" => "Arizalar ro'yxati<br>(Men talabaman saytidagi)",
                    "href" => "/$url2[0]/other_site_requests.php?page=1",
                ],
                // [
                //     "name" => "Ariza qo'shish<br>(abituriyent)",
                //     "href" => "/$url2[0]/add_request.php"
                // ],
                // [
                //     "name" => "Ariza qo'shish<br>(o'qishni ko'chirish)",
                //     "href" => "/$url2[0]/add_request_oqishni_kochirish.php"
                // ]
            ]);

            addMenu("icon-bell4", "Arizalar (1)", [
                [
                    "name" => "Arizalar ro'yxati<br>(Abituriyent)",
                    "href" => "/$url2[0]/requests_list_1.php?reg_type=oddiy&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oddiy"
                    ]
                ],
                [
                    "name" => "Arizalar ro'yxati<br>(o'qishni ko'chirish)",
                    "href" => "/$url2[0]/requests_list_1.php?reg_type=oqishni-kochirish&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oqishni-kochirish"
                    ]
                ]
            ]);

            addMenu("icon-bell4", "Arizalar (2)", [
                [
                    "name" => "Arizalar ro'yxati<br>(Abituriyent)",
                    "href" => "/$url2[0]/requests_list_2.php?reg_type=oddiy&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oddiy"
                    ]
                ],
                [
                    "name" => "Arizalar ro'yxati<br>(o'qishni ko'chirish)",
                    "href" => "/$url2[0]/requests_list_2.php?reg_type=oqishni-kochirish&page=1",
                    "param" => [
                        "name" => "reg_type",
                        "value" => "oqishni-kochirish"
                    ]
                ]
            ]);

            addMenu("icon-paper", "Qayta qo'ng'iroq<br>Izohlar", [
                ["name" => "Izohlar ro'yxati", "href" => "/$url2[0]/descriptions.php?page=1"],
                ["name" => "Izoh qo'shish", "href" => "/$url2[0]/add_description.php?page=1"]
            ]);

            addMenu("icon-bell4", "Qayta qo'ng'iroqlar", [
                [
                    "name" => "Qayta qo'ng'iroqlar<br>ro'yxati",
                    "href" => "/$url2[0]/again_calls_list.php?page=1",
                ]
            ]);

            addMenu("icon-bell4", "Ishga arizalar", [
                [
                    "name" => "Ishga arizalar",
                    "href" => "/$url2[0]/vakansiyalar_list.php?page=1",
                ]
            ]);

            addMenu("icon-shuffle3", "Rektor nutqi", [
                ["name" => "kutib olish nutqi", "href" => "/$url2[0]/rector_speech.php?page=1"],
                ["name" => "nutq qo'shish", "href" => "/$url2[0]/add_rector_speech.php"]
            ]);

            addMenu("icon-shuffle3", "Missiyamiz", [
                ["name" => "Missiya", "href" => "/$url2[0]/mission.php?page=1"],
                // ["name" => "Missiya qo'shish", "href" => "/$url2[0]/add_mission.php"]
            ]);

            addMenu("icon-shuffle3", "Jamoa", [
                ["name" => "Jamoa ro'yxati", "href" => "/$url2[0]/team.php?page=1"],
                ["name" => "Jamoaga odam qo'shish", "href" => "/$url2[0]/add_team.php"]
            ]);
            
            addMenu("icon-shuffle3", "Ilmiy kengashlar", [
                ["name" => "Kengashlar ro'yxati", "href" => "/$url2[0]/council.php?page=1"],
                ["name" => "Kengash qo'shish", "href" => "/$url2[0]/add_council.php"]
            ]);
            
            addMenu("icon-shuffle3", "Ilmiy kengashlar <br> Tarkibi", [
                ["name" => "Tarkiblar ro'yxati", "href" => "/$url2[0]/scientific_council.php?page=1"],
                ["name" => "odamni qo'shish", "href" => "/$url2[0]/add_scientific_council.php"]
            ]);
            
            addMenu("icon-shuffle3", "Axborot resurs <br> markzai", [
                ["name" => "Markaz ma'lumotlari", "href" => "/$url2[0]/information_center.php?page=1"],
                ["name" => "Ma'lumot qo'shish", "href" => "/$url2[0]/add_information_center.php"]
            ]);
            
            addMenu("icon-shuffle3", "Malakaviy imtihon", [
                ["name" => "Malakaviy imtihon", "href" => "/$url2[0]/qualifying_exam.php?page=1"],
                ["name" => "Ma'lumot qo'shish", "href" => "/$url2[0]/add_qualifying_exam.php"]
            ]);

            $firmsArr = $db->in_array("SELECT * FROM firms");
            $firms = [];
            $firmMenu = [];
            foreach ($firmsArr as $firmKey => $firm) {
                $firms[$firm["id"]] = $firm;
                // array_push($firmMenu, [
                //     "name" => $firm["name"],
                //     "href" => "/$url2[0]/requests_list.php?firm_id=" . $firm["id"]
                // ]);
            }

            array_push($firmMenu, ["name" => "Agentlar ro'yxati", "href" => "/$url2[0]/firms_list.php"]);
            array_push($firmMenu, ["name" => "Agent qo'shish", "href" => "/$url2[0]/add_firm.php"]);
            array_push($firmMenu, ["name" => "Sozlamalar", "href" => "/$url2[0]/settings.php"]);
            array_push($firmMenu, ["name" => "Agentlar pullarini yechib olish", "href" => "/$url2[0]/withdrawal_of_money_agents.php"]);

            addMenu("icon-layers", "Agentlar", $firmMenu);

            $coursesArr = $db->in_array("SELECT * FROM courses");
            $courses = [];
            $firmMenu = [];
            foreach ($coursesArr as $firmKey => $firm) {
                $courses[$firm["id"]] = $firm;
                // array_push($firmMenu, [
                //     "name" => $firm["name"],
                //     "href" => "/$url2[0]/requests_list.php?firm_id=" . $firm["id"]
                // ]);
            }

            array_push($firmMenu, ["name" => "Kurslar ro'yxati", "href" => "/$url2[0]/courses_list.php"]);
            array_push($firmMenu, ["name" => "Kurs qo'shish", "href" => "/$url2[0]/add_course.php"]);

            addMenu("icon-layers", "Kurslar", $firmMenu);

            // 

            $document_typesArr = $db->in_array("SELECT * FROM document_types");
            $document_types = [];
            $documentTypeMenu = [];
            foreach ($document_typesArr as $documentType) {
                $document_types[$documentType["id"]] = $documentType;
                // array_push($documentTypeMenu, [
                //     "name" => $documentType["name"],
                //     "href" => "/$url2[0]/requests_list.php?documentType_id=" . $documentType["id"]
                // ]);
            }

            array_push($documentTypeMenu, ["name" => "Xujjat turlari ro'yxati", "href" => "/$url2[0]/document_types_list.php"]);
            array_push($documentTypeMenu, ["name" => "Xujjat turi qo'shish", "href" => "/$url2[0]/add_document_type.php"]);

            addMenu("icon-layers", "Xujjatlar", $documentTypeMenu);

            // 
            $contract_typesArr = $db->in_array("SELECT * FROM contract_types");
            $contract_types = [];
            $contractTypeMenu = [];
            foreach ($contract_typesArr as $contractType) {
                $contract_types[$contractType["id"]] = $contractType;
                // array_push($contractTypeMenu, [
                //     "name" => $contractType["name"],
                //     "href" => "/$url2[0]/requests_list.php?contractType_id=" . $contractType["id"]
                // ]);
            }

            array_push($contractTypeMenu, ["name" => "Kontrakt turlari ro'yxati", "href" => "/$url2[0]/contract_types_list.php"]);
            array_push($contractTypeMenu, ["name" => "Kontrakt turi qo'shish", "href" => "/$url2[0]/add_contract_type.php"]);

            addMenu("icon-layers", "Kontrakt turlari", $contractTypeMenu);

            // 

            addMenu("icon-clipboard4", "Ruxsatnoma dizayni", [
                ["name" => "Ruxsatnoma dizayni", "href" => "/$url2[0]/edit_ruxsatnoma.php"]
            ]);

            addMenu("icon-mail5", "SMS lar", [
                ["name" => "SMS yuborish", "href" => "/$url2[0]/send_sms.php"],
                ["name" => "Yuborilgan smslar<br>ro'yxati", "href" => "/$url2[0]/sms_list.php"]
            ]);

            addMenu("icon-clipboard4", "To'lovlar", [
                ["name" => "To'lovlar ro'yxati", "href" => "/$url2[0]/payments_list.php"],
                ["name" => "Yangi to'lov qo'shish", "href" => "/$url2[0]/add_payment.php"]
            ]);

            addMenu("icon-shuffle3", "Kitob turlari", [
                ["name" => "Kitob turlari ro'yxati", "href" => "/$url2[0]/book_categories_list.php?page=1"],
                ["name" => "Kitob turi qo'shish", "href" => "/$url2[0]/add_book_category.php"]
            ]);

            addMenu("icon-book3", "Kitoblar", [
                ["name" => "Kitoblar ro'yxati", "href" => "/$url2[0]/books_list.php?page=1"],
                ["name" => "Kitob qo'shish", "href" => "/$url2[0]/add_book.php"]
            ]);

            addMenu("icon-air-play", "Media (Videolar)", [
                ["name" => "Media ro'yxati", "href" => "/$url2[0]/medias_list.php?page=1"],
                ["name" => "Media qo'shish", "href" => "/$url2[0]/add_media.php"]
            ]);

            addMenu("icon-image", "Media (Rasmlar)", [
                ["name" => "Rasmlar ro'yxati", "href" => "/$url2[0]/photos_list.php?page=1"],
                ["name" => "Rasm qo'shish", "href" => "/$url2[0]/add_photo.php"]
            ]);

            addMenu("icon-aperture", "Blok testlar", [
                ["name" => "Blok testlar ro'yxati", "href" => "/$url2[0]/block_tests_list.php"],
                ["name" => "Yangi blok test qo'shish", "href" => "/$url2[0]/add_block_test.php"]
            ]);

            addMenu("icon-aperture", "Blok test fanlari", [
                ["name" => "Blok test fanlari ro'yxati", "href" => "/$url2[0]/block_test_sciences_list.php"],
                ["name" => "Yangi blok test fan<br>qo'shish", "href" => "/$url2[0]/add_block_test_science.php"]
            ]);

            addMenu("icon-aperture", "Fan testlari", [
                ["name" => "Fan testlari ro'yxati", "href" => "/$url2[0]/block_test_options_list.php"],
                // ["name" => "Yangi test qo'shish", "href" => "/$url2[0]/add_block_test_option.php"]
            ]);

            addMenu("icon-clipboard4", "Test natijalari", [
                [
                    "name" => "Tugallangan Test<br>natijalari ro'yxati",
                    "href" => "/$url2[0]/block_tests_results_list.php?status=1",
                    "param" => [
                        "name" => "status",
                        "value" => 1
                    ]
                ],
                [
                    "name" => "Tugallanmagan Test<br>natijalari ro'yxati",
                    "href" => "/$url2[0]/block_tests_results_list.php?status=0",
                    "param" => [
                        "name" => "status",
                        "value" => 0
                    ]
                ],
                // ["name" => "Yangi test tashkil qilish", "href" => "/$url2[0]/add_block_test_student.php"]
            ]);

            addMenu("icon-globe2", "Tillar", [
                ["name" => "Tillar ro'yxati", "href" => "/admin/langs_list.php"],
                ["name" => "Til qo'shish", "href" => "/admin/add_lang.php"]
            ]);
            ?>

        </div>

    </div>