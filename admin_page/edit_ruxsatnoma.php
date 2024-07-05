<?php

// tekshiruv
if (!$user_id || $user_id == 0) {
    header('Location:/login');
    exit;
} else if ($systemUser->admin != 1){
    header('Content-Type: text/html');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        siz admin lavozimida emassiz!<br>admin lavozimidagi akkauntga kirish uchun <a href="/exit">akkauntdan chiqish</a> tugmasini bosing.
    </body>
    </html>';
    exit;
}



include('filter.php');

$ruxsatnoma_html = $db->assoc("SELECT * FROM ruxsatnoma_html");

if ($_REQUEST['type'] == "edit_ruxsatnoma"){
    $html_uz = isset($_REQUEST['html_uz']) ? $_REQUEST['html_uz'] : null;
    $html_ru = isset($_REQUEST['html_ru']) ? $_REQUEST['html_ru'] : null;

    $insert_arr = [
        "html_uz" => $html_uz,
        "html_ru" => $html_ru
    ];

    if ($ruxsatnoma_html) {
        $db->update("ruxsatnoma_html", $insert_arr);
    } else {
        $db->insert("ruxsatnoma_html", $insert_arr);
    }

    header("Location: edit_ruxsatnoma.php");
}

if (empty($ruxsatnoma_html["html_uz"]) && empty($ruxsatnoma_html["html_ru"])) {
    $dizayn = file_get_contents("../files/ruxsatnoma-2.html");
    $ruxsatnoma_html["html_uz"] = $dizayn;
    $ruxsatnoma_html["html_ru"] = $dizayn;
}

include('head.php');
?>

<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Ruxsatnoma bo'limini tahrirlash (UZ)</h4>
                            
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                    <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <input type="hidden" name="type" value="edit_ruxsatnoma" required>

                                <iframe src="/<?=$url2[0]?>/html.php?lang=uz" frameborder="0" style="width:100%" id="iframe_uz"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  -->

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="basic-layout-colored-form-control">Ruxsatnoma bo'limini tahrirlash (RU)</h4>
                            
                            <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                    <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                    <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                    <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form action="edit_ruxsatnoma.php" method="POST" class="form" enctype="multipart/form-data" id="ruxsatnoma-form">
                                    <input type="hidden" name="html_uz" id="html_uz">
                                    <input type="hidden" name="html_ru" id="html_ru">

                                    <input type="hidden" name="type" value="edit_ruxsatnoma" required>

                                    <iframe src="/<?=$url2[0]?>/html.php?lang=ru" frameborder="0" style="width:100%" id="iframe_ru"></iframe>

                                    <div class="form-actions right">
                                        <button type="button" class="btn btn-primary" id="submit-ruxsatnoma">
                                            <i class="icon-check2"></i> Saqlash
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

            </div>
        </div>
    </div>
</div>

<?php
    include('scripts.php');
?>

<script>
    $("#submit-ruxsatnoma").on("click", function(){
        var html_uz = document.getElementById('iframe_uz').contentWindow.document.documentElement.outerHTML;
        var html_ru = document.getElementById('iframe_ru').contentWindow.document.documentElement.outerHTML;

        $("#html_uz").val(html_uz);
        $("#html_ru").val(html_ru);

        $("#ruxsatnoma-form").submit()
    });

    $('iframe').on("load", function(){
        document.getElementById("iframe_uz").contentDocument.designMode = "on";
        document.getElementById("iframe_ru").contentDocument.designMode = "on";

        setTimeout(function(){
            $('iframe').height( $('iframe').contents().outerHeight() );
        }, 50);

        setTimeout(function(){
            $('iframe').height( $('iframe').contents().outerHeight() );
        }, 3000);
    })

    $( window ).resize(function() {
        // console.log("test");
        $('iframe').height( $('iframe').contents().outerHeight() );
    })
</script>

<?php
    include('end.php');
?>