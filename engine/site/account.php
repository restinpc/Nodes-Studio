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
                . '<input type="text" class="input" required name="code" placeHolder="'.lang("Confirmation code").'" style="width: 280px;" />'
                . '<br/><br/>'
                . '<input type="submit" class="btn" style="width: 280px;" value="'.lang("Submit").'" />'
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
                    $file = engine::upload_photo("img", "img/pic", 50, 50);
                    if($file != "error"){
                        $file = $_SERVER["DIR"]."/img/pic/".$file;
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
            $this->content = '<h1 style="padding: 5px;">'.lang("Settings").'</h1>';
            if(empty($_SESSION["user"]["email"])){
                $this->content .= '<p>'.lang("Enter your email and password to continue").'</p>';
            }
            $this->content .= '<br/><form method="POST" enctype="multipart/form-data">
                <div style="width: 300px; margin:auto; text-align:center;">
                <table>
                <tr>
                    <td style="padding-bottom: 10px; width: 70px; padding-right: 5px;" align=right><img src="'.$_SESSION["user"]["photo"].'" width=50  style="border: #d0d0d0 4px solid; border-radius: 4px;  margin-top: -5px;" /></td>
                    <td style="padding-bottom: 0px;" valign=top><div style="float:left; text-align:left; padding-left: 5px;">'.lang("Change picture").':<br/><input type="file" name="img" class="input" style="width: 200px;margin-top: 5px;" /></div></td>
                </tr>

                <tr>
                    <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Name").':</td>
                    <td style="padding-bottom: 10px;" ><input type="text" name="name" value="'.$_SESSION["user"]["name"].'" class="input" style="width: 200px;" /></td>
                </tr>';

            if(!empty($_SESSION["user"]["email"])){
                $this->content .= '
                <tr>
                    <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Email").':</td>
                    <td style="padding-bottom: 10px;" ><input type="text" name="email" value="'.$_SESSION["user"]["email"].'" class="input" style="width: 200px;" /></td>
                </tr>
                <tr>
                    <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Password").':</td>
                    <td style="padding-bottom: 10px;" ><input type="password" name="pass" value="" placeHolder="'.lang("New password").'" class="input" style="width: 200px;" /></td>
                </tr>';
            }else{
                $this->content .= '
                <tr>
                    <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Email").':</td>
                    <td style="padding-bottom: 10px;" ><input required type="text" name="email" placeHolder="'.lang("Enter your email").'" class="input" style="width: 200px;" /></td>
                </tr>
                <tr>
                    <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Password").':</td>
                    <td style="padding-bottom: 10px;" ><input required type="password" name="pass" value="" placeHolder="'.lang("Enter your password").'" class="input" style="width: 200px;" /></td>
                </tr>'; 
            }
            $this->content .= '
            <tr>
            ';

if(empty($_SESSION["user"]["url"])){

    $this->content .= '<td colspan=2 style="padding: 5px;">';

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

    $this->content .= '<div style="padding: 5px; border: #eee 1px solid; border-radius: 5px;">Connect with social network<br/><br/>';
    if(!empty($fb_id["value"])) $this->content .= '<a rel="nofollow" target="_parent"  href="'.$_SERVER["DIR"].'/account.php?mode=social&method=fb" style="margin: 15px; margin-left: 0px; cursor: pointer;"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" title="Facebook"/></a>';
    if(!empty($tw_key["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=tw" style="margin: 15px;"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" title="Twitter"/></a>';
    if(!empty($gp_id["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=gp" style="margin: 15px;"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" title="Google+"/></a>';
    if(!empty($vk["value"])) $this->content .= '<a rel="nofollow" target="_parent" href="https://oauth.vk.com/authorize?client_id='.$vk["value"].'&scope=notify&redirect_uri='.  urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account.php?mode=social&method=vk').'&display=page&response_type=token" style="margin: 15px; margin-right: 0px;"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" title="VK"/></a>';
    $this->content .= '</div>';

            }

}else{

    $this->content .= ' <td align=right style="padding-bottom: 10px; width: 70px; padding-right: 5px;">'.lang("Site").':</td>
                    <td align=left style="padding-left: 7px;"><div style="overflow:hidden; height: 14px; width: 200px;"><a href="'.$_SESSION["user"]["url"].'" target="_blank">'.str_replace('/', ' / ', str_replace("http://", '', $_SESSION["user"]["url"])).'</a></div><br/>';

}

$this->content .= '<br/>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;" colspan=2>
                        <input type="submit" class="btn" style="width: 280px;" value="'.lang("Save changes").'" /><br/><br/>
                        <a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Back to account").'"  /></a><br/><br/>
                    </td>
                </tr>
                </table>
                </div>
                </form>';

        }else if($_GET[1]=="inbox"){

            if(!empty($_GET[3])){
                $this->content = engine::error();
                return; 
            }
            $this->title = lang("Messages").' - '.$this->title;
            $this->content .= '<h1 style="padding: 5px;">'.lang("Messages").'</h1><br/>';
            if(empty($_GET[2])){
                $this->content .= '<center><iframe id="message_frame" src="'.$_SERVER["DIR"].'/messages.php?id='.$_SESSION["user"]["id"].'" width=100% height=390 style="max-width: 710px;" ></iframe></center>'
                        . '<br/><a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Back to account").'"  /></a>';
            }else{
                $this->content .= '<center><iframe id="message_frame" src="'.$_SERVER["DIR"].'/messages.php?mode=dialog&id='.$_SESSION["user"]["id"].'&target='.$_GET[2].'" width=100% height=390 style="max-width: 710px;" ></iframe></center>'
                        . '<br/><a href="'.$_SERVER["DIR"].'/account"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Back to account").'"  /></a>';
            }
        }else{
            $this->content = engine::error();
            return;
        }
    }else{
        $this->title = lang("My Account").' - '.$this->title;
        $this->content = '<h1 style="padding: 5px;">'.lang("My Account").'</h1><br/><br/>';
        if($_SESSION["user"]["id"]=="1"){
            $this->content .= '<a href="'.$_SERVER["DIR"].'/admin"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Admin").'" /></a><br/><br/>'
                    . '<a href="http://nodes-studio.com" target="_blank"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Documentation").'" /></a><br/><br/>';
        }
        $this->content .= '<a href="'.$_SERVER["DIR"].'/account/inbox"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Messages").'" /></a><br/><br/>'
        . '<a href="'.$_SERVER["DIR"].'/account/settings"><input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Settings").'" /></a><br/><br/>'
        . '<input type="button" class="btn btnSmall" style="width: 280px;" value="'.lang("Logout").'" onClick="logout();"  /><br/><br/>';
    }
}else{
    $this->title = lang("Access denied").' - '.$this->title;
    $this->content = '<h3 style="padding-top: 100px;">'.lang("Access denied").'</h3><br/>';
}