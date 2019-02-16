<?php
/**
* Google OAuth script.
* @path /engine/api/oauth/google_auth.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "name"';
$res = engine::mysql($query);
$name = mysqli_fetch_array($res);
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "gp_id"';
$res = engine::mysql($query);
$gp_id = mysqli_fetch_array($res);
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "gp_secret"';
$res = engine::mysql($query);
$gp_secret = mysqli_fetch_array($res);
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "gp_dev"';
$res = engine::mysql($query);
$gp_dev = mysqli_fetch_array($res);
global $apiConfig;
$apiConfig = array(
    'use_objects' => true,
    'application_name' => $name["value"],
    'oauth2_client_id' => $gp_id["value"],
    'oauth2_client_secret' => $gp_secret["value"],
    'oauth2_redirect_uri' => $_SERVER["PROTOCOL"].'://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account.php?mode=social&method=gp',
    'developer_key' => $gp_dev["value"],
    'site_name' => $_SERVER["HTTP_HOST"],
    'authClass'    => 'Google_OAuth2',
    'ioClass'      => 'Google_CurlIO',
    'cacheClass'   => 'Google_FileCache',
    'basePath' => 'https://www.googleapis.com',
    'ioFileCache_directory'  =>
        (function_exists('sys_get_temp_dir') ?
            sys_get_temp_dir() . '/Google_Client' :
        '/tmp/Google_Client'),
    'services' => array(
      'analytics' => array('scope' => 'https://www.googleapis.com/auth/analytics.readonly'),
      'calendar' => array(
          'scope' => array(
              "https://www.googleapis.com/auth/calendar",
              "https://www.googleapis.com/auth/calendar.readonly",
          )
      ),
      'books' => array('scope' => 'https://www.googleapis.com/auth/books'),
      'latitude' => array(
          'scope' => array(
              'https://www.googleapis.com/auth/latitude.all.best',
              'https://www.googleapis.com/auth/latitude.all.city',
          )
      ),
      'moderator' => array('scope' => 'https://www.googleapis.com/auth/moderator'),
      'oauth2' => array(
          'scope' => array(
              'https://www.googleapis.com/auth/userinfo.profile',
              'https://www.googleapis.com/auth/userinfo.email',
          )
      ),
      'plus' => array('scope' => 'https://www.googleapis.com/auth/plus.login'),
      'siteVerification' => array('scope' => 'https://www.googleapis.com/auth/siteverification'),
      'tasks' => array('scope' => 'https://www.googleapis.com/auth/tasks'),
      'urlshortener' => array('scope' => 'https://www.googleapis.com/auth/urlshortener')
    )
);
require_once("engine/api/gplus/Google_Client.php");
require_once("engine/api/gplus/contrib/Google_Oauth2Service.php");
$client = new Google_Client();
$oauth2 = new Google_Oauth2Service($client);
if (!empty($_GET["code"])) {
    $client->authenticate($_GET["code"]);
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = $_SERVER["PROTOCOL"].'://'.$_SERVER['HTTP_HOST'].$_SERVER["DIR"].'/account.php?mode=social&method=gp';
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    return;
}if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
}if ($client->getAccessToken()) {
    $user = $oauth2->userinfo->get();
    if(!empty($user->name)&&!empty($user->link)){
        if(!empty($_SESSION["email"])){
            $query = 'UPDATE `nodes_user` SET `url` = "'.$user->link.'" WHERE `email` = "'.$_SESSION["email"].'"';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$_SESSION["email"].'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
        }else{
            $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$user->link.'"'; 
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if(empty($data)){
                $path = '';
                $ext = explode('.', $user->picture);
                $pic = md5($user->picture.date("U")).'.'.$ext[count($ext)-1];
                if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
                $path .= 'img/pic/';
                try{ copy($user->picture, $path.$pic); }catch(Exception $e){ copy($user->picture, $_SERVER["DOCUMENT_ROOT"].'/'.$path.$pic); }
                $query = 'INSERT INTO `nodes_user`(name, photo, url, online, confirm) '
                . 'VALUES("'.$user->name.'", "'.$pic.'", "'.$user->link.'", "'.date("U").'", "1")';
                $res = engine::mysql($query);
                $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$user->link.'"';
                $res = engine::mysql($query);
                $data = mysqli_fetch_array($res);
            }
        }
        unset($data["pass"]);
        unset($data[5]);
        unset($data["token"]);
        unset($data[9]);
        $_SESSION["user"] = array();
        $_SESSION["user"] = $data;
        die('<script>parent.window.location = "'.$_SERVER["DIR"].'/account/settings";</script>');
    }else die('<script>alert("'.lang("Authentication error").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>'); 
}else{
    $_SESSION["email"] = $_SESSION["user"]["email"];
    unset($_SESSION["user"]);
    $authUrl = $client->createAuthUrl();
    die('<script>window.location = "'.$authUrl.'";</script>');
}