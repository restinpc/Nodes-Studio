<?php
/**
* Framework site primary class.
* @path /engine/nodes/site.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
class site{
public $title;          // Page title.
public $content;        // Page HTML data.
public $keywords;       // Array meta keywords.
public $description;    // Page meta description.
public $img;            // Page meta image.
public $onload;         // Page executable JavaScript code.
//------------------------------------------------------------------------------
/**
* Site class constructor.
* Output HTML data of website or die with error. 
*/
function site(){
    array_push($_SERVER["CONSOLE"], "new site()");
    require_once("engine/nodes/headers.php");
    require_once("engine/nodes/session.php");
    require_once("engine/nodes/config.php");
    $this->keywords = array();
    $this->onload = '';
    $this->content = '';
    $this->configs = array();
    $query = 'SELECT * FROM `nodes_config`';
    $res = engine::mysql($query);
    while($data = mysqli_fetch_array($res)){
        $this->configs[$data["name"]] = $data["value"];
    }
    if($this->configs["debug"] && 
        empty($_SESSION["user"]["id"]) && 
        empty($_POST["nocache"])){
        die(engine::error(204));
    }
    $_SESSION["temp_template"] = "";
    $this->description = $this->configs["description"];
    if(!empty($this->configs["name"])){ 
        $this->title = $this->configs["name"];
    }else $this->title = $config["name"];
    if(!empty($this->configs['image'])){
        if(strpos($this->configs["image"], "http")!==FALSE){
            $this->img = $this->configs["image"];
        }else{
            if(strpos($this->configs["image"], $_SERVER["DIR"])!==FALSE){ 
                $this->img = $_SERVER["PROTOCOL"].'://'.$_SERVER["HTTP_HOST"].$this->configs["image"];
            }else{ 
                if($this->configs["image"][0]=="/"){
                    $this->img = $_SERVER["PUBLIC_URL"].$this->configs["image"];
                }else{
                    $this->img = $_SERVER["PUBLIC_URL"].'/'.$this->configs["image"];
                }
            }
        }
    }else{
        $this->img = $_SERVER["PUBLIC_URL"].'/img/cms/nodes_studio.png';
    }
    if(empty($_SESSION["REQUEST_URI"])) $_SESSION["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
    if(empty($_SESSION["Lang"])) $_SESSION["Lang"] = $config["lang"];
    if(!empty($_POST["from"])) $_SESSION["from"] = $_POST["from"];
    if(!empty($_POST["to"])) $_SESSION["to"] = $_POST["to"];
    if(!empty($_POST["count"])) $_SESSION["count"] = intval($_POST["count"]);
    if(!empty($_POST["page"])) $_SESSION["page"] = intval($_POST["page"]);
    if(!empty($_POST["method"])) $_SESSION["method"] = $_POST["method"];
    if(!empty($_POST["order"])) $_SESSION["order"] = $_POST["order"];
    if(empty($_SESSION["count"])) $_SESSION["count"] = 20;
    if(empty($_SESSION["page"])) $_SESSION["page"] = 1;
    if(empty($_SESSION["method"])) $_SESSION["method"] = "ASC";
    if(empty($_SESSION["order"])) $_SESSION["order"] ="id";
    if($_SESSION["REQUEST_URI"] != $_SERVER["REQUEST_URI"]){
        $_SESSION["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
        $_SESSION["count"] = 20;
        $_SESSION["page"] = 1;
        $_SESSION["method"] = "ASC";
        $_SESSION["order"] = "id";
    }
    
    if(!$_POST["jQuery"]) unset($_SESSION["redirect"]);
    if(!empty($_SESSION["user"]["id"])&&
        empty($_SESSION["user"]["email"])&&
            ($_GET[0]!="account"||
            $_GET[1]!="settings")){
            $this->content = '<script>window.location = "'.$_SERVER["DIR"].'/account/settings";</script>';
    }else{
        if($_GET[0]=="admin"){
            $_SERVER["CORE_PATH"] = $_GET[0];
            require_once("engine/nodes/admin.php");
            new admin($this);
        }else{
            $query = 'SELECT * FROM `nodes_backend` WHERE `mode` = "'.$_GET[0].'"';
            $res = engine::mysql($query);
            $object = mysqli_fetch_object($res);
            if(!empty($object->file)){
                $_SERVER["CORE_PATH"] = $object->mode;
                require_once ("engine/site/".$object->file);
            }else{ 
                if($this->configs["default"]!="site.php"){
                    $query = 'SELECT * FROM `nodes_backend` WHERE `file` = "'.$this->configs["default"].'"';
                    $res = engine::mysql($query);
                    $object = mysqli_fetch_object($res);
                    if(!empty($object->file)){
                        $_SERVER["CORE_PATH"] = $object->mode;
                        require_once ("engine/site/".$object->file);  
                    }else{
                        $_SERVER["CORE_PATH"] = "content";
                        require_once ("engine/site/content.php");
                    }
                }else{
                    $_SERVER["CORE_PATH"] = "content";
                    require_once ("engine/site/content.php");
                }
            }
        }
        if(empty($_SESSION["vr_page"])) $_SESSION["page"] = '/';
        $_SERVER["cardboard"] = 0;
        $_SERVER["aframe"] = 0;
        if(strpos($this->content, "a-scene")){
            $_SERVER["aframe"] = 1;
            $_SERVER["cardboard"] = 1;
            if(strpos($this->content, "a-scene")){
                $_SESSION["vr_page"] = $_SERVER["REQUEST_URI"];
                require_once("template/".$_SESSION["template"]."/template.php");
                $template = $_SESSION["template"];
            }else{
                require_once("template/aframe/template.php");
                $template = 'aframe';
            }
        }else{
            if(strpos($this->content, "nodes_headset_content")){
                require_once("template/aframe/template.php");
                $template = 'aframe';
                $_SERVER["cardboard"] = 1;
            }else{
                $_SESSION["vr_page"] = $_SERVER["REQUEST_URI"];
                require_once("template/".$_SESSION["template"]."/template.php");
                $template = $_SESSION["template"];
            }
        }
    }
    if(!isset($_POST["jQuery"])){
        $query = 'SELECT * FROM `nodes_meta` WHERE `url` LIKE "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $this->description = trim(strip_tags($this->description));
        if(!empty($data)){ 
            if(!$data["mode"]) $this->description .= $data["description"];
            else if(!empty($data["description"])) $this->description = $data["description"];
        }
        if(strlen($this->description) > 200) $this->description = mb_substr($this->description, 0, 200).'..';
        $this->title = trim(strip_tags($this->title));
        if(!empty($data)){ 
            if(!$data["mode"]) $this->title .= $data["title"];
            else if(!empty($data["title"])) $this->title = $data["title"];
        }
        if(strlen($this->title) > 100) $this->title = mb_substr($this->title, 0, 100).'..';
        foreach($this->keywords as $keyword) $keywords .= $keyword.', ';   
        $keywords = mb_substr($keywords,0,strlen($keywords)-2);
        if(empty($keywords)) $keywords = str_replace (' ', ', ', $this->description);
        $keywords = trim(strip_tags($keywords));
        if(!empty($data)){ 
            if(!$data["mode"]) $keywords .= $data["keywords"];
            else if(!empty($data["keywords"])) $keywords = $data["keywords"];
        }
        if(strlen($keywords) > 300)
            $keywords = mb_substr($keywords, 0, 300).'..';
   
        $fout = '<!DOCTYPE html> <!-- Powered by Nodes Studio -->
<html itemscope itemtype="http://schema.org/WebSite" lang="'.$_SESSION["Lang"].'" style="background: url('.$_SERVER["DIR"].'/img/load.gif) no-repeat center center fixed; min-heigth: 400px;">
<head>
<title>'.$this->title.'</title>
<meta http-equiv="content-type" content="text/html" />
<meta charset="UTF-8" property="og:type" content="website" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1,shrink-to-fit=no,user-scalable=no,minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="Cache-control" content="no-cache" />
<meta name="robots" content="index, follow" />
<meta http-equiv="content-language" content="'.$_SESSION["lang"].'" />
<meta name="description" itemprop="description" content="'.$this->description.'" />
<meta property="og:title" itemprop="name" content="'.$this->title.'" />
<meta property="og:image" itemprop="image" content="'.$this->img.'" />
<meta property="og:description" content="'.$this->description.'" />
<meta property="og:url" content="'.$_SERVER["SCRIPT_URI"].'" />
<meta name="keywords" itemprop="keywords" content="'.$keywords.'" />
<meta name="apple-mobile-web-app-title" content="'.$this->configs["name"].'" />
<meta name="application-name" content="'.$this->configs["name"].'" />
<link rel="canonical" itemprop="url" href="'.$_SERVER["SCRIPT_URI"].'" />';
        require_once("engine/nodes/meta.php");
        if($_SERVER["aframe"]){
            $fout .= '
<script src="/script/aframe/aframe-master.js"></script>
';
        }
        $fout .= '
</head>
<body style="opacity: 0;" class="nodes">
<div style="position: fixed; bottom: 0px; left: 0px; width: 0%; height: 5px; background: #4473ba; display:none;" id="load_bar"></div>

';
if(!isset($_POST["jQuery"])){
    $fout .= '<script>';
    if($_SERVER["cardboard"]){
        $fout .= 'var vr_state = 1;';
    }else{
        $fout .= 'var vr_state = 0;';
    }
    $fout .= '
    var vr_control_state = -1;'
    . 'var loading_stages = 6;'
    . 'var loading_state = 0; '
    . 'var preloaded = 0;'
    . 'function display(){ '
        . 'if(parent.vr_control_state == -1){'
            . 'window.addEventListener("devicemotion", function(event) { '
                . 'try{ '
                    . 'if(event.rotationRate.alpha){ '
                        . 'document.getElementById("cardboard-control").style.display = "block"; '
                    . '} '
                . '}catch(e){} '
            . '});'
        . '}'
        . 'if(!window.jQuery) setTimeout(function(){ '
            . 'document.body.style.opacity = "1";'
        . '}, 1000); '
        . 'else jQuery("html, body").animate({opacity: 1}, 1000); '
    . '}'
    . 'var tm = setTimeout(display, 5000); '
    . 'function preload(){ if(!preloaded){ '.$this->onload.'; preloaded = 1; }  return 0;  } '
    . 'window.onload = loading_site; '
    . 'function loading_site(){ '
        . 'loading_state++; '
        . 'try{'
            . 'document.getElementById("load_bar").style.width = (loading_state/loading_stages*100)+"%"; '
            . 'document.getElementById("load_bar").style.display="block";'
        . '}catch(e){};'
        . 'if(loading_state!=loading_stages){ return; } '
        . 'try{ '
            . 'preload(); loading_state=2; '
            . 'setTimeout(function(){'
                . 'document.getElementById("load_bar").style.display = "none"; '
                . 'document.getElementById("load_bar").style.width = (loading_state/loading_stages*100)+"%"; '
            . '}, 1000);'
        . '}catch(e){};'
        . 'clearTimeout(tm); display();'
    . '};'
    . '</script>';
}
$fout .= '
<img src="'.$_SERVER["DIR"].'/img/load.gif" style="display:none;" alt="'.lang("Loading").'" />';
    }else{
        $fout .= '<title>'.$this->title.'</title>';
    }
    $fout .= $this->content.'
<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css" onLoad=\'loading_site();\' />
<link href="'.$_SERVER["DIR"].'/template/'.$template.'/template.css" rel="stylesheet" type="text/css" onLoad=\'loading_site();\' />
<script type="text/javascript">var root_dir = "'.$_SERVER["DIR"].'"; var submit_patterns = '.$this->configs["catch_patterns"].';';
if(!isset($_POST["jQuery"])) $fout .= ' var load_events = true;'; 
    $fout .= 
'</script>
<script src="'.$_SERVER["DIR"].'/script/jquery.js" type="text/javascript" onLoad=\'loading_site();\'></script>
<script src="'.$_SERVER["DIR"].'/script/script.js" type="text/javascript" onLoad=\'loading_site();\'></script>
<script src="'.$_SERVER["DIR"].'/template/'.$template.'/template.js" type="text/javascript" onLoad=\'loading_site();\'></script>';
    if((!empty($_SESSION["user"]["id"]) || $this->configs["public_notifications"]) && $_SERVER["PROTOCOL"] == 'https' ){
        if(!isset($_POST["jQuery"])){
            $fout .= '
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
<script src="'.$_SERVER["DIR"].'/notifications.php"></script>';
        }
    }
    if(!empty($_SESSION["user"]["id"])){
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = '.intval($_SESSION["user"]["id"]);
        $res = engine::mysql($query);
        $user = mysqli_fetch_array($res);
        if($user["ban"]=="1"){
            unset($_SESSION["user"]);
            die('<script type="text/javascript">parent.window.location = "'.$_SERVER["DIR"].'/";</script>');
        }else{
            $query = 'UPDATE `nodes_user` SET `online`='.date("U").', `ip` = "'.$_SERVER["REMOTE_ADDR"].'" '
                    . 'WHERE `id` = '.intval($_SESSION["user"]["id"]);
            engine::mysql($query);
            $fout .= engine::print_new_message($this);
        }
    }if(!empty($_POST["jQuery"])){
        $fout .= '<script>jQuery(\'body\').append(\'<audio id="sound" autoplay preload><source src="'.$_SERVER["DIR"].'/res/sounds/load.wav" type="audio/wav"></audio>\');</script>';
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if($data["value"]=="1"){
        $fout .= '<script type="text/javascript"> if(window.jQuery){jQuery.ajax({url: "'.$_SERVER["DIR"].'/cron.php", async: true, type: "GET"});}</script>';
    }
    if(!$_SERVER["cardboard"] && $this->configs["cardboard"]){
        $fout .= '<a href="'.$_SERVER["DIR"].'/aframe" target="_parent"><div id="cardboard-control"></div></a>';
    }
    if(!isset($_POST["jQuery"])){
        $fout .= '
</body>
</html>';
    }else{
        $fout .= '<script type="text/javascript">load_events = false; '.$this->onload.'</script>';
    }
    if($this->configs["debug"]){
        $fout .= '<script type="text/javascript">';
        foreach($_SERVER["CONSOLE"] as $value){
            $fout .= 'console.log("'.$value.'");'."\r\n";
        }
        $fout .= '</script>';
    }
    if($this->configs["compress"] || $_POST["nocache"]){
        $search = array('#>[\s]+<#si', '#>[\s]+([^\s]+)#si', '#([^\s]+)[\s]+<#si');
        $replace = array('> <', '> $1', '$1 <');
        $fout = preg_replace($search, $replace, $fout);
    }echo $fout;
}}