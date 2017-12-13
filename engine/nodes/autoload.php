<?php
/**
* Framework autoloader.
* @path /engine/nodes/autoload.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
error_reporting(0); 
date_default_timezone_set('UTC');
$GLOBALS["time"] = doubleval(microtime(1)); 
$_SERVER["CONSOLE"] = array();
$_SERVER["DIR"] = str_replace("/cron.php", "", str_replace("/index.php", "", 
    str_replace($_SERVER["DOCUMENT_ROOT"], "", $_SERVER["SCRIPT_FILENAME"])));
$_SERVER["PUBLIC_URL"] = "http://".$_SERVER["HTTP_HOST"].$_SERVER["DIR"];
ini_set('include_path', $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]);
require_once('engine/core/engine.php');
if(!file_exists($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/engine/nodes/config.php")
    &&!file_exists("engine/nodes/config.php")){
    die(require_once("engine/code/install.php")); 
}
$request = str_replace("index.php", "", mb_substr($_SERVER["REQUEST_URI"], 
    strpos($_SERVER["SCRIPT_NAME"], "index.php"), strlen($_SERVER["REQUEST_URI"])));
if(strpos($request, "?")!==FALSE){ 
    $args = mb_substr($request, strpos($request, "?"));
    $request = mb_substr($request, 0, strpos($request, "?"));
}else{ 
    $args = '';
}
$get = explode("/", $request);
$_GET = array();
for($i = 0; $i < count($get); $i++){
    if(!empty($get[$i])) $_GET[count($_GET)] = $get[$i];
}
preg_match_all('/[\?|\&]?([^=]+)=([^\&]+)\&?/six', $args, $m);
for($i = 0; $i<count($m[1]);$i++){ 
    $_GET[$m[1][$i]] = $m[2][$i];
}
$_REQUEST = array_merge($_GET, $_POST);
if(empty($_SERVER["SCRIPT_URI"])){
    $_SERVER["SCRIPT_URI"] = $_SERVER["REQUEST_URI"];
}
if(strpos($_SERVER["SCRIPT_URI"], "http://")===FALSE){
    if($_SERVER["SCRIPT_URI"][0] == "/"){
        $_SERVER["SCRIPT_URI"] = "http://".$_SERVER["HTTP_HOST"].
            $_SERVER["DIR"].$_SERVER["SCRIPT_URI"];
    }else{
        $_SERVER["SCRIPT_URI"] = "http://".$_SERVER["HTTP_HOST"].
            $_SERVER["DIR"].'/'.$_SERVER["SCRIPT_URI"];
    }
    $_SERVER["SCRIPT_URI"] = str_replace("http://","\$h", $_SERVER["SCRIPT_URI"]);  
}
while($_SERVER["SCRIPT_URI"][strlen($_SERVER["SCRIPT_URI"])-1]=="/"){
    $_SERVER["SCRIPT_URI"] = mb_substr($_SERVER["SCRIPT_URI"], 0, 
    strlen($_SERVER["SCRIPT_URI"])-1);
}
$_SERVER["SCRIPT_URI"] = str_replace("\$h", "http://", $_SERVER["SCRIPT_URI"]);
if($_SERVER["SCRIPT_URI"] == $_SERVER["PUBLIC_URL"]){
    $_SERVER["SCRIPT_URI"] .= '/';
}
$skip = array('.', '..', 'engine.php');
$files = scandir($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/engine/core/');
foreach($files as $file) {
    if(!in_array($file, $skip)){
        if(is_file($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/engine/core/'.$file)){
            require_once('engine/core/'.$file); 
        }
    }
}
if(strpos($_GET[0], "robots.txt")!==FALSE) 
    $_GET[0] = str_replace("robots.txt", "robots.php", $_GET[0]);
if(strpos($_GET[0], "rss.xml")!==FALSE) 
    $_GET[0] = str_replace("rss.xml", "rss.php", $_GET[0]);
if(strpos($_GET[0], "sitemap.xml")!==FALSE) 
    $_GET[0] = str_replace("sitemap.xml", "sitemap.php", $_GET[0]);
if(!empty($_GET[0]) && strpos($_GET[0], ".php") && 
( file_exists($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/engine/code/".$_GET[0])
|| file_exists("engine/code/".$_GET[0])
)){
    die(require_once ("engine/code/".$_GET[0]));
}else{
    require_once("engine/nodes/site.php"); 
    $site = new site();
    if(empty($_POST["catch"]))
    die("<!-- ".(doubleval(microtime(1)-$GLOBALS["time"]))." -->");
}