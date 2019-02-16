<?php
/**
* Captcha generator.
* @path /engine/code/captcha.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if(!empty($_GET["text"])){
    ob_start();
    $text = urldecode($_GET["text"]);
    $lines = explode("\n", $text);
    $max = 0;
    foreach($lines as $line){
        if(strlen($line)>$max) $max=strlen($line);
    }
    $width = 60+($max*16);
    $height = 60+(count($lines)*25);
    $font_size = 21;
    $let_amount = 6;
    $fon_let_amount = 30;
    $font = "font/Open-Sans-regular/Open-Sans-regular.ttf";	 
    $src = imagecreatetruecolor($width, $height);
    $fon = imagecolorallocatealpha($src,255,255,255,0);
    $text_colour = imagecolorallocate( $src, 0, 0, 0 );
    imagefill($src,0,0,$fon);
    $i = 1;
    foreach($lines as $line){
        $i++;
        imagettftext( $src, $font_size, 0, 30, 25*$i, $text_colour, $font, $line );
    }
    header ("Content-type: image/png");
    imagepng($src);
}