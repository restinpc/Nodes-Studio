<?php
/**
* Print admin language page.
* @path /engine/core/admin/print_admin_language.php
* 
* @name    Nodes Studio    @version 2.0.8
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
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
* @usage <code> engine::print_admin_language($cms); </code>
*/
function print_admin_language($cms){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
        . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "language" '
        . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
        . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysql_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    if(empty($_GET["l"])) $_GET["l"] = 'en';
    if(!empty($_POST)){
        $name = trim(str_replace('"', '\"', $_POST["name"]));
        if(!empty($name)){
            if($admin_access != 2){
                engine::error(401);
                return;
            }
            $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$name.'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(empty($data)){
                $query = 'INSERT INTO `nodes_language`(name, lang, value) VALUES("'.$name.'", "en", "'.$name.'")';
                engine::mysql($query);
            }
        }else if(!empty($_POST["delete"])){
            if($admin_access != 2){
                engine::error(401);
                return;
            }
            $query = 'DELETE FROM `nodes_language` WHERE `name` LIKE "'. base64_decode($_POST["delete"]).'"';
            engine::mysql($query);
        }else if($_POST["language"]=="1"){
            if($admin_access != 2){
                engine::error(401);
                return;
            }
            foreach($_POST as $id=>$value){
                if($id=="language") continue;
                $id = base64_decode($id);
                $id = trim($id);
                if(!empty($id)&&
                        $id!="jQuery"&&
                        $id!="cache"&&
                        $id!="nocache"&&
                        $id!="lang"&&
                        $id!="language"&&
                        $id!=""){
                    $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE  "'.$id.'" AND `lang` = "'.$_GET["l"].'"';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                    if(!empty($data)){
                        $query = 'UPDATE `nodes_language` SET `value` = "'.$value.'" WHERE `name` LIKE  "'.$id.'" AND `lang` = "'.$_GET["l"].'"';
                        engine::mysql($query);
                    }else{
                        $query = 'INSERT INTO `nodes_language`(name, lang, value) VALUES("'.$id.'", "'.$_GET["l"].'", "'.$value.'")';
                        engine::mysql($query);  
                    }
                }
            }
        }
    }
    $fout = '<div class="document980">'.
            lang("Select your language").': <select class="input" onChange="window.location = \''.$_SERVER["DIR"].'/admin/?mode=language&l=\'+this.value;">';
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $arr = explode(";", $data["value"]);
    foreach($arr as $value){
        if(!empty($value)){
            if(!empty($_GET["l"])&&$_GET["l"]==$value){
                $fout .= '<option value="'.$value.'" selected>'.$value.'</option>';
            }else{
                $fout .= '<option value="'.$value.'">'.$value.'</option>';
            }
        }
    }$fout .= '</select><br/><br/>';
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_language` WHERE `lang` = "en"'. 
            ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];;
    $requery = 'SELECT COUNT(*) FROM `nodes_language` WHERE `lang` = "en"';
    $fout .= '<form method="POST" id="new_form"><input type="hidden" name="name" value="" id="new_value" /></form>'
            . '<form method="POST" id="delete_form"><input type="hidden" name="delete" value="" id="delete_value" /></form>'
            . '<form method="POST"><input type="hidden" name="language" value="1" />';
    $table = '
        <div class="table">
        <table id="table" class="mw100p">
        <thead>
        <tr>';
            $array = array(
                "name" => "Name",
                "value" => "Value"
            ); foreach($array as $order=>$value){
                $table .= '<th width=50%>';
                $table .= lang($value);
                $table .= '</th>';
            } $table .= '
        </tr>
        </thead>
        <tbody>';
    $res = engine::mysql($query);
    while($data=  mysql_fetch_array($res)){
        $arr_count++;
        $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$data["name"].'" AND `lang` = "'.$_GET["l"].'"';
        $r = engine::mysql($query);
        $d = mysql_fetch_array($r);

        $table .= '<tr><td width=50% align=left>'.$data["name"].'</td>';
        if($_GET["l"]=="en" && $admin_access == 2){
            $table .= '<td width=50% align=left><input name="'.  base64_encode($data["name"]).'" type="text" value="'.$data["value"].'" class="input w100p" />'
                . '</td><td width=20><div class="close_image"'
                . 'onClick=\'if(confirm("'.lang("Delete").' \"'.$data["name"].'\"?")){document.getElementById("delete_value").value="'.base64_encode($data["name"]).'";'
                . 'document.getElementById("delete_form").submit();}\''
                . '> </div></td>';
        }else{
            $table .= '<td width=50% align=left colspan=2><input name="'.base64_encode($data["name"]).'" '.($admin_access!=2?'disabled':'').' type="text" value="'.$d["value"].'" class="input w270" /></td>'; 
        }
        $table .= '</tr>';
    }
$table .= '</table>
</div>';

if($admin_access == 2){
    $table .= '
    <br/>
    <input type="submit" class="btn w280" value="'.lang("Save changes").'" />';
}

        $table .= '
</form><br/>';
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
    $data = mysql_fetch_array($res);
    $count = $data[0];
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
         }$fout .= '</form><div class="clear"></div>
             </div>
             ';
    }else{
        $fout = '<div class="clear_block">'.lang("Data not found").'</div>';
    }
    if($_GET["l"]=="en" && $admin_access == 2){
        $fout .= '<br/><input type="button" class="btn w280" '
                . 'onClick=\'result = prompt("New value", ""); if(result != ""){document.getElementById("new_value").value=result;'
                . 'document.getElementById("new_form").submit();}\' value="'.lang("Add new value").'" /><br/>';
    }
    return $fout;
}

