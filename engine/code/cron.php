<?php
$error = '';
try{
    require_once ("engine/nodes/headers.php");
    require_once ("engine/nodes/mysql.php");
    require_once ("engine/nodes/session.php");
}catch(Exception $e){
    $error .= '1';
}try{
    $flag = 0;
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "autoupdate"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if($data["value"]){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "checkupdate"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        // check for updates once a hour
        if(intval($data["value"])<=date("U")-3600){
            $flag = 1;
            $GLOBALS["auto"]=1;
            require_once("engine/code/update.php");
        }
    }
}catch(Exception $e){
    $error .= '2';
}try{
    if(!$flag){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron_session"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        // dayly unlink sessions more than week timelife
        if($data["value"]<date("U")-86400){
            $flag = 1;
            if(!empty($_SERVER["DIR"])) $dirct = substr ($_SERVER["DIR"], 1)."/";
            $dirct .= "sessions/";
            $hdl = opendir($dirct);
            while ($file_name = readdir($hdl)){
                if (($file_name != ".") && ($file_name != "..") && is_file($dirct.$file_name)){
                    if(filemtime($dirct.$file_name)<date("U")-604800){
                        unlink($_SERVER["DOCUMENT_ROOT"]."/".$dirct.$file_name);
                    }
                }
            }closedir($hdl);
            $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "cron_session"';
            engine::mysql($query);
        }
    }
}catch(Exception $e){
    $error .= '3';
}try{
    if(!$flag){
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron_images"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        // dayly unlink temp images
        if($data["value"]<date("U")-86400){
            $flag = 1;
            $images = array();
            $query = 'SELECT * FROM  `nodes_products`';
            $res = engine::mysql($query);
            while($data = mysql_fetch_array($res)){
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
            while($data = mysql_fetch_array($res)){
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
            while($data = mysql_fetch_array($res)){
                $img = trim($data["img"]);
                if(!empty($img)){
                    if(!in_array($img, $images)) array_push($images, $img);
                }
            }
            $path = '';
            if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
            $path .= "img/data/big/";
            $dir = $_SERVER["DOCUMENT_ROOT"].'/'.$path;
            $hdl = opendir($dir);
            while ($file_name = readdir($hdl)){
                if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)){
                    if(!in_array($file_name, $images)){
                        try{ unlink($dir.$file_name); } catch (Exception $ex) { unlink($path.$file_name); }
                    }
                }
            }closedir($hdl);
            $path = '';
            if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
            $path .= "img/data/thumb/";
            $dir = $_SERVER["DOCUMENT_ROOT"].'/'.$path;
            $hdl = opendir($dir);
            while ($file_name = readdir($hdl)){
                if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)){
                    if(!in_array($file_name, $images)){
                        try{ unlink($dir.$file_name); } catch (Exception $ex) { unlink($path.$file_name); }
                    }
                }
            }closedir($hdl);
            $images = array();
            $query = 'SELECT * FROM  `nodes_users`';
            $res = engine::mysql($query);
            while($data = mysql_fetch_array($res)){
                $img = trim($data["photo"]);
                if(!empty($img)){
                    if(!in_array($img, $images)) array_push($images, $img);
                }
            }
            $path = '';
            if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
            $path .= 'img/pic/';
            $dir = $_SERVER["DOCUMENT_ROOT"].'/'.$path;
            $hdl = opendir($dir);
            while ($file_name = readdir($hdl)){
                if (($file_name != ".") && ($file_name != "..") && is_file($dir.$file_name)
                        && $file_name != "admin.jpg" && $file_name != "anon.jpg"    ){
                    if(!in_array($file_name, $images)){
                        try{ unlink($dir.$file_name); } catch (Exception $ex) { unlink($path.$file_name); }
                    }
                }
            }closedir($hdl);
            $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "cron_images"';
            engine::mysql($query);
        }
    }
}catch (Exception $e){
    $error .= '4';
}try{
    if(!$flag){
        $query = 'SELECT * FROM `nodes_catch` WHERE `interval` > 0 AND `url` NOT LIKE "cron.php" ORDER BY `date` ASC LIMIT 0, 10';
        $res = engine::mysql($query);
        while($data = mysql_fetch_array($res)){
            if($data["date"]<=intval(date("U")-$data["interval"])){
                $flag = 1;
                if(!strpos($data["url"], "://")) $data["url"] = "http://".$_SERVER["HTTP_HOST"].$data["url"];
                $html = engine::curl_post_query($data["url"], "nocatch=1&lang=".$data["lang"]);
                $c = explode('<!DOCTYPE', $html);
                preg_match('/<title>(.*?)<\/title>.*?<\!-- content -->(.*?)<\!-- \/content -->/sim', $html, $m);
                $title = trim($m[1]);
                $content = trim($m[2]);
                if(!empty($content)){
                    $html='<!DOCTYPE'.str_replace($content, '<content/>', $c[1]);  
                }else{
                    $html='<!DOCTYPE'.$c[1]; 
                }          
                $query = 'UPDATE `nodes_catch` SET `html` = "'.str_replace('"', '\"', $html).'", `date` = "'.date("U").'", `title` = "'.$title.'", `content` = "'.str_replace('"', '\"', trim($content)).'" WHERE `id` = "'.$data["id"].'"';
                engine::mysql($query);
            }
        }
    }
}catch(Exception $e){
    $error .= '5';
}try{
    if(!$flag){
        $query = 'SELECT * FROM `nodes_catch` WHERE `title` = "" AND `url` NOT LIKE "cron.php" ORDER BY `date` ASC LIMIT 0, 10';
        $res = engine::mysql($query);
        while($data = mysql_fetch_array($res)){
            $flag = 1;
            if(!strpos($data["url"], "://")) $data["url"] = "http://".$_SERVER["HTTP_HOST"].$data["url"];
            $html = engine::curl_post_query($data["url"], "nocatch=1&lang=".$data["lang"]);
            $c = explode('<!DOCTYPE', $html);
            preg_match('/<title>(.*?)<\/title>.*?<\!-- content -->(.*?)<\!-- \/content -->/sim', $html, $m);
            $title = trim($m[1]);
            $content = trim($m[2]);
            if(!empty($content)){
                $html='<!DOCTYPE'.str_replace($content, '<content/>', $c[1]);  
            }else{
                $html='<!DOCTYPE'.$c[1]; 
            }          
            $query = 'UPDATE `nodes_catch` SET `html` = "'.str_replace('"', '\"', $html).'", `date` = "'.date("U").'", `title` = "'.$title.'", `content` = "'.str_replace('"', '\"', trim($content)).'" WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
        }  
    }
}catch(Exception $e){
    $error .= '6';
}if(!empty($error)){
    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details)'
            . ' VALUES("9", "-1", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "#'.$error.'")';
    engine::mysql($query);
}