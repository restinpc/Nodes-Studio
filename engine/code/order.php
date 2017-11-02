<?php
/**
* Product purchase processor.
* @path /engine/code/order.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/language.php");
echo '<!DOCTYPE html><html><body class="nodes">';
if(empty($_SESSION["user"]["id"])){  
    $_SESSION["redirect"] = $_SERVER["DIR"]."/order.php";
    require_once("engine/nodes/site.php");
    echo '<div class="fs21 tal"><b>Step 1 \ 4</b></div><br/>';
    $_GET[0] = "register";
    $_POST["jQuery"] = 1;
    $site = new site(1);
}else if(!empty($_POST["order_confirm"]) && !empty($_SESSION["user"]["id"])){  
    $query = 'INSERT INTO `nodes_order`(user_id, date, status) '
            . 'VALUES("'.$_SESSION["user"]["id"].'", "'.date("U").'", "0")';
    engine::mysql($query);
    $query = 'SELECT * FROM `nodes_order` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    foreach($_SESSION["products"] as $key=>$value){
        if($value>0){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$key.'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $query = 'INSERT INTO `nodes_product_order`(product_id, order_id, price, count, status, date) '
            . 'VALUES("'.$key.'", "'.$data["id"].'", "'.$d["price"].'", "'.$value.'", "0", "'.date("U").'")';
            engine::mysql($query);
        }
    }$_SESSION["order_confirm"] = $data["id"];
}else if(!empty($_POST["shipping_confirm"]) && !empty($_SESSION["user"]["id"])){
    $fname = htmlspecialchars($_POST["fname"]);
    $lname = htmlspecialchars($_POST["lname"]);
    $country = htmlspecialchars($_POST["country"]);
    $state = htmlspecialchars($_POST["state"]);
    $city = htmlspecialchars($_POST["city"]);
    $zip = htmlspecialchars($_POST["zip"]);
    $street1 = htmlspecialchars($_POST["street1"]);
    $street2 = htmlspecialchars($_POST["street2"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $query = 'SELECT * FROM `nodes_shipping` WHERE'
            . ' `user_id` = "'.$_SESSION["user"]["id"].'" AND'
            . ' `fname` = "'.$fname.'" AND'
            . ' `lname` = "'.$lname.'" AND'
            . ' `country` = "'.$country.'" AND'
            . ' `state` = "'.$state.'" AND'
            . ' `city` = "'.$city.'" AND'
            . ' `zip` = "'.$zip.'" AND'
            . ' `street1` = "'.$street1.'" AND'
            . ' `street2` = "'.$street2.'" AND'
            . ' `phone` = "'.$phone.'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data)){    
        $shipment = intval($data["id"]);
    }else{
        $query = 'INSERT INTO `nodes_shipping`(user_id, fname, lname, country, state, city, zip, street1, street2, phone) '
                . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$fname.'", "'.$lname.'", "'.$country.'", "'.$state.'", "'.$city.'", "'.$zip.'", "'.$street1.'", "'.$street2.'", "'.$phone.'")';
        engine::mysql($query);  
        $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $shipment = $data["id"];
    }
    $query = 'UPDATE `nodes_order` SET `shipping` = "'.$shipment.'" WHERE `id` = "'.$_SESSION["order_confirm"].'"';
    engine::mysql($query);
    $query = 'INSERT INTO `nodes_transaction`(user_id, order_id, amount, status, date) '
    . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$_SESSION["order_confirm"].'", "0", "0", "'.date("U").'")';
    engine::mysql($query);
    $_SESSION["shipping_confirm"] = $data["id"];
}else if(!empty($_SESSION["order_confirm"]) && !empty($_SESSION["user"]["id"])){
    $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.intval($_SESSION["order_confirm"]).'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if($data["status"]){
        unset($_SESSION["order_confirm"]);
        unset($_SESSION["shipping_confirm"]);
        unset($_SESSION["products"]);
    }
}
if(empty($_SESSION["order_confirm"]) && !empty($_SESSION["user"]["id"])){
    echo '<div class="fs21 tal"><b>Step 2 \ 4</b></div><br/>';
    $fout .= '<h1>'.lang("Confirmation").'</h1><br/><br/>
        <div class="document">
        <form method="POST">
            <input type="hidden" name="order_confirm" value="1" />';
    $price = 0;
    foreach($_SESSION["products"] as $key=>$value){
        if($value>0){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$key.'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $price += $product["price"];
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$product["shipping"].'"';
            $res = engine::mysql($query);
            $shipping = mysql_fetch_array($res);
            $images = explode(";", $product["img"]);
            $fout .= '<div class="order_detail">
            <div class="order_detail_image" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
                <b class="fs21">'.$product["title"].'</b><br/><br/>
                <font class="fs18">$ '.$product["price"].'</font><br/><br/>
                '.lang("Shipping from").': <a title="'.$shipping["country"].', '.$shipping["state"].', '.$shipping["city"].', '.$shipping["street1"].', '.$shipping["street2"].' ">'.$shipping["country"].'</a><br/>
                <div class="order_detail_button">
                    <input type="button" class="btn small w150" name="remove" value="'.lang("Remove product").'" onClick=\'if(confirm("'.lang("Are you sure?").'"))remove_from_bin("'.$key.'");\' />
                </div>
            <div class="clear"></div>
            </div>';
        }
    }if(!$price){
        $fout .= '<br/>'.lang("Your cart is empty").'<br/><br/><br/>';
    }else{
        $fout .= '<br/>'
            . '<div class="tar"><b class="fs21">'.lang("Total price").': $'.$price.'</b></div>'
            . '<br/><br/>'
            . '<input type="submit" class="btn w280" value="'.lang("Next").'" />';
    }
    $fout .= '
        </form>
        </div>
        <br/><br/>';
}else if(empty($_SESSION["shipping_confirm"]) && !empty($_SESSION["user"]["id"])){
    echo '<div class="fs21 tal"><b>Step 3 \ 4</b></div><br/>';
    $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $fout .= '<h1>'.lang("Shipping").'</h1><br/><br/>
    <form method="POST">
        <input type="hidden" name="shipping_confirm" value="1" />
        <input type="text" class="input w280" placeHolder="'.lang("First name").'" name="fname" required value="'.$data["fname"].'" /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Last name").'" name="lname" required value="'.$data["lname"].'" /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Country").'" id="country_selector" name="country" required value="'.$data["country"].'"  /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("State").'" name="state" value="'.$data["state"].'" required /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("City").'" name="city" required value="'.$data["city"].'"  /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Zip code").'" name="zip" required value="'.$data["zip"].'"  /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Street").' 1" name="street1" required value="'.$data["street1"].'"  /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Street").' 2" name="street2" value="'.$data["street2"].'"  /><br/><br/>
        <input type="text" class="input w280" placeHolder="'.lang("Phone number").'" name="phone" required value="'.$data["phone"].'"  /><br/><br/>
        <input type="submit" class="btn w280" value="'.lang("Next").'" />
    </form><br/><br/>';
}else if( !empty($_SESSION["user"]["id"])){
    echo '<div class="fs21 tal"><b>Step 4 \ 4</b></div><br/>';
    $fout .= '<div class="document">
        <h1>'.lang("Payment").'</h1><br/>';       
    $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$_SESSION["order_confirm"].'"';
    $res = engine::mysql($query);
    $price = 0;
    $products = '<div class="order_detail_left">
        <b>'.lang("Order").'</b><br/><br/>';
    while($data = mysql_fetch_array($res)){
        if($data["count"]>0){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$data["product_id"].'"';
            $r = engine::mysql($query);
            $product = mysql_fetch_array($r);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$product["shipping"].'"';
            $r = engine::mysql($query);
            $shipping = mysql_fetch_array($r);
            $images = explode(";", $product["img"]);
            $products .= '<div class="order_detail">
            <div class="order_detail_preview" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
                <b class="fs21">'.$product["title"].'</b><br/><br/>
                <font class="fs18">$ '.$product["price"].'</font><br/>
            <div class="clear"></div>
            </div>';
            $price += $data["price"];
        }
    }$products .= '</div>';
    $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $shipping = '<div class="order_detail_shipping">
        <b>'.lang("Shipping").'</b><br/><br/>
        '.lang("First name").': '.$data["fname"].'<br/><br/>
        '.lang("Last name").': '.$data["lname"].'<br/><br/>
        '.lang("Country").': '.$data["country"].'<br/><br/>
        '.lang("State").': '.$data["state"].'<br/><br/>
        '.lang("City").': '.$data["city"].'<br/><br/>
        '.lang("Zip code").': '.$data["zip"].'<br/><br/>
        '.lang("Street").': '.$data["street1"].'<br/>'
        .$data["street2"].'<br/><br/>
        </div>';
    $fout .= $shipping.$products;
    $fout .= '<div class="clear"><br/></div>
        <h6>'.lang("Total price").': $'.$price.'</h6><br/><br/>';
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "sandbox"';
    $res = engine::mysql($query);
    $configs = mysql_fetch_array($res);
    if($configs["value"]){
        $fout .= '
            <button class="btn w280" onClick=\'process_payment("'.$_SESSION["order_confirm"].'","'.$price.'");\'>'.lang("Process payment").'</button><br/><br/>
            ';
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "paypal_test"';
        $res = engine::mysql($query);
        $configs = mysql_fetch_array($res);
        if($configs["value"]) $domain = 'www.sandbox.paypal.com';
        else $domain = 'www.paypal.com';       
        $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "paypal_id"';
        $res = engine::mysql($query);
        $paypal_id = mysql_fetch_array($res);
        $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "paypal_description"';
        $res = engine::mysql($query);
        $paypal_desc = mysql_fetch_array($res);
        $fout .= '
            <form action="https://'.$domain.'/cgi-bin/webscr" method="post" target="top">			
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="'.$paypal_id["value"].'">
            <input type="hidden" name="item_name" value="'.$paypal_desc["value"].'">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="amount" value="'.$price.'">
            <input type="hidden" name="cancel_return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/order.php">
            <input type="hidden" name="return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/purchases">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="notify_url" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/paypal.php?order_id='.$_SESSION["order_confirm"].'">
            <button type="submit" class="btn w280">PayPal</button><br/><br/>
            </form>';
    }
    $fout .= '</div>';
}
$fout .= '<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css" />
<link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var root_dir = "'.$_SERVER["DIR"].'";</script>
<script src="'.$_SERVER["DIR"].'/script/jquery.js" type="text/javascript"></script>
<script src="'.$_SERVER["DIR"].'/script/script.js" type="text/javascript"></script>
<script src="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.js" type="text/javascript"></script>
<script>jQuery("#country_selector").countrySelect({  defaultCountry: "us" })</script>
</body><script>document.body.style.opacity = "1";</script></html>';
echo $fout;