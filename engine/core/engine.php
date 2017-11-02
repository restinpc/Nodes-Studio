<?php
/**
* Framework engine class.
* @path /engine/core/engine.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @example <code> engine::timezone_list(); </code>
*/
class engine{
//------------------------------------------------------------------------------
/**
* Includes a file from engine/core/../<function_name>.php and 
* execute <function_name>($arguments[0], $arguments[1], ...);
* 
* @param string $name <function_name>
* @param array $arguments Array of arguments.
* @return mixed Returns result of targeted function or die with error.
* 
* @example <code> 
*  engine::print_paypal_form($site, 10, "/account/finance"); 
* </code> 
*/
public static function __callStatic($name, $arguments) {
    $exec = function_exists($name);
    if(!$exec && !empty($_SERVER["CORE_PATH"])){
        if(is_file('engine/core/'.$_SERVER["CORE_PATH"].'/'.$name.'.php')){
            require_once('engine/core/'.$_SERVER["CORE_PATH"].'/'.$name.'.php'); 
            $exec = 1;
        }
    }
    if(!$exec && is_file('engine/core/function/'.$name.'.php')){
        require_once('engine/core/function/'.$name.'.php');
        $exec = 1;
    }
    if(!$exec){
        $skip = array('.', '..', 'function', $_SERVER["CORE_PATH"]);
        $files = scandir('engine/core/');
        foreach($files as $file) {
            if(!in_array($file, $skip)){
                if(is_file('engine/core/'.$file.'/'.$name.'.php')){
                    require_once('engine/core/'.$file.'/'.$name.'.php'); 
                    $exec = 1;
                    break;
                }
            }
        }
    }
    if($exec){
        $count = count($arguments);
        if(!$count){
            return $name();
        }else if($count==1){
            return $name($arguments[0]);
        }else if($count==2){
            return $name(
                    $arguments[0], 
                    $arguments[1]
                    );
        }else if($count==3){
            return $name(
                    $arguments[0], 
                    $arguments[1], 
                    $arguments[2]
                    );
        }else if($count==4){
            return $name(
                    $arguments[0], 
                    $arguments[1], 
                    $arguments[2], 
                    $arguments[3]
                    );
        }else if($count==5){
            return $name(
                    $arguments[0], 
                    $arguments[1], 
                    $arguments[2], 
                    $arguments[3], 
                    $arguments[4]
                    );
        }
    }else self::error();
}
//------------------------------------------------------------------------------
/**
* Register error in a DB and print error page.
* 
* @param string $error_code HTTP code of error.
* @usage <code> engine::error(401); </code>
*/
static function error($error_code='0'){
    if($error_code != 0) $_GET[$error_code] = 1;
    if(!isset($_GET["204"]) && !isset($_GET["504"])){
        $_SERVER["SCRIPT_URI"] = str_replace("http://","\$h", $_SERVER["SCRIPT_URI"]);   
        while($_SERVER["SCRIPT_URI"][strlen($_SERVER["SCRIPT_URI"])-1]=="/"){
            $_SERVER["SCRIPT_URI"] = mb_substr($_SERVER["SCRIPT_URI"], 0, strlen($_SERVER["SCRIPT_URI"])-1);
        }$_SERVER["SCRIPT_URI"] = str_replace("\$h", "http://", $_SERVER["SCRIPT_URI"]);
        if(empty($_SERVER["SCRIPT_URI"])) $_SERVER["SCRIPT_URI"]="/";
        $get = $session = $post = '';
        foreach($_GET as $key=>$value) $get .= ' | '.$key.'='.$value;
        foreach($_POST as $key=>$value) $post .= ' | '.$key.'='.$value;
        foreach($_SESSION as $key=>$value)
            if($key != "Lang" && $key != "REQUEST_URI" && $key != "user")
                $session .= ' | '.$key.'='.$value;
        $get = mysql_real_escape_string($get);
        $post = mysql_real_escape_string($post);
        $session = mysql_real_escape_string($session);
        $query = 'DELETE FROM `nodes_cache` WHERE `url` = "'.$_SERVER["SCRIPT_URI"].'" '
                . 'AND `lang` = "'.$_SESSION["Lang"].'"';
        engine::mysql($query);
        $query = 'SELECT * FROM `nodes_error` WHERE '
                . '`url` = "'.$_SERVER["SCRIPT_URI"].'" AND '
                . '`lang` = "'.$_SESSION["Lang"].'" AND '
                . '`ip` = "'.$_SERVER["REMOTE_ADDR"].'" AND '
                . '`get` = "'.$get.'" AND '
                . '`post` = "'.$post.'" AND '
                . '`session` = "'.$session.'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(empty($data)){
            $query = 'INSERT INTO `nodes_error`(`url`, `lang`, `date`, `ip`, `get`, `post`, `session`, `count`) '
            . 'VALUES("'.$_SERVER["SCRIPT_URI"].'", '
                    . '"'.$_SESSION["Lang"].'", '
                    . '"'.date("U").'", '
                    . '"'.$_SERVER["REMOTE_ADDR"].'", '
                    . '"'.$get.'", '
                    . '"'.$post.'", '
                    . '"'.$session.'", '
                    . '"1")';
        }else{
           $query = 'UPDATE `nodes_error` SET `date` = "'.date("U").'", `count` = "'.($data["count"]+1).'" WHERE `id` = "'.$data["id"].'"'; 
        }
        self::mysql($query);
    }
    if(empty($_POST["jQuery"])){ echo '<!DOCTYPE html>
    <html style="background: #fff; font-family: sans-serif;">
    <head><title>Error</title><meta charset="UTF-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head><body>';
        require_once("engine/code/error.php");
        echo '</body></html>';   
    }else{
        require_once("engine/code/error.php");
    }
    $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "debug"';
    $res = self::mysql($query);
    $data = mysql_fetch_array($res);
    if($data[0]){
        echo "<!--------------------------------------"."\n"."PHP:"."\n";
        print_r(error_get_last());
        echo "\n"."----------------------------------------"."\n"."MySQL:"."\n";
        print_r(mysql_error());
        echo "\n"."----------------------------------------"."\n"."--!>";
    }die();
}
//------------------------------------------------------------------------------
/**
* Sends a query to the currently active MySQL DB.
* 
* @param string $query MySQL request.
* @return mixed Returns a resource on success, or die with error.
* @usage <code>
*  $res = engine::mysql($query); 
*  $data = mysql_fetch_array($res);
* </code>
*/
static function mysql($query){
    require_once("engine/nodes/mysql.php");
    @mysql_query("SET NAMES utf8");
    $res = mysql_query($query) or die(self::error());
    return $res;
}
//------------------------------------------------------------------------------
/**
* Sends a mail.
* 
* @param string $email Receiver, or receivers of the mail.
* @param string $header Sender of the mail.
* @param string $theme Subject of the email to be sent.
* @param string $message Text of the email to be sent.
* @return bool Returns TRUE on success, FALSE on failure.
* @usage <code>
*  engine::send_mail("dev@null.com", "admin@server.com", "Hello", "Text");
* </code>
*/
static function send_mail($email, $header, $theme, $message){
    $text = "To: ".$email."\n";
    $text .= "Theme: ".$theme."\n";
    $text .= "Text: ".$message;
    preg_replace('/<style>.*?<\/style>/', '', $text);
    $text = mysql_real_escape_string(strip_tags($text, '<a><br/>'));
    $header = "From: {$header}\nContent-Type: text/html; charset=utf-8";
    $theme = '=?utf-8?B?' . base64_encode($theme) . '?=';
    if(mail($email, "{$theme}\n", $message, $header)){
        $query = 'INSERT INTO `nodes_log`(action, user_id, ip, date, details) '
                . 'VALUES("9", "-1", "127.0.0.1", "'.date("U").'", "'.$text.'")';
        self::mysql($query);
        return true;
    } return false;
}
//------------------------------------------------------------------------------
/**
* Convert a string to URL-compatible format.
* 
* @param string $str The input string.
* @return string Returns the convetrted string.
* @usage <code> engine::url_translit("Hello world!"); </code>
*/
static function url_translit($str){
    $translit = array(
        "А"=>"A", "Б"=>"B", "В"=>"V", "Г"=>"G", "Д"=>"D", "Е"=>"E", "Ж"=>"J", 
        "З"=>"Z", "И"=>"I", "Й"=>"Y", "К"=>"K", "Л"=>"L", "М"=>"M", "Н"=>"N", 
        "О"=>"O", "П"=>"P", "Р"=>"R", "С"=>"S", "Т"=>"T", "У"=>"U", "Ф"=>"F", 
        "Х"=>"H", "Ц"=>"TS", "Ч"=>"CH", "Ш"=>"SH", "Щ"=>"SCH", "Ъ"=>"", "Ы"=>"YI", 
        "Ь"=>"", "Э"=>"E", "Ю"=>"YU", "Я"=>"YA", "а"=>"a", "б"=>"b", "в"=>"v", 
        "г"=>"g", "д"=>"d", "е"=>"e", "ж"=>"j", "з"=>"z", "и"=>"i", "й"=>"y", 
        "к"=>"k", "л"=>"l", "м"=>"m", "н"=>"n", "о"=>"o", "п"=>"p", "р"=>"r", 
        "с"=>"s", "т"=>"t", "у"=>"u", "ф"=>"f", "х"=>"h", "ц"=>"ts", "ч"=>"ch", 
        "ш"=>"sh", "щ"=>"sch", "ъ"=>"y", "ы"=>"yi", "ь"=>"", "э"=>"e", "ю"=>"yu", 
        "я"=>"ya", "  "=>" ", " "=>"_"
    );
    $str = strtr($str,$translit);
    $str = preg_replace ("/[^a-zA-ZА-Яа-я0-9_\s]/", "", $str);
    return $str;
}
//------------------------------------------------------------------------------
/**
* Send GET request using CURL Library. 
* 
* @param string $url Request URL.
* @param bool $format Remove all non-text chars from string if TRUE.
* @return string Returns result of request.
* @usage <code> engine::curl_get_query("http://google.com"); </code>
*/
static function curl_get_query($url, $format=0){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Nodes Studio 2.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);  
    if(empty($html)){
        return $error;
    }if($format){
        $html = str_replace("\r", "", $html);
        $html = str_replace("\f", "", $html);
        $html = str_replace("\v", " ", $html);
        $html = str_replace("\n", "", $html);
        $html = str_replace("\t", "", $html);
    }return $html;
}
//------------------------------------------------------------------------------
/**
* Send POST request using CURL Library. 
* 
* @param string $url Request URL.
* @param string $url Formated POST data.
* @param bool $format Remove all non-text chars from string if TRUE.
* @return string Returns result of request.
* @usage <code> engine::curl_post_query("http://google.com", 'foo=1&bar=2'); </code>
*/
static function curl_post_query($url, $query, $format=0){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Nodes Studio 2.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $html = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);  
    if(empty($html)){
        return $error;
    }if($format){
        $html = str_replace("\r", "", $html);
        $html = str_replace("\f", "", $html);
        $html = str_replace("\v", " ", $html);
        $html = str_replace("\n", "", $html);
        $html = str_replace("\t", "", $html);
    }return $html;
}
//------------------------------------------------------------------------------
static function redirect($url){
    header( 'Location: '.$url );
    die('<script>window.location = "'.$url.'";</script>');
}
//------------------------------------------------------------------------------
static function encrypt($encrypt, $key){
    $encrypt = serialize($encrypt);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
    $key = pack('H*', $key);
    $mac = hash_hmac('sha256', $encrypt, mb_substr(bin2hex($key), -32));
    $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
    $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
    return $encoded;
}
//------------------------------------------------------------------------------
static function decrypt($decrypt, $key){
    $decrypt = explode('|', $decrypt.'|');
    $decoded = base64_decode($decrypt[0]);
    $iv = base64_decode($decrypt[1]);
    if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
    $key = pack('H*', $key);
    $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
    $mac = mb_substr($decrypted, -64);
    $decrypted = mb_substr($decrypted, 0, -64);
    $calcmac = hash_hmac('sha256', $decrypted, mb_substr(bin2hex($key), -32));
    if($calcmac!==$mac){ return false; }
    $decrypted = unserialize($decrypted);
    return $decrypted;
}
//------------------------------------------------------------------------------
/**
* Gets the timezone list used by all date/time functions.
* 
* @return array Returns array with timezone.
* @usage <code> engine::timezone_list(); </code>
*/
static function timezone_list(){
    $zones_array = array();
    $timestamp = time();
    $default = date_default_timezone_get();
    foreach(timezone_identifiers_list() as $key => $zone) {
      date_default_timezone_set($zone);
      $zones_array[$key]['zone'] = $zone;
      $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    date_default_timezone_set($default);
    return $zones_array;
}
//------------------------------------------------------------------------------
/**
* Check URL in @mysql[nodes_content] and @mysql[nodes_catalog].
* 
* @return array Returns TRUE if URL is article.
* @usage <code> engine::is_article(); </code>
*/
static function is_article($url){
    $url = str_replace($_SERVER["PUBLIC_URL"].'/', '', $url);
    if(strpos($url, "content/")!==FALSE) $url = mb_substr($url, 8);
    $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$url.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data)) return 1;
    $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$url.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data)) return 1;
    return 0;
}
//------------------------------------------------------------------------------
/**
* Check URL in @mysql[nodes_product].
* 
* @return array Returns TRUE if URL is porduct.
* @usage <code> engine::is_product(); </code>
*/
static function is_product($url){
    $url = str_replace($_SERVER["PUBLIC_URL"].'/', '', $url);
    if(strpos($url, "product/")!==FALSE){
        $id = intval(mb_substr($url, 8));
        $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$id.'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(!empty($data)) return $id;
    }else return 0;
}
//------------------------------------------------------------------------------
/**
* Matching pattern with database.
* 
* @param int $url Page URL. 
* @return string Returns formatted string.
* @usage <code>
*   $url = '/test1';
*   $fout = engine::match_patterns($url);
* </code>
*/
static function match_patterns($url){
    $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date`, `cache`.`id` as `cache_id` '
            . 'FROM `nodes_attendance` AS `att` '
            . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
            . 'WHERE `att`.`token` = "'.session_id().'" ORDER BY `att`.`date` DESC';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $cache_id = $data["cache_id"];
    $is_article = $is_product = 0;
    if(engine::is_article($url)) $is_article = 1;
    else if(engine::is_product($url)) $is_product = 1; 
    else die();
    $pattern = array($url);
    $i = 1;
    while($data = mysql_fetch_array($res)){
        if(!empty($data["value"])){
            $pattern[$i++] = $data["value"];
        }
    }
    // echo "Current pattern: "; print_r($pattern); echo '<br/><hr/>';
    $query = 'SELECT `att`.`token` as `token` '
        . 'FROM `nodes_attendance` AS `att` '
        . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
        . 'WHERE `cache`.`id` = "'.$cache_id.'" AND `att`.`token` <> "'.session_id().'"';
    $res = engine::mysql($query);
    $nodes = array();
    while($data = mysql_fetch_array($res)){
        if(!in_array($data["token"], $nodes))
            array_push($nodes, $data["token"]);
    }
    // echo "Same patterns: "; print_r($nodes); echo '<br/><hr/>';
    $output = array();
    foreach($nodes as $node){
        $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date`, `cache`.`id` as `cache_id` '
            . 'FROM `nodes_attendance` AS `att` '
            . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
            . 'WHERE `att`.`token` = "'.$node.'"';
        $r = engine::mysql($query);
        while($d = mysql_fetch_array($r)){
            if($is_article){ 
                if(engine::is_article($d["value"])){
                    $output[$d["value"]]++;
                }
            }else if($is_product){ 
                if(engine::is_product($d["value"])){
                    $output[$d["value"]]++;  
                }
            }
        }
    }
    // echo "Unsorted pages: "; print_r($output); echo '<br/><hr/>';
    $pages = array();
    $views = array();
    $i = 0;
    foreach($output as $key=>$value){
        if(!in_array($key, $pattern)){
            $pages[$i] = $key;
            $views[$i++] = $value;
        }
    }
    for($i = 1; $i < count($views); $i++){
        for($j = 0; $j<$i; $j++){
            if($views[$i]>$views[$j]){
                $temp = $pages[$j];
                $pages[$j] = $pages[$i];
                $pages[$i] = $temp;
                $temp = $views[$j];
                $views[$j] = $views[$i];
                $views[$i] = $temp;
            }
        }
    } 
    // echo "Sorted pages: "; print_r($pages); echo '<br/><hr/>';
    $fout = '';
    for($i = 0; $i < count($pages); $i++){
        $pages[$i] = str_replace($_SERVER["PUBLIC_URL"].'/', '', $pages[$i]);
        if(mb_strpos($pages[$i], 'content/') !== FALSE || 
            mb_strpos($pages[$i], 'product/') !== FALSE)
                $pages[$i] = mb_substr($pages[$i], 8);
        if(empty($fout)){
            $fout .= $pages[$i];
        }else{
            $fout .= ';'.$pages[$i];
        }
    } return $fout;
}
}