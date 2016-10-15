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

if(!empty($_GET[1])){
    $this->content = engine::error();
    return; 
}else if(!empty($_SESSION["user"]["id"])){
    die('<script language="JavaScript">window.location = "'.$_SERVER["DIR"].'/account";</script>');
    return;
}

$this->title = lang("Sign Up").' - '.$this->title;

if(!empty($_POST["email"])&&!empty($_POST["pass"])){
    if($_POST["captcha"] != $_SESSION["captcha"]){
        $this->activejs .= ' alert("'.lang("Error").'. '.lang("Invalid conformation code").'."); ';      
        $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
        . 'VALUES("2", "0", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "Invalid conformation code")';
        engine::mysql($query);
    }else{
        $name = mysql_real_escape_string($_POST["name"]);
        $email = strtolower(mysql_real_escape_string($_POST["email"]));
        $code = substr(md5(date("U")), 0, 4);
        $confirm = !$this->configs["confirm_signup_email"];
        $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'"';
        $r = engine::mysql($query);
        $d = mysql_fetch_array($r);
        if(!empty($d)){
            $this->activejs .= ' alert("'.lang("Error").'. '.lang("Email").' '.lang("allready exist").'."); '; 
            $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
            . 'VALUES("2", "0", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "Email '.$_POST["email"].' allready exist")';
            engine::mysql($query);
            unset($_POST["email"]);
        }else if(strpos($email, "@")){
            $query = 'INSERT INTO `nodes_users` (`name`, `photo`, `email`, `pass`, `online`, `confirm`, `code`) 
                VALUES ("'.$name.'", "anon.jpg", "'.$email.'", "'.md5(trim($_POST["pass"])).'", "'.date("U").'", "'.$confirm.'", "'.$code.'")';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'" AND `pass` = "'.md5(trim($_POST["pass"])).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            unset($data["pass"]);
            unset($data[5]);
            unset($data["token"]);
            unset($data[9]);
            $_SESSION["user"] = $data;
            $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
            . 'VALUES("1", "'.$data["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "Sucsessful registration")';
            engine::mysql($query);
            if($this->configs["confirm_signup_email"]){
                require_once("engine/include/send_email.php");
                send_email::confirmation($email, $name, $code);  
            }else if($this->configs["send_registration_email"]){
                require_once("engine/include/send_email.php");
                send_email::registration($email, $name);  
            }
            if(empty($_SESSION["redirect"])){
                die('<script language="JavaScript">window.location = "'.$_SERVER["DIR"].'/";</script>');
            }else{
                die('<script language="JavaScript">window.location = "'.$_SESSION["redirect"].'";</script>');
            }return;   
        }else{
            $this->activejs .= ' alert("'.lang("Error").'. '.lang("Incorrect email").'."); '; 
            $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
            . 'VALUES("2", "0", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "Incorrect email '.$_POST["email"].'")';
            engine::mysql($query);
            unset($_POST["email"]);
        }
    }
}
$this->content .= '<h1>'.lang("Sign Up").'.</h1>'
. '<br/>'
. '<a target="_parent" href="'.$_SERVER["DIR"].'/login" onClick="event.preventDefault(); show_login_form();">'.lang("Already have an account?").'</a><br/><br/>'
. '<form method="POST">'
. '<input required type="text" name="email" value="'.$_POST["email"].'" class="input" style="padding: 5px; width: 100%;max-width: 265px; margin-top: 0px;" placeHolder="'.lang("Email").'" title="'.lang("Email").'" /><br/>'
. '<input required type="text" name="name" value="'.$_POST["name"].'" class="input" style="padding: 5px;width: 100%;max-width: 265px; margin-top: 10px;" placeHolder="'.lang("Name").'" title="'.lang("Name").'"  /><br/>'
. '<input required type="password" name="pass" class="input" style="width: 100%;padding: 5px;max-width: 265px; margin-top: 10px;" placeHolder="'.lang("Password").'" title="'.lang("Password").'"  value="'.$_POST["pass"].'" /><br/>'
. '<br/>'
. '<center><img src="'.$_SERVER["DIR"].'/captcha.php?'.md5(date("U")).'" /></center>'
. '<input required type="text" name="captcha" class="input" style="width: 100%;padding: 5px;max-width: 265px; margin-top: 5px;" placeHolder="'.lang("Confirmation code").'" title="'.lang("Confirmation code").'" />'
. '<br/><input type="submit" class="btn" value="'.lang("Submit").'" style="width: 100%;max-width: 265px;margin-top: 10px;"  /></form>'
. '<br/><br/>';