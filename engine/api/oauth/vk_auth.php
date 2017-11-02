<?php
/**
* VK OAuth script.
* @path /engine/api/oauth/vk_auth.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
if(!empty($_REQUEST["name"])){
    if(!empty($_REQUEST["url"])){
        if($_SESSION["request"]>date("U")-60){
            $_SESSION["request"] = '';
            if(!empty($_SESSION["user"]["email"])){
                $query = 'UPDATE `nodes_user` SET `url` = "'.$_REQUEST["url"].'" WHERE `email` = "'.$_SESSION["user"]["email"].'"';
                engine::mysql($query);
                $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$_SESSION["user"]["email"].'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
            }else{
                $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$_REQUEST["url"].'"'; 
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(empty($data)){
                    $path = '';
                    $ext = explode('.', $_REQUEST["photo"]);
                    $pic = md5($_REQUEST["photo"].date("U")).'.'.$ext[count($ext)-1];
                    if(!empty($_SERVER["DIR"])) $path = substr ($_SERVER["DIR"], 1)."/";
                    $path .= 'img/pic/';
                    try{ copy($_REQUEST["photo"], $path.$pic); }catch(Exception $e){ copy($_REQUEST["photo"], $_SERVER["DOCUMENT_ROOT"].'/'.$path.$pic); }
                    $query = 'INSERT INTO `nodes_user`(name, photo, url, online, confirm) VALUES("'.$_REQUEST["name"].'", "'.$pic.'", "'.$_REQUEST["url"].'", "'.date("U").'", "1")';
                    $res = engine::mysql($query);
                    $query = 'SELECT * FROM `nodes_user` WHERE `url` = "'.$_REQUEST["url"].'"';
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
            $_SESSION["request"] = '';
            die('<script>alert("'.lang("Authentication timeout").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>');
        }
    }else{
        $_SESSION["request"] = '';
        die('<script>alert("'.lang("Authentication error").'"); parent.window.location = "'.$_SERVER["DIR"].'/";</script>'); 
    }
}else{
    $_SESSION["request"] = date("U");    
    die('<form method="POST" action="'.$_SERVER["DIR"].'/account.php?mode=social&method=vk" id="form">
        <input type="hidden" id="name" name="name" value="" />
        <input type="hidden" id="photo" name="photo" value="" />
        <input type="hidden" id="url" name="url" value="" />
    </form>
    <script type="text/javascript">
    function callbackFunc(result) {
        document.getElementById("name").value = result.response[0].first_name+" "+result.response[0].last_name;
        document.getElementById("photo").value = result.response[0].photo;
        document.getElementById("url").value = "http://vk.com/id"+result.response[0].uid;
        document.getElementById("form").submit();
    }
    var sub = document.location.hash.replace(/#/gi,"'.$_SERVER["DIR"].'/account.php?mode=social&method=vk&").split("&"); 
    var token = sub[2].split("="); 
    var user_id = sub[4].split("=");
    this.document.write(unescape(\'<script type="text/javascript" src="https://api.vk.com/method/users.get?uids=\'+user_id[1]+\'&access_token=\'+token[1]+\'&fields=uid,first_name,last_name,photo&callback=callbackFunc">%3C/script%3E\')); 
</script>');  
}
