<?php
/**
* Print admin outbox page.
* @path /engine/core/admin/print_admin_outbox.php
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
* @usage <code> engine::print_admin_outbox($cms); </code>
*/
function print_admin_outbox($cms){
    if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $fout = '<div class="document640">';
    if($_GET["act"]=="new"){
        if(!empty($_POST["caption"])){
            $caption = trim(mysql_real_escape_string($_POST["caption"]));
            $action = intval($_POST["action"]);
            $text = mysql_real_escape_string(str_replace("\n", "<br/>", $_POST["text"]));
            $query = 'SELECT `id` FROM `nodes_outbox` WHERE `caption` = "'.$caption.'" AND `text` LIKE "'.$text.'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(!empty($data)){
                $fout .= '<script>alert("'.lang("This bulk message already exist").'");</script>';
            }else{
                $query = 'INSERT INTO `nodes_outbox`(caption, text, action, date) '
                        . 'VALUES("'.$caption.'", "'.$text.'", "'.$action.'", "'.date("U").'")';
                engine::mysql($query);
                $id = mysql_insert_id();
                $query = 'SELECT * FROM `nodes_user` WHERE `bulk_ignore` = 0 AND `id` > 1';
                $res = engine::mysql($query);
                while($user = mysql_fetch_array($res)){
                    $query = 'INSERT INTO `nodes_user_outbox`(user_id, outbox_id, date, status) '
                            . 'VALUES("'.$user["id"].'", "'.$id.'", "0", "0")';
                    engine::mysql($query);
                }
                $fout = '<script>alert("'.lang("Bulk message is sending now").'"); window.location="'.$_SERVER["DIR"].'/admin?mode=outbox";</script>';
                return $fout;
            }
        }
        $fout .= '<h1>'.lang("New bulk message").'</h1><br/>
        <div class="table">
            <form method="POST">
            <table width=100% id="table">
            <tr>
                <td>'.lang("Caption").'</td>
                <td><input type="text" name="caption" class="input w100p" /></td>
            </tr>
            <tr>
                <td>'.lang("Action").'</td>
                <td>
                    <select type="text" name="action" class="input w100p" >
                        <option value="0">'.lang("Send to email").'</option>
                        <option value="1">'.lang("Send in chat").'</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan=2><textarea name="text" class="input w100p" rows=5 placeHolder="'.lang("Text of message").'" ></textarea></td>
            </tr>
            </table><br/>
            <input type="submit" class="btn w280" value="'.lang("Send messages").'" />
            </form><br/>
            <a href="'.$_SERVER["DIR"].'/admin/?mode=outbox"><input class="btn w280" type="button" value="'.lang("Back to outbox").'" /></a>
        </div>';
    }else{
        if(!empty($_POST["id"])){
            $query = 'DELETE FROM `nodes_outbox` WHERE `id` = "'.intval($_POST["id"]).'"';
            engine::mysql($query);
        }
        $query = 'SELECT `outbox`.*, '
                . '( SELECT COUNT(`id`) FROM `nodes_user_outbox` WHERE `outbox_id` = `outbox`.`id` ) AS `total`, '
                . '( SELECT COUNT(`id`) FROM `nodes_user_outbox` WHERE `outbox_id` = `outbox`.`id` AND `status` = "1" ) AS `sended` '
                . 'FROM `nodes_outbox` AS `outbox`';
        $requery = 'SELECT COUNT(*) FROM `nodes_outbox`';
        $query .= ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
        $table = '
            <div class="table">
            <table width=100% id="table">
            <thead>
            <tr>';
                $array = array(
                    "outbox`.`caption" => "Caption",
                    "outbox`.`action" => "Action",
                    "sended" => "Sended",
                    "outbox`.`date" => "Date"
                ); foreach($array as $order=>$value){
                    $table .= '<th>';
                    if($_SESSION["order"]==$order){
                        if($_SESSION["method"]=="ASC") $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "DESC"; submit_search_form();\'>'.lang($value).'&nbsp;&uarr;</a>';
                        else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'&nbsp;&darr;</a>';
                    }else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'</a>';
                    $table .= '</th>';
                }
                $table .= '
            <th></th>
            </tr>
            </thead>';
        $res = engine::mysql($query);
        while($data = mysql_fetch_array($res)){
            $arr_count++;
            if($data["action"]) $action = "Chat";
            else $action = "Email";
            $table .= '<tr>
                <td align=left valign=middle onClick=\'alert("'.$data["text"].'");\' class="pointer" title="'.strip_tags($data["text"]).'">'.$data["caption"].'</td>
                <td align=left valign=middle>'.$action.'</td>
                <td align=left valign=middle>'.$data["sended"].' / '.$data["total"].'</td>
                <td align=left valign=middle>'.date("d/m/Y H:i", $data["date"]).'</td>
                <td width=20 align=left valign=middle>
                    <form method="POST">
                        <input type="hidden" name="id" value="'.$data["id"].'" />
                        <input type="submit" value="'.lang("Delete").'" onClick=\'if(!confirm("'.lang("Are you sure?").'")){event.preventDefault(); return 0;}\' class="btn small" />
                    </form>
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
             }$fout .= '<div class="clear"></div>';
        $fout .= '</form>';
        }else{
            $fout .= '<div class="clear_block">'.lang("Messages not found").'</div>';
        }
        $fout .= '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=outbox&act=new"><input type="button" class="btn w280" value="'.lang("New bulk message").'"></a>';
    }
    $fout .= '</div>';
    return $fout;
}