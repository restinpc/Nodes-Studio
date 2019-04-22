<?php
/**
* Coinpayments payment processor.
* @path /engine/code/coinpayments.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/session.php");
$fname = "btc.txt";
$fname = fopen($fname, 'w');
$code = print_r($_GET, 1);
$code .= print_r($_POST, 1);
$code .= print_r($_SESSION, 1);
fwrite($fname, $code);
fclose($fname);
if(intval($_POST["status"]) >= 100 && !empty($_GET["order_id"]) && !empty($_GET["secret"])){
    $invoice_id = intval($_GET["order_id"]);
    $amount = doubleval($_POST["amount1"]);
    $op_id = $_POST["txn_id"]; 
    $datetime = date("Y-m-d H:i:s");
    $secret = engine::escape_string($_GET["secret"]);
    //----------
    $query = 'SELECT * FROM `nodes_invoice` WHERE `id` = "'.$invoice_id.'"';
    $res = engine::mysql($query);
    $invoice = mysqli_fetch_array($res);
    $user_id = $invoice["user_id"];
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_transaction` WHERE `invoice_id` = "'.$invoice_id.'" ORDER BY `id` DESC LIMIT 0, 100';
    $res = engine::mysql($query);
    $flag = 0;
    $transaction_id = 0;
    while($transaction = mysqli_fetch_array($res)){
        if($secret == md5($user["id"].'x'.$invoice_id.'x'.$transaction["txn_id"])){
            $flag = 1;
            $transaction_id = $transaction["id"];
            break;
        }
    }
    if($flag && $transaction_id){
        $query = 'DELETE FROM `nodes_transaction` WHERE `id` = "'.$transaction_id.'"';
        engine::mysql($query);
        $query = 'INSERT INTO `nodes_transaction`(user_id, invoice_id, order_id, amount, `txn_id`, `payment_date`, status, date, gateway, comment, ip) '
                . 'VALUES("'.$user_id.'", "'.$invoice_id.'", "-1", "'.$amount.'", "'.$op_id.'", "'.$datetime.'", "2", "'.date("U").'", "Coinpayments", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
        engine::mysql($query);
        $balance = doubleval($user["balance"]);
        $balance += $amount;
        $query = 'UPDATE `nodes_user` SET `balance` = "'.$balance.'" WHERE `id` = "'.$user["id"].'"';
        engine::mysql($query);
        email::new_transaction($invoice_id, $amount);
    }else engine::error();
}else engine::error();