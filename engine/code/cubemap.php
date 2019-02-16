<?php
/**
* Cubemap generation.
* @path /engine/code/cubemap.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
$side = $_GET["name"];
$image_parts = explode(";base64,", $_POST["url"]);
if(intval($_GET["img_id"])>0){
    $img_id = intval($_GET["img_id"]);
}else{
    $query = 'SELECT MAX(`id`) FROM `nodes_vr_scene`';
    $r = engine::mysql($query);
    $d = mysqli_fetch_array($r);
    $img_id = $d[0]+1;
}
$scene_id = $img_id;
mkdir($_SERVER["DOCUMENT_ROOT"].'/img/scenes/'.  $scene_id);
$image_type_aux = explode("image/", $image_parts[0]);
$image_type = $image_type_aux[1];
$image_base64 = base64_decode($image_parts[1]);
$file = $_SERVER["DOCUMENT_ROOT"].'/img/scenes/'.$scene_id.'/base_'.$side.'.png';
file_put_contents($file, $image_base64);
$img_size = getimagesize($file);

$width = $img_size[0];
$height = $img_size[1];
//--------
$res = 32;
if($height/8 >= 64){
    $res = 64; 
}
if($height/8 >= 128){
    $res = 128;
}
if($height/8>=256){
    $res = 256;
}
if($height/8>=512){
    $res = 512;
}
for($i = 0; $i < 8; $i++){
    for($j = 0; $j < 8; $j++){
        $img = new image($file); 
        $img->crop(
                intval($i*($width/8)), 
                intval($j*($height/8)),
                intval(($width/8)),
                intval(($height/8))
            );
        $id = ($i*8+$j);
        $img->resize($res, $res);
        $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/', 'f_4_'.$side.'_'.$id, 'png', true, 100); 
    }
}
//--------
$res = 32;
if($height/4 >= 64){
    $res = 64; 
}
if($height/4 >= 128){
    $res = 128;
}
if($height/4>=256){
    $res = 256;
}
if($height/4>=512){
    $res = 512;
}
for($i = 0; $i < 4; $i++){
    for($j = 0; $j < 4; $j++){
        $img = new image($file); 
        $img->crop(
                intval($i*($width/4)), 
                intval($j*($height/4)),
                intval(($width/4)),
                intval(($height/4))
            );
        $id = ($i*4+$j);
        $img->resize($res, $res);
        $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/', 'f_3_'.$side.'_'.$id, 'png', true, 90); 
    }
}
//--------
$res = 32;
if($height/2 >= 64){
    $res = 64; 
}
if($height/2 >= 128){
    $res = 128;
}
if($height/2>=256){
    $res = 256;
}
if($height/2>=512){
    $res = 512;
}
for($i = 0; $i < 2; $i++){
    for($j = 0; $j < 2; $j++){
        $img = new image($file); 
        $img->crop(
                intval($i*($width/2)), 
                intval($j*($height/2)),
                intval(($width/2)),
                intval(($height/2))
            );
        $id = ($i*2+$j);
        $img->resize($res, $res);
        $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/', 'f_2_'.$side.'_'.$id, 'png', true, 80); 
    }
}
//--------
 $res = 32;
if($height >= 64){
    $res = 64; 
}
if($height >= 128){
    $res = 128;
}
if($height>=256){
    $res = 256;
}
if($height>=512){
    $res = 512;
}
for($i = 0; $i < 1; $i++){
    for($j = 0; $j < 1; $j++){
        $img = new image($file); 
        $id = ($i*1+$j);
        $img->resize($res, $res);
        $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/', 'f_1_'.$side.'_'.$id, 'png', true, 70); 
    }
}