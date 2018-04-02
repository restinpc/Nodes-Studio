<?php
/**
* User login window.
* @path /engine/code/account.php
*
* @name    Nodes Studio    @version 2.0.7
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if($_GET["mode"] == "login"){
    $fout = '<!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css">
    <link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css">';
    require_once("template/meta.php");
    $fout .= '
    </head>
    <body style="background: #fff; opacity: 1; min-width: 200px;" class="nodes">';
    if(!empty($_POST["email"]) && !empty($_POST["pass"])){
        $email = strtolower(str_replace('"', "'", $_POST["email"]));
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'" AND `pass` = "'.md5(trim(strtolower($_POST["pass"]))).'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(!empty($data)){
            if($data["ban"]=="1"){
                $fout .= '<div class="center pt100 w200">'.lang("Access denied").'.</div>'
                        . '<script>function redirect(){parent.js_hide_wnd();}setTimeout(redirect, 3000);</script>';
            }else{
                unset($data["pass"]);
                unset($data[5]);
                $_SESSION["user"] = $data;
                if(!empty($_SESSION["redirect"])){
                    $fout .= '<script language="JavaScript">setTimeout(function(){ parent.window.location = "'.($_SESSION["redirect"]).'"; }, 1);</script>';
                    unset($_SESSION["redirect"]);
                }else{
                    $fout .= '<script language="JavaScript">setTimeout(function(){ parent.window.location = "'.$_SERVER["DIR"].'/account"; }, 1);</script>';
                }
            }
        }else{
            $fout .= '<div class="center pt100 w200">'.lang("Incorrect email of password").'.</div>'
                    . '<script>function redirect(){window.location="'.$_SERVER["DIR"].'/account.php?mode=form";}setTimeout(redirect, 3000);</script>';
        }
    }else{
        if(intval($_SESSION["user"]['id'])>0){
            $fout .= '<script language="JavaScript">parent.history.go(0);</script>';
        }else{
            $fout .= engine::print_login_form();
        }
    }
    $fout .= '</body></html>';
}else if($_GET["mode"] == "remember"){
    $fout = '<!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css">
    <link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css">';
    require_once("template/meta.php");
    $fout .= '
    </head>
    <body style="background: #fff;  opacity: 1; min-width: 200px;" class="nodes">';
    if(!empty($_GET["email"])&&!empty($_GET["code"])){
        $email = urldecode($_GET["email"]);
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysql_num_rows($res);
        if($data){
            $code = mb_substr(md5($email.date("Y-m-d")), 0, 4);
            if($code == $_GET["code"]){
                $new_pass = mb_substr(md5($email.date("Y-m-d")), 0, 8);
                $query = 'UPDATE `nodes_user` SET `pass` = "'.md5($new_pass).'" WHERE `email` = "'.$email.'"';   
                engine::mysql($query);
                $fout .= '<div class="center pt100">'.lang("New password activated!").'</div>
                        <script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';
            }else{
                $fout .= '<div class="center pt100">'.lang("Invalid confirmation code").'.</div>'
                 . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';  
            }
        }else{
           $fout .= '<div class="center pt100">Email '.lang("not found").'.</div>'
            . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';  
        }
    }else if(!empty($_POST["email"])){
        $email = str_replace('"', "'", $_POST["email"]);
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(!empty($data)){
            $code = mb_substr(md5($email.date("Y-m-d")), 0, 4);
            $new_pass = mb_substr(md5($email.date("Y-m-d")), 0, 8);
            email::restore_password($data["email"], $new_pass, $code);
            $fout .= '<div class="center pt100">'.lang("Message with new password is sended to email").'</div><script>'
                    . 'function redirect(){this.location = "'.$_SERVER["DIR"].'/account.php?mode=login";}setTimeout(redirect, 3000); </script>';   
        }else{
            $fout .= '<div class="center pt100 w200">Email '.lang("not found").'.</div>'
            . '<script>function redirect(){window.location="'.$_SERVER["DIR"].'/account.php?mode=remember";}setTimeout(redirect, 3000);</script>';   
        }
    }else{
        $fout .= '<div class="left w200"><script>parent.document.getElementById("nodes_iframe").style.height="235px";</script>'
        . '<center><h4 class="c555 m5 fs21">'.lang("Restore password").'</h4></center><br/><form method="POST">'
        . '<input type="text" required name="email" value="'.$_POST["email"].'" class="input w200 p5 mt5" placeHolder="Email" /><br/>'
        . '<div class="login_links"><a onClick=\'parent.window.location = "'.$_SERVER["DIR"].'/register";\'>'.lang("Sign Up").'</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a rel="nofollow" href="'.$_SERVER["DIR"].'/account.php">'.lang("Login").'</a></div>'
        . '<input type="submit" class="btn w200" value="'.lang("Submit").'" /></form></div>';   
    }
    $fout .= '</body></html>';
}else if($_GET["mode"] == "social"&&!empty($_GET["method"])){ 
    if($_GET["method"]=="fb"){
        require_once("engine/api/oauth/fb_auth.php");
    }else if($_GET["method"]=="vk"){
        require_once("engine/api/oauth/vk_auth.php");
    }else if($_GET["method"]=="tw"){
        require_once("engine/api/oauth/twitter_auth.php");
    }else if($_GET["method"]=="gp"){
        require_once("engine/api/oauth/google_auth.php");
    }
}else if($_GET["mode"] == "logout"){  
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
        <link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css">
        <link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css">';
    require_once("template/meta.php");
    $fout .= '
        </head>
        <body style="background: #fff;  opacity: 1; min-width: 200px;" class="nodes">'
            .engine::print_login_form().'
        </body>
        </html>';
    }
}die($fout);