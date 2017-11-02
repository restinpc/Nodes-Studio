<?php
/**
* Framework session loader.
* @path /engine/nodes/session.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
ini_set('session.name', 'session_id');
ini_set('session.save_path', $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/session');
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length', '512');
session_set_cookie_params(0, '/', '.'.$_SERVER["HTTP_HOST"]);
session_name('session_id');
session_start();
require_once("engine/nodes/mysql.php");
if(!empty($_COOKIE["token"])){
    if($_GET["mode"]=="logout"){
        unset($_COOKIE['token']);
        unset($_COOKIE['session_id']);
        setcookie('token', null, -1, '/');
        setcookie('session_id', null, -1, '/');
    }else if(empty($_SESSION["user"])){
        $query = 'SELECT * FROM `nodes_user` WHERE `token` = "'.$_COOKIE["token"].'" '
                . 'AND `ip` = "'.$_SERVER["REMOTE_ADDR"].'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(!empty($data)){
            unset($data["pass"]);
            unset($data[5]);
            unset($data["token"]);
            unset($data[9]);
            $_SESSION["user"] = $data;
        }
    }
}else{
    setcookie("token", session_id(), time() + 2592000, '/');
    $_COOKIE["token"] = session_id();
}
if(!empty($_SESSION["user"])){
    $query = 'UPDATE `nodes_user` SET `token` = "'.session_id().'", '
        . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
    engine::mysql($query);
}
if(!empty($_POST["template"])){
    $_SESSION["template"] = $_POST["template"];
}else if(empty($_SESSION["template"])){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $_SESSION["template"] = $template = $data["value"];
}
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "token_limit"';
$res = engine::mysql($query);
$data = mysql_fetch_array($res);
$query = 'SELECT * FROM `nodes_attendance` WHERE `token` = "'.$_COOKIE["token"].'" ORDER BY `date` DESC LIMIT '.($data["value"]-1).', 1';
$res = engine::mysql($query);
$data= mysql_fetch_array($res);
$date = $data["date"];
if(date("U")-$date<60){
    die("Too many requests in this session. Try again after ".(60-(date("U")-$date))." seconds.");
}else if(!empty($_SERVER["REMOTE_ADDR"]) && !intval($_SERVER["CRON"])){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "ip_limit"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_attendance` WHERE `ip` = "'.$_SERVER["REMOTE_ADDR"].'" ORDER BY `date` DESC LIMIT '.($data["value"]-1).', 1';
    $res = engine::mysql($query);
    $data= mysql_fetch_array($res);
    $date = $data["date"];
    if(date("U")-$date<60){
        die("Too many requests from your IP. Try again after ".(60-(date("U")-$date))." seconds.");
    }else{
        $query = 'SELECT * FROM `nodes_agent` WHERE `name` LIKE "'.$_SERVER["HTTP_USER_AGENT"].'"';
        $res = engine::mysql($query);
        $agent = mysql_fetch_array($res);
        $is_bot = 0;
        if(!empty($agent)){ 
            $agent_id = $agent["id"];
            if($agent["bot"]) $is_bot = 1;
        }else{
            $query = 'INSERT INTO `nodes_agent`(name, bot) VALUES("'.$_SERVER["HTTP_USER_AGENT"].'", "0")';
            engine::mysql($query);
            $agent_id = mysql_insert_id();
        }
        $query = 'SELECT * FROM `nodes_referrer` WHERE `name` LIKE "'.$_SERVER["HTTP_REFERER"].'"';
        $res = engine::mysql($query);
        $ref = mysql_fetch_array($res);
        if(!empty($agent)){ 
            $ref_id = $ref["id"];
        }else if(!empty($_SERVER["HTTP_REFERER"])){
            if(strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) === false){
                $query = 'INSERT INTO `nodes_referrer`(name) VALUES("'.$_SERVER["HTTP_REFERER"].'")';
                engine::mysql($query);
                $ref_id = mysql_insert_id();
            }else $ref_id = -1;
        }else $ref_id = 0;
        if(strpos($_SERVER["SCRIPT_URI"], "/search")===false
            && strpos($_SERVER["SCRIPT_URI"], "/account")===false
            && strpos($_SERVER["SCRIPT_URI"], "/admin")===false
            && strpos($_SERVER["SCRIPT_URI"], ".php")===false
            && strpos($_SERVER["SCRIPT_URI"], ".xml")===false
            && strpos($_SERVER["SCRIPT_URI"], ".txt")===false){
            if(empty($_SERVER["SCRIPT_URI"])) $_SERVER["SCRIPT_URI"]='/';
            $cache = new cache();
            $cache_id = $cache->page_id();
            if($cache_id && !$is_bot){
                $query = 'INSERT INTO `nodes_attendance`(cache_id, user_id, token, ref_id, ip, agent_id, date, display) '
                        . 'VALUES("'.$cache_id.'", "'.intval($_SESSION["user"]["id"]).'", "'.session_id().'", "'.$ref_id.'", "'.$_SERVER["REMOTE_ADDR"].'", "'.$agent_id.'", "'.date("U").'", "'.intval($_SESSION["display"]).'")';
            }else if($cache_id){
                $query = 'INSERT INTO `nodes_attendance`(cache_id, user_id, token, ref_id, ip, agent_id, date, display) '
                . 'VALUES("'.$cache_id.'", "'.intval($_SESSION["user"]["id"]).'", "'.session_id().'", "'.$ref_id.'", "'.$_SERVER["REMOTE_ADDR"].'", "'.$agent_id.'", "'.date("U").'", "0")';     
            }
            engine::mysql($query);
        }  
    }
}