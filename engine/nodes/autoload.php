<?php /* Nodes Studio system file. Do not edit! */
error_reporting(0); $time=microtime(); 
$_SERVER["DIR"] = str_replace("/index.php", "", 
    str_replace($_SERVER["DOCUMENT_ROOT"], "", $_SERVER["SCRIPT_FILENAME"])); 
if(!file_exists("engine/nodes/engine.php")){ 
    die(require_once("engine/code/install.php")); 
}require_once("engine/nodes/engine.php");
$request = str_replace("index.php", "", str_replace("index.php?", "", 
    substr($_SERVER["REQUEST_URI"], strpos($_SERVER["SCRIPT_NAME"], "index.php"),
    strlen($_SERVER["REQUEST_URI"]))));
if(strpos($request, "?")!==FALSE){ 
    $args = substr($request, strpos($request, "?"));
    $request = substr($request, 0, strpos($request, "?"));
}$get = explode("/", $request); unset($_GET);
for($i = 0; $i < count($get); $i++) if(!empty($get[$i])) $_GET[count($_GET)] = $get[$i];
preg_match_all('/[\?|\&]?([^=]+)=([^\&]+)\&?/six', $args, $m);
for($i = 0; $i<count($m[1]);$i++) 
    $_GET[$m[1][$i]] = $m[2][$i];
$_REQUEST = array_merge($_GET, $_POST);
if(empty($_GET[0])){ unset($_GET); }else{
    if(strpos($_GET[0], "robots.txt")!==FALSE) $_GET[0] = str_replace("robots.txt", "robots.php", $_GET[0]);
    if(strpos($_GET[0], "rss.xml")!==FALSE) $_GET[0] = str_replace("rss.xml", "rss.php", $_GET[0]);
    if(strpos($_GET[0], "sitemap.xml")!==FALSE) $_GET[0] = str_replace("sitemap.xml", "sitemap.php", $_GET[0]);
}if(strpos($_GET[0], ".php")){
    if(file_exists("engine/code/".$_GET[0])){
        die(require_once ("engine/code/".$_GET[0]));
    }else engine::error();
}else{ 
    require_once("engine/nodes/site.php");
    $site = new site();
    if(empty($_POST["catch"])) die("<!-- ".(microtime()-$time)." -->");
}