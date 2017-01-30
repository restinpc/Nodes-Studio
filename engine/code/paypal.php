<?php
/**
* Paypal payment processor.
* @path /engine/code/paypal.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
if(!empty($_GET["deposit"]) || !empty($_POST["payment_gross"])){
    $amount = $_POST["payment_gross"];
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$_GET["deposit"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "sandbox"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $balance = doubleval($user["balance"]);
    if($data["value"]){
        $balance += $amount;
        $query = 'INSERT INTO `nodes_transaction`(user_id, order_id, amount, status, date, comment, ip) '
                . 'VALUES("'.$_GET["deposit"].'", "-1", "'.$amount.'", "2", "'.date("U").'", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
        engine::mysql($query);
        $query = 'UPDATE `nodes_user` SET `balance` = "'.$balance.'" WHERE `id` = "'.$_GET["deposit"].'"';
        engine::mysql($query);
        email::new_transaction($_GET["deposit"], $amount);
    }else{
        $postdata = ""; 
        foreach ($_POST as $key=>$value) $postdata .= $key."=".urlencode($value)."&"; 
        $postdata .= "cmd=_notify-validate";  
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
            $query = 'INSERT INTO `nodes_transaction`(user_id, order_id, amount, `txn_id`, `payment_date`, status, date, comment, ip) '
                    . 'VALUES("'.$_GET["deposit"].'", "-1", "'.$amount.'", "'.$_POST["txn_id"].'", "'.$_POST["payment_date"].'", "2", "'.date("U").'", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
            engine::mysql($query);
            $query = 'UPDATE `nodes_user` SET `balance` = "'.$balance.'" WHERE `id` = "'.$_GET["deposit"].'"';
            engine::mysql($query);
            $_SESSION["user"]["balance"] = $balance;
            email::new_transaction($_GET["deposit"], $amount);
        }
    }  
}
if(!empty($_GET["order_id"])){
    $query = 'SELECT * FROM `nodes_transaction` WHERE `order_id` = "'.intval($_GET["order_id"]).'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "sandbox"';
    $res = engine::mysql($query);
    $flag = mysql_fetch_array($res);
    if($flag["value"]){
        $query = 'UPDATE `nodes_transaction` SET `amount` = "'.($data["amount"]+$_POST["price"]).'", '
                . '`txn_id` = "test_transaction", '
                . '`payment_date` = "'.date("Y-m-d H:iLs").'", '
                . '`status` = "2", '
                . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                . 'WHERE `id` = "'.$data["id"].'"';
        engine::mysql($query);
        $query = 'UPDATE `nodes_order` SET `status` = "1" WHERE `id` = "'.intval($_GET["order_id"]).'"';
        engine::mysql($query);
        email::new_purchase($_GET["order_id"]);
        unset($_SESSION["order_confirm"]);
        unset($_SESSION["shipping_confirm"]);
        unset($_SESSION["products"]); 
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "paypal_test"';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        if(!$d["value"]){ 
            $domain = 'www.paypal.com';
            $postdata = ""; 
            foreach ($_POST as $key=>$value) $postdata .= $key."=".urlencode($value)."&"; 
            $postdata .= "cmd=_notify-validate";  
            if($response == "VERIFIED"){
                $query = 'SELECT * FROM `nodes_transaction` WHERE `id` = "'.intval($data["id"]).'"';
                $res = engine::mysql($query);
                $d = mysql_fetch_array($res);
                $query = 'UPDATE `nodes_transaction` SET `amount` = "'.($d["amount"]+$_POST["payment_gross"]).'", '
                        . '`txn_id` = "'.$_POST["txn_id"].'", '
                        . '`payment_date` = "'.$_POST["payment_date"].'", '
                        . '`status` = "2", '
                        . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                        . 'WHERE `id` = "'.$d["id"].'"';
                engine::mysql($query);
                $query = 'UPDATE `nodes_order` SET `status` = "1" WHERE `id` = "'.intval($_GET["order_id"]).'"';
                engine::mysql($query);
                email::new_purchase($_GET["order_id"]);
            } 
        }else{
            $query = 'UPDATE `nodes_transaction` SET `amount` = "'.($data["amount"]+$_POST["payment_gross"]).'", '
                    . '`txn_id` = "'.$_POST["txn_id"].'", '
                    . '`payment_date` = "'.$_POST["payment_date"].'", '
                    . '`status` = "2", '
                    . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                    . 'WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query); 
            $query = 'UPDATE `nodes_order` SET `status` = "1" WHERE `id` = "'.intval($_GET["order_id"]).'"';
            engine::mysql($query);
            email::new_purchase($_GET["order_id"]);
        }
    }
}else engine::error();