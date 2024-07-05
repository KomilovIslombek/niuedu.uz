<?php
$user_id = $systemUser->id;
$rights = $systemUser->rights;
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


$page = (int)$_GET['page'];
if (empty($page)){
    $page = 1;
    $_GET["page"] = 1;
}

$method_name = "university-dashboard/accepted-applications";
$other_site_res = addApi("GET", $method_name, '', $page);
// $other_site_student = addApi("GET", "application-form", 44081, $page);

$res = json_decode($other_site_res, true);
if($res["message"] == 'Unauthorized') {
    $other_site_res = $db->assoc("SELECT * FROM other_site_res");

    $tokenArr = getTokenApi();
    $token = json_decode($tokenArr, true);

    if(empty($other_site_res["id"])) {
        $db->insert("other_site_res", [
            "creator_user_id" => $systemUser["user_id"],
            "token" => $token["token"],
        ]);
    } else {
        $db->update("other_site_res", [
            "token" => $token["token"],
        ], [
            "id" => $other_site_res["id"]
        ]);
    }

    $other_site_res = addApi("GET", $method_name, '', $page, $token["token"]);
    $res = json_decode($other_site_res, true);
}

$pageInfo = $res["pageInfo"];
$other_site_requests = $res["entities"];

$page_count = $_GET["page_count"] ? $_GET["page_count"] : count($other_site_requests);
$page_end = $page * $page_count;
$page_start = $page_end - $page_count;

// echo "<pre>";
// print_r($other_site_requests);
// echo count($other_site_requests);
// exit;

function name2($str) {
    return mb_strtolower(
        // str_replace(" ", "-", $str)
        strtr($str, [ 
            ":" => "",
            "`" => "",
            "‘" => "",
            "’" => "",
            "'" => "",
            "(" => "",
            ")" => "",
        ])
    );
}

$directions_arr = $db->in_array("SELECT id, code, name FROM directions");
$directions = [];
foreach ($directions_arr as $direction) {
    $direction["name"] = name2(lng($direction["name"], "uz"));
    $directions[$direction["name"]] = $direction["id"];
}

// echo "<pre>";
// print_r($directions);
// echo name2("Boshlang‘ich ta’lim");
// echo $directions[name2("Boshlang‘ich ta’lim")];

// exit;
include('filter.php');

include('head.php')
?>

<!--  -->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-body">

            <div class="row">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Men talabaman saytidagi arizalar</h4>
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
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#id</th>
                                            <th>F.I.SH</th>
                                            <th>Raqami</th>
                                            <th>Passport seria va raqami</th>
                                            <th>Darajasi</th>
                                            <th>Yo'nalishi</th>
                                            <th>Yo'nalish id</th>
                                            <th>Ta'lim shakli</th>
                                            <th>Ta'lim tili</th>
                                            <th>Statusi</th>
                                            <th>Qo'shilgan sana</th>
                                            <th>Import</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            
                                            foreach ($other_site_requests as $request){
                                                // $image = image($savol_javob["image_id"]);
                                                $have_request = $db->assoc("SELECT * FROM requests WHERE imported_id = ?", [ $request["id"] ]);
                                                $direction_id = $directions[name2($request["direction_name_uz"])];

                                                echo '<tr>';
                                                    echo '<th scope="row">'.$request["id"].'</th>';
                                                    echo '<td>'.$request["full_name"].'</td>';
                                                    echo '<td>'.$request["phone"].'</td>';
                                                    echo '<td>'.$request["serial_number"].'</td>';
                                                    echo '<td>'.$request["degree_uz"].'</td>';
                                                    echo '<td>'.$request["direction_name_uz"].'</td>';
                                                    echo '<td>'.$direction_id.'</td>';
                                                    echo '<td>'.$request["education_type_uz"].'</td>';
                                                    echo '<td>'.$request["education_lang_uz"].'</td>';
                                                    echo '<td>'.translate($request["status"]).'</td>';
                                                    echo '<td>'.$request["created_at"].'</td>';
                                                    echo '<td>';
                                                        if($have_request["id"]) {
                                                            echo 'Import qilingan! <br> <a href="requests_list.php?&q='.$have_request["code"].'"class="tag tag-default tag-primary text-white">ko\'rish</a>' ;
                                                        } else {
                                                            echo '<a href="other_site__request_import.php?id='.$request['id'].'&direction_id='.$direction_id.'&direction_name='.name2($request["direction_name_uz"]).'&learn_type='.$request["education_type_uz"].'&page='.$page.'" class="tag tag-default tag-success text-white">import</a>';
                                                        }
                                                    echo '</td>';
                                                echo '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="text-xs-center mb-3">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?
                                        // $count = (int)$db->assoc("SELECT COUNT(*) FROM savol_javoblar")["COUNT(*)"] / $page_count;
                                        $count = (int)count($other_site_requests) / $page_count;

                                        if (gettype($count) == "double") $count = (int)($count + 1);
                        
                                        if ($page != 1){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="other_site_requests.php?page='.($page-1).'">
                                                        <span aria-hidden="true">«</span>
                                                    </a>
                                                </li>';
                                        } else {
                                          echo '<li style="cursor:no-drop" class="page-item">
                                                    <a style="cursor:no-drop" class="page-link">
                                                        <span aria-hidden="true">«</span>
                                                    </a>
                                                </li>';
                                        }
                                        
                                        $max = 4;
                                        for ($i = 0; $i <= $count; $i++) {
                                            if ($i == 1 || $i == $count || $i >= $page && $i <= $page + ($max - 1)) {
                                                echo '<li class="page-item '.($page == $i ? "active" : "").'"><a href="other_site_requests.php?page='.$i.'" class="page-link">'.$i.'</a></li>';
                                            }
                                        }
                        
                                        if ($page != $count){
                                          echo '<li class="page-item">
                                                    <a class="page-link" href="other_site_requests.php?page='.($page+1).'">
                                                        <span aria-hidden="true">»</span>
                                                    </a>
                                                </li>';
                                        } else {
                                            echo '<li style="cursor:no-drop" class="page-item">
                                                      <a style="cursor:no-drop" class="page-link">
                                                        <span aria-hidden="true">»</span>
                                                      </a>
                                                  </li>';
                                        }
                                        ?>
                                        <form action="" method="GET" style="display:inline-block;margin-left:50px">
                                            <input type="number" name="page" min="1" max="<?=$count?>" style="width:auto" class="page-to" placeholder="<?=$_GET['page']?>">
                                            <button type="submit" class="page-to">&raquo;</button>
                                        </form>

                                        <style>
                                            button.page-to {
                                                cursor: pointer;
                                            }
                                            button.page-to:hover {
                                                background-color: #ddd;
                                            }
                                            input.page-to {
                                                width: 70px;
                                                padding-right: 0;
                                            }
                                            input.page-to:focus,
                                            button.page-to:focus {
                                                outline: none;
                                            }
                                            .page-to {
                                                position: relative;
                                                float: left;
                                                padding: 0.5rem 0.75rem;
                                                margin-left: -1px;
                                                color: #7A54D8;
                                                text-decoration: none;
                                                background-color: #fff;
                                                border: 1px solid #ddd;
                                                line-height: 1.8;
                                            }
                                        </style>
                                    </ul>
                                </nav>
                            </div>
                            <!-- End Pagination -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<? include "scripts.php"; ?>

<? include('end.php'); ?>