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
ob_start();
$width = 200;
$height = 70;
$font_size = 21;
$let_amount = 6;
$fon_let_amount = 30;
$font = "font/Open-Sans-regular/Open-Sans-regular.ttf";
$letters = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");		
$colors = array("90","110","130","150","170","190","210");	 
$src = imagecreatetruecolor($width,$height);
$fon = imagecolorallocatealpha($src,255,255,255,0);
imagefill($src,0,0,$fon);
for($i=0;$i < $fon_let_amount;$i++){
    $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);	
    $letter = $letters[rand(0,sizeof($letters)-1)];								
    $size = rand($font_size-2,$font_size+2);											
    imagettftext($src,$size,rand(0,45),
    rand($width*0.1,$width-$width*0.1),
    rand($height*0.2,$height),$color,$font,$letter);
}
if(date("U") > intval($_SESSION["captcha_date"]+60) || empty($_SESSION["captcha_img"])){
    $_SESSION["captcha"] = '';
    for($i=0;$i < $let_amount;$i++){
        $letter = $letters[rand(0,sizeof($letters)-1)];
        $_SESSION["captcha"] .= $letter;
    }
    $_SESSION["captcha_date"] = date("U");
    for($i=0;$i < $let_amount;$i++){
        $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],
        $colors[rand(0,sizeof($colors)-1)],
        $colors[rand(0,sizeof($colors)-1)],rand(20,40)); 
        $letter = $_SESSION["captcha"][$i];
        $size = rand($font_size*2-2,$font_size*2+2);
        $x = ($i+1)*$font_size + rand(1,5);
        $y = (($height*2)/3) + rand(0,5);	
        imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
    }
    $file = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/temp/captcha_'.session_id().'.png';
    imagepng($src, $file);
    $_SESSION["captcha_img"] = $file;
    header ("Content-type: image/png");
    imagepng($src);
}else{
    $image = file_get_contents($_SESSION["captcha_img"]);
    header ("Content-type: image/png");
    echo($image);
}