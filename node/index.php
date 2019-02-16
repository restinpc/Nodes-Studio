<?php
header('Content-Type: text/html; charset=UTF-8');
$nodejs = 'C:\Program Files\nodejs\node.exe';
$request = explode('/', $_SERVER["REQUEST_URI"]);
for($i = count($request)-1; $i > 0; $i--){
    if(strpos($request[$i], '.js')){
        $file = $_SERVER["DOCUMENT_ROOT"].'/node/'.$request[$i];
        if (file_exists($file)) {
            $command = '"'.$nodejs.'" "'.$file.'"';
            echo shell_exec($command);
        }else{
            header("HTTP/1.0 404 Not Found");
            die("File not found");
        }
    }
}