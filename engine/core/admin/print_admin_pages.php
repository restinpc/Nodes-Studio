<?php
/**
* Print admin pages file.
* @path /engine/core/admin/print_admin_pages.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $cms->site - Site object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin_pages($cms); </code>
*/
function print_admin_pages($cms){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
            . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "pages" '
            . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
            . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysqli_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    if(!empty($_POST["id"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        if($_POST["date"] == "-3"){
            $query = 'DELETE FROM `nodes_cache` WHERE `id` = "'.$_POST["id"].'"';
        }else{
            $query = 'UPDATE `nodes_cache` SET `interval` = "'.$_POST["date"].'", `title` = "", `html` = "", `content` = "" WHERE `id` = "'.$_POST["id"].'"';
        }engine::mysql($query);
    }
    $fout = '<div class="document980" style="max-width: 1200px;">
        <form method="POST" id="admin_lang_select">'.lang("Select your language").': 
        <select vr-control id="select-lang" class="input" name="lang" onChange=\'document.getElementById("admin_lang_select").submit();\'>';
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $arr = explode(";", $data["value"]);
    foreach($arr as $value){
        if(!empty($value)){
            if(!empty($_SESSION["Lang"])&&$_SESSION["Lang"]==$value){
                $fout .= '<option vr-control id="option-lang-'.$value.'" value="'.$value.'" selected>'.$value.'</option>';
            }else{
                $fout .= '<option vr-control id="option-lang-'.$value.'" value="'.$value.'">'.$value.'</option>';
            }
        }
    }$fout .= '</select></form><br/>';
    if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT `catch`.`url`,'
            . ' `catch`.`id`,'
            . ' `catch`.`interval`,'
            . ' `catch`.`description` AS `desc`,'
            . ' `catch`.`keywords` AS `key`,'
            . ' `catch`.`title` AS `tit`,'
            . ' `seo`.`title`,'
            . ' `seo`.`description`,'
            . ' `seo`.`keywords`,'
            . ' `seo`.`mode`'
            . ' FROM `nodes_cache` as `catch` '
            . ' LEFT JOIN `nodes_meta` AS `seo`'
            . ' ON (`catch`.`url` = `seo`.`url`'
            . '     AND `catch`.`lang` = `seo`.`lang`)'
            . ' WHERE `catch`.`url` NOT LIKE "%/admin%"'
            . '     AND `catch`.`url` LIKE "%'.$_SERVER["HTTP_HOST"].'%"'
            . '     AND `catch`.`lang` = "'.$_SESSION["Lang"].'"'
            . ' ORDER BY `catch`.`'.$_SESSION["order"].'` '.$_SESSION["method"].''
            . ' LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*)'
            . ' FROM `nodes_cache` as `catch` '
            . ' LEFT JOIN `nodes_meta` AS `seo`'
            . ' ON (`catch`.`url` = `seo`.`url`'
            . '     AND `catch`.`lang` = `seo`.`lang`)'
            . ' WHERE `catch`.`url` NOT LIKE "%/admin%"'
            . '     AND `catch`.`url` LIKE "%'.$_SERVER["HTTP_HOST"].'%"'
            . '     AND `catch`.`lang` = "'.$_SESSION["Lang"].'"';
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "url" => "Link",
                "title" => "Title",
                "description" => "Description",
                "keywords" => "Keywords",
                "mode" => "Mode",
                "interval" => "Cache"
            ); foreach($array as $order=>$value){
                $table .= '<th>';
                if($_SESSION["order"]==$order){
                    if($_SESSION["method"]=="ASC") $table .= '<a vr-control id="table-order-'.$order.'" class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "DESC"; submit_search_form();\'>'.lang($value).'&nbsp;&uarr;</a>';
                    else $table .= '<a vr-control id="table-order-'.$order.'" class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'&nbsp;&darr;</a>';
                }else $table .= '<a vr-control id="table-order-'.$order.'" class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'</a>';
                $table .= '</th>';
            }
            $table .= '
        <th></th>
        </tr>
        </thead>';
    $res = engine::mysql($query);
    while($data = mysqli_fetch_array($res)){
        $arr_count++;
        $opt = array();
        $opt[$data["interval"]] = "selected";
        $url = str_replace($_SERVER["PROTOCOL"]."://".$_SERVER["HTTP_HOST"], "", $data["url"]);
        if(strlen($url)>25) $url = mb_substr($url,0,25).'..';
        $table .= '
        <tr>
            <td align=left valign=middle>
                <input type="hidden" name="id" value="'.$data["id"].'" />
                <a vr-control id="link-'.$data["id"].'" href="'.$data["url"].'" target="_blank" class="nowrap" >'.$url.'</a>
            </td>
            <td align=left valign=middle>
                <input vr-control id="page-title-'.$data["id"].'" type="text" '.($admin_acces!=2?'disabled':'').' class="input" name="title" id="title_'.$data["id"].'" placeHolder="'.$data["title"].'" value="'.$data["title"].'"
                    onChange=\'document.getElementById("button_'.$data["id"].'").style.display="block"; jQuery("#button_'.$data["id"].'").removeClass("hidden");\'
                />
            </td>
            <td align=left valign=middle>
                <input vr-control id="page-desc-'.$data["id"].'" type="text" '.($admin_acces!=2?'disabled':'').' class="input" name="description" id="description_'.$data["id"].'" placeHolder="'.$data["desc"].'" value="'.$data["description"].'"
                   onChange=\'document.getElementById("button_'.$data["id"].'").style.display="block"; jQuery("#button_'.$data["id"].'").removeClass("hidden");\' 
                />
            </td>
            <td align=left valign=middle>
                <input vr-control id="page-keywords-'.$data["id"].'" type="text" '.($admin_acces!=2?'disabled':'').' class="input" name="keywords" id="keywords_'.$data["id"].'"  placeHolder="'.$data["key"].'" value="'.$data["keywords"].'" 
                    onChange=\'document.getElementById("button_'.$data["id"].'").style.display="block"; jQuery("#button_'.$data["id"].'").removeClass("hidden");\'
                />
            </td>
            <td align=left valign=middle>
                <select vr-control name="mode" '.($admin_acces!=2?'disabled':'').' class="input" id="mode_'.$data["id"].'"
                    onChange=\'document.getElementById("button_'.$data["id"].'").style.display="block"; jQuery("#button_'.$data["id"].'").removeClass("hidden");\' >
                    <option vr-control id="option-mode-0" value="0">'.lang("Add").'</option>
                    <option vr-control id="option-mode-1" value="1" '.(($data["mode"]||is_null($data["mode"]))?'selected':'').'>'.lang("Replace").'</option>
                </select>
            </td>
            <td align=left valign=middle>
                <form method="POST" id="form_'.$data["id"].'"><input type="hidden" name="id" value="'.$data["id"].'" />
                <select vr-control id="select-page-'.$arr_count.'" name="date" '.($admin_acces!=2?'disabled':'').' class="table_selector input w120" onChange=\'document.getElementById("form_'.$data["id"].'").submit();\'>
                    <option vr-control id="option-interval-0" value="-1" '.$opt[-1].'>'.lang("Not cathing").'</option>
                    <option vr-control id="option-interval-1" value="0" '.$opt[0].'>'.lang("Not refreshing").'</option>
                    <option vr-control id="option-interval-2" value="60" '.$opt[60].'>1 '.lang("minut").'</option>
                    <option vr-control id="option-interval-3" value="600" '.$opt[600].'>10 '.lang("minuts").'</option>
                    <option vr-control id="option-interval-4" value="3600" '.$opt[3600].'>1 '.lang("hours").'</option>
                    <option vr-control id="option-interval-5" value="43200" '.$opt[43200].'>12 '.lang("hours").'</option>
                    <option vr-control id="option-interval-6" value="86400" '.$opt[86400].'>'.lang("Dayly").'</option>
                    <option vr-control id="option-interval-7" value="-3">'.lang("Delete").'</option>
                </select>
                </form>
            </td>
            <td width=40 align=left valign=middle>';
        if($admin_access == 2){
        $table .= '
            <input vr-control id="button_'.$data["id"].'" type="button" onClick=\'edit_seo("'.intval($data["id"]).'");\' class="btn small hidden" value="&#10004;" />
                ';
        }
        $table .= '
            </td>
        </tr>';
    }$table .= '</table>
</div>
<br/>';
    if($arr_count){
        $fout .= $table.'
    <form method="POST"  id="query_form"  onSubmit="submit_search();">
    <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
    <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
    <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
    <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
    <input type="hidden" name="reset" id="query_reset" value="0" />
    <div class="total-entry">';
    $res = engine::mysql($requery);
    $data = mysqli_fetch_array($res);
    $count = $data[0];
    if($to > $count) $to = $count;
    if($data[0]>0){
        $fout .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
            <nobr><select vr-control id="select-pagination" class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
             <option vr-control id="option-pagination-20"'; if($_SESSION["count"]=="20") $fout.= ' selected'; $fout.= '>20</option>
             <option vr-control id="option-pagination-50"'; if($_SESSION["count"]=="50") $fout.= ' selected'; $fout.= '>50</option>
             <option vr-control id="option-pagination-100"'; if($_SESSION["count"]=="100") $fout.= ' selected'; $fout.= '>100</option>
            </select> '.lang("per page").'.</nobr></p>';
    }$fout .= '
    </div><div class="cr"></div>';
    if($count>$_SESSION["count"]){
       $fout .= '<div class="pagination" >';
            $pages = ceil($count/$_SESSION["count"]);
           if($_SESSION["page"]>1){
                $fout .= '<span vr-control id="page-prev" onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
            }$fout .= '<ul>';
           $a = $b = $c = $d = $e = $f = 0;
           for($i = 1; $i <= $pages; $i++){
               if(($a<2 && !$b && $e<2)||
                   ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
               ($i>$pages-2 && $e<2)){
                   if($a<2) $a++;
                   $e++; $f = 0;
                   if($i == $_SESSION["page"]){
                       $b = 1; $e = 0;
                      $fout .= '<li class="active-page">'.$i.'</li>';
                   }else{
                       $fout .= '<li vr-control id="page-'.$i.'" onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                   }
               }else if((!$c||!$b) && !$f && $i<$pages){
                   $f = 1; $e = 0;
                   if(!$b) $b = 1;
                   else if(!$c) $c = 1;
                   $fout .= '<li class="dots">. . .</li>';
               }
           }if($_SESSION["page"]<$pages){
               $fout .= '<li vr-control id="page-next" class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
           }$fout .= '
     </ul>
    </div>';
         }$fout .= '<div class="clear"></div>
             </form>';
    }else{
        $fout .= '<div class="clear_block">'.lang("Pages not found").'</div>';
    }
    $fout .= '</div>';
    return $fout;
}

