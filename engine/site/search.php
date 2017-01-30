<?php
/**
* Backend search page file.
* @path /engine/site/search.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/
if(!empty($_GET[2])){
    $this->content = engine::error();
    return; 
}else if(empty($_GET[1])){
    $this->content = '<div class="clear_block">'.lang("Empty search query").'</div>';
    return;
}
$request = mysql_real_escape_string(urldecode($_GET[1]));
$this->title = lang("Search").' - '.$this->title;
$this->description = lang("Search results for").' '.$request;
$this->content .= '<div class="document">'.
    lang("Search results for").'<br/><br/>'
    . '<h1> "'.$request.'"</h1><br/><br/>';
if(!empty($_POST["from"])) $_SESSION["from"] = $_POST["from"];
if(!empty($_POST["to"])) $_SESSION["to"] = $_POST["to"];
if(!empty($_POST["count"])) $_SESSION["count"] = intval($_POST["count"]);
if(!empty($_POST["page"])) $_SESSION["page"] = intval($_POST["page"]);
if(!empty($_POST["method"])) $_SESSION["method"] = $_POST["method"];
if(!empty($_POST["order"])) $_SESSION["order"] = $_POST["order"];
if($_SESSION["order"]=="id") $_SESSION["order"] = "date";   
$arr_count = 0;
$count = 0;
$uncount = 1;
$from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
$to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
$query = 'SELECT * FROM `nodes_cache` WHERE (`content` LIKE "%'.  $request.'%" or `title` LIKE "%'.$request.'%") AND `interval` > -2'
. ' AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"];
$requery = 'SELECT * FROM `nodes_cache` WHERE (`content` LIKE "%'.$request.'%" or `title` LIKE "%'.$request.'%") AND `interval` > -2  AND `lang` = "'.$_SESSION["Lang"].'"';
$res = engine::mysql($query);
while($data = mysql_fetch_array($res)){
    if(mb_strpos($data["url"], "search")===FALSE){
        if(empty($data["url"])) $data["url"] = "/";
        if(!empty($data["title"])) $title = $data["title"];
        else $title = $data["url"];
        if(!empty($data["content"])) $content = $data["content"];
        else $content = $data["html"];
        if(!empty($data["html"])){
            $result = engine::print_search_result($this, $title, $content, $data["url"]);
            if($result){
                if($uncount<$from){
                    $uncount++;
                }elseif($arr_count<$_SESSION["count"]){
                    $arr_count++;
                    $fout .= $result;
                }else{
                    break;
                }
            }
        }
    }
}
$res = engine::mysql($requery);
while($data = mysql_fetch_array($res)){
    if(empty($data["url"])) $data["url"] = "/";
    if(!empty($data["title"])) $title = $data["title"];
    else $title = $data["url"];
    if(!empty($data["content"])) $content = $data["content"];
    else $content = $data["html"];
    if(!empty($data["html"])){
        if(engine::print_search_result($this, $title, $content, $data["url"])) $count++;
    }
}
if($arr_count){
    $this->content .= $fout.'
    <form method="POST"  id="query_form"  onSubmit="submit_search();">
    <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
    <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
    <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
    <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
    <input type="hidden" name="reset" id="query_reset" value="0" />
    <div class="total-entry">';
    if($to > $count) $to = $count;
    if($count>0){
        $this->content .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
            <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
             <option'; if($_SESSION["count"]=="20") $this->content .= ' selected'; $this->content .= '>20</option>
             <option'; if($_SESSION["count"]=="50") $this->content .= ' selected'; $this->content .= '>50</option>
             <option'; if($_SESSION["count"]=="100") $this->content .= ' selected'; $this->content .= '>100</option>
            </select> '.lang("per page").'.</nobr></p>';
    }$this->content .= '
    </div><div class="cr">';
    if($count>$_SESSION["count"]){
       $this->content .= '<div class="pagination" >';
            $pages = ceil($count/$_SESSION["count"]);
           if($_SESSION["page"]>1){
                $this->content .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
            }$this->content .= '<ul>';
           $a = $b = $c = $d = $e = $f = 0;
           for($i = 1; $i <= $pages; $i++){
               if(($a<2 && !$b && $e<2)||
                   ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
               ($i>$pages-2 && $e<2)){
                   if($a<2) $a++;
                   $e++; $f = 0;
                   if($i == $_SESSION["page"]){
                       $b = 1; $e = 0;
                      $this->content .= '<li class="active-page">'.$i.'</li>';
                   }else{
                       $this->content .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                   }
               }else if((!$c||!$b) && !$f && $i<$pages){
                   $f = 1; $e = 0;
                   if(!$b) $b = 1;
                   else if(!$c) $c = 1;
                   $this->content .= '<li class="dots">. . .</li>';
               }
           }if($_SESSION["page"]<$pages){
               $this->content .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
           }$this->content .= '
     </ul>
    </div>';
         }$this->content .= '<div class="clear"></div>'
                . '</div>'
            . '</div>';
}else{
    $this->content .= '<div class="clear_block">'.lang("Sorry, no results found").'</div>';
}$this->content .= '</div>';