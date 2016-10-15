<?php
require_once ("engine/nodes/headers.php");
require_once ("engine/nodes/session.php");
require_once ("engine/nodes/mysql.php");
require_once ("engine/nodes/language.php");
if(!strpos($_SERVER["REQUEST_URI"], ".xml")){
    header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>'.lang("Sitemap").' '.$_SERVER["HTTP_HOST"].'</title>';
require_once ("templates/meta.php");
echo $fout;
echo '
</head>
<body style="background: #2e3137; font-family: Sans-Serif;">
    <div style="text-align:center; color: #fff; padding-top: 0px;"><h1>'.lang("Sitemap").'</h1></div>
    <div style="width: 100%; min-width: 280px; max-width: 400px; margin: auto;">
        <center><form method="POST" id="admin_lang_select" style="color:#fff;">'.lang("Select your language").': 
        <select class="input" name="lang" onChange=\'document.getElementById("admin_lang_select").submit();\'>';
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "languages"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $arr = explode(";", $data["value"]);
    $fout = '';
    foreach($arr as $value){
        $value = trim($value);
        if(!empty($value)){
            if(!empty($_SESSION["Lang"])&&$_SESSION["Lang"]==$value){
                echo '<option value="'.$value.'" selected>'.$value.'</option>';
            }else{
                $fout .= '<li style="display:none;"><a href="'.$_SERVER["DIR"].'/sitemap.php?lang='.$value.'" hreflang="'.  strtolower($value).'" style="color: #fff; text-decoration: none;" >'.lang("Sitemap").' ('.  strtoupper($value).')</a></li>
';
                echo '<option value="'.$value.'">'.$value.'</option>';
            }
        }
    }
    echo '</select></form></center>
    <ul style="list-style: none; padding-left: 10px; line-height: 2.0;">
';
$query = 'SELECT * FROM `nodes_catch` WHERE `interval` > -2 AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY `url` ASC';
$res = engine::mysql($query);
while($data = mysql_fetch_array($res)){
    if(empty($data["url"])) $data["url"] = "/";
    if(!empty($data["title"])){ 
        $title = $data["title"];
    }
    
    if(!empty($data["content"])){
        $html = preg_replace('#<script>.*?</script>#', '', $data["content"]);
        $html = preg_replace('#<[^>]+>#', " ", $html);
        $html = trim(preg_replace('#[\s]+#', ' ', $html));
        $desc = substr($html,0,50)."..";
    }else $title = $data["url"];
    
    if(!strpos(" ".$data["url"], "/img/")&&
            !strpos(" ".$data["url"], "/register")&&
            !strpos(" ".$data["url"], "/account")&&
            !strpos(" ".$data["url"], "/admin")&&
            !strpos(" ".$data["url"], "/search")){
        if($data["lang"]=="en"||empty($data["lang"])){
    echo '<li><a href="'.$data["url"].'" target="_blank" style="color: #fff; text-decoration: none;"  hreflang="'.$data["lang"].'" title="'.$desc.'">'.$title.'</a></li>
        ';
        }else{
    echo '<li><a href="'.$data["url"].'?lang='.$data["lang"].'" hreflang="'.$data["lang"].'" target="_blank" style="color: #fff; text-decoration: none;" title="'.$desc.'">'.$title.'</a></li>
        ';
        }
    }
}
echo $fout;
echo '
    </ul>
    </div>
</body>
</html>';

}else{
    $query = 'SELECT * FROM `nodes_catch` WHERE `interval` > -2 ORDER BY `url` ASC';
    $res = engine::mysql($query);
    header('Content-Type: application/rss+xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
';
    
while($data = mysql_fetch_array($res)){
    if(!strpos(" ".$data["url"], "/img/")&&
            !strpos(" ".$data["url"], "/register")&&
            !strpos(" ".$data["url"], "/account")&&
            !strpos(" ".$data["url"], "/admin")&&
            !strpos(" ".$data["url"], "/search")){
        if($data["lang"]=="en"||empty($data["lang"])){
    echo '<url> 
  <loc>'.$data["url"].'</loc>
  <lastmod>2013-03-21T12:38:44+00:00</lastmod>
</url>
';
        }else{
    echo '<url> 
  <loc>'.$data["url"].'?lang='.$data["lang"].'</loc>
  <lastmod>2013-03-21T12:38:44+00:00</lastmod>
</url>
';     
        }
            }
}
echo '</urlset>';
}