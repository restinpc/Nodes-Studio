<?php
/**
* Print admin users page.
* @path /engine/core/admin/print_admin_users.php
* 
* @name    Nodes Studio    @version 3.0.0.1
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
* @usage <code> engine::print_admin_users($cms); </code>
*/
function print_admin_users($cms){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
            . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "users" '
            . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
            . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysqli_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    if(!empty($_POST["delete"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        $query = 'DELETE FROM `nodes_user` WHERE `id` = "'.intval($_POST["delete"]).'"';
        engine::mysql($query);
    }else if(!empty($_POST["ban"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        $query = 'UPDATE `nodes_user` SET `ban` = "1" WHERE `id` = "'.intval($_POST["ban"]).'"';
        engine::mysql($query);
    }else if(!empty($_POST["die"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        $query = 'UPDATE `nodes_user` SET `ban` = "-1" WHERE `id` = "'.intval($_POST["die"]).'"';
        engine::mysql($query);
    }else if(!empty($_POST["unban"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        $query = 'UPDATE `nodes_user` SET `ban` = "0" WHERE `id` = "'.intval($_POST["unban"]).'"';
        engine::mysql($query);
    }else if(!empty($_POST["confirm"])){
        if($admin_access != 2){
            engine::error(401);
            return;
        }
        $query = 'UPDATE `nodes_user` SET `confirm` = "1" WHERE `id` = "'.intval($_POST["confirm"]).'"';
        engine::mysql($query);
    }
    $fout = '<div class="document640">';
    if($_SESSION["order"]=="id") $_SESSION["order"] = "name";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_user` WHERE `ban` >= 0 AND `admin` = 0'
            . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*) FROM `nodes_user` WHERE `ban` >= 0 AND `admin` = 0';
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "name" => "Name",
                "email" => "Email",
                "balance" => "Balance",
                "online" => "Last visit"
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
        if($data["online"] > date("U")-300) $online = '<center>'.lang("Online").'</center>';
        else $online = date("d/m/Y", $data["online"]);
        $ban = '<form method="POST" id="ban_form"><input type="hidden" name="ban" id="ban_value" value="0" /></form>'
            . '<form method="POST" id="unban_form"><input type="hidden" name="unban" id="unban_value" value="0" /></form>'
            . '<form method="POST" id="delete_form"><input type="hidden" name="delete" id="delete_value" value="0" /></form>'
            . '<form method="POST" id="die_form"><input type="hidden" name="die"  id="die_value" value="0" /></form>'
            . '<form method="POST" id="confirm_form"><input type="hidden" name="confirm"  id="confirm_value" value="0" /></form>'
            . '<select vr-control id="select-user-'.$i.'" class="input" onChange=\'if(confirm("'.lang("Are you sure?").'")){if(this.value=="1"){
document.getElementById("unban_value").value="'.$data["id"].'";
document.getElementById("unban_form").submit();
                }else if(this.value=="2"){
if(confirm("'.lang("Confirm deleting banned user").'")){
    document.getElementById("die_value").value="'.$data["id"].'";
    document.getElementById("die_form").submit();
}
                }else if(this.value=="3"){
document.getElementById("ban_value").value="'.$data["id"].'"; 
document.getElementById("ban_form").submit();
                }else if(this.value=="4"){
document.getElementById("delete_value").value="'.$data["id"].'";
document.getElementById("delete_form").submit();
                }else if(this.value=="5"){
new_transaction('.$data["id"].', "'.lang("Transfer amount").'");
                }
            }else{this.selectedIndex=0;}\'>';
            if(intval($data["ban"])){ 
                $ban .= '<option vr-control id="option-user-'.$i.'-0" value="0" selected disabled>'.lang("Banned").'</option>'
                        . '<option vr-control id="option-user-'.$i.'-1" value="1">'.lang("Unban").'</option>'
                        . '<option vr-control id="option-user-'.$i.'-2" value="2">'.lang("Delete").'</option>';
            }else{ 
                $ban .= '<option vr-control id="option-user-'.$i.'-0" value="0" selected disabled>'.lang("Active").'</option>'
                        . '<option vr-control id="option-user-'.$i.'-3" value="3">'.lang("Ban").'</option>'
                        . '<option vr-control id="option-user-'.$i.'-4" value="4">'.lang("Delete").'</option>';
            }
        $ban .= '<option vr-control id="option-user-'.$i.'-5" value="5">'.lang("New transaction").'</option>';
        $ban .= '</select>';
        if($data["confirm"]) $flag = '<input type="checkbox" checked disabled />';
        else $flag = '<input vr-control id="input-checkbox-'.$arr_count.'" type="checkbox" title="'.lang("Code").': '.$data["code"].'" '
                . 'onClick=\'document.getElementById("confirm_value").value="'.$data["id"].'"; '
                . 'document.getElementById("confirm_form").submit();\' />';
        $table .= '<tr><td align=left class="nowrap">'.$flag.'&nbsp;<a vr-control id="user-'.$data["id"].'" href="'.$_SERVER["DIR"].'/account/inbox/'.$data["id"].'">'.$data["name"].'</a></td>'
                . '<td align=left><a href="mailto:'.$data["email"].'">'.$data["email"].'</a></td>'
                . '<td align=left>'.$data["balance"].'$</td>'
                . '<td align=left>'.$online.'</td>'
                . '<td width=45 align=left>';
        if($admin_access == 2){
            $table .= $ban;
        }
        $table .= '</td></tr>';
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
         }$fout .= '</form>
            <div class="clear"></div>
            </div>';
    }else{
        $fout = '<div class="clear_block">'.lang("Users not found").'</div>';
    }return $fout;
}

