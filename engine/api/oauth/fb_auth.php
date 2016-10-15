<?php
/* Nodes Studio system file. Do not edit! */
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "fb_id"';
$res = engine::mysql($query);
$id = mysql_fetch_array($res);
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "fb_secret"';
$res = engine::mysql($query);
$secret = mysql_fetch_array($res);
$_SESSION["request"] = date("U");
preg_match('#code=(.*+)#six',$_SERVER["REQUEST_URI"], $m);
$code = $m[1];
if(empty($code)){
    header('Location: https://www.facebook.com/dialog/oauth?client_id='.$id["value"].'&redirect_uri='.urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"]."/account.php?mode=social&method=fb").'&response_type=code');
    die('<script>parent.window.location = "https://www.facebook.com/dialog/oauth?client_id='.$id["value"].'&redirect_uri='.urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"]."/account.php?mode=social&method=fb").'&response_type=code";</script>');
}else{
    $path = "https://graph.facebook.com/oauth/access_token?client_id=".$id["value"]."&redirect_uri=".urlencode("http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"]."/account.php?mode=social&method=fb")."&client_secret=".$secret["value"]."&code=".$code;
    $var = file_get_contents($path);
    preg_match_all('/access_token=(.*?)&/', $var, $m);
    $graphUrl = "https://graph.facebook.com/me?access_token=".$m[1][0];
    $oUser = json_decode(file_get_contents($graphUrl));
    $link = 'https://www.facebook.com/'.$oUser->id;
    if($_SESSION["request"]>date("U")-60){
        $_SESSION["request"] = '';
        if(!empty($oUser->name)&&!empty($oUser->id)){
            if(!empty($_SESSION["user"]["email"])){
                $query = 'UPDATE `nodes_users` SET `url` = "'.$link.'" WHERE `email` = "'.$_SESSION["user"]["email"].'"';
                engine::mysql($query);
                $query = 'SELECT * FROM `nodes_users` WHERE `email` = "'.$_SESSION["user"]["email"].'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
            }else{
                $query = 'SELECT * FROM `nodes_users` WHERE `url` = "'.$link.'"'; 
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(empty($data)){
                    $query = 'INSERT INTO `nodes_users`(name, photo, url, online, confirm) VALUES("'.$oUser->name.'", "anon.jpg", "'.$link.'", "'.date("U").'", "1")';
                    $res = engine::mysql($query);
                    $query = 'SELECT * FROM `nodes_users` WHERE `url` = "'.$link.'"';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                }
            }
            unset($data["pass"]);
            unset($data[5]);
            unset($data["token"]);
            unset($data[9]);
            $_SESSION["user"] = array();
            $_SESSION["user"] = $data;
            die('<script>parent.window.location = "'.$_SERVER["DIR"].'/account/settings";</script>');
            
        }else die('<script>alert("'.lang("Authentication error").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>'); 
    }else{
        $_SESSION["request"] = '';
        die('<script>alert("'.lang("Authentication timeout").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>');
    }
}