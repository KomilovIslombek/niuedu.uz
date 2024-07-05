<?php
function pagination($page_count, $href, $count) {
    if (gettype($count) == "double") $count = (int)($count + 1);

    // qurl
    unset($_REQUEST["page"]);
    $qurl = http_build_query($_REQUEST);

    global $page;

    $output = '';
    if (!isset($page)) $page = 1;
    if ($count != 0) $pages = ceil($page_count / $count);
    if ($pages == 1 || $pages == 0) return;

    if ($page != 1){
        $output .= '<li class="prev">
                        <a href="'.$href.($page-1).($qurl ? "/?$qurl" : "").'" class="link-btn link-prev">
                            Oldingi
                            <i class="arrow_left"></i>
                            <i class="arrow_left"></i>
                        </a>
                    </li>';
    } else {
        $output .= '<li class="prev" style="cursor:no-drop">
                        <a href="javascript:void(0);" class="link-btn link-prev" style="cursor:no-drop">
                            Oldingi
                            <i class="arrow_left"></i>
                            <i class="arrow_left"></i>
                        </a>
                    </li>';
    }
    
    //if pages exists after loop's lower limit
    if ($pages > 1) {
        if (($page - 3) > 0) {
            $output .= '<li>
                            <a href="'.$href."1".($qurl ? "/?$qurl" : "").'">
                                <span>1</span>
                            </a>
                        </li>';
        }
        if (($page - 3) > 1) {
            $output .= '<li>
                            <a href="javascript:void(0);">
                                <span>...</span>
                            </a>
                        </li>';
        }
        
        //Loop for provides links for 2 pages before and after selected page
        for ($i = ($page - 2); $i<=($page + 2); $i++)	{
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i) {
                $output .= '<li class="active">
                                <a href="javascript:void(0);">
                                    <span>'.$i.'</span>
                                </a>
                            </li>';
            } else {
                $output .= '<li>
                            <a href="'.$href.$i.($qurl ? "/?$qurl" : "").'">
                                <span>'.$i.'</span>
                            </a>
                        </li>';
            }
        }
        
        //if pages exists after loop's upper limit
        if (($pages-($page + 2)) > 1) {
            $output .= '<li>
                            <a href="javascript:void(0);">
                                <span>...</span>
                            </a>
                        </li>';
        }
        if (($pages-($page + 2)) > 0) {
            $output .= '<li class="'.($page == $pages ? "active" : "").'">
                            <a href="'.$href.$pages.($qurl ? "/?$qurl" : "").'">
                                <span>'.$pages.'</span>
                            </a>
                        </li>';
        }
    }

    if ($page != $pages){
        $output .= '<li class="next">
                        <a href="'.$href.($page+1).($qurl ? "/?$qurl" : "").'" class="link-btn link-prev">
                            Oldingi
                            <i class="arrow_right"></i>
                            <i class="arrow_right"></i>
                        </a>
                    </li>';
    } else {
        $output .= '<li class="next" style="cursor:no-drop">
                        <a href="javascript:void(0);" class="link-btn link-next" style="cursor:no-drop">
                            Keyingi
                            <i class="arrow_right"></i>
                            <i class="arrow_right"></i>
                        </a>
                    </li>';
    }

    return '<div class="row">
                <div class="col-xxl-12">
                    <div class="basic-pagination wow fadeInUp mt-30" data-wow-delay=".2s" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                        <ul class="d-flex align-items-center"> 
                            '.$output.'
                        </ul>
                    </div>
                </div>
            </div>';
}
?>