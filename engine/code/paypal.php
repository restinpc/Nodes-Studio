<?php
/**
* Paypal payment processor.
* @path /engine/code/paypal.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/session.php");
if(!empty($_GET["invoice_id"]) && !empty($_POST["mc_gross"])){
    $amount = $_POST["mc_gross"];
    $invoice_id = intval($_GET["invoice_id"]);
    $query = 'SELECT * FROM `nodes_invoice` WHERE `id` = "'.$invoice_id.'"';
    $res = engine::mysql($query);
    $invoice = mysqli_fetch_array($res);
    $user_id = $invoice["user_id"];
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    $balance = $user["balance"];
    $postdata = ""; 
    foreach ($_POST as $key=>$value) $postdata .= $key."=".urlencode($value)."&"; 
    $postdata .= "cmd=_notify-validate";  
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "paypal_test"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if($data["value"]){ 
        $domain = 'ipnpb.sandbox.paypal.com';
        $gateway = "Paypal Sandbox";
    }else{ 
        $domain = 'ipnpb.paypal.com';
        $gateway = 'Paypal';
    }
    $curl = curl_init("https://".$domain."/cgi-bin/webscr"); 
    curl_setopt ($curl, CURLOPT_HEADER, 0);  
    curl_setopt ($curl, CURLOPT_POST, 1); 
    curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata); 
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);  
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1); 
    $response = curl_exec ($curl); 
    curl_close ($curl); 
    if($response == "VERIFIED"){
        $balance += $amount;
        $query = 'INSERT INTO `nodes_transaction`(user_id, invoice_id, order_id, amount, `txn_id`, `payment_date`, status, date, gateway, comment, ip) '
                . 'VALUES("'.$user_id.'", "'.$invoice_id.'", "-1", "'.$amount.'", "'.$_POST["txn_id"].'", "'.$_POST["payment_date"].'", "2", "'.date("U").'", "'.$gateway.'", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
        engine::mysql($query);
        $query = 'UPDATE `nodes_user` SET `balance` = "'.$balance.'" WHERE `id` = "'.$user_id.'"';
        engine::mysql($query);
        email::new_transaction($user_id, $amount);
    }
}else engine::error();