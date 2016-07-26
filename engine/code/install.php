<?php
function output(){
$output = '';
if(!empty($_POST["mysql_server"])){
    $error_output = '<br/><br/>'
    . '<center>'
    . '<a href="'.$_SERVER["DIR"].'/install.php"><input type="button" value="Reinstall site" class="btn" style="width: 280px;" /></a><br/><br/>'
    . '</center>';
    $flag = 0;
    $output .= 'Checking MySQL connection.. ';
    if(mysql_connect($_POST["mysql_server"], 
        $_POST["mysql_login"],
        $_POST["mysql_pass"])){
        if(mysql_select_db($_POST["mysql_db"]))
            $flag = 1;
    }if($flag){
        $output .= "Ok.<br/>";
        $query = "DROP TABLE IF EXISTS 
    `nodes_attendance`,
    `nodes_catalog` ,
    `nodes_catch` ,
    `nodes_comments` ,
    `nodes_config` ,
    `nodes_content` ,
    `nodes_language` ,
    `nodes_message` ,
    `nodes_module` ,
    `nodes_transactions` ,
    `nodes_users` ,
    `nodes_logs` ;
        
CREATE TABLE IF NOT EXISTS `nodes_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `text` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `nodes_config` (`name`, `value`, `text`) VALUES
('name', '".mysql_real_escape_string($_POST["name"])."', 'Site name'),
('description', '".mysql_real_escape_string($_POST["description"])."', 'Description'),
('image', '".mysql_real_escape_string($_POST["image"])."', 'Site image'),
('email', '".mysql_real_escape_string($_POST["admin_email"])."', 'Site email'),
('language', '".mysql_real_escape_string($_POST["language"])."', 'Site language'),
('languages', '".mysql_real_escape_string(str_replace("'", "\'", $_POST["languages"]))."', 'Available languages'),
('encoding', '".intval($_POST["encoding"])."', 'Encoding source');

CREATE TABLE IF NOT EXISTS `nodes_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(400) NOT NULL,
  `photo` varchar(400) NOT NULL,
  `url` varchar(400) NOT NULL,
  `email` varchar(400) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `balance` double NOT NULL,
  `ip` varchar(20) NOT NULL,
  `ban` tinyint(1) NOT NULL,
  `online` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
    `confirm` BOOLEAN NOT NULL,
    `code` INT( 4 ) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `nodes_users` (`id`, `name`, `photo`, `url`, `email`, `pass`, `balance`, `ip`, `ban`, `online`, `token`, `confirm`, `code`) VALUES
(1, '".mysql_real_escape_string(str_replace("'", "\'", $_POST["admin_name"]))."', '".$_SERVER["DIR"]."/img/pic/admin.jpg', '', '".htmlspecialchars($_POST["admin_email"])."', '".md5(strtolower($_POST["admin_pass"]))."', 0, '', -1, '', 'd4365236bc832dad6871c4ccd4d310f0', '1', '0');

";      $arr = explode(";
", $query);
    $flag = 0;
        foreach($arr as $a){
            $a = trim($a);
            if(!empty($a)){
                @mysql_query("SET NAMES utf8");
                mysql_query($a) or die(mysql_error());
            }
        }
        $output .= "Receiving MySQL data.. ";
        $sql = file_get_contents("http://nodes-studio.com/source/1/mysql.txt");
        $arr = explode(";"."\n", $sql);
        $flag = 0;
        foreach($arr as $a){
            $a = trim($a);
            if(!empty($a)){
                @mysql_query("SET NAMES utf8");
                mysql_query($a) or die(mysql_error());
                $flag++;
            }
        }

        if($flag) $output .= "Ok.<br/>Executed ".$flag." MySQL commands.</br>";
        else if(empty($arr)) $output .= "Ok. File is empty.<br/>";
        else return($output."Error.<br/>Installation aborted.".$error_output);
        $output .= 'Generation config.php.. ';
        $fname = "engine/nodes/config.php";
        $fname = fopen($fname, 'w') or die("Error. Can't open file engine/nodes/config.php");
        $source = 'global $config; 
$config = array(
    "name" => "'. mysql_real_escape_string($_POST["name"]).'",
    "sql_server" => "'. mysql_real_escape_string($_POST["mysql_server"]).'",
    "sql_login" => "'. mysql_real_escape_string($_POST["mysql_login"]).'",
    "sql_pass" => "'. mysql_real_escape_string($_POST["mysql_pass"]).'",
    "sql_db" => "'. mysql_real_escape_string($_POST["mysql_db"]).'"
);';    
        if(intval($_POST["encoding"])){
            $encode = base64_encode($source);
            $code = '<?php /* Nodes Studio system file. Do not edit! */ eval(base64_decode("'.$encode.'"));';
        }else{
            $code = '<?php /* Nodes Studio system file. Do not edit! */ '.$source;
        }
        fwrite($fname, $code);
        fclose($fname);
        $output .= 'Ok.<br/>Downloading engine files..';
        $files = file_get_contents("http://nodes-studio.com/source/1/engine.txt");
        $arr = explode("\n", $files);
        foreach($arr as $a){
            $a = trim(str_replace("\r", "", $a));
            if(!empty($a)){
                if(intval($_POST["encoding"])){
                    $file = file_get_contents("http://nodes-studio.com/source/installer.php?version=1&file=/".$a);
                }else{
                    $file = file_get_contents("http://nodes-studio.com/source/installer.php?version=1&no_encode=1&file=/".$a);
                }
                if($file!="error" && !empty($file)){
                    if(!empty($_SERVER["DIR"])) $dir = substr($_SERVER["DIR"], 1)."/";
                    $a = fopen($a, 'w') or die("Can't open file ".$a);
                    fwrite($a, $file);
                    fclose($a);
                }else{
                    return($output."<br/>Error while receiving ".$a.".<br/>Installation aborted.".$error_output);
                }
            }
        }$output .= ' Ok.<br/>';
        $query = 'SELECT * FROM `nodes_users` WHERE `id` = "1"';
        @mysql_query("SET NAMES utf8");
        $res = mysql_query($query) or die(mysql_error());
        $data = mysql_fetch_array($res);
        unset($data["pass"]);
        unset($data[5]);
        unset($data["token"]);
        unset($data[9]);
        $_SESSION["user"] = $data;
        $_SESSION["Lang"] =  strtolower ($_POST["language"]);
        $new_version = file_get_contents("http://nodes-studio.com/source/updater.php?version=1");
        return($output.'
Installation completed!
<script>
function new_update(){
    jQuery("#content").animate({opacity: 0}, 300);
    document.getElementById("post_form").submit();
}setTimeout(new_update, 5000);
</script>
<form method="POST" id="post_form" action="'.$_SERVER["DIR"].'/install.php">
    <input type="hidden" name="version" value="'.intval($new_version).'" />
    Updating engine to latest version after 5 seconds.<br/>
</form>
    ');
    }else{
        return($output."Error.<br/>Installation aborted.".$error_output);
    }
}else if(!empty($_POST["version"])){
    require_once("engine/code/update.php");
    $fout .= '<br/><center>
        <a href="'.$_SERVER["DIR"].'/"><input type="button" class="btn" style="width: 280px;" value="Main page" /></a><br/><br/>
        <a href="'.$_SERVER["DIR"].'/admin"><input type="button" class="btn" style="width: 280px;" value="Admin page" /></a><br/><br/>
        </center>';
    return $fout;
}else{
    if(function_exists('exec')&&function_exists('base64_decode')&&function_exists('base64_encode')){
        $options = '<option value="1" selected>Yes</option><option value="0">No</option>';
    }else{
        $options = '<option value="1">Yes</option><option value="0" selected>No</option>';   
    }
    $output = '<div style="float:right; text-align:center;" class=\'right-center\'>
    <img itemprop="logo" src="'.$_SERVER["DIR"].'/img/cms/nodes_studio.png" style="width: 100%; max-width: 395px;" />
    <br/>
</div> 
<div style="clear:left;"></div>
<div style="float:left; text-align: left; padding-left: 10%; padding-top: 5px; white-space:nowrap;">
    <form method="POST" id="post_form">
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql server</div> <input class="input" type="text" required="required" name="mysql_server" value="localhost" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql login</div> <input class="input" type="text" required="required" name="mysql_login" value="root" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql pass</div> <input class="input" type="text" name="mysql_pass" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql DB</div> <input class="input" type="text" required="required" name="mysql_db" value="" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin name</div> <input class="input" required="required" type="text" name="admin_name" value="Admin" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin email</div> <input class="input" required="required" type="text" name="admin_email" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin pass</div> <input class="input" required="required" type="text" name="admin_pass" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site name</div> <input class="input" required="required" type="text" name="name" value="Nodes Studio" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site description</div> <input class="input" required="required" type="text" name="description" value="Web 2.0 Framework" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site image</div> <input class="input" required="required" type="text" name="image" value="/img/logo.png" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site language</div> <input class="input" required="required" type="text" name="language" value="en" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site languages</div> <input class="input" required="required" type="text" name="languages" value="en;ru;gr;" ><br/>
    <div style="width: 210px; float: left; margin-right: 10px;">Encoding source</div> <select class="input" type="text" name="encoding">'.$options.'</select><br/>
    </form><br/>
</div>
<div style=\'width: 100%; max-width: 395px; text-align: center; float:right;\' class=\'right-center\'>
    <input type="button" class="btn" onClick=\'document.getElementById("content").style.display = "none"; document.getElementById("post_form").submit();\' value="Install Now" style="width: 280px;" />
    <br/><br/>
</div>
<div style="clear:both;"></div>';
}return $output;
}
session_name("token");
session_cache_limiter('private');
session_cache_expire((1*1));
session_start();
if(!file_exists("engine/nodes/engine.php")||$_SESSION["user"]["id"]=="1"){
    $output = output();
}else{
    die("Access Denied");
}
?><!DOCTYPE html>
<html lang="en" style="background: url(<?php echo $_SERVER["DIR"]; ?>/img/load.gif) no-repeat center center fixed;">
<script language="JavaScript" type="text/javascript">new Image().src = "<?php echo $_SERVER["DIR"]; ?>/img/load.gif";</script>
<head>
<title>Nodes Studio - Framework Setup</title>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="Nodes Studio - Web 2.0 Framework" />
<meta property="og:description" content="Nodes Studio - Web 2.0 Framework" />
<meta property="og:image" content="/favicon.png" />
<meta name="keywords" content="Nodes Studio" />
<link href="<?php echo $_SERVER["DIR"]; ?>/templates/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $_SERVER["DIR"]; ?>/templates/default/template.css" rel="stylesheet" type="text/css" />
<?php
require_once 'templates/meta.php';
echo $fout;
?>
</head>
<body>
<div id="content">
<!-- content -->
<section id="contentSection">
<div class="container">
<section id="topSection"  style="padding-top: 20px;">
<div class="container">
    <h1><strong>Nodes Studio</strong></h1><br/>
    <p>Web 2.0 Framework Setup</p>
</div>
</section>
<div id="mainSection" style="padding-top: 70px; line-height: 2.2; text-align: left;">
<?php echo $output; ?>
</div>
</div>
</section>
<!-- /content -->
</div>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script language="JavaScript" type="text/javascript"> if(!window.jQuery) document.write(unescape('<script type="text/javascript" src="<?php echo $_SERVER["DIR"]; ?>/script/jquery-1.11.1.min.js">%3C/script%3E')); </script>
</body>
<script language="JavaScript" type="text/javascript">
function display(){ if(!window.jQuery) setTimeout(function(){ document.body.style.opacity = "1";}, 1000); 
else jQuery("html, body").animate({opacity: 1}, 1000); }var tm = setTimeout(display, 5000); window.onload = function(){ clearTimeout(tm); display(); 
if(!window.jQuery) document.write(unescape('<script type="text/javascript" src="<?php echo $_SERVER["DIR"]; ?>/script/jquery-1.11.1.min.js">%3C/script%3E')); };</script>
</html><?php die(); ?>