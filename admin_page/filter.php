<?php
// $myObj = (object)[];
// $_REQUEST = array();
// //kelayotgan hamma POST va GET so'rovlarini filter qilib req nomli array ga biriktirib qo'yish
// if(isset($_POST) && $_SERVER['REQUEST_METHOD'] == "POST" OR isset($_GET) && $_SERVER['REQUEST_METHOD'] == "GET"){
//     foreach($_REQUEST as $key => $value){
//         if (gettype($value) == "string") {
//             $_REQUEST += [
//               $key => $translate::toLatin(trim(htmlspecialchars($value)))
//             ];
//         } else if (gettype($value) == 'array') {
//             if ($value['uz']) $value['uz'] = $translate::toLatin(trim($value['uz']));
//             $_REQUEST += [
//                 $key => trim(json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))
//             ];
//         }
//     }
// }

function pagination($page_count, $href, $qurl, $count) {
    global $page;
    $output = '';
    if (!isset($page)) $page = 1;
    if ($count != 0) $pages = ceil($page_count / $count);
    if ($pages == 1 || $pages == 0) return;

    if ($page != 1){
        $output .= '<li class="page-item">
                        <a class="page-link" href="'.$href.'?page='.($page-1).($qurl ? "&".$qurl : "").'">
                          <span aria-hidden="true">«</span>
                        </a>
                    </li>';
    } else {
        $output .= '<li style="cursor:no-drop" class="page-item">
                        <a style="cursor:no-drop" class="page-link">
                          <span aria-hidden="true">«</span>
                        </a>
                    </li>';
    }
    
    //if pages exists after loop's lower limit
    if ($pages > 1) {
        if (($page - 3) > 0) {
            $output .= '<li class="page-item">
                            <a href="'.$href.'?page=1'.($qurl ? "&".$qurl : "").'" class="page-link">1</a>
                        </li>';
        }
        if (($page - 3) > 1) {
            $output .= '<li class="page-item">
                           <a class="page-link">...</a>
                        </li>';
        }
        
        //Loop for provides links for 2 pages before and after selected page
        for ($i = ($page - 2); $i<=($page + 2); $i++)	{
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i) {
                $output .= '<li class="page-item active">
                                <a class="page-link">'.$i.'</a>
                            </li>';
            } else {
                $output .= '<li class="page-item">
                                <a href="'.$href.'?page='.$i.($qurl ? "&".$qurl : "").'" class="page-link">'.$i.'</a>
                            </li>'; 
            }
        }
        
        //if pages exists after loop's upper limit
        if (($pages-($page + 2)) > 1) {
            $output .= '<li class="page-item">
                           <a class="page-link">...</a>
                        </li>';
        }
        if (($pages-($page + 2)) > 0) {
            $output .= '<li class="page-item '.($page == $pages ? "active" : "").'">
                            <a href="'.$href.'?page='.$pages.($qurl ? "&".$qurl : "").'" class="page-link">'.$pages.'</a>
                        </li>';
        }
    }

    if ($page != $pages){
        $output .= '<li class="page-item">
                        <a class="page-link" href="'.$href.'?page='.($page+1).($qurl ? "&".$qurl : "").'">
                          <span aria-hidden="true">»</span>
                        </a>
                    </li>';
    } else {
        $output .= '<li style="cursor:no-drop" class="page-item">
                        <a style="cursor:no-drop" class="page-link">
                          <span aria-hidden="true">»</span>
                        </a>
                    </li>';
    }

    $output .= '<form action="" method="GET" style="display:inline-block;margin-left:50px">
                    <input type="number" name="page" min="1" max="'.$pages.'" style="width:auto" class="page-to" placeholder="'.$page.'">
                    <button type="submit" class="page-to">&raquo;</button>
                </form>';

    return $output;
}


$lavozimlar = ["o'quvchi", "o'qituvchi", "admin"];

function delete_image($image_id) {
    global $db;
    if (!$image_id) return;
    $image = image($image_id);
    if ($image["id"] > 0) {
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$image["file_folder"])) unlink($_SERVER["DOCUMENT_ROOT"]."/".$image["file_folder"]);
        $db->delete("images", $image_id);
    }
}

function delete_video($video_id) {
    global $db;
    if (!$video_id) return;
    $video = video($video_id);
    if ($video["id"] > 0) {
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$video["file_folder"])) unlink($_SERVER["DOCUMENT_ROOT"]."/".$video["file_folder"]);
        $db->delete("videos", $video_id);
    }
}

function delete_audio($audio_id) {
    global $db;
    if (!$audio_id) return;
    $audio = audio($audio_id);
    if ($audio["id"] > 0) {
        if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$audio["file_folder"])) unlink($_SERVER["DOCUMENT_ROOT"]."/".$audio["file_folder"]);
        $db->delete("audios", $audio_id);
    }
}

// function name($str) {
//     $str = str_replace("®", "", $str);
//     $str = str_replace("'", "", $str);
//     $str = str_replace("`", "", $str);
//     $str = str_replace("?", "", $str);
//     $str = str_replace("-", " ", $str);
//     $res = mb_strtolower(
//       str_replace(" ", "-", preg_replace("/[^A-Za-z0-9 ]/", '', $str))
//     );
//     $res = str_replace("--", "-", $res);
//     if (substr($res, -1) == "-") $res = substr($res, 0, mb_strlen($res) - 1);
//     if (substr($res, 0, 1) == "-") $res = substr($res, 1);
//     return $res;
// }

// $_REQUESTUEST_URI = $_SERVER['REQUEST_URI'];
// if ($_SERVER["QUERY_STRING"]) {
//     $_REQUESTUEST_URI = explode("?", $_REQUESTUEST_URI)[0];
// }
  
// $url = [];
// $fr2url = explode('/', mb_substr(urldecode($_REQUESTUEST_URI), 1, mb_strlen(urldecode($_REQUESTUEST_URI))));
// if ($fr2url){
//     foreach($fr2url as $frurl){
//         if ($frurl) $url2[] = $frurl;
//     }
// }
?>