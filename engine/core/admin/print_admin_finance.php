<?php
/**
* Print admin finance page.
* @path /engine/core/admin/print_admin_finance.php
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
* @usage <code> engine::print_admin_finance($cms); </code>
*/
function print_admin_finance($cms){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
        . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "finance" '
        . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
        . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysql_fetch_array($admin_res);
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
        if($_POST["submit_btn"]=="Confirm payment"){
            $query = 'SELECT * FROM `nodes_transaction` WHERE `id` = "'.$_POST["id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $query = 'UPDATE `nodes_user` SET balance=balance-'.$data["amount"].' WHERE `id` = "'.$data["user_id"].'"';
            engine::mysql($query);
            $query = 'UPDATE `nodes_transaction` SET `status` = "2" WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
            email::finish_withdrawal($data["user_id"]);
        }else if($_POST["submit_btn"]=="Delete"){
            $query = 'DELETE FROM `nodes_transaction` WHERE `id` = "'.intval($_POST["id"]).'"';
            engine::mysql($query);
        }
    }
    $fout = '<div class="document640">';
    if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_transaction` WHERE `status` > 0'
            . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*) FROM `nodes_transaction` WHERE `status` > 0';
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "user_id" => "User",
                "order_id" => "Type",
                "amount" => "Amount",
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
        if($data["user_id"]){
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $user = '<span title="'.$d["name"].'">'.mb_substr($d["name"],0,20).((strlen($d["name"])>20)?'...':'').'</span>';
        }else{
            $user = "Anonim";
        }if($data["order_id"]=="0"){
            $data["amount"] = -$data["amount"];
            $type = lang("Withdrawal request");
        }else if($data["order_id"]=="-1"){
            $type = lang("Money deposit");  
        }else{
            $type = lang("Order")." #".$data["order_id"];
        }
        $table .= '<tr>
            <td align=left valign=middle>'.$user.'</td>
            <td align=left valign=middle>'.$type.'</td>
            <td align=left valign=middle>$'.$data["amount"].'</td>
            <td align=left valign=middle>'.date("d/m/Y H:i", $data["date"]).'</td>
            <td width=30 align=left valign=middle class="nowrap">';
            if($admin_access == 2){
                $table .= '<form method="POST">
                        <input type="hidden" name="id" value="'.$data["id"].'" />';
                        if(!$data["order_id"] && $data["status"]==1){
                            $table .= '<input onClick=\'if(!confirm("'.lang("Please, confirm transaction").' $'.$data["amount"].' - PayPal '.$data["comment"].'")){ return; }\' type="submit" name="submit_btn"  class="btn small" value="'.lang("Confirm payment").'" />';
                        }else{
                            $table .= '<input type="submit" name="submit_btn" value="'.lang("Delete").'" class="btn small" />';
                        }
                if(intval($data["invoice_id"]) > 0){
                    $table .= ' <input type="button" onClick=\'window.open("/invoice.php?id='.$data["invoice_id"].'");\' class="btn small" value="'.lang("View invoice").'">';
                }
                $table .= '</form>';
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
    }$fout .= '</form>
        <div class="clear"></div>
        </div>';
    }else{
        $fout = '<div class="clear_block">'.lang("Transactions not found").'</div>';
    }return $fout;
}

