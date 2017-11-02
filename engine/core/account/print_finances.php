<?php
/**
* Print account finance page.
* @path /engine/core/account/print_finances.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
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
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_finances($site); </code>
*/
function print_finances($site){
    $fout .= '<div class="document640">';
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($_POST["amount"])&&$site->configs["sandbox"]){
        $amount = doubleval($_POST["amount"]);
        $query = 'INSERT INTO `nodes_transaction`(user_id, order_id, amount, status, date, comment, ip) '
                . 'VALUES("'.$_SESSION["user"]["id"].'", "-1", "'.$amount.'", "2", "'.date("U").'", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
        engine::mysql($query);
        $query = 'UPDATE `nodes_user` SET `balance` = "'.  doubleval($data["balance"]+$amount).'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
        engine::mysql($query);
        $data["balance"] += doubleval($_POST["amount"]);
    }
    $balance = $data["balance"];
    if($balance > $_SESSION["user"]["balance"]){
        $site->onload .= '
            alert("'.lang("The funds have been added to your account balance").'");
            ';
        $_SESSION["user"]["balance"] = $balance;
    }
    $pending = 0;
    $query = 'SELECT * FROM `nodes_product` WHERE `user_id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    while($d = mysql_fetch_array($res)){
        $query = 'SELECT * FROM `nodes_product_order` WHERE `product_id` = "'.$d["id"].'" AND `status` = "1"';
        $r = engine::mysql($query);
        while($order = mysql_fetch_array($r)){
            $pending += $order["price"];
        }
    }
    $fout.= lang('Balance').': <b>$'.$balance."</b>";
    if($pending>0) $fout.= "  ".lang("Pending").": <b>$".$pending.'</b>';
    $fout.= '<br/><br/>';
    if($site->configs["sandbox"]){
        $fout.= '
            <form method="POST" class="hidden">
            <input type="hidden" name="amount" id="paypal_price" value="'.$price.'">
            <input type="submit" id="pay_button"  /><br/><br/>
            </form>';
    }else{
        if($site->configs["paypal_test"]) $domain = 'www.sandbox.paypal.com';
        else $domain = 'www.paypal.com';
        $fout.= '
            <form action="https://'.$domain.'/cgi-bin/webscr" method="post" class="hidden">			
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="'.$site->configs["paypal_id"].'">
            <input type="hidden" name="item_name" value="'.$site->configs["paypal_description"].'">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="amount" id="paypal_price" value="'.$price.'">
            <input type="hidden" name="cancel_return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/finances">
            <input type="hidden" name="return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/finances">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="notify_url" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/paypal.php?deposit='.$_SESSION["user"]["id"].'">
            <button type="submit" class="btn w280" id="pay_button" >PayPal</button><br/><br/>
            </form>';
    }
    if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_transaction` WHERE `status` > 0 AND `user_id` = "'.$_SESSION["user"]["id"].'"'
            . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*) FROM `nodes_transaction` WHERE `status` > 0 AND `user_id` = "'.$_SESSION["user"]["id"].'"';
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "order_id" => lang("Type"),
                "amount" => lang("Amount"),
                "status" => lang("Status"),
                "date" => lang("Date")
            ); foreach($array as $order=>$value){
                $table .= '<th>';
                if($_SESSION["order"]==$order){
                    if($_SESSION["method"]=="ASC") $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "DESC"; submit_search_form();\'>'.lang($value).' &uarr;</a>';
                    else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).' &darr;</a>';
                }else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'</a>';
                $table .= '</th>';
            }
            $table .= '
        </tr>
        </thead>';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        $arr_count++;
        if($data["order_id"]=="0"){
            $type = lang("Withdrawal request");
            $data["amount"] = -$data["amount"];
        }else if($data["order_id"]=="-1"){
            $type = lang("Money deposit");
        }else if($data["order_id"]=="-2"){
            $type = lang("Transaction from admin");
        }else{
            $type = lang("Order")." #".$data["order_id"]." payment";
        }
        if($data["status"] == "0"){
            $status = lang("New");
        }else if($data["status"] == "1"){
            $status = lang("Pending");
        }else if($data["status"] == "2"){
            $status = lang("Finished");
        }
        $table .= '<tr>
            <td align=left valign=middle>'.$type.'</td>
            <td align=left valign=middle>'.$data["amount"].'$</td>
            <td align=left valign=middle>'.$status.'</td>
            <td align=left valign=middle title="'.date("d.m H:i", $data["date"]).'">'.date("d.m", $data["date"]).'</td>
        </tr>';
    }$table .= '</table>
    </div>';
    if($arr_count){
        $fout.= $table.'
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
        $fout.= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
            <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
             <option'; if($_SESSION["count"]=="20") $fout.= ' selected'; $fout.= '>20</option>
             <option'; if($_SESSION["count"]=="50") $fout.= ' selected'; $fout.= '>50</option>
             <option'; if($_SESSION["count"]=="100") $fout.= ' selected'; $fout.= '>100</option>
            </select> '.lang("per page").'.</nobr></p>';
    }$fout.= '
    </div><div class="cr"></div>';
    if($count>$_SESSION["count"]){
       $fout.= '<div class="pagination" >';
            $pages = ceil($count/$_SESSION["count"]);
           if($_SESSION["page"]>1){
                $fout.= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
            }$fout.= '<ul>';
           $a = $b = $c = $d = $e = $f = 0;
           for($i = 1; $i <= $pages; $i++){
               if(($a<2 && !$b && $e<2)||
                   ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
               ($i>$pages-2 && $e<2)){
                   if($a<2) $a++;
                   $e++; $f = 0;
                   if($i == $_SESSION["page"]){
                       $b = 1; $e = 0;
                      $fout.= '<li class="active-page">'.$i.'</li>';
                   }else{
                       $fout.= '<li onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                   }
               }else if((!$c||!$b) && !$f && $i<$pages){
                   $f = 1; $e = 0;
                   if(!$b) $b = 1;
                   else if(!$c) $c = 1;
                   $fout.= '<li class="dots">. . .</li>';
               }
           }if($_SESSION["page"]<$pages){
               $fout.= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
           }$fout.= '
     </ul>
    </div>';
         }$fout.= '<div class="clear"><br/></div>';
    }else{
        $fout.= '<div class="clear_block">'.lang('Transactions not found').'</div>';
    }            
    if($balance>0){
        $fout.= '<input type="button" class="btn w280" value="'.lang("Request withdrawal").'" onClick=\'withdrawal("'.lang("Confirm your PayPal").'");\' /><br/><br/>';
    }$fout.=  '<input type="button" class="btn w280" value="'.lang("Deposit money").'" onClick=\'deposit("'.lang("Amount to deposit").'");\' /><br/><br/>';
    $fout .= '</div>';
    return $fout;
}