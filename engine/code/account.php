<?php
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/language.php");

$query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';
$res = engine::mysql($query);
$data = mysql_fetch_array($res);
$template = $data["value"];

function print_login_details(){
    
    $flag = 0;
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
    
    $fout = '<div style="text-align:left;width: 200px;">'
    . '<script>parent.document.getElementById("nodes_iframe").style.height="290px";'
    . '</script>'
    . '<center><h3 style="color: #555;">'.lang("Login").'</h3></center><br/>'
    . '<div style="text-align:center; white-space: nowrap;">';
    
    if(!empty($fb_id["value"])){ $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=fb" style="margin: 10px; margin-left: 0px;"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" title="Facebook"/></a>';
    }if(!empty($tw_key["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=tw" style="margin: 9px;"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" title="Twitter"/></a>';
    }if(!empty($gp_id["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=gp" style="margin: 9px;"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" title="Google+"/></a>';
    }if(!empty($vk["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="https://oauth.vk.com/authorize?client_id='.$vk["value"].'&scope=notify&redirect_uri='.  urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account.php?mode=social&method=vk').'&display=page&response_type=token" style="margin: 10px; margin-right: 0px;"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" title="Vkontakte"/></a>';
    }if(!$flag){
        $fout .= '<br/>';
    }
    
    $fout .= '</div><br/>'
    . '<form method="POST" action="'.$_SERVER["DIR"].'/account.php?mode=login">'
    . '<input type="text" required name="email" value="'.$_POST["email"].'" class="input" style="width: 200px; padding: 5px;" placeHolder="Email" /><br/><br/>'
    . '<input type="password" required name="pass" class="input" style="width: 200px; padding: 5px;" value="'.$_POST["pass"].'" placeHolder="'.lang("Password").'" /><br/>'
    . '<div style="white-space: nowrap; padding-top: 17px; padding-bottom: 20px; text-align: center; font-size: 14px;">'
    . '<a onClick=\'parent.window.location = "'.$_SERVER["DIR"].'/register";\'>'.lang("Sign Up").'</a> | <a rel="nofollow" href="'.$_SERVER["DIR"].'/account.php?mode=remember">'.lang("Lost password").'?</a>'
    . '</div>'
    . '<input type="submit" class="btn" value="'.lang("Submit").'" style="width: 200px;" /></form>'
    . '</div>';   
    return $fout;
}

if($_GET["mode"] == "login"){
    $fout = '<!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="'.$_SERVER["DIR"].'/templates/style.css" rel="stylesheet" type="text/css">
    <link href="'.$_SERVER["DIR"].'/templates/'.$template.'/template.css" rel="stylesheet" type="text/css">';
    require_once ("templates/meta.php");
    $fout .= '
    </head>
    <body style="background: #fff; opacity: 1; ">';
    if(!empty($_POST["email"]) && !empty($_POST["pass"])){
        $email = strtolower(str_replace('"', "'", $_POST["email"]));
        $query = 'SELECT * FROM `nodes_logs` WHERE `details` = "'.$_POST["email"].'" AND `action` = 4 ORDER BY `date` DESC LIMIT 2, 1';
        $res = engine::mysql($query);
        $data= mysql_fetch_array($res);
        $date = $data["date"];
        if(date("U")-$date<180){
            $fout .= '<div style="text-align:center; padding-top: 100px; width: 200px;">'.lang("Too many failed attempts").'.'
                    . '<br/><br/>'.lang("Try again after").' <a id="tick">'.(180-(date("U")-$date)).'</a> '.lang("seconds").'.</div>'
                    . '<script>var sec = '.(181-(date("U")-$date)).'; function tick(){sec--; document.getElementById("tick").innerHTML=sec;} setInterval(tick, 1000);'
                    . 'function redirect(){window.location="'.$_SERVER["DIR"].'/account.php?mode=form";}setTimeout(redirect, '.((180-(date("U")-$date))*1000).');</script>';
        }else{
            $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'" AND `pass` = "'.md5(strtolower($_POST["pass"])).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(!empty($data)){
                if($data["ban"]=="1"){
                    $fout .= '<div style="text-align:center; padding-top: 100px; width: 200px;">'.lang("Access denied").'.</div>'
                            . '<script>function redirect(){parent.js_hide_wnd();}setTimeout(redirect, 3000);</script>';
                    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
                            . 'VALUES("4", "'.$data["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "Ban")';
                    engine::mysql($query);
                }else{
                    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
                            . 'VALUES("3", "'.$data["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$email.'")';
                    engine::mysql($query);
                    unset($data["pass"]);
                    unset($data[5]);
                    unset($data["token"]);
                    unset($data[9]);
                    $_SESSION["user"] = $data;
                    if(!empty($_SESSION["redirect"])){
                        $fout .= '<script language="JavaScript">parent.window.location = "'.($_SESSION["redirect"]).'";</script>';
                        unset($_SESSION["redirect"]);
                    }else{
                        $fout .= '<script language="JavaScript">parent.window.location = "'.$_SERVER["DIR"].'/account#'.$_SESSION['user']['id'].'";</script>';
                    }
                }
            }else{
                $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(!empty($data)){
                    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
                            . 'VALUES("4", "'.$data["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$email.'")';
                }else{
                    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
                            . 'VALUES("4", "0", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$email.'")';
                }engine::mysql($query);

                $fout .= '<div style="text-align:center; padding-top: 100px; width: 200px;">'.lang("Incorrect email of password").'.</div>'
                        . '<script>function redirect(){window.location="'.$_SERVER["DIR"].'/account.php?mode=form";}setTimeout(redirect, 3000);</script>';
            }
        }
    }else{
        if(intval($_SESSION["user"]['id'])>0){
            $fout .= '<script language="JavaScript">parent.history.go(0);</script>';
        }else{
            $fout .= print_login_details();
        }
    }
    $fout .= '</body></html>';
}else if($_GET["mode"] == "remember"){
    $fout = '<!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="'.$_SERVER["DIR"].'/templates/style.css" rel="stylesheet" type="text/css">
    <link href="'.$_SERVER["DIR"].'/templates/'.$template.'/template.css" rel="stylesheet" type="text/css">';
    require_once ("templates/meta.php");
    $fout .= '
    </head>
    <body style="background: #fff;  opacity: 1;">';
    if(!empty($_GET["email"])&&!empty($_GET["code"])){
        $email = urldecode($_GET["email"]);
        $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysql_num_rows($res);
        if($data){
            $code = substr(md5($email.date("Y-m-d")), 0, 6);
            if($code == $_GET["code"]){
                $new_pass = substr(md5($email.date("U")), 0, 6);
                $query = 'UPDATE `nodes_users` SET `pass` = "'.md5($new_pass).'" WHERE `email` = "'.$email.'"';   
                engine::mysql($query);
                engine::send_mail($email, "no-reply@".$_SERVER["HTTP_HOST"], lang("New password for")." ".$_SERVER["HTTP_HOST"], 
                        lang("New password is").": ".$new_pass.'<br/><br/><a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'">'.$_SERVER["HTTP_HOST"].'</a>');
                $fout .= '<div style="text-align:center; padding-top: 100px;">'.lang("Message with new password is sended to email").'.</div>
                        <script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/";}setTimeout(redirect, 3000);</script>';
            }else{
                $fout .= '<div style="text-align:center; padding-top: 100px;">'.lang("Invalid confirmation code").'.</div>'
                 . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/";}setTimeout(redirect, 3000);</script>';  
            }
        }else{
           $fout .= '<div style="text-align:center; padding-top: 100px;">Email '.lang("not found").'.</div>'
            . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/";}setTimeout(redirect, 3000);</script>';  
        }
    }else if(!empty($_POST["email"])){
        $email = str_replace('"', "'", $_POST["email"]);
        $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(!empty($data)){
            $code = substr(md5($email.date("Y-m-d")), 0, 6);
            engine::send_mail($email, "no-reply@".$_SERVER["HTTP_HOST"], lang("Restore your password")." ".$_SERVER["HTTP_HOST"], 
            lang("To restore your password, use this code").': <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/account.php?mode=remember&email='.$email.'&code='.$code.'">'.$code.'</a><br/><br/><a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'">'.$_SERVER["HTTP_HOST"].'</a>');
            $fout .= '<div style="text-align:left;width: 200px;"><script>parent.document.getElementById("nodes_iframe").style.height="235px";</script><center><h3 style="color: #555;">'.lang("Confirmation code").'</h3></center><br/>'
            . '<form method="GET"><input type="hidden" name="mode" value="remember" /><input  type="hidden" name="email" value="'.$_POST["email"].'" />'
            . '<input type="text" required name="code" class="input" style="width: 200px; padding: 5px; margin-top: 5px;" placeHolder="'.lang("Code").'" /><br/>'
            . '<div style="padding-top: 17px; padding-bottom: 20px; margin: auto; text-align: center;  font-size: 12px;"><a onClick=\'parent.window.location = "'.$_SERVER["DIR"].'/register";\'>'.lang("Sign Up").'</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a rel="nofollow" href="'.$_SERVER["DIR"].'/account.php?mode=login">'.lang("Login").'</a></div>'
            . '<input type="submit" class="btn" value="'.lang("Submit").'" style="width: 200px;" /></form></div>'
            . '<script>alert("'.lang("Message with confirmation code is sended to email").'.");</script>';   
        }else{
            $fout .= '<div style="text-align:center; padding-top: 100px; width: 200px;">Email '.lang("not found").'.</div>'
            . '<script>function redirect(){window.location="'.$_SERVER["DIR"].'/account.php?mode=remember";}setTimeout(redirect, 3000);</script>';   
        }
    }else{
        $fout .= '<div style="text-align:left;width: 200px;"><script>parent.document.getElementById("nodes_iframe").style.height="235px";</script><center><h3 style="color: #555;">'.lang("Restore password").'</h3></center><br/><form method="POST">'
        . '<input type="text" required name="email" value="'.$_POST["email"].'" class="input" style="width: 200px; padding: 5px; margin-top: 5px;" placeHolder="Email" /><br/>'
        . '<div style="padding-top: 17px; padding-bottom: 20px; margin: auto; text-align: center;  font-size: 12px;"><a onClick=\'parent.window.location = "'.$_SERVER["DIR"].'/register";\'>'.lang("Sign Up").'</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a rel="nofollow" href="'.$_SERVER["DIR"].'/account.php?mode=login">'.lang("Login").'</a></div>'
        . '<input type="submit" class="btn" value="'.lang("Submit").'" style="width: 200px;" /></form></div>';   
    }
    $fout .= '</body></html>';
}else if($_GET["mode"] == "social"&&!empty($_GET["method"])){ 
    if($_GET["method"]=="fb"){
        require_once 'engine/api/oauth/fb_auth.php';
    }else if($_GET["method"]=="vk"){
        require_once 'engine/api/oauth/vk_auth.php';
    }else if($_GET["method"]=="tw"){
        require_once 'engine/api/oauth/twitter_auth.php';
    }else if($_GET["method"]=="gp"){
        require_once 'engine/api/oauth/google_auth.php';
    }
}else if($_GET["mode"] == "logout"){  
    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
    . 'VALUES("5", "'.$_SESSION["user"]["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "")';
    engine::mysql($query);
    unset($_SESSION["user"]);
    $fout = '<script language="JavaScript">parent.window.location = "'.$_SERVER["DIR"].'/";</script>';
}else{
    if(intval($_SESSION["user"]["id"])>0){
        $fout = '<script language="JavaScript">parent.window.location = "'.$_SERVER["DIR"].'/account";</script>';
    }else{
        $fout = '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="UTF-8" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link href="'.$_SERVER["DIR"].'/templates/style.css" rel="stylesheet" type="text/css">
        <link href="'.$_SERVER["DIR"].'/templates/'.$template.'/template.css" rel="stylesheet" type="text/css">';
    require_once ("templates/meta.php");
    $fout .= '
        </head>
        <body style="background: #fff;  opacity: 1;">'
            .print_login_details().'
        </body>
        </html>';
    }
}die($fout);