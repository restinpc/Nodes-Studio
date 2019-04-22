<?php
/**
* Backend login page file.
* @path /engine/site/login.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
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
if(!empty($_GET[4])){
    $this->content = engine::error();
    return; 
}
$this->content .= '<div class="w320 pt20">';
if(empty($_GET[1])){
    $flag = 0;
    $this->title = lang("Login").' - '.$this->title;
    if(!empty($_POST["email"]) && !empty($_POST["pass"])){
        $email = strtolower(str_replace('"', "'", $_POST["email"]));
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $pass = trim(strtolower($_POST["pass"]));
        if(!empty($data) && engine::match_passwords($pass, $data["pass"], $data["salt"])){
            if($data["ban"]=="1"){
                $this->onload .= 'alert("'.lang("Access denied").'");';
            }else{
                $_SESSION["user"] = $data;
                if(!empty($_SESSION["redirect"])){
                    $this->content .= '<script language="JavaScript">setTimeout(function(){ window.location = "'.($_SESSION["redirect"]).'"; }, 1);</script>';
                    unset($_SESSION["redirect"]);
                }else{
                    $this->content .= '<script language="JavaScript">setTimeout(function(){ window.location = "'.$_SERVER["DIR"].'/account"; }, 1);</script>';
                }
                $flag = 1;
            }
        }else{
            $this->onload .= 'alert("'.lang("Incorrect email of password").'");';
        }
    }
    if(!$flag){
        $this->content .= '<h1>'.lang("Login").'</h1><br/>'
                . '<a vr-control id="link-sign-up" href="'.$_SERVER["DIR"].'/register">'.lang("Do not have an account?").'</a><br/>'
                . '<br/><br/>'
            . '<form method="POST" action="'.$_SERVER["DIR"].'/login" id="login_form" class="lh2">'
                . '<input vr-control id="input-login-email" type="text" required name="email" value="'.$_POST["email"].'" class="input reg_email" placeHolder="Email" /><br/>'
                . '<input vr-control id="input-login-password" type="password" required name="pass" class="input reg_name" value="'.$_POST["pass"].'" placeHolder="'.lang("Password").'" /><br/>'
                . '<input vr-control id="input-login-submit" type="submit" class="btn reg_submit" value="'.lang("Continue").'" /><br/>'
                . '<div style="color: #0e2556; font-size: 14px; padding-top: 5px;">'
                . '<a vr-control id="link-restore-pass" rel="nofollow" href="'.$_SERVER["DIR"].'/login/restore">'.lang("Forgot password").'?</a></div>
            </form>';
    }
}else if($_GET[1] == "restore"){
    $flag = 0;
    if(!empty($_GET[2])&&!empty($_GET[3])){
        $this->title = lang("Setup new password").' - '.$this->title;
        $email = urldecode($_GET[2]);
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysqli_num_rows($res);
        if($data){
            $code = mb_substr(md5($email.date("Y-m-d")), 0, 8);
            if($code == $_GET[3]){
                if(!empty($_POST["pass"])){
                    $password = engine::encode_password(trim(strtolower($_POST["pass"])));
                    $pass = $password["pass"];
                    $salt = $password["salt"];
                    $query = 'UPDATE `nodes_user` SET `pass` = "'.$pass.'", `salt` = "'.$salt.'" WHERE `email` = "'.$email.'"';   
                    engine::mysql($query);
                    $this->content .= '<div class="clear_block">'.lang("Your password has been updated").'!</div>'
                    . '<script>function redirect(){window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';
                }else{
                    $this->content .= ''
                    . '<h1>'.lang("Setup new password").'</h1><br/>'
                    . '<form method="POST" class="lh2">'
                    . '<input vr-control id="input-login-password" type="password" required name="pass" value="'.$_POST["email"].'" class="input reg_email" placeHolder="'.lang("Password").'" /><br/>'
                    . '<input vr-control id="input-login-submit" type="submit" class="btn reg_submit" value="'.lang("Submit").'" />
                    </form>';
                }
                $flag = 1;
            }else{
                $this->onload .= 'alert("'.lang("Invalid confirmation code").'");';
            }
        }else{
            $this->onload .= 'alert("Email '.lang("not found").'");';
        }
    }
    if(!empty($_POST["email"])){
        $this->title = 'Reset password - '.$this->title;
        $email = str_replace('"', "'", $_POST["email"]);
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(!empty($data)){
            $code = mb_substr(md5($email.date("Y-m-d")), 0, 4);
            $new_pass = mb_substr(md5($email.date("Y-m-d")), 0, 8);
            email::restore_password($data["email"], $new_pass, $code);
            $this->content .= '<div class="clear_block">'.lang("To process restore, please check your email").'.</div>'
                    . '<script>'
                    . 'function redirect(){this.location = "'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000); </script>';   
            $flag = 1;
        }else{
            $this->onload .= 'alert("Email '.lang("not found").'");';   
        }
    }
    if(!$flag){
        $this->title = lang("Reset password").' - '.$this->title;
        $this->content .= '<h1>'.lang("Reset password").'</h1><br/>'
        . '<br/><form method="POST" class="lh2">'
        . '<input vr-control id="input-login-email" type="text" required name="email" value="'.$_POST["email"].'" class="input reg_email" placeHolder="Email" /><br/>'
        . '<input vr-control id="input-login-submit" type="submit" class="btn reg_submit" value="'.lang("Submit").'" /></form>';   
    }
}
$this->content .= '</div>';