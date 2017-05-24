<?php
/**
* Prints content articles page.
* @path /engine/core/content/print_articles.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param object $site Site class object.
* @param int $data @mysql[nodes_catalog].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_articles($site, $data); </code>
*/
function print_articles($site, $data=array()){
    $cat_id = $data["id"];
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    if(!empty($data)){
        $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"'
                        . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
        $requery = 'SELECT COUNT(*) FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
    }else{
        $query = 'SELECT * FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'"'
                . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
        $requery = 'SELECT COUNT(*) FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'"'; 
    }
    $res = engine::mysql($requery);
    $data = mysql_fetch_array($res);
    $count = $data[0];
    $res = engine::mysql($query);
    $table = '<div class="preview_blocks">';
    $flag = 0;
    while($d = mysql_fetch_array($res)){
        $flag = 1;
        $table .= engine::print_preview($site, $d);
    }
    $table .= '</div><div class="clear"></div><br/>';
    if($flag){
        $fout .= $table.'
        <form method="POST"  id="query_form"  onSubmit="submit_search();">
        <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
        <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
        <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
        <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
        <input type="hidden" name="reset" id="query_reset" value="0" />
        <div class="total-entry">';
        if($to > $count) $to = $count;
        if($data[0]>0){
            $fout .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
                <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
                 <option'; if($_SESSION["count"]=="20") $fout .= ' selected'; $fout .= '>20</option>
                 <option'; if($_SESSION["count"]=="50") $fout .= ' selected'; $fout .= '>50</option>
                 <option'; if($_SESSION["count"]=="100") $fout .= ' selected'; $fout .= '>100</option>
                </select> '.lang("per page").'.</nobr></p>';
        }$fout .= '
        </div><div class="cr"></div>';
        if($count>$_SESSION["count"]){
           $fout .= '<div class="pagination" >';
                $pages = ceil($count/$_SESSION["count"]);
               if($_SESSION["page"]>1){
                    $fout .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
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
                           $fout .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                       }
                   }else if((!$c||!$b) && !$f && $i<$pages){
                       $f = 1; $e = 0;
                       if(!$b) $b = 1;
                       else if(!$c) $c = 1;
                       $fout .= '<li class="dots">. . .</li>';
                   }
               }if($_SESSION["page"]<$pages){
                   $fout .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
               }$fout .= '
         </ul>
        </div>';
        }
        $fout .= '</form>'
            . '<div class="clear"></div>';
    }
    if(!$count){
        $fout .= '<div class="clear_block">'.lang("Content not found").'</div>';
    }$fout .= '<br/>';
    if($_SESSION["user"]["id"]=="1" && $cat_id){
        $fout .= '<br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$cat_id.'"><input type="button" class="btn w280" value="'.lang("Add article").'" /></a>'
        . '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$cat_id.'&act=edit"><input type="button" class="btn w280" value="'.lang("Edit directory").'" /></a><br/><br/>';
    }  
    return $fout;
}