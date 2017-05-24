<?php
/**
* Print admin content page.
* @path /engine/core/admin/print_admin_content.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
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
* @usage <code> engine::print_admin_content($cms); </code>
*/
function print_admin_content($cms){
    if($_SESSION["order"]=="id") $_SESSION["order"] = "caption";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $cms->onload = ' tinymce_init(); ';
    if(!empty($_POST["caption"])&&!empty($_POST["text"]) ){
        $caption = trim(htmlspecialchars($_POST["caption"]));
        $text = trim(mysql_real_escape_string($_POST["text"]));
        $img = $_POST["file1"];
        $visible = $_POST["visible"];
        if(empty($img)) $img = $_POST["file"];
        if(!empty($_POST["url"]))
            $url = trim(htmlspecialchars($_POST["url"]));
        else
            $url = engine::url_translit(engine::strtolower_utf8($caption));
        if(!empty($_GET["id"])){
            // checking url before updating content
            $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$url.'" AND `id` <> "'.$_GET["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $i = 0;
            while(!empty($d)){
                $newurl = $url."-".(++$i);
                $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$newurl.'" AND `id` <> "'.$_GET["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                if(empty($d)){
                    $url = $newurl;
                }
            }
            if($_POST["noimg"]){
                $query = 'UPDATE `nodes_content` SET `img` = "" WHERE `id` = "'.$_GET["id"].'"';
                engine::mysql($query); 
            }
            $query = 'UPDATE `nodes_content` SET `caption` = "'.$caption.'", `text` = "'.$text.'", `url` = "'.$url.'", `date` = "'.date("U").'" WHERE `id` = "'.$_GET["id"].'"';  
            engine::mysql($query);
            if(!empty($img)){
                $query = 'UPDATE `nodes_content` SET `img` = "'.$img.'" WHERE `id` = "'.$_GET["id"].'"';
                engine::mysql($query);  
            }
        }else if(!empty($_GET["cat_id"])){
            if($_GET["act"] == "edit"){
                // checking url before updating catalog
                $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$url.'" AND `id` <> "'.$_GET["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                $i = 0;
                while(!empty($d)){
                    $newurl = $url."-".(++$i);
                    $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$newurl.'" AND `id` <> "'.$_GET["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
                    $r = engine::mysql($query);
                    $d = mysql_fetch_array($r);
                    if(empty($d)){
                        $url = $newurl;
                    }
                }

                $query = 'UPDATE `nodes_catalog` SET `caption` = "'.$caption.'", `text` = "'.$text.'", `url` = "'.$url.'", `visible` = "'.$visible.'" WHERE `id` = "'.$_GET["cat_id"].'"';
                engine::mysql($query);
                if($_POST["noimg"]){
                    $query = 'UPDATE `nodes_catalog` SET `img` = "" WHERE `id` = "'.$_GET["cat_id"].'"';
                    engine::mysql($query); 
                }
                if(!empty($img)){
                    $query = 'UPDATE `nodes_catalog` SET `img` = "'.$img.'" WHERE `id` = "'.$_GET["cat_id"].'"';
                    engine::mysql($query); 
                }
            }else{
                // checking url before adding content
                $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$url.'"';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                $i = 0;
                while(!empty($d)){
                    $newurl = $url."-".(++$i);
                    $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$newurl.'"';
                    $r = engine::mysql($query);
                    $d = mysql_fetch_array($r);
                    if(empty($d)){
                        $url = $newurl;
                    }
                }

                $query = 'INSERT INTO `nodes_content`(cat_id, url, lang, caption, text, img, date, public_date) '
                        . 'VALUES("'.$_GET["cat_id"].'", "'.$url.'", "'.$_SESSION["Lang"].'", "'.$caption.'", "'.$text.'", "'.$img.'", "'.date("U").'", "'.date("U").'")';
                engine::mysql($query);
                $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$_GET["cat_id"].'&act=list";</script>';
                return $fout;
            }
        }else{
            // checking url before adding catalog
            $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$url.'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $i = 0;
            while(!empty($d)){
                $newurl = $url."-".(++$i);
                $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$newurl.'"';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                if(empty($d)){
                    $url = $newurl;
                }
            }$visible = $_POST["visible"];
            if(!empty($img)){
                $query = 'INSERT INTO `nodes_catalog`(caption, text, url, lang, img, visible, date, public_date) VALUES("'.$caption.'", "'.$text.'", "'.$url.'", "'.$_SESSION["Lang"].'", "'.$img.'", "'.$visible.'", "'.date("U").'", "'.date("U").'")';
            }else{
                $query = 'INSERT INTO `nodes_catalog`(caption, text, url, lang, visible, date, public_date) VALUES("'.$caption.'", "'.$text.'", "'.$url.'", "'.$_SESSION["Lang"].'", "'.$visible.'", "'.date("U").'", "'.date("U").'")';
            }engine::mysql($query);
        }
    }
    $fout .= '<div class="document640">
        <form method="POST" action="'.$_SERVER["DIR"].'/admin/?mode=content" id="admin_lang_select">'.lang("Select your language").': 
        <select class="input" name="lang" onChange=\'document.getElementById("admin_lang_select").submit();\'>';
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $arr = explode(";", $data["value"]);
    foreach($arr as $value){
        if(!empty($value)){
            if(!empty($_SESSION["Lang"])&&$_SESSION["Lang"]==$value){
                $fout .= '<option value="'.$value.'" selected>'.$value.'</option>';
            }else{
                $fout .= '<option value="'.$value.'">'.$value.'</option>';
            }
        }
    }$fout .= '</select></form><br/>';
    if(!empty($_GET["id"])){
        if($_GET["act"] == "remove"){
            $query = 'DELETE FROM `nodes_content` WHERE `id` = "'.$_GET["id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>';
            return $fout;
        }else if($_GET["act"] == "up"){
            $query = 'SELECT MAX(`order`) FROM `nodes_content`';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $order = $data[0]+1;
            $query = 'UPDATE `nodes_content` SET `order` = "'.$order.'" WHERE `id` = "'.$_GET["id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.intval($_GET["cat_id"]).'&act=list";</script>';
            return $fout; 
        }else if($_GET["act"] == "down"){
            $query = 'SELECT MIN(`order`) FROM `nodes_content`';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $order = $data[0]-1;
            $query = 'UPDATE `nodes_content` SET `order` = "'.$order.'" WHERE `id` = "'.$_GET["id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.intval($_GET["cat_id"]).'&act=list";</script>';
            return $fout;
        }else if($_GET["act"] == "copy" && !empty($_GET["target"])){
            $query = 'SELECT `url` FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(!empty($data)){
                $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$data["url"].'" AND `lang` = "'.$_GET["target"].'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(!empty($data)){
                    $cat_id = $data["id"];
                    $query = 'SELECT * FROM `nodes_content` WHERE `id` = "'.$_GET["id"].'"';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                    $query = 'SELECT `id` FROM `nodes_content` WHERE `url` = "'.$data["url"].'" AND `lang` = "'.$_GET["target"].'"';
                    $res = engine::mysql($query);
                    $d = mysql_fetch_array($res);
                    if(!empty($d)){
                        die('<script>alert("'.lang("This article already exist in").' '.$_GET["target"].' '.lang("translation").'"); window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>');
                    }else{
                        $query = 'INSERT INTO `nodes_content`(cat_id, url, lang, caption, text, img, date, public_date) '
                                . 'VALUES("'.$cat_id.'", "'.$data["url"].'", "'.$_GET["target"].'", "'.$data["caption"].'", "'.$data["text"].'", "'.$data["img"].'", "'.date("U").'", "'.date("U").'")';
                        engine::mysql($query);
                        die('<script>window.location = "'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$cat_id.'&act=list&lang='.$_GET["target"].'";</script>');
                    }
                }else{
                    die('<script>alert("'.lang("This catalog is not exist in").' '.$_GET["target"].' '.lang("translation").'"); window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>');
                }
            }
        }else{
            $query = 'SELECT * FROM `nodes_content` WHERE `id` = "'.$_GET["id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $cms->title = $data["caption"];
            $fout .= '<form method="POST" id="edit_form"  ENCTYPE="multipart/form-data">
            <h2 class="fs21">'.lang("Edit article").'</h2><br/>
            <input type="text" required class="input w600" name="caption" placeHolder="'.lang("Caption").'" value="'.$data["caption"].'" /><br/><br/>
            <input type="text" class="input w600" name="url" placeHolder="URL" value="'.$data["url"].'" /><br/><br/>';
            if(!empty($data["img"])){
                $fout .= '<div id="delete_image_block"><img src="'.$_SERVER["DIR"].'/img/data/thumb/'.$data["img"].'" /><br/><br/>'
                        . '<input type="hidden" id="noimg" name="noimg" value="0" />'
                        . '<input type="button" onClick=\'  document.getElementById("noimg").value="1"; '
                        . '                                 document.getElementById("edit_form").submit();\' '
                        . 'class="btn w280 mb3" value="'.lang("Delete image").'" /><br/></div>';
            }
            for($i = 1; $i<2; $i++){
                $fout .= '<div class="new_photo" id="new_photo_'.$i.'" title="none">
                <input type="hidden" name="file'.$i.'" id="file'.$i.'" value="" />
                </div>';
            }$fout .= '
            <div class="clear"><br/></div>
            <input type="button" id="upload_btn" value="'.lang("Upload new image").'" class="btn w280"  onClick=\'show_photo_editor(0,0);\' /><br/><br/>
            <div class="w600">
            <textarea class="input w600" id="editable" name="text" >'.$data["text"].'</textarea>
            </div>
            <div class="clear"><br/></div>';
            if(!empty($_POST["comment"])){
                $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
                $text = str_replace("\n", "<br/>", $text);
                $query = 'SELECT * FROM `nodes_comment` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$data["url"].'" AND `user_id` = "1"';
                $res = engine::mysql($query);
                $d = mysql_fetch_array($res);
                if(empty($d)){
                    $query = 'INSERT INTO `nodes_comment` (`url`, `reply`, `user_id`, `text`, `date`) '
                    . 'VALUES("'.$data["url"].'", "'.intval($_POST["reply"]).'", "1", "'.$text.'", "'.date("U").'")';
                    engine::mysql($query);                  
                }
            }
            if(!empty($_POST["delete_comment"])){
               $query = 'DELETE FROM `nodes_comment` WHERE `id` = "'.$_POST["delete_comment"].'"';
               engine::mysql($query);
            }
            /*
            $query = 'SELECT * FROM `nodes_comment` WHERE `url` = "/'.$data["url"].'"';
            $res = engine::mysql($query);
            while($d = mysql_fetch_array($res)){
                if(intval($d["id"])>0){
                    $fout .= '<center><table width=100% align=center border=0 class="table comments">';
                    $fout .= engine::print_comment($d["id"]);
                    $fout .= '</table></center><br/><br/>';
                }
            }
             * 
             */
            $fout .= '<input type="submit" class="btn w280" value="'.lang("Save changes").'" /><br/><br/>
            <a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$_GET["cat_id"].'&act=list"><input type="submit" class="btn w280" value="'.lang("Back to list").'" /></a><br/>
            </form>';
        }
    }else if(!empty($_GET["cat_id"])){
        if($_GET["act"] == "remove"){
            $query = 'DELETE FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>';
            return $fout; 
        }else if($_GET["act"] == "up"){
            $query = 'SELECT MAX(`order`) FROM `nodes_catalog`';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $order = $data[0]+1;
            $query = 'UPDATE `nodes_catalog` SET `order` = "'.$order.'" WHERE `id` = "'.$_GET["cat_id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>';
            return $fout; 
        }else if($_GET["act"] == "down"){
            $query = 'SELECT MIN(`order`) FROM `nodes_catalog`';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $order = $data[0]-1;
            $query = 'UPDATE `nodes_catalog` SET `order` = "'.$order.'" WHERE `id` = "'.$_GET["cat_id"].'"';
            engine::mysql($query);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>';
            return $fout;
        }else if($_GET["act"] == "edit"){
            $fout .= '<form method="POST" id="edit_form"  ENCTYPE="multipart/form-data">';
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $cms->title = $data["caption"];
            $fout .= '<h2 class="fs21">'.lang("Edit directory").'</h2><br/>
            <input required type="text" name="caption" title="'.lang("Caption").'" placeHolder="'.lang("Caption").'" class="input w600" value="'.$data["caption"].'" /><br/><br/>
            <input type="text" name="url" class="input w600" value="'.$data["url"].'" title="URL" placeHolder="URL" /><br/><br/>
            '.lang("Show in navigation").' <select name="visible" class="input">';
            if($data["visible"]){
                $fout .= '<option value="0">'.lang("No").'</option><option value="1" selected>'.lang("Yes").'</option>';
            }else{
                $fout .= '<option value="0" selected>'.lang("No").'</option><option value="1">'.lang("Yes").'</option>';
            }
            $fout .= '</select><br/><br/>';
            if(!empty($data["img"])){
                $fout .= '<div id="delete_image_block"><img src="'.$_SERVER["DIR"].'/img/data/thumb/'.$data["img"].'" /><br/><br/>'
                . '<input type="hidden" id="noimg" name="noimg" value="0" />'
                . '<input type="button" onClick=\'  document.getElementById("noimg").value="1"; '
                . '                                 document.getElementById("edit_form").submit();\' '
                . 'class="btn w280 mb3" value="'.lang("Delete image").'" /><br/></div>';
            }
            for($i = 1; $i<2; $i++){
                $fout .= '<div class="new_photo" id="new_photo_'.$i.'" title="none">
                <input type="hidden" name="file'.$i.'" id="file'.$i.'" value="" />
                </div>';
            }$fout .= '
                <div class="clear"><br/></div>
                <input type="button" id="upload_btn" value="'.lang("Upload new image").'" class="btn w280"  onClick=\'show_photo_editor(0,0);\' /><br/><br/>
            <div class="w600">
            <textarea class="input w600" id="editable" name="text">'.$data["text"].'</textarea>
            </div><br/>
            <input type="submit" class="btn w280" value="'.lang("Save changes").'" />
            </form>';
            if(!empty($_POST["comment"])){
                $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
                $text = str_replace("\n", "<br/>", $text);
                $query = 'SELECT * FROM `nodes_comment` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$url.'" AND `user_id` = "1"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(empty($data)){
                    $query = 'INSERT INTO `nodes_comment` (`url`, `reply`, `user_id`, `text`, `date`) '
                    . 'VALUES("'.$url.'", "'.intval($_POST["reply"]).'", "1", "'.$text.'", "'.date("U").'")';
                    engine::mysql($query);                  
                }
            }
            /*
            $flag = 0;
            $query = 'SELECT * FROM `nodes_comment` WHERE `url` = "'.$url.'"';
            $res = engine::mysql($query);
            while($data = mysql_fetch_array($res)){
                if(intval($data["id"])>0){
                    $fout .= '<center><table width=100% align=center border=0 class="table comments">';
                    $fout .= engine::print_comment($data["id"]);
                    $fout .= '</table></center>';
                    $flag = 1;
                }
            }
             * 
             */
        }else if($_GET["act"] == "list"){
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $cms->title = $data["caption"];
            $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$_GET["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'"'
                . ' ORDER BY `order` DESC LIMIT '.($from-1).', '.$_SESSION["count"];
            $requery = 'SELECT COUNT(*) FROM `nodes_content` WHERE `cat_id` = "'.$_GET["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
            $table = '<div class="table">
            <table id="table" class="w100p">
            <tr>
                <th align=center>'.lang("Name").'</th>
                <th align=center>'.lang("Date").'</th>
                <th align=center>'.lang("Action").'</th>
            </tr>';
            $res = engine::mysql($query);
            while($data = mysql_fetch_array($res)){
                $arr_count++;
                $table .= '<tr><td align=left><a href="'.$_SERVER["DIR"].'/content/'.$data["url"].'" target="_blank">'.$data["caption"].'</a></td>';
                $table .= '<td align=left>'.date("d/m/Y H:i", $data["date"]).'</td>';
                $table .= '<td align=left><select class="input" onChange=\'if(confirm("'.lang("Are you sure?").'")){window.location=this.value;}else{this.selectedIndex=0;}\'>
                    <option disabled selected>'.lang("Select an action").'</option>
                    <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'&id='.$data["id"].'&act=edit">'.lang("Edit article").'</option>';
                $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
                $rr = engine::mysql($query);
                $dd = mysql_fetch_array($rr);
                $arr = explode(";", $dd["value"]);
                foreach($arr as $value){
                    if(!empty($value)){
                        if($_SESSION["Lang"]!=$value){
                            $table .= '<option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'&id='.$data["id"].'&act=copy&target='.$value.'">'.lang("Copy to").' '.$value.' '.lang("translation").'</option>';
                        }
                    }
                }
                $table .= '<option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'&id='.$data["id"].'&act=up">'.lang("Up to top").'</option>
                    <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'&id='.$data["id"].'&act=down">'.lang("Down to bottom").'</option>
                    <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'&id='.$data["id"].'&act=remove">'.lang("Delete article").'</option>
                    </select></td></tr>';
            }$table .= '</table></div>';
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
                }$fout .= '</div><div class="cr"></div>';
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
                    }$fout .= '</ul>
                    </div>';
                 }$fout .= '</form>
                     <div class="clear"></div>';
            }else{
                $fout .= '<div class="clear_block">'.lang("There is no articles").'</div>';
            }$fout .= '<br/><a href="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$_GET["cat_id"].'">'
                . '<input type="button" class="btn w280" value="'.lang("Add new article").'" /></a><br/>';
        }else if($_GET["act"] == "copy" && !empty($_GET["target"])){
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $query = 'SELECT `id` FROM `nodes_catalog` WHERE `url` = "'.$data["url"].'" AND `lang` = "'.$_GET["target"].'"';
            $res = engine::mysql($query);
            $d = mysql_fetch_array($res);
            if(!empty($d)){
                die('<script>alert("'.lang("This catalog already exist in").' '.$_GET["target"].' '.lang("translation").'"); window.location = "'.$_SERVER["DIR"].'/admin/?mode=content";</script>');
            }else{
                $query = 'INSERT INTO `nodes_catalog`(caption, text, url, img, visible, lang, date, public_date) '
                        . 'VALUES("'.$data["caption"].'", "'.$data["text"].'", "'.$data["url"].'", "'.$data["img"].'", "'.$data["visible"].'", "'.$_GET["target"].'". "'.date("U").'", "'.date("U").'")';
                engine::mysql($query);
                die('<script>window.location = "'.$_SERVER["DIR"].'/admin/?mode=content&lang='.$_GET["target"].'";</script>');
            }
        }else{
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$_GET["cat_id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $cms->title = $data["caption"];
            $fout .= '<div class="document">
                <form method="POST"  ENCTYPE="multipart/form-data">
                <h2 class="fs21">'.lang("Add new article").'</h2><br/>
                <center>
                <input type="text" class="input w600" name="caption" required placeHolder="'.lang("Caption").'" value="'.$_POST["caption"].'" /><br/><br/>
                <input type="text" class="input w600" name="url" placeHolder="URL" value="'.$_POST["url"].'" /><br/><br/>';
            for($i = 1; $i<2; $i++){
                $fout .= '<div class="new_photo" id="new_photo_'.$i.'" title="none">
                <input type="hidden" name="file'.$i.'" id="file'.$i.'" value="" />
                </div>';
            }$fout .= '
                <div class="clear"><br/></div>
                <input type="button" id="upload_btn" value="'.lang("Upload new image").'" class="btn w280"  onClick=\'show_photo_editor(0,0);\' /><br/><br/>
                <div class="w600">
                <textarea class="input w600" id="editable" name="text">'.$_POST["text"].'</textarea>
                </div><br/><br/>
                <input type="submit" class="btn w280" value="'.lang("Submit").'" /><br/><br/>
                <a href="'.$_SERVER["DIR"].'/admin/?mode=content"><input type="submit" class="btn w280" value="'.lang("Back to content").'" /></a>
                <br/></center>
                </form>
            </div>';
        }
    }else{
        // print base directory
        $query = 'SELECT * FROM `nodes_catalog` WHERE `lang` = "'.$_SESSION["Lang"].'"'
                . ' ORDER BY `order` DESC LIMIT '.($from-1).', '.$_SESSION["count"];
        $requery = 'SELECT COUNT(*) FROM `nodes_catalog` WHERE `lang` = "'.$_SESSION["Lang"].'"';
        $table = '
            <div class="table">
            <table id="table" class="w100p">
            <thead>
            <tr>
                <th>'.lang("Name").'</th>
                <th>'.lang("Articles").'</th>
                <th>'.lang("Action").'</th>
            </tr>
            </thead>';
        $res = engine::mysql($query);
        while($data = mysql_fetch_array($res)){
            $query = 'SELECT COUNT(`id`) FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $arr_count++;
            $table .= '<tr><td align=left><a href="'.$_SERVER["DIR"].'/content/'.$data["url"].'" target="_blank">'.$data["caption"].'</a></td>
            <td align=left>'.$d[0].'</td>
            <td align=left>
            <select class="input" onChange=\'if(confirm("'.lang("Are you sure?").'")){window.location=this.value;}\'>
                <option disabled selected>'.lang("Select an action").'</option>
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=list">'.lang("List articles").'</option>
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'">'.lang("Add article").'</option>';
                $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
                $rr = engine::mysql($query);
                $dd = mysql_fetch_array($rr);
                $arr = explode(";", $dd["value"]);
                foreach($arr as $value){
                    if(!empty($value)){
                        if($_SESSION["Lang"]!=$value){
                            $table .= '<option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=copy&target='.$value.'">'.lang("Copy to").' '.$value.' '.lang("translation").'</option>';
                        }
                    }
                }
                $table .= '
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=edit">'.lang("Edit catalog").'</option>
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=up">'.lang("Up to top").'</option>
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=down">'.lang("Down to bottom").'</option>
                <option value="'.$_SERVER["DIR"].'/admin/?mode='.$_GET["mode"].'&cat_id='.$data["id"].'&act=remove">'.lang("Delete catalog").'</option>
            </select>
            </td></tr>';
                    }$table .= '
            </table></div><br/>';
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
            }$fout .= '</div><div class="cr"></div>';
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
                }$fout .= '</ul>
                </div>
                ';
             }$fout .= '
                </form><div class="clear"></div>';
        }else{
            $fout .= '<div class="clear_block">'.lang("Content not found").'</div>';
        }
        $fout .= '<br/>
        <input type="button" onClick=\'document.getElementById("new_directory").style.display="block"; this.style.display="none";\' value="'.lang("Add a new directory").'" class="btn w280" />
        <div id="new_directory" class="hidden document" >
        <form method="POST"  ENCTYPE="multipart/form-data">
        <center>
        <h2 class="fs21">'.lang("Add a new directory").'</h2><br/>
        <input required type="text" class="input w600" name="caption" placeHolder="'.lang("Caption").'" /><br/><br/>
        <input type="text" class="input w600" name="url" placeHolder="URL" /><br/><br/>
        '.lang("Show in navigation").' <select name="visible" class="input">'
        . '<option value="0">'.lang("No").'</option>'
        . '<option value="1" selected>'.lang("Yes").'</option>'
        . '</select><br/><br/>';
        
        for($i = 1; $i<2; $i++){
            $fout .= '<div class="new_photo" id="new_photo_'.$i.'" title="none">
            <input type="hidden" name="file'.$i.'" id="file'.$i.'" value="" />
            </div>';
        }$fout .= '
            <div class="clear"><br/></div>
        <input type="button" id="upload_btn" value="'.lang("Upload new image").'" class="btn w280"  onClick=\'show_photo_editor(0,0);\' /><br/><br/>';
        
        $fout .= '
        <div class="w600">
            <textarea class="input w600" id="editable" name="text"  placeHolder="Text"></textarea>
        </div>
        <br/>
        <input type="submit" value="'.lang("Submit").'" class="btn w280" />
        </center></form></div>';
    }
    $fout .= '</div>';
    return $fout;
}

