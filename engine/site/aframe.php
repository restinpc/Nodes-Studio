<?php
/**
* Backend VR pages file.
* @path /engine/site/aframe.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/    

$_SESSION["redirect"] = $_SERVER["SCRIPT_URI"];
if(empty($_GET[1]) || $_GET[1] == "cardboard"){
    require_once("engine/aframe/cardboard.php");
}else{
    $mode = engine::escape_string($_GET[1]);
    $query = 'SELECT * FROM `nodes_aframe` WHERE `url` = "'.$mode.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data) && file_exists($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/engine/aframe/'.$data["file"])){
        $this->title .= ' - '.$data["caption"];
        $this->description = $data["text"];
        $this->img = $_SERVER["PUBLIC_URL"].$data["image"];
        require_once('engine/aframe/'.$data["file"]);
    }else{
        engine::error(404);
    }
}