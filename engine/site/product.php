<?php 
/* 
$this->title - Page title 
$this->content - Page HTML data 
$this->menu - Page HTML navigation 
$this->keywords - Page meta keywords 
$this->description - Page meta description 
$this->img - Page meta image 
$this->js - Page JavaScript code 
$this->activejs - Page executable JavaScript code 
$this->css - Page CSS data 
$this->configs - Array MySQL configs 
*/
//----------------------------------------------------
if(intval($_GET[1])>0){
    require_once("engine/include/print_product.php");
    $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.intval($_GET[1]).'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data)){   
        if(!empty($_POST["title"]) && $_SESSION["user"]["id"] == $data["user_id"]){
            $property = '';
            foreach($_POST as $key=>$value){
                if(strpos(' '.$key, 'property_')){
                    $key = str_replace('property_', '', $key);
                }if(intval($key) > 0){
                    $property .= '['.$key.','.intval($_POST["property_".$key]).']';
                }
            }
            $title = trim(htmlspecialchars($_POST["title"]));
            $text = trim(htmlspecialchars($_POST["text"]));
            $price = doubleval($_POST["price"]);
            $query = 'UPDATE `nodes_products` SET '
                    . '`title` = "'.$title.'", '
                    . '`text` = "'.$text.'", '
                    . '`price` = "'.$price.'", '
                    . '`properties` = "'.$property.'" '
                    . 'WHERE `id` = "'.intval($data["id"]).'"';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.intval($_GET[1]).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
        }
        $this->title = $data["title"].' - '.$this->title;
        $this->description = mb_substr($data["text"],0,300);
        $this->content = print_product($data);
        if(!empty($_POST["edit"])){
            $this->activejs .= '
                edit_product("'.lang("Save Changes").'");
            ';
        }
    }return;
}

if(!empty($_POST["from"])) $_SESSION["from"] = $_POST["from"];
if(!empty($_POST["to"])) $_SESSION["to"] = $_POST["to"];
if(!empty($_POST["count"])) $_SESSION["count"] = intval($_POST["count"]);
if(!empty($_POST["page"])) $_SESSION["page"] = intval($_POST["page"]);
if(!empty($_POST["method"])) $_SESSION["method"] = $_POST["method"];
if(!empty($_POST["reset"])){
    unset($_SESSION["details"]);
}else if(!empty($_POST["details"])){
    foreach($_POST as $key=>$value){
        $_SESSION["details"][$key] = $value;
    }
}

$from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
$to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];

require_once('engine/include/print_product_filter.php'); 

$this->content = '
<style>
#contentSection .container{
    max-width: 1220px;
}
</style>
<section id="productSection" class="inner_products">
<div class="filter">
    <div class="results">'.lang("FILTER RESULTS").'</div>
    '.print_product_filter(1, $_GET[1]).'
</div>
<div class="product_content">
    <div class="products">
    <a href="'.$_SERVER["DIR"].'/product">'.lang("All Items").'</a>';
$query = 'SELECT * FROM `nodes_products` WHERE `status` = 1';   
if(!empty($_POST["request"])){
    $query .= ' AND (`title` LIKE "%'.  mysql_real_escape_string($_POST["request"]).'%" OR `text` LIKE "%'.  mysql_real_escape_string($_POST["request"]).'%") ';
}
if(!empty($_SESSION["details"])){
    foreach($_SESSION["details"] as $key=>$value){
        if($key != "details" && $key != "category"){
            $requery = 'SELECT * FROM `nodes_properties` WHERE `id` = "'.$key.'"';
            $r = engine::mysql($requery);
            $d = mysql_fetch_array($r);
            if($value>0){
                $query .= ' AND `properties` LIKE "%['.$key.','.$value.']%"';
            }
        }
    }
}
if(!empty($_GET[1])){
    $requery = 'SELECT * FROM `nodes_category` WHERE `url` LIKE "'. mysql_real_escape_string(strtolower($_GET[1])).'"';
    $r = engine::mysql($requery);
    $d = mysql_fetch_array($r);
    if(!empty($d)){
        $this->title = $d["value"].' - '.$this->title;
        $this->content .= ' › '.$d["value"];
        $query .= ' AND `properties` LIKE "%[1,'.$d["id"].']%"';
    }else{
        $this->content = engine::error();
        return;
    }
}else{
    $this->title = lang("All Items").' - '.$this->title;
    if(!empty($_POST["request"])){
        $this->content .= ' › '.lang("Search").' “'.$_POST["request"].'”';
    }
}
$this->content .= '</div>
    <div class="mobile_filter">'.print_product_filter(2, $_GET[1]).'</div>';

$requery = str_replace('*', 'count(*)', $query);
$query .= ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
$res = engine::mysql($query);
$r = engine::mysql($requery);
$d = mysql_fetch_array($r);
$count = $d[0];
$from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
$to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
$arr_count = 0;
$table = '<div class="product_list">';
require_once('engine/include/print_product_preview.php');
while($data = mysql_fetch_array($res)){
    $arr_count++;
    $table .= print_product_preview($data);
}$table .= '</div>';
if($arr_count){
$this->content .= $table.' <div style="clear:both;"></div>
<form method="POST"  id="query_form"  onSubmit="submit_search();">
<input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
<input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
<input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
<input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
<input type="hidden" name="reset" id="query_reset" value="0" />
<div class="total-entry">';
if($to > $count) $to = $count;
if($count>0){
    $this->content .= '<p style="padding: 5px;">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
        <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
         <option'; if($_SESSION["count"]=="20") $this->content .= ' selected'; $this->content .= '>20</option>
         <option'; if($_SESSION["count"]=="50") $this->content .= ' selected'; $this->content .= '>50</option>
         <option'; if($_SESSION["count"]=="100") $this->content .= ' selected'; $this->content .= '>100</option>
        </select> '.lang("per page").'.</nobr></p>';
}$this->content .= '
</div><div style="clear:right;"></div>';
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
     }$this->content .= '<div style="clear:both;"></div></form>';
}else{
    $this->content .= '<div style="padding-top: 70px; padding-bottom: 70px;">'.lang("Products not found").'</div>';
}
$this->content .= '<div style="clear:both;"></div>'
. '</div>'
. '</section>';