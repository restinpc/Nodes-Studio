<?php
/**
* User identity system script.
* @path /engine/code/account.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if($_GET["mode"] == "remember" && !empty($_GET["email"]) && !empty($_GET["code"])){
    $email = urldecode($_GET["email"]);
    $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
    $res = engine::mysql($query);
    $data = mysqli_num_rows($res);
    if($data){
        $code = substr(md5($email.date("Y-m-d")), 0, 4);
        if($code == $_GET["code"]){
            $new_pass = substr(md5($email.date("Y-m-d")), 0, 8);
            $password = engine::encode_password(trim(strtolower($new_pass)));
            $pass = $password["pass"];
            $salt = $password["salt"];
            $query = 'UPDATE `nodes_user` SET `pass` = "'.$pass.'", `salt` = "'.$salt.'" WHERE `email` = "'.$email.'"';   
            engine::mysql($query);
            echo '<div class="center pt100">'.lang("New password activated!").'</div>
                    <script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';
        }else{
            echo '<div class="center pt100">'.lang("Invalid confirmation code").'.</div>'
             . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';  
        }
    }else{
       echo '<div class="center pt100">Email '.lang("not found").'.</div>'
        . '<script>function redirect(){parent.window.location="'.$_SERVER["DIR"].'/login";}setTimeout(redirect, 3000);</script>';  
    }
}else if($_GET["mode"] == "social" && !empty($_GET["method"])){ 
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
    unset($_COOKIE['token']);
    setcookie('token', null, -1, '/');
    die('<script language="JavaScript">parent.window.location = "'.$_SERVER["DIR"].'/";</script>');
}else engine::error(404);