<?php
/**
* Source code viewer.
* @path /engine/code/edit.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if(!empty($_GET["file"])&&$_SESSION["user"]["admin"]=="1"){
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <style type="text/css" media="screen">
    body {overflow: hidden;}
    #editor {margin: 0;position: absolute;top: 0;bottom: 0;left: 0;right: 0;overflow:scroll;}
  </style>
</head>
<body>
<pre id="editor">';
    $file = '';
    $name = explode('.', $_GET["file"]);
    $ext = $name[count($name)-1];
    $source = str_replace($ext, 'source.'.$ext, $_GET["file"]);
    if(file_exists($source)){
        $file = $source;
    }else{
        $file = $_GET["file"];
    }
    $file = file_get_contents($file);
    if($ext == "js") $ace_mode = 'javascript';
    if($ext == "css") $ace_mode = 'css';
    if($ext == "php"){ 
        $ace_mode = 'php';
        $file = htmlspecialchars($file);
    }
    echo $file.'
</pre>
</body>
</html>';
}else engine::error();