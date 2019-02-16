<?php
/**
* Perfomance graph.
* @path /engine/code/perfomance.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
$W=600;     // Width
$H=300;     // Height
$MB=20;     // Padding bottom
$ML=8;      // Padding left
$M=5;       // Padding right & top
$county=10; // Lines count
//------------------------------------------------------------------------------
/**
* Draws a wide canvas line.
* 
* @param resource $image Source image.
* @param int $x1 X-coordinate for point 1.
* @param int $y1 Y-coordinate for point 1.
* @param int $x2 X-coordinate for point 2.
* @param int $y2 Y-coordinate for point 2.
* @param hex $color Line color from 0x000 to 0xfff.
* @param int $thick Line width in px.
* @return bool Returns TRUE on success or FALSE on failure.
* @usage <code> draw_line($image, 0, 0, 100, 100, 0xf00, 20); </code>
*/
function draw_line($image, $x1, $y1, $x2, $y2, $color, $thick = 1){
    array_push($_SERVER["CONSOLE"], "draw_line(..)");
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, 
            round(min($x1, $x2) - $t), 
            round(min($y1, $y2) - $t), 
            round(max($x1, $x2) + $t), 
            round(max($y1, $y2) + $t), 
            $color
        );
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}
//------------------------------------------------------------------------------
require_once("engine/nodes/session.php");
if($_SESSION["user"]["id"]!=1){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "lastreport"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if($data["value"]>=date("U")-26000){
        die(engine::error(401));
    }
}
$DATA=Array();
for ($i=0;$i<10;$i++) {
    if(!empty($_GET["date"])){
        $date = $_GET["date"];
    }else{
        $date = date("Y-m-d");
    }
    if($_GET["interval"]=="day"){
        $from = strtotime($date." 23:59:59 - ".(10-$i)." days");
        $to = strtotime($date." 23:59:59 - ".(9-$i)." days");
        $DATA["x"][]=date("d/m", $to);
        $step = 2;
    }else if($_GET["interval"]=="week"){
        $from = strtotime($date." 23:59:59 - ".((10-$i)*7)." days");
        $to = strtotime($date." 23:59:59 - ".((9-$i)*7)." days");
        $DATA["x"][]=date("d/m", $to);
        $step = 1.5;
    }else if($_GET["interval"]=="month"){
        $from = strtotime($date." 23:59:59 - ".(10-$i)." month");
        $to = strtotime($date." 23:59:59 - ".(9-$i)." month");
        $DATA["x"][]=date("m/Y", $to);
        $step = 3;
    }else{
        $step = 2.5;
        $from = strtotime($date." ".date("H:i:s")." - ".((10-$i)*120)." minutes");
        $to = strtotime($date." ".date("H:i:s")." - ".((9-$i)*120)." minutes");
        $DATA["x"][]=date("H:i", $to); 
    }
    $query = 'SELECT * FROM `nodes_perfomance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'"';
    $res = engine::mysql($query);
    $script = 0;
    $server = 0;
    $count=0;
    while($data = mysqli_fetch_array($res)){
        $script += $data["script_time"];
        $server += $data["server_time"];
        $count++;
    }
    $query = 'SELECT AVG(`script_time`) FROM `nodes_perfomance` WHERE `script_time` > 0';
    $res = engine::mysql($query);
    $mid_script = mysqli_fetch_array($res);
    $query = 'SELECT AVG(`server_time`) FROM `nodes_perfomance` WHERE `server_time` > 0';
    $res = engine::mysql($query);
    $mid_server = mysqli_fetch_array($res);
    if($script>0){
        $script_time = $script/$count;
    }else{ 
        $script_time = 0;
    }
    if($server>0){
        $server_time = $server/$count;
    }else{ 
        $server_time = 0;
    }
    $DATA[0][] = $script_time;
    $DATA[1][] = $server_time;
}
$LW=imagefontwidth(2);
$count=count($DATA[0]);
if (count($DATA[1])>$count) $count=count($DATA[1]);
if (count($DATA[2])>$count) $count=count($DATA[2]);
if ($count==0) $count=1;
$max=0;
for ($i=0;$i<$count;$i++) {
    $max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
    $max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
    $max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
}
$max=  round($max+($max/10), 2);
$im=imagecreate($W,$H);
$bg[0]=imagecolorallocate($im,255,255,255);
$bg[1]=imagecolorallocate($im,231,231,231);
$bg[2]=imagecolorallocate($im,212,212,212);
$c=imagecolorallocate($im,184,184,184);
$text=imagecolorallocate($im,136,136,136);
$bar[0]=imagecolorallocate($im,240,40,40);
$bar[1]=imagecolorallocate($im,68,115,186);
$bar[2]=imagecolorallocate($im,20,180,180);
$bar[3]=imagecolorallocate($im,220,0,0);
$text_width=0;
for ($i=1;$i<=$county;$i++) {
    $strl=strlen(($max/$county)*$i)*$LW;
    if ($strl>$text_width) $text_width=$strl;
}
$ML+=$text_width;
$RW=$W-$ML-$M;
$RH=$H-$MB-$M;
$X0=$ML;
$Y0=$H-$MB;
$step=$RH/$county;
imagefilledrectangle($im, $X0, $Y0-$RH, $X0+$RW, $Y0, $bg[1]);
imagerectangle($im, $X0, $Y0, $X0+$RW, $Y0-$RH, $c);
for ($i=1;$i<=$county;$i++) {
    $y=$Y0-$step*$i;
    imageline($im,$X0,$y,$X0+$RW,$y,$c);
    imageline($im,$X0,$y,$X0-($ML-$text_width)/4,$y,$text);
}
for ($i=0;$i<$count;$i++) {
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0,$c);
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
}
$dx=($RW/$count)/2;
$pi=$Y0-($RH/$max*$DATA[0][0]);
$po=$Y0-($RH/$max*$DATA[1][0]);
$pf=$Y0-($RH/$max*$DATA[2][0]);
$px=intval($X0+$dx);
for ($i=1;$i<$count;$i++) {
    $x=intval($X0+$i*($RW/$count)+$dx);
    if($DATA[0][$i]>=$DATA[1][$i]){
        $y=$Y0-($RH/$max*$DATA[0][$i]);
        if($DATA[0][$i]>0){
            if($mid_script[0]<$DATA[0][$i]){
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[0],10);
            }else{
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[2],10);
            }
        }
        $y=$Y0-($RH/$max*$DATA[1][$i]);
        if($DATA[1][$i]>0){
            if($mid_script[0]<$DATA[0][$i]){
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[3],10);
            }else{
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[1],10);
            }
        }
    }else{
        $y=$Y0-($RH/$max*$DATA[1][$i]);
        if($DATA[1][$i]>0){
            if($mid_script[0]<$DATA[0][$i]){
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[3],10);
            }else{
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[1],10);
            }
        } 
        $y=$Y0-($RH/$max*$DATA[0][$i]);
        if($DATA[0][$i]>0){
            if($mid_script[0]<$DATA[0][$i]){
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[0],10);
            }else{
                draw_line($im,$x,$Y0-5,$x,$y+5,$bar[2],10);
            }
        }
    }
    $po=$y;
    $y=$Y0-($RH/$max*$DATA[2][$i]);
    $pf=$y;
    $px=$x;
}
$ML-=$text_width;
for ($i=1;$i<=$county;$i++) {
    $str=  round(($max/$county)*$i, 2);
    imagestring($im,2, $X0-strlen($str)*$LW-$ML/4-2,$Y0-$step*$i-
                       imagefontheight(2)/2,$str,$text);
}
$prev=100000;
$twidth=$LW*strlen($DATA["x"][0])+6;
$i=$X0+$RW;
while ($i>$X0) {
    if ($prev-$twidth>$i) {
        $drawx=$i-($RW/$count)/2;
        if ($drawx>$X0) {
            $str=$DATA["x"][round(($i-$X0)/($RW/$count))-1];
            imageline($im,$drawx,$Y0,$i-($RW/$count)/2,$Y0+5,$text);
            imagestring($im,2, $drawx-(strlen($str)*$LW)/2, $Y0+7,$str,$text);
            }
        $prev=$i;
    }$i-=$step;
}
header("Content-Type: image/gif");
imagegif($im);
imagedestroy($im);