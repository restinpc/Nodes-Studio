<?php
/**
* Twitter OAuth script.
* @path /engine/api/oauth/twitter_auth.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "tw_key"';
$res = engine::mysql($query);
$key = mysql_fetch_array($res);
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "tw_secret"';
$res = engine::mysql($query);
$secret = mysql_fetch_array($res);
define ('TWITTER_CONSUMER_KEY', $key["value"]);
define ('TWITTER_CONSUMER_SECRET', $secret["value"]);
define ('TWITTER_URL_CALLBACK', 'http://'.$_SERVER["HTTP_HOST"].''.$_SERVER["DIR"].'/account.php?mode=social&method=tw&auth=1');
define ('URL_REQUEST_TOKEN', 'https://api.twitter.com/oauth/request_token');
define ('URL_AUTHORIZE', 'https://api.twitter.com/oauth/authorize');
define ('URL_ACCESS_TOKEN', 'https://api.twitter.com/oauth/access_token');
define ('URL_ACCOUNT_DATA', 'https://api.twitter.com/1.1/users/show.json');
if(!$_GET["auth"]){
    $oauth_nonce = md5(uniqid(rand(), true));
    $oauth_timestamp = time();
    $oauth_base_text = "GET&";
    $oauth_base_text .= urlencode(URL_REQUEST_TOKEN)."&";
    $oauth_base_text .= urlencode("oauth_callback=".urlencode(TWITTER_URL_CALLBACK)."&");
    $oauth_base_text .= urlencode("oauth_consumer_key=".TWITTER_CONSUMER_KEY."&");
    $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
    $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
    $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
    $oauth_base_text .= urlencode("oauth_version=1.0");
    $key = TWITTER_CONSUMER_SECRET."&";
    $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
    $url = URL_REQUEST_TOKEN;
    $url .= '?oauth_callback='.urlencode(TWITTER_URL_CALLBACK);
    $url .= '&oauth_consumer_key='.TWITTER_CONSUMER_KEY;
    $url .= '&oauth_nonce='.$oauth_nonce;
    $url .= '&oauth_signature='.urlencode($oauth_signature);
    $url .= '&oauth_signature_method=HMAC-SHA1';
    $url .= '&oauth_timestamp='.$oauth_timestamp;
    $url .= '&oauth_version=1.0';
    $response = engine::curl_get_query($url);
    parse_str($response, $result);
    $_SESSION['oauth_token'] = $oauth_token = $result['oauth_token'];
    $_SESSION['oauth_token_secret'] = $oauth_token_secret = $result['oauth_token_secret'];
    $url = URL_AUTHORIZE;
    $url .= '?oauth_token='.$oauth_token;
    header('Location: '.$url);
    echo '<script>this.location = "'.$url.'";</script>';
}else{
    $oauth_nonce = md5(uniqid(rand(), true));
    $oauth_timestamp = time();
    $oauth_token = $_GET['oauth_token'];
    $oauth_verifier = $_GET['oauth_verifier'];
    $oauth_token_secret = $_SESSION['oauth_token_secret'];
    $oauth_base_text = "GET&";
    $oauth_base_text .= urlencode(URL_ACCESS_TOKEN)."&";
    $oauth_base_text .= urlencode("oauth_consumer_key=".TWITTER_CONSUMER_KEY."&");
    $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
    $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
    $oauth_base_text .= urlencode("oauth_token=".$oauth_token."&");
    $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
    $oauth_base_text .= urlencode("oauth_verifier=".$oauth_verifier."&");
    $oauth_base_text .= urlencode("oauth_version=1.0");
    $key = TWITTER_CONSUMER_SECRET."&".$oauth_token_secret;
    $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
    $url = URL_ACCESS_TOKEN;
    $url .= '?oauth_nonce='.$oauth_nonce;
    $url .= '&oauth_signature_method=HMAC-SHA1';
    $url .= '&oauth_timestamp='.$oauth_timestamp;
    $url .= '&oauth_consumer_key='.TWITTER_CONSUMER_KEY;
    $url .= '&oauth_token='.urlencode($oauth_token);
    $url .= '&oauth_verifier='.urlencode($oauth_verifier);
    $url .= '&oauth_signature='.urlencode($oauth_signature);
    $url .= '&oauth_version=1.0';
    $response = file_get_contents($url);
    parse_str($response, $result);
    $oauth_nonce = md5(uniqid(rand(), true));
    $oauth_timestamp = time();
    $oauth_token = $result['oauth_token'];
    $oauth_token_secret = $result['oauth_token_secret'];
    $screen_name = $result['screen_name'];
    $oauth_base_text = "GET&";
    $oauth_base_text .= urlencode(URL_ACCOUNT_DATA).'&';
    $oauth_base_text .= urlencode('oauth_consumer_key='.TWITTER_CONSUMER_KEY.'&');
    $oauth_base_text .= urlencode('oauth_nonce='.$oauth_nonce.'&');
    $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
    $oauth_base_text .= urlencode('oauth_timestamp='.$oauth_timestamp."&");
    $oauth_base_text .= urlencode('oauth_token='.$oauth_token."&");
    $oauth_base_text .= urlencode('oauth_version=1.0&');
    $oauth_base_text .= urlencode('screen_name=' . $screen_name);
    $key = TWITTER_CONSUMER_SECRET . '&' . $oauth_token_secret;
    $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
    $url = URL_ACCOUNT_DATA;
    $url .= '?oauth_consumer_key=' . TWITTER_CONSUMER_KEY;
    $url .= '&oauth_nonce=' . $oauth_nonce;
    $url .= '&oauth_signature=' . urlencode($signature);
    $url .= '&oauth_signature_method=HMAC-SHA1';
    $url .= '&oauth_timestamp=' . $oauth_timestamp;
    $url .= '&oauth_token=' . urlencode($oauth_token);
    $url .= '&oauth_version=1.0';
    $url .= '&screen_name=' . $screen_name;
    $response = engine::curl_get_query($url);
    $user_data = json_decode($response);
    $link = "https://twitter.com/".$user_data->screen_name;
    $_SESSION["request"] = '';
    if(!empty($user_data->name)&&!empty($user_data->screen_name)){
        if(!empty($_SESSION["user"]["email"])){
            $query = 'UPDATE `nodes_user` SET `url` = "'.$link.'" WHERE `email` = "'.$_SESSION["user"]["email"].'"';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$_SESSION["user"]["email"].'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
        }else{
            $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$link.'"'; 
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(empty($data)){
                $path = '';
                $ext = explode('.', $user_data->profile_image_url);
                $pic = md5($user_data->profile_image_url.date("U")).'.'.$ext[count($ext)-1];
                if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
                $path .= 'img/pic/';
                try{ copy($user_data->profile_image_url, $path.$pic); }catch(Exception $e){ copy($user_data->profile_image_url, $_SERVER["DOCUMENT_ROOT"].'/'.$path.$pic); }
                $query = 'INSERT INTO `nodes_user`(name, photo, url, online, confirm) VALUES("'.$user_data->name.'", "'.$pic.'", "'.$link.'", "'.date("U").'", "1")';
                $res = engine::mysql($query);
                $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$link.'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
            }
        }
        unset($data["pass"]);
        unset($data[5]);
        unset($data["token"]);
        unset($data[9]);
        $_SESSION["user"] = array();
        $_SESSION["user"] = $data;
        die('<script>parent.window.location = "'.$_SERVER["DIR"].'/account/settings";</script>');
    }else{
        die('<script>alert("'.lang("Authentication error").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>'); 
    }
}