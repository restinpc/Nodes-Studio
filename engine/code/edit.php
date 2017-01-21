<?php
/**
* Source code viewer.
* @path /engine/code/edit.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
if(!empty($_GET["file"])&&$_SESSION["user"]["id"]=="1"){
    $file = '';
    $name = explode('.', $_GET["file"]);
    $ext = $name[count($name)-1];
    $source = str_replace($ext, 'source.'.$ext, $_GET["file"]);
    if(empty($_GET["mode"])){
        if(file_exists($source)){
            echo lang("Select file").':<br/><br/>
                <a href="'.$_SERVER["DIR"].'/edit.php?mode=base&file='.$_GET["file"].'">'.$_GET["file"].'</a> - '.lang("Editable file").'<br/><br/>
                <a href="'.$_SERVER["DIR"].'/edit.php?mode=source&file='.$_GET["file"].'">'.$source.'</a> - '.lang("Source file");
            return;
        }else{
            $file = $_GET["file"];
        }
    }if($_GET["mode"]=="source"){
        $file = $source;
    }else{
        $file = $_GET["file"]; 
    }
    $file = file_get_contents($file);
    $file = htmlspecialchars($file);
    $file = str_replace("\n", '<br/>', $file);
    $file = str_replace(" ", '&nbsp;', $file);
    echo $file;
}else engine::error();