<?php
/**
* Backend register page file.
* @path /engine/site/register.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/
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
        $this->onload .= ' alert("'.lang("Error").'. '.lang("Invalid conformation code").'."); ';      
    }else{
        $name = engine::escape_string($_POST["name"]);
        $email = strtolower(engine::escape_string($_POST["email"]));
        $code = mb_substr(md5(date("U")), 0, 4);
        $password = engine::encode_password(trim(strtolower($_POST["pass"])));
        $pass = $password["pass"];
        $salt = $password["salt"];
        $confirm = !$this->configs["confirm_signup_email"];
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        if(!empty($d)){
            $this->onload .= ' alert("'.lang("Error").'. '.lang("Email").' '.lang("allready exist").'."); '; 
            unset($_POST["email"]);
        }else if(strpos($email, "@")){
            $query = 'INSERT INTO `nodes_user` (`name`, `photo`, `email`, `pass`, `salt`, `lang`, `online`, `confirm`, `code`) 
                VALUES ("'.$name.'", "anon.jpg", "'.$email.'", "'.$pass.'", "'.$salt.'", "'.$_SESSION["Lang"].'", "'.date("U").'", "'.$confirm.'", "'.$code.'")';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.  mysqli_insert_id($_SERVER["sql_connection"]).'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            $_SESSION["user"] = $data;
            if($this->configs["confirm_signup_email"]){
                email::confirmation($email, $name, $code);  
            }else if($this->configs["send_registration_email"]){
                email::registration($email, $name);  
            }
            if(empty($_SESSION["redirect"])){
                die('<script language="JavaScript">window.location = "'.$_SERVER["DIR"].'/";</script>');
            }else{
                die('<script language="JavaScript">window.location = "'.$_SESSION["redirect"].'";</script>');
            }return;   
        }else{
            $this->onload .= ' alert("'.lang("Error").'. '.lang("Incorrect email").strpos($email, "@").'."); '; 
            unset($_POST["email"]);
        }
    }
}
$this->content .= '<div class="w320 pt20">'
. '<h1>'.lang("Sign Up").'.</h1><br/>'
. '<a vr-control id="link-login" href="'.$_SERVER["DIR"].'/login">'.lang("Already have an account?").'</a><br/><br/><br/>'
. '<form method="POST"  id="reg_form" onSubmit=\'event.preventDefault(); if($id("pass1").value==$id("pass2").value){$id("reg_form").submit();}else{alert("'.lang("Passwords do not match").'"); $id("pass2").value="";}\'>'
. '<input vr-control id="input-email" required type="email" name="email" value="'.$_POST["email"].'" class="input reg_email" placeHolder="'.lang("Email").'" title="'.lang("Email").'" /><br/>'
. '<input vr-control id="input-name" required type="text" name="name" value="'.$_POST["name"].'" class="input reg_name" placeHolder="'.lang("Name").'" title="'.lang("Name").'"  /><br/>'
. '<input vr-control id="pass1" required type="password" name="pass" class="input reg_name" placeHolder="'.lang("Password").'" title="'.lang("Password").'"  value="'.$_POST["pass"].'" /><br/>'
. '<input vr-control id="pass2" required type="password" name="pass_repeat" class="input reg_name" placeHolder="'.lang("Repeat password").'" title="'.lang("Repeat password").'"  value="'.$_POST["pass_repeat"].'" /><br/>'
. '<br/><center><img src="'.$_SERVER["DIR"].'/captcha.php?'.md5(date("U")).'" /></center>'
. '<input vr-control id="input-captcha" required type="text" name="captcha" class="input reg_captcha" placeHolder="'.lang("Confirmation code").'" title="'.lang("Confirmation code").'" />'
. '<br/><div style="padding: 10px; padding-bottom: 5px; line-height: 1.5;">'.
        lang("By registering on the site, you accept the").'<br/> <a vr-control id="link-terms" href="/terms_and_conditions" target="_blank">'.lang("Terms and Conditions").'</a> <br/>'.
        lang("and are familiar with the").' <a vr-control id="link-privacy" href="/privacy_policy" target="_blank">'.lang("Privacy Policy").'</a>'.'</div>'
. '<input vr-control id="input-submit" type="submit" class="btn reg_submit" value="'.lang("Submit").'" /></form>'
. '<br/><br/>'
. '</div>';