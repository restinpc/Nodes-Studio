<?php
/**
* Email library.
* @path /engine/core/email.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @example <code> email::daily_report(); </code>
*/
class email{
//------------------------------------------------------------------------------
/**
* Generates HTML template for a message.
* 
* @param string $text Text of message.
* @return string Returns generated HTML of message to email.
*/
static function email_template($text){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "name"';
    $res = engine::mysql($query);
    $site_name = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email_image"';
    $res = engine::mysql($query);
    $site_image = mysql_fetch_array($res);
    $css = file_get_contents("template/email.css");
    if(empty($css)) $css = file_get_contents ($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/template/email.css');
    if($site_image["value"][0]=="/") $site_image["value"] = 
    $_SERVER["PUBLIC_URL"].$site_image["value"];
    $fout = '<style>'.$css.'</style>
    <div class="document">';
    if(!empty($site_image["value"])){
        $file = engine::curl_get_query($site_image["value"]);
        $image = base64_encode($file);
        $fout .= '<img src="data:image/png;base64,'.$image.'" alt="'.$site_name["value"].'" title="'.$site_name["value"].'" /><br/><br/>';
    }       
    $fout .= ' <p>'.$text.'</p><hr/>
    <center>'.lang("Thanks for using our service").' <a href="'.$_SERVER["PUBLIC_URL"].'/" target="_blank">'.$site_name["value"].'</a></center>
    </div>';
    return $fout;
}
//----------------------------------------------------
/**
* Sends a message to specified user.
* 
* @param array $data Array, based on @mysql[nodes_user_outbox].
*/
static function bulk_mail($data){
    $query = 'SELECT `id`,`name`,`email` FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_outbox` WHERE `id` = "'.$data["outbox_id"].'"';
    $res = engine::mysql($query);
    $outbox = mysql_fetch_array($res);
    if($outbox["action"]){
        $query = 'INSERT INTO `nodes_inbox`(`from`, `to`, `text`, `date`) '
            . 'VALUES("1", "'.intval($user["id"]).'", "'.$outbox["text"].'", "'.date("U").'")';
        engine::mysql($query);
        $caption = lang("New message at").' '.$_SERVER["HTTP_HOST"];
        $body = lang('Dear').' '.$user["name"].'!<br/><br/>
            Admin '.lang("sent a message for you").'!<br/>
            '.lang("For details, click").' <a href="'.$_SERVER["PUBLIC_URL"].'/account/inbox/1" target="_blank">'.lang("here").'</a>.';
        if(engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body))){
            $status = 1;
        }else{
            $status = $data["status"]-1;
        }
    }else{
        if(engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $outbox["caption"], email::email_template($outbox["text"]))){
            $status = 1;
        }else{
            $status = $data["status"]-1;
        }
    }
    $query = 'UPDATE `nodes_user_outbox` SET `status` = "'.$status.'", `date` = "'.date("U").'" WHERE `id` = "'.$data["id"].'"';
    engine::mysql($query);
}
//----------------------------------------------------
/**
* Sends a message with daily report to admin.
*/
static function daily_report(){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"'; 
    $r_email = engine::mysql($query);
    $d_email = mysql_fetch_array($r_email);
    $from = strtotime(date("Y-m-d")." 00:00:00");
    $to = strtotime(date("Y-m-d").' 23:59:59');
    $query = 'SELECT COUNT(`id`) FROM `nodes_attendance` WHERE `display` = 1 AND `date` >= "'.$from.'" AND `date` <= "'.$to.'"';
    $res = engine::mysql($query);
    $d = mysql_fetch_array($res);
    $views = round($d[0]);
    $query = 'SELECT COUNT(DISTINCT(`token`)) FROM `nodes_attendance` WHERE `display` = 1 AND `date` >= "'.$from.'" AND `date` <= "'.$to.'"';
    $res = engine::mysql($query);
    $d = mysql_fetch_array($res);
    $visitors = round($d[0]);
    $query = 'SELECT AVG(`script_time`) FROM `nodes_perfomance` WHERE `script_time` > 0 AND `date` >= "'.$from.'" AND `date` <= "'.$to.'"';
    $res = engine::mysql($query);
    $d = mysql_fetch_array($res);
    $perfomance = round($d[0],2);
    $file = engine::curl_get_query($_SERVER["PUBLIC_URL"].'/perfomance.php?interval=day&date='.date("Y-m-d"));
    $perfomance_image = base64_encode($file);
    $file = engine::curl_get_query($_SERVER["PUBLIC_URL"].'/attandance.php?interval=day&date='.date("Y-m-d"));
    $attandance_image = base64_encode($file);
    $caption = $_SERVER["HTTP_HOST"].' '.date("d/m/Y").' '.lang('daily report');
    $body = lang("Dear").' Admin!<br/><br/>
        '.lang("This is a daily report for the website traffic and performance on").' '.date("d/m/Y").'<br/><br/>
        <center>'.lang("Visitors").': <b>'.$visitors.'</b> &nbsp; '.lang("Views").': <b>'.$views.'</b><br/>
        <a href="'.$_SERVER["PUBLIC_URL"].'/admin?mode=attandance" target="_blank"><img src="data:image/png;base64,'.$attandance_image.'" alt="'.lang("Attendance").'"></a></center><br/><br/>
        <center>'.lang("Perfomance").': <b>'.$perfomance.'</b><br/>
        <a href="'.$_SERVER["PUBLIC_URL"].'/admin?mode=perfomance" target="_blank"><img src="data:image/png;base64,'.$perfomance_image.'" alt="'.lang("Perfomance").'"></a></center><br/><br/>';
    engine::send_mail($d_email["value"], "no-reply@".$_SERVER["HTTP_HOST"], 
            $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message after registration.
* 
* @param string $email User email.
* @param string $name User name.
*/
static function registration($email, $name){
    $caption = lang('Registration at').' '.$_SERVER["HTTP_HOST"];
    $body = lang('Dear').' '.$name.'!<br/><br/>'
            .lang('We are glad to confirm successful registration at').' '
            . '<a href="'.$_SERVER["PUBLIC_URL"].'/">'.$_SERVER["HTTP_HOST"].'</a>';
    engine::send_mail($email, "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message after request to restore password.
* 
* @param string $email User email.
* @param string $new_pass New password.
* @param string $code Confirmation code.
*/
static function restore_password($email, $new_pass, $code){
    $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $body = lang("Dear").' '.$user["name"].'!<br/><br/>'.
        lang("New password is")." <b>".$new_pass.'</b><br/>'
        . '<br/>'.lang("To confirm this password, use").
        ' <a href="'.$_SERVER["PUBLIC_URL"].'/account.php?mode=remember&email='.$email.'&code='.$code.'">'.lang("this link").'</a>';
    $caption = lang("New password for")." ".$_SERVER["HTTP_HOST"];
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message to admin when new comment is submited.
* 
* @param int $user_id @mysql[nodes_user]->id.
* @param string $url Page URL.
*/
static function new_comment($user_id, $url){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"'; 
    $r_email = engine::mysql($query);
    $d_email = mysql_fetch_array($r_email);
    $caption = lang("New comment at")." ".$_SERVER["HTTP_HOST"];
    $message = lang("User").' '.$_SESSION["user"]["name"].' '.lang("add new comment").'!<br/>'.
            lang("For details, click").' <a href="'.$_SERVER["PUBLIC_URL"].$url.'" target="_blank">'.lang("here").'</a>';
    engine::send_mail($d_email["value"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($message));
}
//----------------------------------------------------
/**
* Sends a message to user when account balance updated.
* 
* @param int $user_id @mysql[nodes_user]->id.
* @param double $amount Transaction sum.
*/
static function new_transaction($user_id, $amount){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $caption = lang("The funds have been added to your account balance").' '.$_SERVER["HTTP_HOST"];
    $body = lang('Dear').' '.$user["name"].'!<br/><br/>
        '.lang('The funds').' ( $'.$amount.' ) '.lang("has beed added to your account balance").'!<br/>
        '.lang("For details, click").' <a href="'.$_SERVER["PUBLIC_URL"].'/account/finance" target="_blank">'.lang("here").'</a>.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message to user when new message in chat.
* 
* @param int $user_id To user ID @mysql[nodes_user]->id.
* @param int $sender_id From user ID @mysql[nodes_user]->id.
*/
static function new_message($user_id, $sender_id){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$sender_id.'"';
    $res = engine::mysql($query);
    $sender = mysql_fetch_array($res);
    $caption = lang("New message at").' '.$_SERVER["HTTP_HOST"];
    $body = lang('Dear').' '.$user["name"].'!<br/><br/>
        '.lang("User").' '.$sender["name"].' '.lang("sent a message for you").'!<br/>
        '.lang("For details, click").' <a href="'.$_SERVER["PUBLIC_URL"].'/account/inbox/'.$sender["id"].'" target="_blank">'.lang("here").'</a>.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message to user and admin when new withdrawal request is created.
* 
* @param int $user_id @mysql[nodes_user]->id.
* @param double $amount Widthdrawal sum.
* @param string $paypal Receiver PayPal ID.
*/
static function new_withdrawal($user_id, $amount, $paypal){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
    $res = engine::mysql($query);
    $email = mysql_fetch_array($res);
    $caption = lang("Withdrawal request at")." ".$_SERVER["HTTP_HOST"];
    $body = lang('Dear').' '.$user["name"].'!<br/><br/>
        '.lang("You withdrawal request is pending now").'.<br/>
        '.lang("After some time you will receive").' $'.$amount.' '.lang("on your PayPal account").' <b>'.$paypal.'</b>.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
    $body =  lang("Dear").' Admin!<br/><br/>'
        . lang("There in new withdrawal request at").' '.$_SERVER["HTTP_HOST"].'.<br/>'
        . lang("Need to pay").' $'.$amount.' '.lang("on PayPal account").' <b>'.$paypal.'</b> '.lang("and confirm request").'.<br/>'
        . lang("Details").' <a target="_blank" href="'.$_SERVER["PUBLIC_URL"].'/admin/?mode=finance">'.lang("here").'</a>.';
    engine::send_mail($email["value"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
}
//----------------------------------------------------
/**
* Sends a message to user when withdrawal is comlplete.
* 
* @param int $user_id User ID @mysql[nodes_user]->id.
*/
static function finish_withdrawal($user_id){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $caption = lang("Withdrawal is complete at")." ".$_SERVER["HTTP_HOST"];
    $body = lang("Dear").' '.$user["name"].'!<br/><br/>
        '.lang("You withdrawal is complete").'!<br/>
        '.lang("Thanks for using our service and have a nice day").'.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));   
}
//----------------------------------------------------
/**
* Sends a message to user and admin when new order is created.
* 
* @param int $id Order ID @mysql[nodes_order]->id.
*/
static function new_purchase($id){
    $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$id.'"';
    $res = engine::mysql($query);
    $order = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$order["user_id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $caption = lang("New purchase at").' '.$_SERVER["HTTP_HOST"];
    $body = lang("Dear").' '.$user["name"].'!<br/><br/>
        '.lang("Congratulations on your purchase at").' '.$_SERVER["HTTP_HOST"].'.<br/>
        '.lang("You can see details of your purchases").' <a target="_blank" href="'.$_SERVER["PUBLIC_URL"].'/account/purchases">'.lang("here").'</a>.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
    $query = 'SELECT * FROM `nodes_transaction` WHERE `order_id` = "'.$order["id"].'"';
    $res = engine::mysql($query);
    $transaction = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
    $res = engine::mysql($query);
    $email = mysql_fetch_array($res);
    $caption = lang("New purchase at").' '.$_SERVER["HTTP_HOST"];
    $body = lang("Dear").' Admin!<br/><br/>'
            . lang("There in new purchase at").' '.$_SERVER["HTTP_HOST"].'. '
            . lang("Details").' <a target="_blank" href="'.$_SERVER["PUBLIC_URL"].'/admin/?mode=orders">'.lang("here").'</a>.';
    if($transaction["txt_id"]!="test_transaction"){
        $body .= '<br/>'.$user["name"].'</a> '.lang("make a payment").' $'.$transaction["amount"].' '.lang("to your PayPal account").'.';
    }
    engine::send_mail($email["value"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
    $query = 'INSERT INTO `nodes_inbox`(`from`, `to`, `text`, `date`, `system`) '
            . 'VALUES("'.$user["id"].'", "1", "The user makes a purchase", "'.date("U").'", "1")';
    engine::mysql($query);
}
//----------------------------------------------------
/**
* Sends a message to user when order is shipped.
* 
* @param int $id Order ID @mysql[nodes_order]->id.
*/
static function shipping_confirmation($id){
    $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($id).'"';
    $res = engine::mysql($query);
    $product_order = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$product_order["order_id"].'"';
    $res = engine::mysql($query);
    $order = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$product_order["product_id"].'"';
    $res = engine::mysql($query);
    $product = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$order["user_id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $caption = lang("Your order has been shipped at").' '.$_SERVER["HTTP_HOST"];
    $body = lang("Dear").' '.$user["name"].'!<br/><br/>
        '.lang("Your order").' "'.$product["title"].'" '.lang("has been shipped").'.<br/>
        '.lang("After receiving, please update purchase status").' <a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].'/account/purchases">'.lang("here").'</a>.';
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
    $query = 'INSERT INTO `nodes_inbox`(`from`, `to`, `text`, `date`, `system`) '
    . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$user["id"].'", "Order has been shipped", "'.date("U").'", "1")';
    engine::mysql($query);
}
//----------------------------------------------------
/**
* Sends a message to user and admin when order delivery is done.
* 
* @param int $id Order ID @mysql[nodes_order]->id.
*/
static function delivery_confirmation($id){
    $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($id).'"';
    $res = engine::mysql($query);
    $product = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$product["order_id"].'"';
    $res = engine::mysql($query);
    $order = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$product["product_id"].'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    $caption = lang("Your order has been completed at").' '.$_SERVER["HTTP_HOST"];
    $body = lang("Dear").' '.$user["name"].'!<br/><br/>
        '.lang("Your order has been completed").'! '; 
    if($user["id"]!="1"){
        $body .= '<br/>'.lang("Funds added to your account and available for withdrawal").' '
        . '<a target="_blank" href="http://'.$_SERVER["HTTP_HOST"].'/account/finances">'.lang("here").'</a>.';
    }
    engine::send_mail($user["email"], "no-reply@".$_SERVER["HTTP_HOST"], $caption, email::email_template($body));
    $query = 'INSERT INTO `nodes_inbox`(`from`, `to`, `text`, `date`, `system`) '
    . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$user["id"].'", "The user confirmed reception", "'.date("U").'", "1")';
    engine::mysql($query);
}
}