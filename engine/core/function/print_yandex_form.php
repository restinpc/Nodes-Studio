<?php
/**
* Prints Yandex payment form.
* @path /engine/core/function/print_yandex_form.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param int $invoice_id @mysql[nodes_invoice->id].
* @param double $sum Amount to pay via PayPal.
* @param string $return URL for redirection after payment.
* @param bool $autopay Autosubmit form flag.
* @return string Returns content of block on success, or die with error.
* @usage <code> 
*   $return = $_SERVER["PROTOCOL"]."://".$_SERVER["HTTP_HOST"].$_SERVER["DIR]."/";
*   engine::print_yandex_form(1, 100, $return); 
* </code>
*/
function print_yandex_form($invoice_id, $sum, $return, $autopay=0){
    if(empty($_SESSION["user"]["email"])) return engine::error(401);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "yandex_money"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $id = $data["value"];
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "payment_description"';
    $res = engine::mysql($query);
    $paypal = mysqli_fetch_array($res);
    $paypal_desc = $paypal["value"];
    if(strpos("http", $return) != 0){
        $return = $_SERVER["PROTOCOL"].'://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].$return;
    }
    $fout .= '
        <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" id="yandex_form" target="_top">    
            <input type="hidden" name="receiver" value="'.$id.'">    
            <input type="hidden" name="formcomment" value="'.$paypal_desc.'">    
            <input type="hidden" name="short-dest" value="'.$paypal_desc.'">    
            <input type="hidden" name="label" value="'.$invoice_id.'">   
            <input type="hidden" name="targets" value="'.$invoice_id.'">
            <input type="hidden" name="quickpay-form" value="shop">    
            <input type="hidden" name="sum" value="'.intval($sum).'">     
            <input type="hidden" name="need-fio" value="false">    
            <input type="hidden" name="need-email" value="false">    
            <input type="hidden" name="need-phone" value="false">    
            <input type="hidden" name="need-address" value="false"> 
            <input type="hidden" name="successURL" value="'.$return.'" />
            <button vr-control id="yandex-button-payment" type="submit" class="btn w280">'.lang("Make a payment").'</button>
        </form>';
    if($autopay){
        $fout.= '<script>document.getElementById("yandex_form").submit();</script>';
    }
    return $fout;
}