<?php 
/**
* Framework language file.
* @path /engine/nodes/language.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
if(empty($_SESSION["Lang"])){ 
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "language"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $_SESSION["Lang"] = $data["value"];
}
if(!empty($_GET["lang"])){
    $_SESSION["Lang"] = strtolower ($_GET["lang"]);
    $url = str_replace("&lang=".$_SESSION["Lang"], '',
        str_replace("?lang=".$_SESSION["Lang"], '', $_SERVER["REQUEST_URI"]));
    header('Location: '.$url);
    die('<script>window.location = "'.$url.'"</script>');
}else if(!empty($_POST["lang"])){
    $_SESSION["Lang"] =  strtolower ($_POST["lang"]);
}
function lang($key){
    $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$key.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data["value"])){
        return $data["value"];
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "language"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_language` WHERE `name` LIKE "'.$key.'" AND `lang` = "en" AND `value` <> ""';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        if(!empty($d)){
            return $d["value"];
        }else{
            $query = 'INSERT INTO `nodes_language`(name, lang, value) VALUES("'.$key.'", "en", "'.$key.'")';
            engine::mysql($query);
            return $key;
        }
    }
}