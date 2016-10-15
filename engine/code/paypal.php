<?php
require_once("engine/nodes/session.php");
if(!empty($_SESSION["user"]["id"])){
    if(!empty($_GET["order_id"])){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "sandbox"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if($data["value"]){
            $query = 'SELECT * FROM `nodes_transactions` WHERE `order_id` = "'.intval($_GET["order_id"]).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $query = 'UPDATE `nodes_transactions` SET `amount` = "'.($data["amount"]+$_POST["price"]).'", '
                    . '`txn_id` = "test_transaction", '
                    . '`payment_date` = "'.date("Y-m-d H:iLs").'", '
                    . '`status` = "2", '
                    . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                    . 'WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
            $query = 'UPDATE `nodes_orders` SET `status` = "1" WHERE `id` = "'.intval($_GET["order_id"]).'"';
            engine::mysql($query);
            require_once("engine/include/send_email.php");
            send_email::new_purchase($_GET["order_id"]);
            unset($_SESSION["order_confirm"]);
            unset($_SESSION["shipping_confirm"]);
            unset($_SESSION["products"]); 
        }else{
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "paypal_test"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if($data["value"]) $domain = 'www.sandbox.paypal.com';
            else $domain = 'www.paypal.com';
            $postdata = ""; 
            foreach ($_POST as $key=>$value) $postdata .= $key."=".urlencode($value)."&"; 
            $postdata .= "cmd=_notify-validate";  
            $curl = curl_init("https://'.$domain.'/cgi-bin/webscr"); 
            curl_setopt ($curl, CURLOPT_HEADER, 0);  
            curl_setopt ($curl, CURLOPT_POST, 1); 
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata); 
            curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);  
            curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1); 
            $response = curl_exec ($curl); 
            curl_close ($curl); 
            if($response == "VERIFIED"){
                $query = 'SELECT * FROM `nodes_transactions` WHERE `order_id` = "'.intval($_GET["order_id"]).'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                $query = 'UPDATE `nodes_transactions` SET `amount` = "'.($data["amount"]+$_POST["payment_gross"]).'", '
                        . '`txn_id` = "'.$_POST["txn_id"].'", '
                        . '`payment_date` = "'.$_POST["payment_date"].'", '
                        . '`status` = "2", '
                        . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                        . 'WHERE `id` = "'.$data["id"].'"';
                engine::mysql($query);
                $query = 'UPDATE `nodes_orders` SET `status` = "1" WHERE `id` = "'.intval($_GET["order_id"]).'"';
                engine::mysql($query);
                require_once("engine/include/send_email.php");
                send_email::new_purchase($_GET["order_id"]);
            } 
        }
    }else engine::error();
}else engine::error();