<?php
/**
* Invoice page
* @path /engine/code/invoice.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if(!empty($_GET["id"])){
    $id = intval($_GET["id"]);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "sandbox"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $sandbox = intval($data["value"]);
    $query = 'SELECT * FROM `nodes_invoice` WHERE `id` = "'.$id.'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if($_SESSION["user"]["id"] != "1" && $_SESSION["user"]["id"] != $data["user_id"]){
        die(engine::error(401));
    }
    if(!empty($data)){
        $amount = $data["amount"];
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
        $res = engine::mysql($query);
        $user = mysqli_fetch_array($res);
        if(intval($data["order_id"]) > 0){
            $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$data["order_id"].'"';
            $res = engine::mysql($query);
            $flag = 0;
            while($order = mysqli_fetch_array($res)){
                $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$order["product_id"].'"';
                $r = engine::mysql($query);
                $product = mysqli_fetch_array($r);
                if($flag) $caption .= '<hr style="border-color: #ddd;" />';
                $caption .= '<table style="width: 100%;"><td><b>'.$product["title"].'</b></td><td style="text-align:right;" width=200>$'.$product["price"].'</td></table>';
                $flag++;
            }
        }else{
            $caption = '<b>'.lang("Money deposit").'</b> <div style="float:right;">$'.$data["amount"].'</div>';
        }
        if($sandbox && doubleval($_POST["demo_payment"])>0){
            $query = 'INSERT INTO `nodes_transaction`(user_id, invoice_id, order_id, amount, status, date, gateway, comment, ip) '
                    . 'VALUES("'.$user["id"].'", "'.$id.'", "-1", "'.doubleval($_POST["demo_payment"]).'", "2", "'.date("U").'", "Demo", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
            engine::mysql($query);
            $query = 'UPDATE `nodes_user` SET `balance` = "'.($user["balance"]+doubleval($_POST["demo_payment"])).'" WHERE `id` = "'.$user["id"].'"';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
            $res = engine::mysql($query);
            $user = mysqli_fetch_array($res);
        }
        if($data["order_id"]>0){
            $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$data["order_id"].'"';
            $r = engine::mysql($query);
            $order = mysqli_fetch_array($r);
            if(!$order["status"]){
                $query = 'SELECT * FROM `nodes_user`  WHERE `id` = "'.$user["id"].'"';
                $res = engine::mysql($query);
                $user = mysqli_fetch_array($res);
                if($user["balance"] >= $data["amount"]){
                    $query = 'INSERT INTO `nodes_transaction`(user_id, invoice_id, order_id, amount, status, date, gateway, comment, ip) '
                            . 'VALUES("'.$user["id"].'", "'.$id.'", "'.$data["order_id"].'", "-'.$amount.'", "2", "'.date("U").'", "Demo", "Order payment", "'.$_SERVER["REMOTE_ADDR"].'")';
                    engine::mysql($query);
                    $query = 'UPDATE `nodes_user` SET `balance` = "'.($user["balance"]-$amount).'" WHERE `id` = "'.$user["id"].'"';
                    engine::mysql($query);
                    $query = 'UPDATE `nodes_order` SET `status` = "1" WHERE `id` = "'.intval($data["order_id"]).'"';
                    engine::mysql($query);
                    email::new_purchase($data["order_id"]);
                    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
                    $res = engine::mysql($query);
                    $user = mysqli_fetch_array($res);
                }
            }
        }
        if($data["order_id"] > 0){
            $query = 'SELECT * FROM `nodes_transaction` WHERE `invoice_id` = "'.$data["id"].'" AND `order_id` = "'.$data["order_id"].'"';
        }else{
            $query = 'SELECT * FROM `nodes_transaction` WHERE `invoice_id` = "'.$data["id"].'"';
        }
        $r = engine::mysql($query);
        $sum = 0;
        $flag = 0;
        $payment_date = '';
        while($d = mysqli_fetch_array($r)){
            if($d["status"] == "2"){
                $sum += $d["amount"];
                $payment_date = $d["payment_date"];
            }
        }
        if($data["order_id"] > 0){
            $sum *= -1;
        }
        if($payment_date != ''){
            $payment_date = lang("Payment date").' '.$payment_date.'<br/>';
        }
        $options = 0;
        $payment = '';
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "yandex_money"';
        $res = engine::mysql($query);
        $yandex = mysqli_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "paypal_id"';
        $res = engine::mysql($query);
        $paypal = mysqli_fetch_array($res);
        if($data["order_id"] > 0){
            $atb = $amount-$sum-$user["balance"];
        }else{
            $atb = $amount-$sum;
        }
        if($amount <= $sum){
            $status = '<span style="color:#0a0;">'.lang("Successfully paid").'</span>';
        }else if($sum > 0){
            $script = '<script>setTimeout(function(){window.location="'.$_SERVER["DIR"].'/invoice.php?id='.$id.'";}, 30000);</script>';
            $status = '<span style="color:#00f;">'.lang("Partially paid").'</span>';
            if(!empty($paypal["value"])){
                $payment .= engine::print_paypal_form($id, $atb, $_SERVER["PUBLIC_URL"].'/invoice.php?id='.$id);
                $options++;
            }
            if(!empty($yandex["value"])){
                $options++;
                $payment .= engine::print_yandex_form($id, $atb, $_SERVER["PUBLIC_URL"].'/invoice.php?id='.$id);
            }
        }else{
            $script = '<script>setTimeout(function(){window.location="'.$_SERVER["DIR"].'/invoice.php?id='.$id.'";}, 30000);</script>';
            $status = '<span style="color:#f00;">'.lang("Pending payment").'</span>';
            if(!empty($paypal["value"])){
                $options++;
                $payment .= engine::print_paypal_form($id, $atb, $_SERVER["PUBLIC_URL"].'/invoice.php?id='.$id);
            }
            if(!empty($yandex["value"])){
                $options++;
                $payment .= engine::print_yandex_form($id, $atb, $_SERVER["PUBLIC_URL"].'/invoice.php?id='.$id);
            }
        }
        
        if($_SESSION["user"]["id"] == $data["user_id"]){
            if( $amount > $sum){
                if($sandbox){
                    $button = '<br/><form method="POST">
                        <input type="hidden" name="demo_payment" value="'.($atb).'">
                        <input vr-control id="make-payment-input" type="submit" class="btn w280" value="'.lang("Make payment").'" />
                    </form>';
                }else{
                    if($options == 1){
                        if(!empty($paypal["value"])){
                            $button = '<br/><input vr-control id="make-payment-input"  type="button" onClick=\'document.getElementById("paypal_form").submit();\' class="btn w280" value="'.lang("Make payment").'" />';
                        }else if(!empty($yandex["value"])){
                            $button = '<br/><input  vr-control id="make-payment-input" type="button" onClick=\'document.getElementById("yandex_form").submit();\' class="btn w280" value="'.lang("Make payment").'" />';
                        }
                    }else{
                        $button = '<br/>
                            <select vr-control class="input w280" id="payment_method">';
                        if(!empty($paypal["value"])){
                            $button .= '<option vr-control id="option-paypal" value="paypal">PayPal</option>';
                        }
                        if(!empty($yandex["value"])){
                            $button .= '<option vr-control id="option-yandex" value="yandex">Yandex Money</option>';
                        }
                        $button .= '
                            </select><br/>
                            <input vr-control id="make-payment-input"  type="button" class="btn w280" value="'.lang("Make payment").'" onClick=\''
                                . 'if(document.getElementById("payment_method").value=="paypal"){'
                                . '     document.getElementById("paypal_form").submit();'
                                . '}else if(document.getElementById("payment_method").value=="yandex"){'
                                . '     document.getElementById("yandex_form").submit();'
                                . '}\' /> ';
                    }
                }
            }else{
                if(intval($data["order_id"]) > 0){
                    $button = '<br/><a vr-control id="back-to-account" href="'.$_SERVER["DIR"].'/account/purchases" target="_top" class="btn w280">'.lang("Back to account").'</a>';
                }else{
                    $button = '<br/><a vr-control id="back-to-account" href="'.$_SERVER["DIR"].'/account/finances" target="_top" class="btn w280">'.lang("Back to account").'</a>'; 
                }
            }
        }else{
            $button = '<br/><a vr-control id="back-to-admin" href="'.$_SERVER["DIR"].'/admin" target="_top" class="btn w280">'.lang("Back to admin").'</a>'; 
        }
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "invoice_image"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        if(!empty($d)){
            $logo = '<img src="'.$d["value"].'" />';
        }else{
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "name"';
            $r = engine::mysql($query);
            $d = mysqli_fetch_array($r);
            $logo = $d["value"];
        }
$fout = '<!DOCTYPE html>
<!-- Powered by Nodes Studio -->
<html>
<head>
<title>'.lang("Invoice").' № '.$data["id"].'</title>
<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css" />
<link href="'.$_SERVER["DIR"].'/template/invoice.css" rel="stylesheet" type="text/css" />
</head>
<body class="nodes">
    <div class="invoice">
        <div class="invoice_logo">
            <a vr-control id="invoice-logo-img" href="'.$_SERVER["PUBLIC_URL"].'" target="_blank">'.$logo.'</a>
        </div>
        <div class="invoice_date">
            <div class="status">'.$status.'</div>
            <h1>'.lang("An invoice for payment").'</h1>
            <br/>
            '.lang("Invoice").' № '.$data["id"].'<br/>
            '.lang("Invoice date for payment").' '.$data["date"].'<br/>
            '.$payment_date.'<br/>
        </div>
        <div class="clear"></div>
        <div class="invoice_content">
            '.$user["name"].' ('.$user["email"].')<br/>
            <br/>
            <div class="invoice_details">
                '.$caption.'
            </div>
            <br/>
        </div>
        <div class="clear"></div>
        <div class="hidden">'.$payment.'</div>
        <div class="invoice_pay">  
            '.lang("Total").': <b>$'.$amount.'</b><br/>
            '.lang('Balance').': <b>$'.$user["balance"].'</b><br/>
            '.lang("Total Paid").': <b>$'.($sum).'</b><br/>
            '.(($atb>0)?lang("Amount to be paid").': <b>$'.($atb).'</b><br/>':'').'
            '.$button.'
        </div>
        <div class="clear"></div>
    </div>
    '.$script.'
</body>
</html>';
echo $fout;
    }else{
        engine::error();
    }
}else{
    engine::error();
}