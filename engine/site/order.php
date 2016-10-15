<?php
/*
$this->title - Page title
$this->content - Page HTML data
$this->menu - Page HTML navigation
$this->keywords - Page meta keywords
$this->description - Page meta description
$this->img - Page meta image
$this->js - Page JavaScript code
$this->activejs - Page executable JavaScript code
$this->css - Page CSS data
$this->configs - Array MySQL configs
*/
//----------------------------------------------------

if(empty($_SESSION["user"]["id"])){
    
    $_SESSION["redirect"] = $_SERVER["DIR"]."/order";
    require_once("engine/site/register.php");
    return;
    
}else if(!empty($_POST["order_confirm"]) && !empty($_SESSION["user"]["id"])){

    $query = 'INSERT INTO `nodes_orders`(user_id, date, status) '
            . 'VALUES("'.$_SESSION["user"]["id"].'", "'.date("U").'", "0")';
    engine::mysql($query);
    $query = 'SELECT * FROM `nodes_orders` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    foreach($_SESSION["products"] as $key=>$value){
        if($value>0){
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$key.'"';
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
    $query = 'UPDATE `nodes_orders` SET `shipping` = "'.$shipment.'" WHERE `id` = "'.$_SESSION["order_confirm"].'"';
    engine::mysql($query);
    $query = 'INSERT INTO `nodes_transactions`(user_id, order_id, amount, status, date) '
    . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$_SESSION["order_confirm"].'", "0", "0", "'.date("U").'")';
    engine::mysql($query);
    $_SESSION["shipping_confirm"] = $data["id"];

}else if(!empty($_SESSION["order_confirm"]) && !empty($_SESSION["user"]["id"])){

    $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.intval($_SESSION["order_confirm"]).'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if($data["status"]){
        unset($_SESSION["order_confirm"]);
        unset($_SESSION["shipping_confirm"]);
        unset($_SESSION["products"]);
    }
    
}
$this->title = lang("Order products").' - '.$this->title; 
if(empty($_SESSION["order_confirm"])){
    
    $this->content .= '<h1>'.lang("Confirmation").'</h1><br/>
        '.lang("Please, confirm your order").'.<br/><br/>
        <div class="document">
        <form method="POST">
            <input type="hidden" name="order_confirm" value="1" />';

    $price = 0;
    foreach($_SESSION["products"] as $key=>$value){
        if($value>0){
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$key.'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $price += $product["price"];
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$product["shipping"].'"';
            $res = engine::mysql($query);
            $shipping = mysql_fetch_array($res);
            $images = explode(";", $product["img"]);
            $this->content .= '<div class="order_detail">
            <div style="float:left; width: 180px; height: 180px; background: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].') center no-repeat;background-size: cover; margin-right: 10px;">&nbsp;</div>
                <b style="font-size: 21px;">'.$product["title"].'</b><br/><br/>
                <font style="font-size: 18px;">$ '.$product["price"].'</font><br/><br/>
                '.lang("Shipping from").': <a title="'.$shipping["country"].', '.$shipping["state"].', '.$shipping["city"].', '.$shipping["street1"].', '.$shipping["street2"].' ">'.$shipping["country"].'</a><br/>
                <div style="padding-top: 10px; text-align:center; float:right;">
                    <input type="button" class="btn" style="margin: 5px; width: 170px; margin-top: 10px;" name="remove" value="'.lang("Remove product").'" onClick=\'remove_from_bin("'.$id.'");\' />
                </div>
            <div style="clear:both;"></div>
            </div>';
        }
    }if(!$price){
        $this->content .= '<br/>'.lang("Sorry, there is no products").'<br/><br/><br/>';
    }else{
        $this->content .= '<br/>'
                . '<div style="text-align:right;"><h6>'.lang("Total price").': $'.$price.'</h6></div>'
                . '<br/><br/>'
                . '<input type="submit" class="btn" value="'.lang("Next").'" style="width: 280px;" />';
    }
    $this->content .= '
        </form>
        </div>
        <br/><br/>
        ';
        
}else if(empty($_SESSION["shipping_confirm"])){
    
    $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);

    $this->content .= '<h1>'.lang("Shipping").'</h1><br/>
    '.lang("Please, confirm your shipping address").'<br/><br/><br/>
    <form method="POST">
        <input type="hidden" name="shipping_confirm" value="1" />
        <input type="text" class="input" placeHolder="'.lang("First name").'" style="width: 280px;" name="fname" required value="'.$data["fname"].'" /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Last name").'" style="width: 280px;" name="lname" required value="'.$data["lname"].'" /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Country").'" id="country_selector" style="width: 280px;"  name="country" required value="'.$data["country"].'"  /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("State").'" style="width: 280px;" name="state" value="'.$data["state"].'" required /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("City").'" style="width: 280px;" name="city" required value="'.$data["city"].'"  /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Zip code").'" style="width: 280px;" name="zip" required value="'.$data["zip"].'"  /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Street").' 1" style="width: 280px;" name="street1" required value="'.$data["street1"].'"  /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Street").' 2" style="width: 280px;" name="street2" value="'.$data["street2"].'"  /><br/><br/>
        <input type="text" class="input" placeHolder="'.lang("Phone number").'" style="width: 280px;" name="phone" required value="'.$data["phone"].'"  /><br/><br/>
        <input type="submit" class="btn" value="'.lang("Next").'" style="width: 280px;" />
    </form><br/><br/>'; 
    $this->activejs .= '
            jQuery("#country_selector").countrySelect({
                    defaultCountry: "us"
            });
        ';
}else{
    
    $this->content .= '
        <div class="document">
        <h1>'.lang("Payment").'</h1><br/>';
            
    $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$_SESSION["order_confirm"].'"';
    $res = engine::mysql($query);
    $price = 0;
    $products = '<div style="text-align:left; float:left; width: 290px; border: 0px solid; padding:10px;">
        <b>'.lang("Order").'</b><br/><br/>';
    while($data = mysql_fetch_array($res)){
        if($data["count"]>0){
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$data["product_id"].'"';
            $r = engine::mysql($query);
            $product = mysql_fetch_array($r);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$product["shipping"].'"';
            $r = engine::mysql($query);
            $shipping = mysql_fetch_array($r);
            $images = explode(";", $product["img"]);
            $products .= '<div class="order_detail">
            <div style="float:left; width: 100px; height: 100px; background: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].') center no-repeat;background-size: cover; margin-right: 10px;">&nbsp;</div>
                <b style="font-size: 21px;">'.$product["title"].'</b><br/><br/>
                <font style="font-size: 18px;">$ '.$product["price"].'</font><br/>
            <div style="clear:both;"></div>
            </div>';
            $price += $data["price"];
        }
    }$products .= '</div>';
    
    $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $shipping = '<div style="text-align:left; float:left; width: 290px; border: 0px solid; padding: 10px;">
        <b>'.lang("Shipping").'</b><br/><br/>
        '.lang("First name").': '.$data["fname"].'<br/><br/>
        '.lang("Last name").': '.$data["lname"].'<br/><br/>
        '.lang("Country").': '.$data["country"].'<br/><br/>
        '.lang("State").': '.$data["state"].'<br/><br/>
        '.lang("City").': '.$data["city"].'<br/><br/>
        '.lang("Zip code").': '.$data["zip"].'<br/><br/>
        '.lang("Street").': '.$data["street1"].'<br/>'
            .$data["street2"].'<br/><br/>
            </div>
        ';
    
    $this->content .= $shipping.$products;
    
    $this->content .= '<div style="clear:both;"><br/></div>
        <h6>'.lang("Total price").': $'.$price.'</h6><br/><br/>';
    
    if($this->configs["sandbox"]){
        $this->content .= '
            <button class="btn" style="width: 280px;" onClick=\'process_payment("'.$_SESSION["order_confirm"].'","'.$price.'");\'>'.lang("Process payment").'</button><br/><br/>
            ';
    }else{
        if($this->configs["paypal_test"]) $domain = 'www.sandbox.paypal.com';
        else $domain = 'www.paypal.com';
        $this->content .= '
            <form action="https://'.$domain.'/cgi-bin/webscr" method="post">			
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="'.$this->configs["paypal_id"].'">
            <input type="hidden" name="item_name" value="'.$this->configs["paypal_description"].'">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="amount" value="'.$price.'">
            <input type="hidden" name="cancel_return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/order">
            <input type="hidden" name="return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/purchases">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="notify_url" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/paypal.php?order_id='.$_SESSION["order_confirm"].'">
            <button type="submit" class="btn" style="width: 280px;">PayPal</button><br/><br/>
            </form>';
    }
    $this->content .= '</div>';
}