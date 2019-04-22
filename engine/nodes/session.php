<?php
/**
* Framework session loader.
* @path /engine/nodes/session.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
$session_lifetime = 2592000;
session_set_cookie_params($session_lifetime, '/', '.'.$_SERVER["HTTP_HOST"]);
session_name('token');
session_start();
require_once("engine/nodes/mysql.php");
if(!empty($_COOKIE["token"])){
    if(empty($_SESSION["user"])){
        $query = 'SELECT * FROM `nodes_user` WHERE `token` = "'.$_COOKIE["token"].'" '
                . 'AND `ip` = "'.$_SERVER["REMOTE_ADDR"].'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(!empty($data)){
            unset($data["pass"]);
            unset($data[5]);
            unset($data["token"]);
            unset($data[9]);
            $_SESSION["user"] = $data;
        }
    }
}else{
    $_COOKIE["token"] = session_id();
    if(!empty($_SESSION["user"])){
        $query = 'UPDATE `nodes_user` SET `token` = "'.session_id().'", '
            . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
        engine::mysql($query);
    }
}
if(!empty($_POST["template"])){
    $_SESSION["template"] = $_POST["template"];
}else if(empty($_SESSION["template"])){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $_SESSION["template"] = $template = $data["value"];
}
if(empty($_SESSION["Lang"])){ 
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "language"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $_SESSION["Lang"] = $data["value"];
}
if(!empty($_GET["lang"])){
    $_SESSION["Lang"] = strtolower ($_GET["lang"]);
}else if(!empty($_POST["lang"])){
    $_SESSION["Lang"] =  strtolower ($_POST["lang"]);
}
function lang($key){
    $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$key.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data["value"])){
        return $data["value"];
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "language"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$key.'" AND `lang` = "en" AND `value` <> ""';
        $res = engine::mysql($query);
        $d = mysqli_fetch_array($res);
        if(!empty($d)){
            return $d["value"];
        }else{
            $query = 'INSERT INTO `nodes_language`(name, lang, value) VALUES("'.$key.'", "en", "'.$key.'")';
            engine::mysql($query);
            return $key;
        }
    }
}
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "token_limit"';
$res = engine::mysql($query);
$data = mysqli_fetch_array($res);
$query = 'SELECT * FROM `nodes_attendance` WHERE `token` = "'.$_COOKIE["token"].'" ORDER BY `date` DESC LIMIT '.($data["value"]-1).', 1';
$res = engine::mysql($query);
$data= mysqli_fetch_array($res);
$date = $data["date"];
if(date("U")-$date<60){
    header('HTTP/ 429 Too Many Requests', true, 429);
    die("Too many requests in this session. Try again after ".(60-(date("U")-$date))." seconds.");
}else if(!empty($_SERVER["REMOTE_ADDR"]) && !intval($_SERVER["CRON"])){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "ip_limit"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_attendance` WHERE `ip` = "'.$_SERVER["REMOTE_ADDR"].'" ORDER BY `date` DESC LIMIT '.($data["value"]-1).', 1';
    $res = engine::mysql($query);
    $data= mysqli_fetch_array($res);
    $date = $data["date"];
    if(date("U")-$date<60){
        header('HTTP/ 429 Too Many Requests', true, 429);
        die("Too many requests from your IP. Try again after ".(60-(date("U")-$date))." seconds.");
    }else{
        $query = 'SELECT * FROM `nodes_agent` WHERE `name` LIKE "'.$_SERVER["HTTP_USER_AGENT"].'"';
        $res = engine::mysql($query);
        $agent = mysqli_fetch_array($res);
        $is_bot = 0;
        if(!empty($agent)){ 
            $agent_id = $agent["id"];
            if($agent["bot"]) $is_bot = 1;
        }else{
            if(strpos($_SERVER["HTTP_USER_AGENT"], 'bot') || strpos($_SERVER["HTTP_USER_AGENT"], 'crawler')){
                $bot = 1;
            }else{
                $bot = 0;
            }
            $query = 'INSERT INTO `nodes_agent`(name, bot) VALUES("'.$_SERVER["HTTP_USER_AGENT"].'", "'.$bot.'")';
            engine::mysql($query);
            $agent_id = mysqli_insert_id($_SERVER["sql_connection"]);
        }
        $query = 'SELECT * FROM `nodes_referrer` WHERE `name` LIKE "'.$_SERVER["HTTP_REFERER"].'"';
        $res = engine::mysql($query);
        $ref = mysqli_fetch_array($res);
        if(!empty($agent)){ 
            $ref_id = $ref["id"];
        }else if(!empty($_SERVER["HTTP_REFERER"])){
            if(strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) === false){
                $query = 'INSERT INTO `nodes_referrer`(name) VALUES("'.$_SERVER["HTTP_REFERER"].'")';
                engine::mysql($query);
                $ref_id = mysqli_insert_id($_SERVER["sql_connection"]);
            }else $ref_id = -1;
        }else $ref_id = 0;
        if(empty($_SESSION["user"]["name"]) && empty($_POST["nocache"])){
            $query = 'SELECT * FROM `nodes_anonim` WHERE `token` = "'.session_id().'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if(!empty($data)){
                $_SESSION["user"]["name"] = lang("Anonim").' '.$data["id"];
                $_SESSION["user"]["anonim"] = $data["id"];
            }else{
                $query = 'INSERT INTO `nodes_anonim`(`token`, `ip`, `date`) '
                        . 'VALUES("'.session_id().'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("Y-m-d H:i:s").'")';
                engine::mysql($query);
                $anonim_id = mysqli_insert_id($_SERVER["sql_connection"]);
                $_SESSION["user"]["name"] = lang("Anonim").' '.$anonim_id;
                $_SESSION["user"]["anonim"] = $anonim_id;
            }
        }
        if(strpos($_SERVER["SCRIPT_URI"], "/search")===false
            && strpos($_SERVER["SCRIPT_URI"], "/account")===false
            && strpos($_SERVER["SCRIPT_URI"], "/admin")===false
            && strpos($_SERVER["SCRIPT_URI"], ".php")===false
            && strpos($_SERVER["SCRIPT_URI"], ".xml")===false
            && strpos($_SERVER["SCRIPT_URI"], ".js")===false
            && strpos($_SERVER["SCRIPT_URI"], ".txt")===false){
            if(empty($_SERVER["SCRIPT_URI"])) $_SERVER["SCRIPT_URI"]='/';
            $cache = new cache();
            $cache_id = $cache->page_id();
            $date_now = date("U");
            if($cache_id && !$is_bot){
                $query = 'INSERT INTO `nodes_attendance`(cache_id, user_id, token, ref_id, ip, agent_id, date, display) '
                        . 'VALUES("'.$cache_id.'", "'.intval($_SESSION["user"]["id"]).'", "'.session_id().'", "'.$ref_id.'", "'.$_SERVER["REMOTE_ADDR"].'", "'.$agent_id.'", "'.$date_now.'", "'.intval($_SESSION["display"]).'")';
            }else if($cache_id){
                $query = 'INSERT INTO `nodes_attendance`(cache_id, user_id, token, ref_id, ip, agent_id, date, display) '
                . 'VALUES("'.$cache_id.'", "'.intval($_SESSION["user"]["id"]).'", "'.session_id().'", "'.$ref_id.'", "'.$_SERVER["REMOTE_ADDR"].'", "'.$agent_id.'", "'.$date_now.'", "0")';     
            }
            engine::mysql($query);
        }  
    }
}