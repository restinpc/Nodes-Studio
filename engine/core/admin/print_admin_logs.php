<?php
/**
* Print admin logs page.
* @path /engine/core/admin/print_admin_logs.php
* 
* @name    Nodes Studio    @version 2.0.3
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
* @usage <code> engine::print_admin_logs($cms); </code>
*/
function print_admin_logs($cms){
    if($_POST["reset"]){
        $query = 'DELETE FROM `nodes_log` WHERE `action` > 0';
        engine::mysql($query);
    }else if(!empty($_POST["id"])){
        $query = 'DELETE FROM `nodes_log` WHERE `id` = "'.intval($_POST["id"]).'"';
        engine::mysql($query);
    }
    if(!isset($_SESSION["type"])) $_SESSION["type"] = "0";
    if(isset($_POST["type"])) $_SESSION["type"] = $_POST["type"];
    $fout .= '<div class="document640">
        <form method="POST" id="log_select">
        '.lang("Select an action").': 
        <select class="input" name="type" onChange=\'document.getElementById("log_select").submit();\'>
            <option value="0">'.lang("Any action").'</option>
            <option value="1" '.($_SESSION["type"]==1?'selected':'').'>'.lang("Register").'</option>
            <option value="2" '.($_SESSION["type"]==2?'selected':'').'>'.lang("Try to register").'</option>
            <option value="3" '.($_SESSION["type"]==3?'selected':'').'>'.lang("Login").'</option>
            <option value="4" '.($_SESSION["type"]==4?'selected':'').'>'.lang("Trying to login").'</option>
            <option value="5" '.($_SESSION["type"]==5?'selected':'').'>'.lang("Logout").'</option>
            <option value="6" '.($_SESSION["type"]==6?'selected':'').'>'.lang("Comment posted").'</option>
            <option value="7" '.($_SESSION["type"]==7?'selected':'').'>'.lang("New message").'</option>
            <option value="8" '.($_SESSION["type"]==8?'selected':'').'>'.lang("Engine update").'</option>
            <option value="9" '.($_SESSION["type"]==9?'selected':'').'>'.lang("Email sended").'</option>
        </select>
        </form>';
    if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_log` WHERE `action` > 0';
    $requery = 'SELECT COUNT(*) FROM `nodes_log` WHERE `action` > 0';
    if(!empty($_SESSION["type"])){
        $condition = ' AND `action` = '.$_SESSION["type"];
        $query .= $condition;
        $requery .= $condition;
    }
    $query .= ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "action" => "Action",
                "user_id" => "User",
                "ip" => "IP",
                "date" => "Date"
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
        if($data["user_id"]>0){
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $user = mb_substr($d["name"],0,20).((strlen($d["name"])>20)?'...':'');
        }else if($data["user_id"]=="-1"){
            $user = "Cron";
        }else{
            $user = "Anonim";
        }
        switch($data["action"]){
            case "1":
                $action = lang("Register");
                break;
            case "2":
                $action = lang("Try to register");
                break;
            case "3":
                $action = lang("Login");
                break;
            case "4":
                $action = lang("Trying to login");
                break;
            case "5":
                $action = lang("Logout");
                break;
            case "6":
                $action = lang("Comment posted");
                break;
            case "7":
                $action = lang("New message");
                break;
            case "8":
                $action = lang("Engine update");
                break;
            case "9":
                $action = lang("Email sended");
                break;
        }
        $table .= '<tr>
            <td align=left valign=middle onClick=\'alert("'.  mysql_real_escape_string(str_replace("\n", '<br/>', $data["details"])).'");\' class="pointer">'.$action.'</td>
            <td align=left valign=middle>'.$user.'</td>
            <td align=left valign=middle>'.$data["ip"].'</td>
            <td align=left valign=middle>'.date("d/m/Y H:i", $data["date"]).'</td>
            <td width=20 align=left valign=middle>
                <form method="POST">
                    <input type="hidden" name="id" value="'.$data["id"].'" />
                    <input type="submit" value="'.lang("Delete").'" class="btn small" />
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
           }$fout .= '</ul></div>';
         }$fout .= '<div class="clear"></div>';
    $fout .= '</form><br/>
    <form method="POST">
        <input type="submit" class="btn w280" name="reset" value="'.lang("Clear logs").'" /><br/>
    </form>';
    }else{
        $fout .= '<div class="clear_block">'.lang("Logs not found").'</div>';
    }
    $fout .= '</div>';
    return $fout;
}
