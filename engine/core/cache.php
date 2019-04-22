<?php
/**
* Cache library.
* @path /engine/core/cache.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
* 
* @example <code>
*  $cache = new cache();
*  $cache_id = $cache->page_id();
* </code>
*/
class cache{
//------------------------------------------------------------------------------
/*
* Update a cache data in DB.
* 
* @param string $url Page URL.
* @param bool $jQuery jQuery mode.
* @param string $lang Request language.
* @return string Returns HTML contents of page.
* $usage <code> cache::update_cache("/", TRUE); </code>
*/
public static function update_cache($url, $jQuery = 0, $lang="en"){
    if(strpos($url, $_SERVER["PROTOCOL"]."://".$_SERVER["HTTP_HOST"])===FALSE) 
        $path = $_SERVER["PROTOCOL"]."://".$_SERVER["HTTP_HOST"].$url;
    else $path = $url;
    $current = doubleval(microtime(1));
    $html = engine::curl_post_query($path, "nocache=1&lang=".$lang);
    $load_time = doubleval(microtime(1)-$current);
    $c = explode('<!DOCTYPE', $html);
    preg_match('/<title>(.*?)<\/title>.*?itemprop="description" content="(.*?)".*?itemprop="keywords" '
            . 'content="(.*?)".*?<\!-- content -->(.*?)<\!-- \/content -->/sim', $html, $m);
    $title = trim($m[1]);
    $description = trim($m[2]);
    $keywords = trim($m[3]);
    $content = trim($m[4]);
    if(!empty($content)){
        $fout='<!DOCTYPE'.str_replace('<content/>', $content, $c[1]);  
    }else{
        $fout='<!DOCTYPE'.$c[1]; 
    }
    if(!empty($content)){
        $html = str_replace("\\\\", "\\\\\\", str_replace('"', '\"', trim($html)));
        $content = str_replace("\\\\", "\\\\\\", str_replace('"', '\"', trim($content)));
        $query = 'UPDATE `nodes_cache` SET `html` = "'.$html.'", '
            . '`date` = "'.date("U").'", '
            . '`title` = "'.$title.'", '
            . '`description` = "'.$description.'", '
            . '`keywords` = "'.$keywords.'", '
            . '`content` = "'.$content.'", '
            . '`time` = "'.$load_time.'" '
            . 'WHERE `url` = "'.$url.'" AND `lang` = "'.$lang.'"';
        engine::mysql($query);
    }else if(empty($html)){
        $query = 'DELETE FROM `nodes_cache` WHERE `url` = "'.$url.'" AND `lang` = "'.$lang.'"';
        engine::mysql($query);
        return;
    }
    if(!$jQuery){
        return($fout."
<!-- Refreshing cache. Time loading: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->");
    }else{
        return('<title>'.$data["title"].'</title>'.base64_encode($content."
<!-- Refreshing cache and return content. Time loading: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->"));
    }
}
//------------------------------------------------------------------------------
/*
* Output requested page from cache DB.
* 
* @return string Returns HTML contents of requested page.
* $usage <code> $cache = new cache(); </code>
*/
public function cache(){
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cache"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $is_cache = intval($data["value"]);
    $fout = ''; 
    if( empty($_POST) || !empty($_POST["cache"]) ){
        $query = 'SELECT * FROM `nodes_cache` WHERE `url` = "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(!empty($data) && $data["interval"]>0){
            if($data["date"]<=intval(date("U")-$data["interval"])){
                die(self::update_cache($_SERVER["SCRIPT_URI"], 0, $data["lang"]));
            }else if(!empty($data["html"])){
                if(!empty($data["content"])){
                    $html = $data["html"];
                }else{
                    $html = str_replace('<content/>', $data["content"], $data["html"]);
                }
                die($html."
<!-- Time loading from cache: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->");
            }
            $fout .= "<!-- Cache is empty -->";
        }else if(empty($data)){
            $fout .= "<!-- Cache is empty -->";
            $query = 'INSERT INTO `nodes_cache`(url, date, lang, `interval`, html, content) '
            . 'VALUES("'.$_SERVER["SCRIPT_URI"].'", "'.date("U").'", "'.$_SESSION["Lang"].'", -1, "", "")';
            engine::mysql($query);
        }else if($data["interval"]=="0"){
            if($is_cache){
                if(empty($data["html"]) || !empty($_POST["cache"])){
                    die(self::update_cache($_SERVER["SCRIPT_URI"], 0, $data["lang"]));
                }
                if(empty($data["content"])){
                    $html = $data["html"];
                }else{
                    $html = str_replace('<content/>', $data["content"], $data["html"]);
                }
                die($html."
<!-- Time loading form cache: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->");
            }
        }
    // cacheing for asinc jquery requests
    }else if(count($_POST)==1 && isset($_POST["jQuery"])){
        $query = 'SELECT * FROM `nodes_cache` WHERE `url` = "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(!empty($data) && $data["interval"]>0){
            if($data["date"]<=intval(date("U")-$data["interval"])){
                die(self::update_cache($_SERVER["SCRIPT_URI"], 1, $data["lang"]));
            }else if(!empty($data["html"])){
                die('<title>'.$data["title"].'</title>'.base64_encode($data["content"]."
<!-- Time loading from cache: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->"));
            }
            $fout .= "<!-- Cache is empty -->";
        }else if(empty($data)){
            $fout .= "<!-- Cache is empty -->";
            $query = 'INSERT INTO `nodes_cache`(url, date, lang, `interval`, html, content) '
            . 'VALUES("'.$_SERVER["SCRIPT_URI"].'", "'.date("U").'", "'.$_SESSION["Lang"].'", -1, "", "")';
            engine::mysql($query);
        }else if($data["interval"]=="0"){
            if($is_cache){
                if(empty($data["html"]) || !empty($_POST["cache"])){
                    die(self::update_cache($_SERVER["SCRIPT_URI"], 1, $data["lang"]));
                }die(base64_encode('<title>'.$data["title"].'</title>'.$data["content"]."
<!-- Time loading form cache: ".(doubleval(microtime(1))-$GLOBALS["time"])." -->"));
            }
        }
    }return $fout;
}
//------------------------------------------------------------------------------
/*
* Gets current page ID.
* 
* @return string Returns id of page in MySQL DB.
* $usage <code> 
*  $cache = new cache(); 
*  $id = $cache->page_id();
* </code>
*/
public function page_id(){
    if(empty($_POST["nocache"])){
        $query = 'SELECT `id` FROM `nodes_cache` WHERE `url` = "'.$_SERVER["SCRIPT_URI"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $cache = mysqli_fetch_array($res);
        if(!empty($cache)){
            $cache_id = $cache["id"];
        }else{
            $query = 'INSERT INTO `nodes_cache`(url, date, lang, `interval`, html, content) '
            . 'VALUES("'.$_SERVER["SCRIPT_URI"].'", "'.date("U").'", "'.$_SESSION["Lang"].'", -1, "", "")';
            engine::mysql($query);
            $cache_id = mysqli_insert_id($_SERVER["sql_connection"]);
        }
    }else $cache_id = 0;
    return $cache_id;
}
}