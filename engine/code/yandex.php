<?php
/**
* Yandex payment processor.
* @path /engine/code/yandex.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/session.php");
$validate = $_POST["notification_type"].'&'.$_POST["operation_id"].'&'.$_POST["amount"].'&'.$_POST["currency"].'&'.$_POST["datetime"].'&'.$_POST["sender"].'&'.$_POST["codepro"].'&0rpDbwhcMPf+lRF4sqY+Z+TJ&'.$_POST["label"];
if( !empty($_POST["withdraw_amount"]) && 
    $_POST["sha1_hash"] == sha1($validate) && 
    $_POST["currency"] == 643 && 
    $_POST["unaccepted"] == "false"
){
    $invoice_id = intval($_POST["label"]);
    $query = 'SELECT * FROM `nodes_invoice` WHERE `id` = "'.$invoice_id.'"';
    $res = engine::mysql($query);
    $invoice = mysqli_fetch_array($res);
    $user_id = $invoice["user_id"];
    $amount = $_POST["withdraw_amount"];
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    $query = 'INSERT INTO `nodes_transaction`(user_id, invoice_id, order_id, amount, `txn_id`, `payment_date`, status, date, gateway, comment, ip) '
            . 'VALUES("'.$user_id.'", "'.$invoice_id.'", "-1", "'.$amount.'", "'.$_POST["operation_id"].'", "'.$_POST["datetime"].'", "2", "'.date("U").'", "Yandex", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
    engine::mysql($query);
    $balance = doubleval($user["balance"]);
    $balance += $amount;
    $query = 'UPDATE `nodes_user` SET `balance` = "'.$balance.'" WHERE `id` = "'.$user["id"].'"';
    engine::mysql($query);
    email::new_transaction($_POST["label"], $amount);
}else engine::error();