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

// TODO - Your code here
//----------------------------

if(!empty($_GET[3])){
    $this->content = engine::error();
    return; 
}

if(!empty($_SESSION["user"]["id"])){
    $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    if(!$user["confirm"]){
        if(!empty($_POST["code"])){
            if($_POST["code"]==$user["code"]){
                $query = 'UPDATE `nodes_users` SET `confirm` = 1 WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
                die('<script>window.location = "'.$_SERVER["DIR"].'/account";</script>');
            }else{
                $this->content .= '<script>alert("'.lang("Error").'. '.lang("Invalid confirmation code").'");</script>';
            }
        }
        $this->title .= ' - '.lang("Account confirmation");
        $this->content .= '<h3>'.lang("Account confirmation").'</h3><br/><br/>'
                . '<form method="POST">'
                . '<input type="text" class="input w280" required name="code" placeHolder="'.lang("Confirmation code").'" />'
                . '<br/><br/>'
                . '<input type="submit" class="btn w280" value="'.lang("Submit").'" />'
                . '</form>';
        return;
    }
    if(!empty($_GET[1])){
        if($_GET[1] == "settings"){
            if(!empty($_POST["name"])){
                $name = mysql_real_escape_string($_POST["name"]);
                $email = strtolower(mysql_real_escape_string($_POST["email"]));
                $query = 'UPDATE `nodes_users` SET `name` = "'.$name.'", `email` = "'.$email.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
                $_SESSION["user"]["name"] = $name;
                $_SESSION["user"]["email"] = $email;
                if(!empty($_FILES["img"]["tmp_name"])){
                    $file = engine::upload_photo("img", "img/pic", 100, 100);
                    if($file != "error"){
                        $query = 'UPDATE `nodes_users` SET `photo` = "'.$file.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                        engine::mysql($query);
                        $_SESSION["user"]["photo"] = $file; 
                    }
                }
            }if(!empty($_POST["pass"])){
                $password = md5(trim($_POST["pass"])); 
                $query = 'UPDATE `nodes_users` SET `pass` = "'.$password.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
            }
            $this->title = lang("Settings").' - '.$this->title;
            $this->content = '<h1>'.lang("Settings").'</h1>';
            if(empty($_SESSION["user"]["email"])){
                $this->content .= '<p>'.lang("Enter your email and password to continue").'</p>';
            }
            $this->content .= '<br/><form method="POST" enctype="multipart/form-data">
                <div class="settings_block">
                <table>
                <tr>
                    <td class="settings_pic" align=right><img src="'.$_SERVER["DIR"].'/img/pic/'.$_SESSION["user"]["photo"].'" width=50 /></td>
                    <td valign=top><div class="settings_upload">'.lang("Change picture").':<br/><input type="file" name="img" class="input" /></div></td>
                </tr>

                <tr>
                    <td align=right class="settings_caption">'.lang("Name").':</td>
                    <td class="pb10"><input type="text" name="name" value="'.$_SESSION["user"]["name"].'" class="input w200" /></td>
                </tr>';

            if(!empty($_SESSION["user"]["email"])){
                $this->content .= '
                <tr>
                    <td align=right class="settings_caption">'.lang("Email").':</td>
                    <td class="pb10"><input type="text" name="email" value="'.$_SESSION["user"]["email"].'" class="input w200" /></td>
                </tr>
                <tr>
                    <td align=right class="settings_caption">'.lang("Password").':</td>
                    <td class="pb10"><input type="password" name="pass" value="" placeHolder="'.lang("New password").'" class="input w200" /></td>
                </tr>';
            }else{
                $this->content .= '
                <tr>
                    <td align=right class="settings_caption">'.lang("Email").':</td>
                    <td class="pb10"><input required type="text" name="email" placeHolder="'.lang("Enter your email").'" class="input w200" /></td>
                </tr>
                <tr>
                    <td align=right class="settings_caption">'.lang("Password").':</td>
                    <td class="pb10"><input required type="password" name="pass" value="" placeHolder="'.lang("Enter your password").'" class="input w200" /></td>
                </tr>'; 
            }
            $this->content .= '
            <tr>
            ';

if(empty($_SESSION["user"]["url"])){

    $this->content .= '<td colspan=2 class="p5">';

    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "vk_id"';
    $res = engine::mysql($query);
    $vk = mysql_fetch_array($res);

    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "fb_id"';
    $res = engine::mysql($query);
    $fb_id = mysql_fetch_array($res);

    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "tw_key"';
    $res = engine::mysql($query);
    $tw_key = mysql_fetch_array($res);

    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "gp_id"';
    $res = engine::mysql($query);
    $gp_id = mysql_fetch_array($res);

    if(!empty($fb_id["value"])||
            !empty($tw_key["value"])||
            !empty($gp_id["value"])||
            !empty($vk["value"])){

    $this->content .= '<div class="settings_social">'.lang("Connect with social network").'<br/><br/>';
    if(!empty($fb_id["value"])) $this->content .= '<a rel="nofollow" target="_parent"  href="'.$_SERVER["DIR"].'/account.php?mode=social&method=fb" class="settings_fb"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" title="Facebook"/></a>';
    if(!empty($tw_key["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=tw" class="m15"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" title="Twitter"/></a>';
    if(!empty($gp_id["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=gp" class="m15"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" title="Google+"/></a>';
    if(!empty($vk["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="https://oauth.vk.com/authorize?client_id='.$vk["value"].'&scope=notify&redirect_uri='.  urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account.php?mode=social&method=vk').'&display=page&response_type=token" class="settings_vk"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" title="VK"/></a>';
    $this->content .= '</div>';

            }

}else{

    $this->content .= ' <td align=right  class="settings_caption">'.lang("Site").':</td>
        <td align=left class="pl7"><div class="settings_url"><a href="'.$_SESSION["user"]["url"].'" target="_blank">'.str_replace('/', ' / ', str_replace("http://", '', $_SESSION["user"]["url"])).'</a></div><br/>';

}

$this->content .= '<br/>
                    </td>
                </tr>
                <tr>
                    <td class="pt20" colspan=2>
                        <input type="submit" class="btn w280" value="'.lang("Save changes").'" /><br/><br/>
                        <a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn w280" value="'.lang("Back to account").'"  /></a><br/><br/>
                    </td>
                </tr>
                </table>
                </div>
                </form>';

        }else if($_GET[1]=="confirm" && !empty($_GET[2])){
            
            $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($_GET[2]).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            
            if(!empty($data)){
                
                $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$data["product_id"].'"';
                $r = engine::mysql($query);
                $product = mysql_fetch_array($r);
                $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.$data["order_id"].'"';
                $r = engine::mysql($query);
                $order = mysql_fetch_array($r);
                
                if($order["user_id"] != $_SESSION["user"]["id"]){
                    
                    $this->title = lang("Access denied").' - '.$this->title;
                    $this->content = '<script language="JavaScript">parent.window.location = "'.$_SERVER["DIR"].'/account";</script>';
                    return;
                    
                }else if(!empty($_POST["rating"])){
                    
                    $comment = htmlspecialchars($_POST["comment"]);
                    $rating = intval($_POST["rating"]);
                    
                    $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$product["user_id"].'"';
                    $r = engine::mysql($query);
                    $seller = mysql_fetch_array($r);
                    
                    if(!empty($_POST["comment"])){
                        $url = '/product/'.$product["id"];
                        $url = trim(str_replace('"', "'", urldecode($url)));
                        $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
                        $text = str_replace("\n", "<br/>", $text);
                        $query = 'SELECT * FROM `nodes_comments` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$url.'" AND `user_id` = "'.$_SESSION["user"]["id"].'"';

                        $res = engine::mysql($query);
                        $d = mysql_fetch_array($res);
                        if(empty($d) && intval($_SESSION["user"]["id"]>0)){
                            $query = 'INSERT INTO `nodes_comments` (`url`, `reply`, `user_id`, `text`, `date`) '
                            . 'VALUES("'.$url.'", "'.intval($_POST["reply"]).'", "'.$_SESSION["user"]["id"].'", "'.$text.'", "'.date("U").'")';
                            engine::mysql($query); 
                        }
                    }
                    $query = 'UPDATE `nodes_products` SET `rating` = "'.($product["rating"]+$_POST["rating"]).'", '
                            . '`votes` = "'.($product["votes"]+1).'" WHERE `id` = "'.$product["id"].'"';
                    engine::mysql($query);

                    require_once("engine/include/send_email.php");
                    send_email::delivery_confirmation($data["id"]);

                    $query = 'UPDATE `nodes_product_order` SET `status` = "2" WHERE `id` = "'.$data["id"].'"';
                    engine::mysql($query);

                    $query = 'UPDATE `nodes_users` SET `balance` = "'.($seller["balance"]+doubleval($data["price"])).'" WHERE `id` = "'.$seller["id"].'"';
                    engine::mysql($query);
                   
                    die('<script>window.location = "'.$_SERVER["DIR"].'/account/purchases"; </script>');
                }
                $images = explode(';', $product["img"]);
                $this->title .= ' - '.lang("Delivery confirmation");
                $this->content = '<h1>'.lang("Delivery confirmation").'</h1><br/><br/>
                <div class="document delivery">
                    <div class="delivery_confirm">
                        <div class="delivery_image" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
                        <div>
                        <form method="POST">
                            <input type="hidden" name="rating" id="total_rating" value="5" />    
                            <div class="delivery_quality">'.lang("Quality").':</div>
                            <div id="raiting_star">
                                <div id="raiting" >
                                    <div id="raiting_blank"></div>
                                    <div id="raiting_hover"></div>
                                    <div id="raiting_votes"></div>
                                </div>
                            </div><br/>
                            <textarea name="comment" class="input delivery_textarea" placeHolder="'.lang("Your comment here").'"></textarea><br/><br/>
                            <input type="submit" class="btn w280" value="'.lang("Submit").'" /><br/>
                        </form>
                        </div>
                    </div>
                </div>';
                $this->activejs .= ' star_rating(5); ';
            }
            
        }else if($_GET[1]=="purchases"){
            $this->title .= ' - '.lang("Purchases");
            $this->content = '<h1>'.lang("Purchases").'</h1><br/>'
                    . '<div class="document">';
            
            $query = 'SELECT * FROM `nodes_orders` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" AND `status` > 0 ORDER BY `date` DESC';
            $res = engine::mysql($query);
            $flag = 0;
            while($data = mysql_fetch_array($res)){
                if($data["status"]=="1"){
                    $this->activejs .= '
                        alert("'.lang("Thank you for your order! Shipment in process now.").'");
                        document.getElementById("purcases_count").innerHTML = "";
                        document.getElementById("purcases").style.display = "none";
                        ';
                    $query = 'UPDATE `nodes_orders` SET `status` = "2" WHERE `id` = "'.$data["id"].'"';
                    engine::mysql($query);
                }
                $flag = 1;
                require_once('engine/include/print_purchase.php');
                $this->content .= print_purchase($data);
            }if(!$flag){
                $this->content .= '<div class="clear_block">'.lang("There is no purchases").'</div>';
            }$this->content .= '<br/><a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn w280" value="'.lang("Back to account").'"  /></a>'
                    . '</div>';
            
        }else if($_GET[1]=="inbox"){
            
            $this->title = lang("Messages").' - '.$this->title;
            $this->content .= '<div class="chat_window">'
                . '<h1>'.lang("Messages").'</h1><br/><br/>';
            
            if(!empty($_GET[2])){
                $this->content .= '<div id="chat">';
                $this->content .= '</div>';
                $this->activejs .= '
                refresh_chat("'.$_GET[2].'");
                setTimeout(refresh_chat, 10000, "'.$_GET[2].'");
                ';
                $query = 'UPDATE `nodes_message` SET `readed` = "'.date("U").'" WHERE `to` = "'.$_SESSION["user"]["id"].'" AND `readed` = 0';
                engine::mysql($query);
                $query = 'SELECT * FROM `nodes_users` WHERE `id` = '.$_SESSION["user"]["id"];
                $res = engine::mysql($query);
                $u = mysql_fetch_array($res);
                $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$_GET[2].'"';
                $res = engine::mysql($query);
                $target= mysql_fetch_array($res);

                if($target["online"] > date("U")-300){
                    $online = '<br/><font class="chat_font">'.lang("online").'</font>';
                }else{
                    $online = '<br/><font class="chat_font">'.lang("offline").'</font>'; 
                }
                $this->content .= '<div class="chat_user_left">
                        <img src="'.$_SERVER["DIR"].'/img/pic/'.$u["photo"].'" width=50 /><br/>
                        <div class="chat_user_name">
                            <font class="chat_user_name_font">'.$u["name"].'</font>
                            <font class="chat_font">'.lang("online").'</font>
                        </div>
                    </div>
                    <div class="chat_user_right">
                        <img src="'.$_SERVER["DIR"].'/img/pic/'.$target["photo"].'" width=50 /><br/>
                        <div class="chat_user_name">
                            <font class="chat_user_name_font">'.$target["name"].'</font>
                            '.$online.'
                        </div>
                    </div>
                    <div class="chat_center">
                        <textarea name="text" id="message_text" class="input" placeHolder="'.lang("Your message here").'"
                        onkeypress=\'if(event.keyCode==13&&!event.shiftKey){ event.preventDefault(); post_message("'.$_GET[2].'"); } \'
                        ></textarea><br/>
                        <input type="button" onClick=\'post_message("'.$_GET[2].'");\' class="btn" value="'.lang("Send message").'"  />
                    </div>
                    <div class="clear"></div>';
            }else{

                $query = 'SELECT * FROM `nodes_users` WHERE `id` <> "'.$_SESSION["user"]["id"].'"';
                $res = engine::mysql($query);
                $flag = 0;
                while($data = mysql_fetch_array($res)){
                    $flag = 1;
                }if(!$flag){
                    $this->content .= '<div class="clear_block">'.lang("There is no users, you can send a message").'</div>';
                }else{
                    $query = 'SELECT * FROM `nodes_users` WHERE `id` <> "'.$_SESSION["user"]["id"].'"';
                    $res = engine::mysql($query);
                    while($u = mysql_fetch_array($res)){
                        $query = 'SELECT COUNT(*) FROM `nodes_message` WHERE `to` = "'.intval($_SESSION["user"]["id"]).'" AND `readed` = 0 AND `from` = "'.$u["id"].'"';
                        $r = engine::mysql($query);
                        $d = mysql_fetch_array($r);
                        if($d[0] > 0){ 
                            if($d[0] == 1) $new = '<span class="new_message">'.lang("New message").'</span><br/>';
                            else $new = '<span class="new_message">'.$d[0].' '.lang("new messages").'</span><br/>';
                        }else $new = '';
                        if($u["online"] > date("U")-300){
                            $online = '<font class="chat_font">'.lang("online").'</font>';
                        }else{
                            $online = '<font class="chat_font">'.lang("offline").'</font>'; 
                        }
                        $this->content .= '<a href="'.$_SERVER["DIR"].'/account/inbox/'.$u["id"].'">'
                                . '<div class="user_block">'
                                . '<div class="user_block_image">
                                        <img src="'.$_SERVER["DIR"].'/img/pic/'.$u["photo"].'" width=50 />
                                    </div>
                                    <div class="user_block_name">'.$u["name"].'</div>'.$new.$online
                                . '</div></a>';
                    }
                    $this->content .=  '<div class="clear"></div>';
                }
            }$this->content .= '</div><br/><a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn w280" value="'.lang("Back to account").'"  /></a><br/><br/>';
            
        }else if($_GET[1]=="finances"){

            if(!empty($_GET[2])){
                $this->content = engine::error();
                return; 
            }
            
            $this->title = lang("Finances").' - '.$this->title;
            $this->content .= '<h1>'.lang("Finances").'</h1><br/>';
            $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$_SESSION["user"]["id"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            
            if(!empty($_POST["amount"])&&$this->configs["sandbox"]){
                $amount = doubleval($_POST["amount"]);
                $query = 'INSERT INTO `nodes_transactions`(user_id, order_id, amount, status, date, comment, ip) '
                        . 'VALUES("'.$_SESSION["user"]["id"].'", "-1", "'.$amount.'", "2", "'.date("U").'", "Deposit", "'.$_SERVER["REMOTE_ADDR"].'")';
                engine::mysql($query);
                $query = 'UPDATE `nodes_users` SET `balance` = "'.  doubleval($data["balance"]+$amount).'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
                $data["balance"] += doubleval($_POST["amount"]);
            }
            
            $query = 'SELECT * FROM `nodes_products` WHERE `user_id` = "'.$_SESSION["user"]["id"].'"';
            $res = engine::mysql($query);
            $balance = $data["balance"];
            if($balance > $_SESSION["user"]["balance"]){
                $this->activejs .= '
                    alert("'.lang("The funds have been added to your account").'");
                    ';
                $_SESSION["user"]["balance"] = $balance;
            }
            $pending = 0;
            while($d = mysql_fetch_array($res)){
                $query = 'SELECT * FROM `nodes_product_order` WHERE `product_id` = "'.$d["id"].'" AND `status` = "1"';
                $r = engine::mysql($query);
                while($order = mysql_fetch_array($r)){
                    $pending += $order["price"];
                }
            }
            $this->content .= lang('Balance').': <b>$'.$balance."</b>";
            if($pending>0) $this->content .= "  ".lang("Pending").": <b>$".$pending.'</b>';
            $this->content .= '<br/><br/>';
            if($this->configs["sandbox"]){
                $this->content .= '
                    <form method="POST" class="hidden">
                    <input type="hidden" name="amount" id="paypal_price" value="'.$price.'">
                    <input type="submit" id="pay_button"  /><br/><br/>
                    </form>';
            }else{
                if($this->configs["paypal_test"]) $domain = 'www.sandbox.paypal.com';
                else $domain = 'www.paypal.com';
                $this->content .= '
                    <form action="https://'.$domain.'/cgi-bin/webscr" method="post" class="hidden">			
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="'.$this->configs["paypal_id"].'">
                    <input type="hidden" name="item_name" value="'.$this->configs["paypal_description"].'">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="amount" id="paypal_price" value="'.$price.'">
                    <input type="hidden" name="cancel_return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/finances">
                    <input type="hidden" name="return" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account/finances">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="notify_url" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/paypal.php?deposit='.$_SESSION["user"]["id"].'">
                    <button type="submit" class="btn w280" id="pay_button" >PayPal</button><br/><br/>
                    </form>';
            }
            
            if($_SESSION["order"]=="id") $_SESSION["order"] = "date";

            $arr_count = 0;    
            $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
            $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
            $query = 'SELECT * FROM `nodes_transactions` WHERE `status` > 0 AND `user_id` = "'.$_SESSION["user"]["id"].'"'
                    . ' ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
            $requery = 'SELECT COUNT(*) FROM `nodes_transactions` WHERE `status` > 0 AND `user_id` = "'.$_SESSION["user"]["id"].'"';
            $table = '
                <div class="table">
                <table width=100% id="table">
                <thead>
                <tr>';
                    $array = array(
                        "order_id" => lang("Type"),
                        "amount" => lang("Amount"),
                        "status" => lang("Status"),
                        "date" => lang("Date")
                    ); foreach($array as $order=>$value){
                        $table .= '<th>';
                        if($_SESSION["order"]==$order){
                            if($_SESSION["method"]=="ASC") $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "DESC"; submit_search_form();\'>'.lang($value).' &uarr;</a>';
                            else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).' &darr;</a>';
                        }else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'</a>';
                        $table .= '</th>';
                    }
                    $table .= '
                </tr>
                </thead>';
            $res = engine::mysql($query);
            while($data = mysql_fetch_array($res)){
                $arr_count++;
                if($data["order_id"]=="0"){
                    $type = lang("Withdrawal request");
                    $data["amount"] = -$data["amount"];
                }else if($data["order_id"]=="-1"){
                    $type = lang("Money deposit");
                }else{
                    $type = lang("Order")." #".$data["order_id"]." payment";
                }
                
                if($data["status"] == "0"){
                    $status = lang("New");
                }else if($data["status"] == "1"){
                    $status = lang("Pending");
                }else if($data["status"] == "2"){
                    $status = lang("Finished");
                }
                
                $table .= '<tr>
                    <td align=left valign=middle>'.$type.'</td>
                    <td align=left valign=middle>'.$data["amount"].'$</td>
                    <td align=left valign=middle>'.$status.'</td>
                    <td align=left valign=middle title="'.date("d.m H:i", $data["date"]).'">'.date("d.m", $data["date"]).'</td>
                </tr>';
            }$table .= '</table>
        </div>';

            if($arr_count){

                $this->content .= $table.'
            <form method="POST"  id="query_form"  onSubmit="submit_search();">
            <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
            <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
            <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
            <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />

            <div class="total-entry">';
            $res = engine::mysql($requery);
            $data = mysql_fetch_array($res);
            $count = $data[0];
            if($to > $count) $to = $count;
            if($data[0]>0){
                $this->content .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
                    <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
                     <option'; if($_SESSION["count"]=="20") $this->content .= ' selected'; $this->content .= '>20</option>
                     <option'; if($_SESSION["count"]=="50") $this->content .= ' selected'; $this->content .= '>50</option>
                     <option'; if($_SESSION["count"]=="100") $this->content .= ' selected'; $this->content .= '>100</option>
                    </select> '.lang("per page").'.</nobr></p>';
            }$this->content .= '
            </div><div class="cr"></div>';
            if($count>$_SESSION["count"]){
               $this->content .= '<div class="pagination" >';
                    $pages = ceil($count/$_SESSION["count"]);
                   if($_SESSION["page"]>1){
                        $this->content .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
                    }$this->content .= '<ul>';
                   $a = $b = $c = $d = $e = $f = 0;
                   for($i = 1; $i <= $pages; $i++){
                       if(($a<2 && !$b && $e<2)||
                           ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
                       ($i>$pages-2 && $e<2)){
                           if($a<2) $a++;
                           $e++; $f = 0;
                           if($i == $_SESSION["page"]){
                               $b = 1; $e = 0;
                              $this->content .= '<li class="active-page">'.$i.'</li>';
                           }else{
                               $this->content .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                           }
                       }else if((!$c||!$b) && !$f && $i<$pages){
                           $f = 1; $e = 0;
                           if(!$b) $b = 1;
                           else if(!$c) $c = 1;
                           $this->content .= '<li class="dots">. . .</li>';
                       }
                   }if($_SESSION["page"]<$pages){
                       $this->content .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
                   }$this->content .= '
             </ul>
            </div>';
                 }$this->content .= '<div class="clear"></div>';
            }else{
                $this->content .= '<div class="clear_block">'.lang('Transactions not found').'</div>';
            }            
            if($balance>0){
                $this->content .= '<input type="button" class="btn w280" value="'.lang("Request withdrawal").'" onClick=\'withdrawal("'.lang("Confirm your PayPal").'");\' /><br/><br/>';
            }$this->content .=  '<input type="button" class="btn w280" value="'.lang("Deposit money").'" onClick=\'deposit("'.lang("Amount to deposit").'");\' /><br/><br/>';
            $this->content .= '<a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn w280" value="'.lang("Back to account").'"  /></a><br/><br/>';
            
        }else{
            $this->content = engine::error();
            return;
        }
    }else{   
        $query = 'SELECT * FROM `nodes_orders` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" AND `status` > 0';
        $res = engine::mysql($query);
        $pcount = 0;
        while($data = mysql_fetch_array($res)){
            $query = 'SELECT COUNT(*) FROM `nodes_product_order` WHERE `order_id` = "'.$data["id"].'" AND `status` = 1';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $pcount += $d[0];
        }
        $query = 'SELECT COUNT(*) FROM `nodes_transactions` WHERE `status` = 1 AND `user_id` = "'.$_SESSION["user"]["id"].'"';
        $res = engine::mysql($query);
        $fcount = mysql_fetch_array($res);
        $finance_count = '';
        if($fcount[0]>0) $finance_count = ' ('.$fcount[0].')';
        $purcases_count = '';
        if($pcount>0) $purcases_count = ' ('.$pcount.')';
        $button = '<a href="'.$_SERVER["DIR"].'/account/finances"><input type="button" class="btn w280" value="'.lang("Finances").$finance_count.'" /></a><br/><br/>'
                . '<a href="'.$_SERVER["DIR"].'/account/purchases"><input type="button" class="btn w280" value="'.lang("Purchases").$purcases_count.'" /></a><br/><br/>';

        $query = 'SELECT COUNT(*) FROM `nodes_message` WHERE `to` = "'.$_SESSION["user"]["id"].'" AND `readed` = 0';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $count = '';
        if($data[0]>0) $count = ' ('.$data[0].')';
        $this->title = lang("My Account").' - '.$this->title;
        $this->content = '<h1>'.lang("My Account").'</h1><br/><br/>';
        if($_SESSION["user"]["id"]=="1"){
            $this->content .= '
                <a href="'.$_SERVER["DIR"].'/admin"><input type="button" class="btn w280" value="'.lang("Admin").'" /></a><br/><br/>
                <a href="http://nodes-studio.com" target="_blank"><input type="button" class="btn w280" value="'.lang("About").'" /></a><br/><br/>';
        }else{
            $this->content .= $button;
        }$this->content .= '<a href="'.$_SERVER["DIR"].'/account/inbox"><input type="button" class="btn w280" value="'.lang("Messages").$count.'" /></a><br/><br/>'
        . '<a href="'.$_SERVER["DIR"].'/account/settings"><input type="button" class="btn w280" value="'.lang("Settings").'" /></a><br/><br/>'
        . '<input type="button" class="btn w280" value="'.lang("Logout").'" onClick="logout();"  /><br/><br/>';
    }
}else{
    $this->title = lang("Access denied").' - '.$this->title;
    $this->content = '<h1>'.lang("Access denied").'</h1><script language="JavaScript">setTimeout(function(){window.location = "'.$_SERVER["DIR"].'/login"}, 5000);</script>';
}