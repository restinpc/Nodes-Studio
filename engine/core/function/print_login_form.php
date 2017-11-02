<?php
/**
* Prints login form.
* @path /engine/core/function/print_login_form.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_login_form(); </code>
*/
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/language.php");
function print_login_form(){
    $flag = 0;
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "vk_id"';
    $res = engine::mysql($query);
    $vk = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "fb_id"';
    $res = engine::mysql($query);
    $fb_id = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "tw_key"';
    $res = engine::mysql($query);
    $tw_key = mysql_fetch_array($res);
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "gp_id"';
    $res = engine::mysql($query);
    $gp_id = mysql_fetch_array($res);    
    $fout = '<div class="left w200">'
    . '<script>parent.document.getElementById("nodes_iframe").style.height="290px";'
    . '</script>'
    . '<center><h3 class="c555">'.lang("Login").'</h3></center><br/>'
    . '<div class="center nowrap">';  
    if(!empty($fb_id["value"])){ $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=fb" class="m10 ml0"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" title="Facebook"/></a>';
    }if(!empty($tw_key["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=tw" class="m9"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" title="Twitter"/></a>';
    }if(!empty($gp_id["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=gp" class="m9"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" title="Google+"/></a>';
    }if(!empty($vk["value"])){  $flag++;
        $fout .= '<a rel="nofollow" target="_parent" href="https://oauth.vk.com/authorize?client_id='.$vk["value"].'&scope=notify&redirect_uri='.  urlencode($_SERVER["PUBLIC_URL"].'/account.php?mode=social&method=vk').'&display=page&response_type=token" class="m10 mr0"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" title="Vkontakte"/></a>';
    }if(!$flag){
        $fout .= '<br/>';
    }
    $fout .= '</div><br/>'
    . '<form method="POST" action="'.$_SERVER["DIR"].'/account.php?mode=login">'
    . '<input type="text" required name="email" value="'.$_POST["email"].'" class="input w200 p5" placeHolder="Email" /><br/><br/>'
    . '<input type="password" required name="pass" class="input w200 p5" value="'.$_POST["pass"].'" placeHolder="'.lang("Password").'" /><br/>'
    . '<div class="nowrap pt17 pb20 center fs14">'
    . '<a onClick=\'parent.window.location = "'.$_SERVER["DIR"].'/register";\'>'.lang("Sign Up").'</a> | <a rel="nofollow" href="'.$_SERVER["DIR"].'/account.php?mode=remember">'.lang("Lost password").'?</a>'
    . '</div>'
    . '<input type="submit" class="btn w200" value="'.lang("Submit").'" /></form>'
    . '</div>';   
    return $fout;
}