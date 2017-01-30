<?php
/**
* Framework site class.
* @path /engine/nodes/site.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
class site{
public $title;          // Page title.
public $content;        // Page HTML data.
public $keywords;       // Array meta keywords.
public $description;    // Page meta description.
public $img;            // Page meta image.
public $onload;         // Page executable JavaScript code.
public $configs;        // Array MySQL configs.
//------------------------------------------------------------------------------
/**
* Site class constructor.
* Output HTML data of website or die with error. 
* 
* @param bool $compact "Compact" mode for async jQuery request output.
*/
function site($compact=0){
    require_once("engine/nodes/headers.php");
    require_once("engine/nodes/session.php");
    require_once("engine/nodes/language.php");
    require_once("engine/nodes/config.php");
    $this->keywords = array();
    $this->onload = '';
    $this->content = '';
    $this->configs = array();
    $query = 'SELECT * FROM `nodes_config`';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        $this->configs[$data["name"]] = $data["value"];
    }
    if($this->configs["debug"] && 
        empty($_SESSION["user"]["id"]) && 
        empty($_POST["nocache"])){
        die(engine::error(204));
    }
    $this->description = $this->configs["description"];
    if(!empty($this->configs["name"])){ 
        $this->title = $this->configs["name"];
    }else $this->title = $config["name"];
    if(!empty($this->configs['image'])){
        if(mb_strpos($this->configs["image"], "http://")!==FALSE){
            $this->img = $this->configs["image"];
        }else{
            if(mb_strpos($this->configs["image"], $_SERVER["DIR"])!==FALSE){ 
                $this->img = 'http://'.$_SERVER["HTTP_HOST"].$this->configs["image"];
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
            $object = mysql_fetch_object($res);
            if(!empty($object->file)){
                $_SERVER["CORE_PATH"] = $object->mode;
                require_once ("engine/site/".$object->file);
            }else{ 
                if($this->configs["default"]!="site.php"){
                    $query = 'SELECT * FROM `nodes_backend` WHERE `file` = "'.$this->configs["default"].'"';
                    $res = engine::mysql($query);
                    $object = mysql_fetch_object($res);
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
        require_once("template/".$_SESSION["template"]."/template.php");
    }
    if(!isset($_POST["jQuery"])){
        $query = 'SELECT * FROM `nodes_meta` WHERE `url` LIKE "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $this->description = trim(strip_tags($this->description));
        if(!empty($data)){ 
            if(!$data["mode"]) $this->description .= $data["description"];
            else $this->description = $data["description"];
        }
        if(mb_strlen($this->description) > 200) $this->description = mb_substr($this->description, 0, 200).'..';
        $this->title = trim(strip_tags($this->title));
        if(!empty($data)){ 
            if(!$data["mode"]) $this->title .= $data["title"];
            else $this->title = $data["title"];
        }
        if(mb_strlen($this->title) > 100) $this->title = mb_substr($this->title, 0, 100).'..';
        foreach($this->keywords as $keyword) $keywords .= $keyword.', ';   
        $keywords = mb_substr($keywords,0,mb_strlen($keywords)-2);
        if(empty($keywords)) $keywords = str_replace (' ', ', ', $this->description);
        $keywords = trim(strip_tags($keywords));
        if(!empty($data)){ 
            if(!$data["mode"]) $keywords .= $data["keywords"];
            else $keywords = $data["keywords"];
        }
        if(mb_strlen($keywords) > 300)
            $keywords = mb_substr($keywords, 0, 300).'..';
        $fout = '<!DOCTYPE html> <!-- Powered by Nodes Studio -->
<html itemscope itemtype="http://schema.org/WebSite" lang="'.$_SESSION["Lang"].'" style="background: url('.$_SERVER["DIR"].'/img/load.gif) no-repeat center center fixed; min-heigth: 400px;">
<head>
<title>'.$this->title.'</title>
<meta http-equiv="content-type" content="text/html" />
<meta charset="UTF-8" property="og:type" content="website" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Cache-control" content="no-cache" />
<meta name="robots" content="index, follow" />
<meta name="description" itemprop="description" content="'.$this->description.'" />
<meta property="og:title" itemprop="name" content="'.$this->title.'" />
<meta property="og:image" itemprop="image" content="'.$this->img.'" />
<meta property="og:description" content="'.$this->description.'" />
<meta property="og:url" content="'.$_SERVER["SCRIPT_URI"].'" />
<meta name="keywords" itemprop="keywords" content="'.$keywords.'" />
<meta name="apple-mobile-web-app-title" content="'.$this->configs["name"].'" />
<meta name="application-name" content="'.$this->configs["name"].'" />
<link rel="canonical" itemprop="url" href="'.$_SERVER["SCRIPT_URI"].'" />';
        require_once("template/meta.php");
        $fout .= '
</head>
<body style="opacity: 0;" class="nodes">
<img src="'.$_SERVER["DIR"].'/img/load.gif" style="display:none;" alt="'.lang("Loading").'" />';
    }else{
        $fout .= '<title>'.$this->title.'</title>';
    }
    $fout .= $this->content.'
<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css" />
<link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var root_dir = "'.$_SERVER["DIR"].'";</script>
<script src="'.$_SERVER["DIR"].'/script/jquery-1.11.1.js" type="text/javascript"></script>
<script src="'.$_SERVER["DIR"].'/script/script.js" type="text/javascript"></script>
<script src="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.js" type="text/javascript"></script>';
    if(!empty($_SESSION["user"]["id"])){
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = '.intval($_SESSION["user"]["id"]);
        $res = engine::mysql($query);
        $user = mysql_fetch_array($res);
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
        $fout .= '<script>jQuery(\'body\').append(\'<audio id="sound" autoplay preload><source src="'.$_SERVER["DIR"].'/res/load.wav" type="audio/wav"></audio>\');</script>';
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if($data["value"]=="1"){
        $fout .= '<script type="text/javascript"> if(window.jQuery){jQuery.ajax({url: "'.$_SERVER["DIR"].'/cron.php", async: true, type: "GET"});}</script>';
    }
    if(!isset($_POST["jQuery"])){
        $fout .= '
    <script type="text/javascript">
        function display(){ if(!window.jQuery) setTimeout(function(){ document.body.style.opacity = "1";}, 1000); else jQuery("html, body").animate({opacity: 1}, 1000); }'.
        'var tm = setTimeout(display, 5000); window.onload = function(){ try{ preload(); }catch(e){}; clearTimeout(tm); display(); }; function preload(){ '.$this->onload.'; return 0; }
    </script>
</body>
</html>';
    }else if(!empty($this->onload)){
        $fout .= '<script type="text/javascript">'.$this->onload.'</script>';
    }
    if($this->configs["compress"] || $_POST["nocache"]){
        $search = array('#>[\s]+<#si', '#>[\s]+([^\s]+)#si', '#([^\s]+)[\s]+<#si');
        $replace = array('> <', '> $1', '$1 <');
        $fout = preg_replace($search, $replace, $fout);
    }echo $fout;
}}