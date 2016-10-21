<?php

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 0){
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
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

$DATA=Array();
for ($i=0;$i<10;$i++) {
    if(!empty($_GET["date"])){
        $date = $_GET["date"];
    }else{
        $date = date("Y-m-d");
    }
    if($_GET["interval"]=="day"){
        $from = strtotime($date." 23:23:59 - ".(10-$i)." days");
        $to = strtotime($date." 23:23:59 - ".(9-$i)." days");
        $DATA["x"][]=date("d.m", $to);
    }else if($_GET["interval"]=="week"){
        $from = strtotime($date." 23:23:59 - ".((10-$i)*7)." days");
        $to = strtotime($date." 23:23:59 - ".((9-$i)*7)." days");
        $DATA["x"][]=date("d.m", $to);
    }else{
        $from = strtotime($date." 23:23:59 - ".(10-$i)." month");
        $to = strtotime($date." 23:23:59 - ".(9-$i)." month");
        $DATA["x"][]=date("m.Y", $to);
    }
    $query = 'SELECT COUNT(*) FROM `nodes_attendance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'" AND `display` = "1"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $DATA[0][]=$data[0];
    $query = 'SELECT DISTINCT(`token`), `ip`, `agent` FROM `nodes_attendance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'" AND `display` = "1"';
    $res = engine::mysql($query);
    $users = array();
    while($data = mysql_fetch_array($res)){
        if(strpos($data["agent"], "Googlebot")===FALSE &&
            strpos($data["agent"], "http://yandex.com/bots")===FALSE &&
            strpos($data["agent"], "MSNBot")===FALSE &&
            strpos($data["agent"], "StackRambler")===FALSE &&
            strpos($data["agent"], "Aport")===FALSE &&
            strpos($data["agent"], "Yahoo!")===FALSE){
            if(!in_array($data["ip"], $users)) array_push($users, $data['ip']);
        }
    }
    $DATA[1][]=count($users);
}

// Задаем изменяемые значения #######################################

// Размер изображения

$W=600;
$H=300;

// Отступы
$MB=20;  // Нижний
$ML=8;   // Левый 
$M=5;    // Верхний и правый отступы.
         // Они меньше, так как там нет текста

// Ширина одного символа
$LW=imagefontwidth(2);

// Подсчитаем количество элементов (точек) на графике
$count=count($DATA[0]);
if (count($DATA[1])>$count) $count=count($DATA[1]);
if (count($DATA[2])>$count) $count=count($DATA[2]);

if ($count==0) $count=1;

// Сглаживаем графики ###############################################
if ($_GET["smooth"]==1) {

    // Добавим по две точки справа и слева от графиков. Значения в
    // этих точках примем равными крайним. Например, точка если
    // y[0]=16 и y[n]=17, то y[1]=16 и y[-2]=16 и y[n+1]=17 и y[n+2]=17

    // Такое добавление точек необходимо для сглаживания точек
    // в краях графика

    for ($j=0;$j<3;$j++) {
        $DATA[$j][-1]=$DATA[$j][-2]=$DATA[$j][0];
        $DATA[$j][$count]=$DATA[$j][$count+1]=$DATA[$j][$count-1];
        }

    // Сглаживание графики методом усреднения соседних значений

    for ($i=0;$i<$count;$i++) {
        for ($j=0;$j<3;$j++) {
            $DATA[$j][$i]=($DATA[$j][$i-1]+$DATA[$j][$i-2]+
                           $DATA[$j][$i]+$DATA[$j][$i+1]+
                           $DATA[$j][$i+2])/5;
            }
        }
    }


// Подсчитаем максимальное значение
$max=0;

for ($i=0;$i<$count;$i++) {
    $max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
    $max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
    $max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
    }

// Увеличим максимальное значение на 10% (для того, чтобы столбик
// соответствующий максимальному значение не упирался в в границу
// графика
$max=intval($max+($max/10));

// Количество подписей и горизонтальных линий
// сетки по оси Y.
$county=10;

// Работа с изображением ############################################

// Создадим изображение
$im=imagecreate($W,$H);

// Цвет фона (белый)
$bg[0]=imagecolorallocate($im,255,255,255);

// Цвет задней грани графика (светло-серый)
$bg[1]=imagecolorallocate($im,231,231,231);

// Цвет левой грани графика (серый)
$bg[2]=imagecolorallocate($im,212,212,212);

// Цвет сетки (серый, темнее)
$c=imagecolorallocate($im,184,184,184);

// Цвет текста (темно-серый)
$text=imagecolorallocate($im,136,136,136);

// Цвета для линий графиков
$bar[1]=imagecolorallocate($im,200,20,20);
$bar[0]=imagecolorallocate($im,68,115,186);

$text_width=0;
// Вывод подписей по оси Y
for ($i=1;$i<=$county;$i++) {
    $strl=strlen(($max/$county)*$i)*$LW;
    if ($strl>$text_width) $text_width=$strl;
}

// Подравняем левую границу с учетом ширины подписей по оси Y
$ML+=$text_width;

// Посчитаем реальные размеры графика (за вычетом подписей и
// отступов)
$RW=$W-$ML-$M;
$RH=$H-$MB-$M;

// Посчитаем координаты нуля
$X0=$ML;
$Y0=$H-$MB;

$step=$RH/$county;

// Вывод главной рамки графика
imagefilledrectangle($im, $X0, $Y0-$RH, $X0+$RW, $Y0, $bg[1]);
imagerectangle($im, $X0, $Y0, $X0+$RW, $Y0-$RH, $c);

// Вывод сетки по оси Y
for ($i=1;$i<=$county;$i++) {
    $y=$Y0-$step*$i;
    imageline($im,$X0,$y,$X0+$RW,$y,$c);
    imageline($im,$X0,$y,$X0-($ML-$text_width)/4,$y,$text);
}

// Вывод сетки по оси X
// Вывод изменяемой сетки
for ($i=0;$i<$count;$i++) {
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0,$c);
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
    }

// Вывод линий графика
$dx=($RW/$count)/2;

$pi=$Y0-($RH/$max*$DATA[0][0]);
$po=$Y0-($RH/$max*$DATA[1][0]);

$px=intval($X0+$dx);

for ($i=1;$i<$count;$i++) {
    $x=intval($X0+$i*($RW/$count)+$dx);

    $y=$Y0-($RH/$max*$DATA[0][$i]);
    imagelinethick($im,$px,$pi,$x,$y,$bar[0]);
    $pi=$y;

    $y=$Y0-($RH/$max*$DATA[1][$i]);
    imagelinethick($im,$px,$po,$x,$y,$bar[1]);
    $po=$y;

    $px=$x;
    }

$ML-=$text_width;
for ($i=1;$i<=$county;$i++) {
    $str=intval(($max/$county)*$i);
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
    }$i-=$RW/$count;
}
header("Content-Type: image/gif");
imagegif($im);
imagedestroy($im);