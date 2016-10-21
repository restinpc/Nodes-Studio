<?php
require_once("engine/nodes/language.php");
class send_email{
    static function email_template($text){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "image"';
        $res = engine::mysql($query);
        $site_image = mysql_fetch_array($res);
        $file = file_get_contents($site_image["value"]);
        if(empty($file)) $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].$site_image["value"]);
        $image = base64_encode($file);
        $fout = '<!DOCTYPE html>
<html lang="'.$_SESSION["Lang"].'">
<head>
<style>
html, body, div, a, img{
    margin: 0;
    padding: 0;
    border: 0;
    outline: 0;
    vertical-align: baseline;
}
*, *:before, *:after {
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
a{
    text-decoration: none; 
    color: #4473ba;
}
html{
    line-height: 1.0;
    min-width: 280px;
    padding: 10px;
}
body{
    line-height: 1;
    font-family: Tahoma;
    -webkit-font-smoothing: antialiased;
    text-align: center;
    overflow-x: hidden;
    min-width: 280px;
    min-height: 280px;
    color: #111;
}
.document{
    width: 100%; 
    max-width: 700px; 
    text-align:center; 
    font-size: 18px; 
    margin: 0px auto;
}
.document img{
    margin: 10px; 
    max-width: 220px;
}
.document hr{
    color: #eee; 
    border-top: #eee 1px solid;
}
.document span{
    font-size: 18px;
    font-family: Tahoma;
}
.document p{
    text-align:left;
    line-height: 1.8;
}
</style>
</head>
<body>
<div class="document">
    <img width=100% src="data:image/png;base64,'.$image.'" /><br/><br/>
    <p>'.$text.'</p>
    <br/><br/><hr/><br/>
    <span><a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/" target="_blank">'.$_SERVER["HTTP_HOST"].'</a>, '.date("Y").'.</span></center><br/>
</div>
</body>
</html>';
        return $fout;
    }
    //----------------------------------------------------
    static function registration($email, $name){
        $caption = lang('Registration at').' '.$_SERVER["HTTP_HOST"];
        $body = lang('Dear').' '.$name.'!<br/><br/>'
                .lang('We are glad to confirm successful registration at').' <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/">'.$_SERVER["HTTP_HOST"].'</a>';
        engine::send_mail($email, "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
    }
    //----------------------------------------------------
    static function new_comment($user_id, $url){
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$user_id.'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"'; 
        $r_email = engine::mysql($query);
        $d_email = mysql_fetch_array($r_email);
        $message = lang("User").' '.$_SESSION["user"]["name"].' '.lang("add new comment").'!<br/>'
                . '<a href="//'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].$url.'" target="_blank">'.$url.'</a><br/>'
                . '<br/>'.lang("Comment").'<br/>-----------------------------<br/>'.$text;
        engine::send_mail($d_email["value"], "no-reply@".$_SERVER["HTTP_HOST"], 
                lang("New comment at")." ".$_SERVER["HTTP_HOST"], send_email::email_template($message));
    }
    //----------------------------------------------------
    static function new_message($user_id, $sender_id){
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$user_id.'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$sender_id.'"';
        $res = engine::mysql($query);
        $sender = mysql_fetch_array($res);
        $caption = lang("New message at").' '.$_SERVER["HTTP_HOST"];
        $body = lang('Dear').' '.$user["name"].'!<br/><br/>
            '.lang("User").' '.$sender["name"].' '.lang("sent a message for you").'!<br/>
            '.lang("For details, click").' <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account/inbox/'.$sender["id"].'" target="_blank">'.lang("here").'</a>.';
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
    }
    //----------------------------------------------------
    static function new_withdrawal($user_id, $amount, $paypal){
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$user_id.'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
        $res = engine::mysql($query);
        $email = mysql_fetch_array($res);
        $caption = lang("Withdrawal request at")." ".$_SERVER["HTTP_HOST"];
        $body = lang('Dear').' '.$user["name"].'!<br/><br/>
            '.lang("You withdrawal request is pending now").'.<br/>
            '.lang("After some time you will receive").' $'.$amount.' '.lang("on your PayPal account").' <b>'.$paypal.'</b>.';
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
        $body =  lang("Dear").' Admin!<br/><br/>'
                . lang("There in new withdrawal request at").' '.$_SERVER["HTTP_HOST"].'.<br/>'
                . lang("Need to pay").' $'.$amount.' '.lang("on PayPal account").' <b>'.$paypal.'</b> '.lang("and confirm request").'.<br/>'
                . lang("Details").' <a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/admin/?mode=finance">'.lang("here").'</a>.';
        engine::send_mail($email["value"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
    }
    //----------------------------------------------------
    static function finish_withdrawal($user_id){
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$user_id.'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $caption = lang("Withdrawal is complete at")." ".$_SERVER["HTTP_HOST"];
        $body = lang("Dear").' '.$user["name"].'!<br/><br/>
            '.lang("You withdrawal is complete").'!<br/>
            '.lang("Thanks for using our service and have a nice day").'.';
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));   
    }
    //----------------------------------------------------
    static function new_purchase($id){
        $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.$id.'"';
        $res = engine::mysql($query);
        $order = mysql_fetch_array($res);
        /*
        $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$order["id"].'"';
        $res = engine::mysql($query);
        $sellers = array();
        while($data = mysql_fetch_array($res)){
            if(!in_array($data, $sellers)) array_push($sellers, $data);
        }foreach($sellers as $seller){
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$seller["product_id"].'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$product["user_id"].'"';
            $res = engine::mysql($query);
            $user = mysql_fetch_array($res);
            $caption = 'New order at '.$_SERVER["HTTP_HOST"];
            $body = 'Dear '.$user["name"].'!<br/><br/>
                You have new order at '.$_SERVER["HTTP_HOST"].'<br/>
                Now you should make a shipment, than update order status <a  target="_blanl" href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account/orders">here</a>.';
            engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, $body);  
        }
         */
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$order["user_id"].'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $caption = lang("New purchase at").' '.$_SERVER["HTTP_HOST"];
        $body = lang("Dear").' '.$user["name"].'!<br/><br/>
            '.lang("Congratulations on your purchase at").' '.$_SERVER["HTTP_HOST"].'.<br/>
            '.lang("You can see details of your purchases").' <a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account/purchases">'.lang("here").'</a>.';
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
        $query = 'SELECT * FROM `nodes_transactions` WHERE `order_id` = "'.$order["id"].'"';
        $res = engine::mysql($query);
        $transaction = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
        $res = engine::mysql($query);
        $email = mysql_fetch_array($res);
        $caption = lang("New purchase at").' '.$_SERVER["HTTP_HOST"];
        $body = lang("Dear").' Admin!<br/><br/>'
                . lang("There in new purchase at").' '.$_SERVER["HTTP_HOST"].'. '
                . lang("Details").' <a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/admin/?mode=orders">'.lang("here").'</a>.';
        if($transaction["txt_id"]!="test_transaction"){
            $body .= '<br/>'.$user["name"].'</a> '.lang("make a payment").' $'.$transaction["amount"].' '.lang("to your PayPal account").'.';
        }
        engine::send_mail($email["value"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
        $query = 'INSERT INTO `nodes_message`(`from`, `to`, `text`, `date`, `system`) '
                . 'VALUES("'.$user["id"].'", "1", "The user makes a purchase", "'.date("U").'", "1")';
        engine::mysql($query);
    }
    //----------------------------------------------------
    static function shipping_confirmation($id){
        $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($id).'"';
        $res = engine::mysql($query);
        $product_order = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.$product_order["order_id"].'"';
        $res = engine::mysql($query);
        $order = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$product_order["product_id"].'"';
        $res = engine::mysql($query);
        $product = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$order["user_id"].'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $caption = lang("Your order has been shipped at").' '.$_SERVER["HTTP_HOST"];
        $body = lang("Dear").' '.$user["name"].'!<br/><br/>
            '.lang("Your order").' "'.$product["title"].'" '.lang("has been shipped").'.<br/>
            '.lang("After receiving, please update purchase status").' <a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].'/account/purchases">'.lang("here").'</a>.';
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
        $query = 'INSERT INTO `nodes_message`(`from`, `to`, `text`, `date`, `system`) '
        . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$user["id"].'", "Order has been shipped", "'.date("U").'", "1")';
        engine::mysql($query);
    }
    //----------------------------------------------------
    static function delivery_confirmation($id){
        $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($id).'"';
        $res = engine::mysql($query);
        $product = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.$product["order_id"].'"';
        $res = engine::mysql($query);
        $order = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$product["product_id"].'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$data["user_id"].'"';
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
        $caption = lang("Your order has been completed at").' '.$_SERVER["HTTP_HOST"];
        $body = lang("Dear").' '.$user["name"].'!<br/><br/>
            '.lang("Your order has been completed").'! '; 
        if($user["id"]!="1"){
            $body .= '<br/>'.lang("Funds added to your account and available for withdrawal").' '
            . '<a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].'/account/finances">'.lang("here").'</a>.';
        }
        engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, send_email::email_template($body));
        $query = 'INSERT INTO `nodes_message`(`from`, `to`, `text`, `date`, `system`) '
        . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$user["id"].'", "The user confirmed reception", "'.date("U").'", "1")';
        engine::mysql($query);
    }
}