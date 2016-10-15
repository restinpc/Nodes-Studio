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

// TODO - Your code here
//----------------------------

if(empty($_GET[0])){
    $this->content = engine::error();
    return; 
}

$query = 'SELECT * FROM `nodes_catalog` WHERE `visible` = 1 AND `lang` = "'.$_SESSION["Lang"].'"';
$res = engine::mysql($query);
if($_GET[0]=="content"){
    $this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/content"><b>'.lang("Content").'</b></a>
    ';
}
if($_GET[0]!="content"){ 
    $link = $_GET[0];
    if(!empty($_GET[1])){
        $this->content = engine::error();
        return;
    }
}else{
    $link = $_GET[1];
    if(!empty($_GET[2])){
        $this->content = engine::error();
        return;
    }
}

if(!empty($_POST["from"])) $_SESSION["from"] = $_POST["from"];
if(!empty($_POST["to"])) $_SESSION["to"] = $_POST["to"];
if(!empty($_POST["count"])) $_SESSION["count"] = intval($_POST["count"]);
if(!empty($_POST["page"])) $_SESSION["page"] = intval($_POST["page"]);
if(!empty($_POST["method"])) $_SESSION["method"] = $_POST["method"];
if(!empty($_POST["order"])) $_SESSION["order"] = $_POST["order"];
if($_SESSION["order"]=="id") $_SESSION["order"] = "date";

$arr_count = 0;    
$from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
$to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];

if($_GET[0]!="content" || (!empty($_GET[1]) && $_GET[0]=="content")){            

    // print catalog
    $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$link.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query); 
    $data = mysql_fetch_array($res);
    if(!empty($data)){
        $this->title = $data["caption"].' - '.$this->title;
        $this->description = strip_tags($data["text"]);
        if(!empty($data["img"])) $this->img = $_SERVER["DIR"]."/img/data/big/".$data["img"];

        $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"'
                . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
        $requery = 'SELECT COUNT(*) FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';

        $res = engine::mysql($query);
        if(!$data["visible"]) $this->menu = '';
        $this->content .= '<h1>'.$data["caption"].'</h1><br/>'
        . '<br/>';
        //  print articles
        while($d = mysql_fetch_array($res)){
            $arr_count++;
            require_once ("engine/include/print_preview.php");
            $table .= print_preview($d);
        }

        if($arr_count){
                $this->content .= $table.'
            <form method="POST"  id="query_form"  onSubmit="submit_search();">
            <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
            <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
            <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
            <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
            <input type="hidden" name="reset" id="query_reset" value="0" />

            <div class="total-entry">';
            $res = engine::mysql($requery);
            $data = mysql_fetch_array($res);
            $count = $data[0];
            if($to > $count) $to = $count;
            if($data[0]>0){
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
                        $this->content .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="en" href="#">'.lang("Previous").'</a></span>';
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
                               $this->content .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="en" href="#">'.$i.'</a></li>';
                           }
                       }else if((!$c||!$b) && !$f && $i<$pages){
                           $f = 1; $e = 0;
                           if(!$b) $b = 1;
                           else if(!$c) $c = 1;
                           $this->content .= '<li class="dots">. . .</li>';
                       }
                   }if($_SESSION["page"]<$pages){
                       $this->content .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="en" href="#">'.lang("Next").'</a></li>';
                   }$this->content .= '
             </ul>
            </div>';
            }$this->content .= '<div style="clear:both;"></div></form>';
            if($_SESSION["user"]["id"]=="1"){
                $this->content .= '<br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'"><input type="button" class="btn" style="width: 280px;" value="'.lang("Add article").'" /></a>'
                        . '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'&act=edit"><input type="button" class="btn" style="width: 280px;" value="Edit directory" /></a><br/><br/>';
            }  
        }else{
        //  print catalog
            require_once ("engine/include/print_catalog.php");
            $this->content .= print_catalog($data);
            $this->content .= '<div style="clear:both;"></div>';
            if($_SESSION["user"]["id"]=="1"){
                $this->content .= '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'"><input type="button" class="btn" style="width: 280px;" value="'.lang("Add article").'" /></a>'
                        . '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'&act=edit"><input type="button" class="btn" style="width: 280px;" value="'.lang("Edit directory").'" /></a>';
            }
        }
    }else{
        $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$link.'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query); 
        $data = mysql_fetch_array($res);
        if(empty($data)){
            $query = 'UPDATE `nodes_catch` SET `interval` = "-2" WHERE `url` = "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
            engine::mysql($query);
            engine::error();
            exit();
        }else{
            require_once ("engine/include/print_content.php");
            $this->title = $data["caption"].' - '.$this->title;
            $this->description = strip_tags($data["text"]);
            if(!empty($data["img"])){
                $this->img = $_SERVER["DIR"]."/img/data/big/".$data["img"];
            }
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$data["cat_id"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);

            $this->content .= '<h1>'.$data["caption"].'</h1><br/>';
            $this->content .= print_content($data);

            if($_SESSION["user"]["id"]=="1"){
                $this->content .= '<a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["cat_id"].'&id='.$data["id"].'&act=edit"><input type="button" class="btn" style="width: 280px;" value="Edit article" /></a>';
            }
            $this->content .= '<div style="clear:both;"></div>';
            
            $fout .= '<br/><div style="text-align:left;"><h6>'.lang("You might also be interested in").':</h6><br/></div>
                <div class="preview_blocks">';
            $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$data["cat_id"].'" AND `id` <> "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND()';
            $res = engine::mysql($query); 
            require_once ("engine/include/print_preview.php");
            $count = 0;
            $urls = array();
            while($d = mysql_fetch_array($res)){
                if($count>2) break;
                $count++;
                $fout .= print_preview($d);
                array_push($urls, $d["id"]);
            }
            if($count<3){
                $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` <> "'.$data["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND()';
                $res = engine::mysql($query); 
                while($d = mysql_fetch_array($res)){
                    if(!in_array($d["id"], $urls)){
                        if($count>2) break;
                        $count++;
                        $fout .= print_preview($d);
                        array_push($urls, $d["id"]);
                    }
                }
            }
            $query = 'SELECT * FROM `nodes_products` WHERE `status` = "1" ORDER BY RAND() LIMIT 0, '.(6-$count);
            $res = engine::mysql($query);
            require_once('engine/include/print_product_preview.php');
            while($d = mysql_fetch_array($res)){
                $count++;
                $fout .= print_product_preview($d);
            }
            $fout .= '</div>
                <div style="clear:both;"></div>
                ';
            if($count) $this->content .= $fout;
        }
    }
}else{
    require_once ("engine/include/print_preview.php");
    $this->title = lang("Content").' - '.$this->title;
    $this->content .= '<h1>'.lang("Content").'</h1><br/><br/>';

    $query = 'SELECT * FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'"'
            . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*) FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'"';

    $table = '<div class="preview_blocks">';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        $arr_count++;
        $table .=  print_preview($data);
    }
    $table .= '</div><div style="clear:both;"></div>';
    if($arr_count){
        $this->content .= $table.'
        <form method="POST"  id="query_form"  onSubmit="submit_search();">
        <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
        <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
        <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
        <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
        <input type="hidden" name="reset" id="query_reset" value="0" />

        <div class="total-entry">';
        $res = engine::mysql($requery);
        $data = mysql_fetch_array($res);
        $count = $data[0];
        if($to > $count) $to = $count;
        if($data[0]>0){
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
                    $this->content .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="en" href="#">'.lang("Previous").'</a></span>';
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
                           $this->content .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="en" href="#">'.$i.'</a></li>';
                       }
                   }else if((!$c||!$b) && !$f && $i<$pages){
                       $f = 1; $e = 0;
                       if(!$b) $b = 1;
                       else if(!$c) $c = 1;
                       $this->content .= '<li class="dots">. . .</li>';
                   }
               }if($_SESSION["page"]<$pages){
                   $this->content .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="en" href="#">'.lang("Next").'</a></li>';
               }$this->content .= '
         </ul>
        </div>';
        }$this->content .= '<div style="clear:both;"></div></form>';
    }else{
        $this->content = '<div style="padding-top: 70px; padding-bottom: 70px;">'.lang("No articles found").'</div>';
    }
}

