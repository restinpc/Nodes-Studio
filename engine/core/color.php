<?php
/**
* Pattern color library.
* @path /engine/core/color.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @example <code> $color = engine::pattern_color(); </code>
*/
class color{
//------------------------------------------------------------------------------
/**
* Return base color value of image.
* 
* @param string $src Path to image.
* @return string Returns RGB color.
* @usage <code> $color = color::page_color('ff0000'); </code>
*/
static function image_color($src){
    if (!file_exists($src)) return FALSE;
    $size = getimagesize($src);
    if ($size === false) return FALSE;
    $format = strtolower(mb_substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return FALSE;
    $isrc = $icfunc($src);
    $count = 0;
    $rgb = array();
    for($i = 0; $i<$size[0]; $i++){
        for($j = 0; $j<$size[1]; $j++){
            $color = dechex(imagecolorat($isrc, $i, $j));
            $count++;
            for($k = 0; $k < 6; $k++){
                if(intval($color[$k], 16)<=9){
                    $rgb[$k] = intval(($rgb[$k]+$color[$k]), 16);
                }else{
                    if($color[$k]=='a') $rgb[$k] = intval(($rgb[$k]+10));
                    if($color[$k]=='b') $rgb[$k] = intval(($rgb[$k]+11));
                    if($color[$k]=='c') $rgb[$k] = intval(($rgb[$k]+12));
                    if($color[$k]=='d') $rgb[$k] = intval(($rgb[$k]+13));
                    if($color[$k]=='e') $rgb[$k] = intval(($rgb[$k]+14));
                    if($color[$k]=='f') $rgb[$k] = intval(($rgb[$k]+15));
                }
            }
        }
    }
    for($i = 0; $i<6; $i++){
        $rgb[$i] = intval($rgb[$i]/$count);
        if($rgb[$i]<=9){
            $fout .= ''.intval($rgb[$i]);
        }else{
            if($rgb[$i]==10) $fout .= 'a';
            if($rgb[$i]==11) $fout .= 'b';
            if($rgb[$i]==12) $fout .= 'c';
            if($rgb[$i]==13) $fout .= 'd';
            if($rgb[$i]==14) $fout .= 'e';
            if($rgb[$i]==15) $fout .= 'f';
        }
    }
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Return middle color value of array of images.
* 
* @param array $colors Array of RGB colors for mixing.
* @return string Returns RGB color.
* @usage <code> $color = color::mix_colors('ff0000', 'f0f0f0); </code>
*/
static function mix_colors($colors){
    $fout = '#';
    for($i=0; $i<count($colors); $i++){
        if(mb_strlen($colors[$i]) < 6){
            for($j = mb_strlen($colors[$i]); $j<6; $j++){
                $colors[$i] = '0'.$colors[$i];
            }
        }
    }
    $result = array();
    for($i=0; $i<count($colors); $i++){
        for($j = 0; $j<6; $j++){
            if(intval($colors[$i][$j], 16)<=9){
                $result[$j] = intval(($result[$j]+$colors[$i][$j]),16);
            }else{
                if($colors[$i][$j]=='a') $result[$j] = intval(($result[$j]+10));
                if($colors[$i][$j]=='b') $result[$j] = intval(($result[$j]+11));
                if($colors[$i][$j]=='c') $result[$j] = intval(($result[$j]+12));
                if($colors[$i][$j]=='d') $result[$j] = intval(($result[$j]+13));
                if($colors[$i][$j]=='e') $result[$j] = intval(($result[$j]+14));
                if($colors[$i][$j]=='f') $result[$j] = intval(($result[$j]+15));
            }
        }
    }
    for($i = 0; $i<6; $i++){
        $result[$i] = intval($result[$i]/count($colors));
        if($result[$i]<=9){
            $fout .= ''.intval($result[$i]);
        }else{
            if($result[$i]==10) $fout .= 'a';
            if($result[$i]==11) $fout .= 'b';
            if($result[$i]==12) $fout .= 'c';
            if($result[$i]==13) $fout .= 'd';
            if($result[$i]==14) $fout .= 'e';
            if($result[$i]==15) $fout .= 'f';
        }
    } 
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Return primary color of pattern, based on current session.
* 
* @param string $base_color Base template color.
* @return string Returns RGB color.
* @usage <code> $color = engine::pattern_color('ff0000'); </code>
*/
static function pattern_color($base_color=''){
    if(!empty($_SESSION["user"]["id"])){
        $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date` '
            . 'FROM `nodes_attendance` AS `att` '
            . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
            . 'WHERE `att`.`user_id` = "'.$_SESSION["user"]["id"].'"';
    }else{
        $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date` '
            . 'FROM `nodes_attendance` AS `att` '
            . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
            . 'WHERE `att`.`token` = "'.session_id().'"';
    }
    $res = engine::mysql($query);
    $colors = array();
    while($data = mysql_fetch_array($res)){
        $url = str_replace($_SERVER["PUBLIC_URL"].'/', '', $data["value"]);
        if(!empty($url)){
            $pos = mb_strpos($url, '#');
            if($pos) $url = mb_substr($url, 0, $pos);
            $url = str_replace('content/', '', $url);
            $product_id = engine::is_product($url);
            if($product_id>0){
                $query = 'SELECT `img` FROM `nodes_product` WHERE `id` = "'.$product_id.'"';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                $imgs = explode(';', $d["img"]);
                $query = 'SELECT `color` FROM `nodes_image` WHERE `name` = "'.$imgs[0].'"';
                $r = engine::mysql($query);
                $image = mysql_fetch_array($r);
                if(!empty($image)){
                    array_push($colors, $image["color"]);
                }   
            }else if(engine::is_article($url)){
                $query = 'SELECT `image`.`color` AS `color` FROM `nodes_content` AS `content` '
                        . 'LEFT JOIN `nodes_image` AS `image` ON `image`.`name` = `content`.`img` '
                        . 'WHERE `content`.`url` = "'.$url.'" AND `content`.`lang` = "'.$_SESSION["Lang"].'"';
                $r = engine::mysql($query);
                $image = mysql_fetch_array($r);
                if(!empty($image)){
                    array_push($colors, $image["color"]);
                }
            }
        }
    }
    if(!empty($colors)){
        $color = self::mix_colors($colors);
        if(intval('0x'.$color, 16)>intval('0xDDDDDD', 16)){
            return $base_color;
        } return $color;
    }else{
        return $base_color;
    }
}
//------------------------------------------------------------------------------
/**
* Return color of page, based on preview image.
* 
* @param string $base_color Base template color.
* @return string Returns RGB color.
* @usage <code> $color = color::page_color('ff0000'); </code>
*/
static function page_color($base_color){
    $url = str_replace($_SERVER["PUBLIC_URL"].'/', '', $_SERVER["SCRIPT_URI"]);
    if(!empty($url)){
        $pos = mb_strpos($url, '#');
        if($pos) $url = mb_substr($url, 0, $pos);
        $url = str_replace('content/', '', $url);
        $product_id = engine::is_product($url);
        if($product_id>0){
            $query = 'SELECT `img` FROM `nodes_product` WHERE `id` = "'.$product_id.'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $imgs = explode(';', $d["img"]);
            $query = 'SELECT `color` FROM `nodes_image` WHERE `name` = "'.$imgs[0].'"';
            $r = engine::mysql($query);
            $image = mysql_fetch_array($r);
            if(!empty($image)){
                if(intval('0x'.$image["color"], 16)>intval('0xDDDDDD', 16)){
                    return self::pattern_color($base_color);
                } return '#'.$image["color"];
            }   
        }else if(engine::is_article($url)){
            $query = 'SELECT `image`.`color` AS `color` FROM `nodes_content` AS `content` '
                    . 'LEFT JOIN `nodes_image` AS `image` ON `image`.`name` = `content`.`img` '
                    . 'WHERE `content`.`url` = "'.$url.'" AND `content`.`lang` = "'.$_SESSION["Lang"].'"';
            $r = engine::mysql($query);
            $image = mysql_fetch_array($r);
            if(!empty($image)){
                if(intval('0x'.$image["color"], 16)>intval('0xDDDDDD', 16)){
                    return self::pattern_color($base_color);
                } return '#'.$image["color"];
            }
        }else return self::pattern_color($base_color);
    }else return self::pattern_color($base_color);
}
}