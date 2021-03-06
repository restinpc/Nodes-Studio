<?php
/**
* Crontab system script.
* @path /engine/code/cron.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
$_SERVER["CRON"] = 1;
set_time_limit(60);
ini_set('max_execution_time', "60");
require_once("engine/nodes/headers.php");
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/session.php");
//----------------------------------------------------------
$flag = 0;
$query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "cron_exec"';
engine::mysql($query);
$server = doubleval(microtime(1)-$GLOBALS["time"]);
//------------------------------------------------------------------------------
/*
* Sends bulk mail messages every minute if exists.
*/
$query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "outbox_limit"';
$r = engine::mysql($query);
$d = mysqli_fetch_array($r);
$limit = $d[0];
$query = 'SELECT * FROM `nodes_user_outbox` WHERE `status` > -2 AND `status` < 1 ORDER BY RAND() DESC LIMIT 0, '.$limit;
$res = engine::mysql($query);
while($data = mysqli_fetch_array($res)) email::bulk_mail($data); 
//------------------------------------------------------------------------------
/*
* Clean-up temp BTC transactions.
*/
$query = 'DELETE FROM `nodes_transaction` WHERE `comment` = "Temp" AND `date` < "'.(date("U")-86400).'"';
engine::mysql($query);
//------------------------------------------------------------------------------
/*
* Milestones a performance once a 20 minute.
*/
$query = 'SELECT `date` FROM `nodes_perfomance` WHERE `date` > "'.(date("U")-1200).'"';
$res = engine::mysql($query);
$data = mysqli_fetch_array($res);
if(empty($data)){
    $query = 'SELECT * FROM `nodes_cache` WHERE `interval` >= "-1" AND `url` LIKE "%'.$_SERVER["HTTP_HOST"].'%" ORDER BY RAND() DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data)){
        $flag = 1;
        $current = doubleval(microtime(1));
        $html = engine::curl_post_query($data["url"], "nocache=1");
        $now = doubleval(microtime(1)-$current);
        $query = 'INSERT INTO `nodes_perfomance`(`cache_id`, `server_time`, `script_time`, `date`) '
                . 'VALUES("'.$data["id"].'", "'.$server.'", "'.$now.'", "'.date("U").'")';
        engine::mysql($query);
    }  
}
//------------------------------------------------------------------------------
/*
* Generates site daily report to admin email once a day.
*/
if(!$flag){
    if(date("H")>=23){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "daily_report"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(intval($data["value"])){
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "lastreport"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if($data["value"]<date("U")-86000){
                $flag = 2;
                email::daily_report();
                $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "lastreport"';
                engine::mysql($query);
            }
        }
    }
}
//------------------------------------------------------------------------------
/*
* Unlinks temp images once a day.
*/
if(!$flag){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron_images"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if($data["value"]<date("U")-86400){
        $flag = 4;
        $images = array();
        $query = 'SELECT * FROM  `nodes_product`';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            $imgs = explode(';', $data["img"]);
            foreach($imgs as $img){
                $img = trim($img);
                if(!empty($img)){
                    if(!in_array($img, $images)) array_push($images, $img);
                }
            }
        }
        $query = 'SELECT * FROM `nodes_content`';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            $img = trim($data["img"]);
            if(!empty($img)){
                if(!in_array($img, $images)) array_push($images, $img);
            }
            $imgs = explode(';', $data["imgs"]);
            foreach($imgs as $img){
                $img = trim($img);
                if(!empty($img)){
                    if(!in_array($img, $images)) array_push($images, $img);
                }
            }  
        }
        $query = 'SELECT * FROM `nodes_catalog`';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            $img = trim($data["img"]);
            if(!empty($img)){
                if(!in_array($img, $images)) array_push($images, $img);
            }
        }
        $path = "img/data/big/";
        $dir = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.$path;
        $hdl = opendir($dir);
        while ($file_name = readdir($hdl)){
            if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)){
                if(!in_array($file_name, $images)){
                    unlink($dir.$file_name);
                }
            }
        }
        closedir($hdl);
        $path = "img/data/thumb/";
        $dir = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.$path;
        $hdl = opendir($dir);
        while ($file_name = readdir($hdl)){
            if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)){
                if(!in_array($file_name, $images)){
                    unlink($dir.$file_name);
                }
            }
        }
        closedir($hdl);
        $images = array();
        $query = 'SELECT * FROM  `nodes_user`';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            $img = trim($data["photo"]);
            if(!empty($img)){
                if(!in_array($img, $images)) array_push($images, $img);
            }
        }
        $path = "img/pic/";
        $dir = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.$path;
        $hdl = opendir($dir);
        while ($file_name = readdir($hdl)){
            if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)
                    && ($file_name != "admin.jpg") && ($file_name != "anon.jpg")){
                if(!in_array($file_name, $images)){
                    unlink($dir.$file_name);
                }
            }
        }
        closedir($hdl);
        $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "cron_images"';
        engine::mysql($query);
    }
}
//------------------------------------------------------------------------------
/*
* Backups mysql and data.
*/
if(!$flag){
    $root = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"];
    $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "autobackup"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(intval($data["value"])){
        $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "backup_interval"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $interval = intval($data["value"])*86400;
        $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "backup_limit"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $limit = $data["value"];
        $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "backup_date"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $date = intval($data["value"])+$interval;
        if($date < date("U")){
            $flag = 5;
            $dir = $root.'/backup';
            $count = 0;
            $first = 0;
            $target = '';
            foreach(scandir($dir) as $file) {
                if ('.' === $file || '..' === $file || '.htaccess' == $file) continue;
                $date = mb_substr($file, 0, strpos($file, '.'));
                $time = strtotime($date);
                if($first>$time || !$first){
                    $target = $file;
                    $first = $time;
                }$count++;
            }
            if($count>=$limit){
                if(is_file($root.'/backup/'.$target)){ 
                    unlink ($root.'/backup/'.$target);
                }else{ 
                    file::delete($root.'/backup/'.$target);
                }
            }
            if(mkdir($root.'/backup/'.date("d-m-y"))){
                $filename = 'backup/'.date("d-m-y").'/db.sql';
                $dumper = new dump($_SERVER["config"]["sql_db"], $filename, false, false);
                $dumper->doDump();
                $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "backup_files"';
                $res = engine::mysql($query);
                $data = mysqli_fetch_array($res);
                if(intval($data["value"])){
                    $dir = $root.'/backup/'.date("d-m-y");
                    file::copy($root, $dir);
                    mkdir($dir.'/backup');
                    mkdir($dir.'/session');
                    if(file::zip($dir, $root.'/backup/'.date("d-m-y").'.zip')){
                        chmod($root.'/backup/'.date("d-m-y").'.zip', 0755);
                        file::delete($dir);
                    } 
                }
                $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "backup_date"';
                engine::mysql($query);
            }
        }
    }
}
//------------------------------------------------------------------------------
/*
* Updates a cache info for "cached" pages.
*/
if(!$flag){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cache"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $is_cache = intval($data["value"]);
    if($is_cache){
        $query = 'SELECT COUNT(`id`) FROM `nodes_cache` WHERE `interval` > 0 AND `url` NOT LIKE "cron.php" AND `url` LIKE "%'.$_SERVER["HTTP_HOST"].'%"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $count = round($data[0]/1440);
        if($count<1) $count = 1;
        $query = 'SELECT * FROM `nodes_cache` WHERE `interval` > 0 AND `url` NOT LIKE "cron.php" AND `url` LIKE "%'.$_SERVER["HTTP_HOST"].'%" ORDER BY `date` ASC LIMIT 0, '.$count;
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            if($data["date"]<=intval(date("U")-$data["interval"])){
                $flag = 6;
                $url = $data["url"];
                cache::update_cache($url,0,$data["lang"]);
            }
        }
        if(!$flag){
            $query = 'SELECT * FROM `nodes_cache` WHERE `date` < '.(date("U")-86400).' AND `url` LIKE "%'.$_SERVER["HTTP_HOST"].'%" ORDER BY RAND() ASC LIMIT 0, 1';
            $res = engine::mysql($query);
            while($data = mysqli_fetch_array($res)){
                $flag = 7;
                $url = $data["url"];
                $lang = $data["lang"];
                cache::update_cache($url,0,$lang);
            } 
        }
    }
}
//------------------------------------------------------------------------------
/*
* Updates a cache info for new pages.
*/
if(!$flag){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cache"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $is_cache = intval($data["value"]);
    if($is_cache){
        $query = 'SELECT * FROM `nodes_cache` WHERE `title` = "" AND `url` NOT LIKE "cron.php" AND `url` LIKE "%'.$_SERVER["HTTP_HOST"].'%" ORDER BY `date` ASC LIMIT 0, 1';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            $flag = 7;
            $url = $data["url"];
            $lang = $data["lang"];
            cache::update_cache($url,0,$lang);
        }  
    }
}
//------------------------------------------------------------------------------
/*
* Insert color mask of image in DB.
*/
if(!$flag){
    $dir = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/thumb/';
    $hdl = opendir($dir);
    while ($file_name = readdir($hdl)){
        if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)){
            $query = 'SELECT * FROM `nodes_image` WHERE `name` = "'.$file_name.'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if(empty($data)){
                $color = color::image_color($dir.$file_name);
                $size = getimagesize($dir.$file_name);
                $query = 'INSERT INTO `nodes_image`(name, color, width, height) 
                    VALUES("'.$file_name.'", "'.$color.'", "'.$size[0].'", "'.$size[1].'")';
                engine::mysql($query);
                $flag = 8;
                break;
            }
        }
    }
}
//------------------------------------------------------------------------------
$query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "cron_done"';
engine::mysql($query);
echo $flag;