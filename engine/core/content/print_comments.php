<?php
/**
* Prints comments block.
* @path /engine/core/content/print_comments.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @param string $url Page URL.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_comments("/"); </code>
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
function print_comment($id, $noreply = 0){
    $query = 'SELECT * FROM `nodes_comment` WHERE `id` = "'.intval($id).'"';
    $rc = engine::mysql($query);
    $c = mysql_fetch_array($rc);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$c["user_id"].'"';
    $rd = engine::mysql($query);
    $d = mysql_fetch_array($rd);
    if(!empty($c)){
        $fout .= '<tr><td align=left valign=top class="comment">
                <div class="comment_image">
                    <a href="'.$_SERVER["DIR"].'/profile/'.$d['id'].'"><img src="'.$_SERVER["DIR"].'/img/pic/'.$d["photo"].'" width=50 /></a>
                </div>
                <div class="comment_div">
                    <strong>'.$d["name"].'</strong> <small>'.date("d/m/Y H:i", $c["date"]).'</small>
                    <div class="comment_text">'.$c["text"].'</div>';
        if($_SESSION["user"]["id"]=="1" && !$noreply){
            if($_SESSION["user"]["id"]=="1"){
                $fout .= '<a class="red" onClick=\'delete_comment("'.lang("Are you sure?").'", '.$c["id"].');\'>'.lang("Delete").'</a>';
            }
            if(!$no_reply){
            $fout .= '
                <a onClick=\'document.getElementById("comment_'.$c["id"].'").style.display ="block";'
                    .'this.style.display = "none";\'>'.lang("Reply").'</a><br/>
                <div id="comment_'.$c["id"].'" class="comment_reply">
                ';
                if(empty($_SESSION["user"])){
                    $fout .= '<center>'.lang("To post a comment, please").' <a target="_parent" href="'.$_SERVER["DIR"].'/login" onClick="event.preventDefault(); show_login_form();">'.mb_strtolower(lang("sign in")).'</a> '.mb_strtolower(lang("or")).' <a href="'.$_SERVER["DIR"].'/register" target="account">'.mb_strtolower(lang("register now")).'</a>.</center></div>';
                }else{
                    $fout .= '
                <form method="POST">
                    <input type="hidden" name="reply" value="'.$c["id"].'" />
                    <textarea name="comment" cols=50 class="comment_textarea"></textarea>
                    <br/><br/>
                    <input type="submit" class="btn"  value="'.lang("Add comment").'" />
                </form>
                </div>';
                }
            }
        }
        $fout1 .= '<table align=center class="comment_table">';
        $query = 'SELECT * FROM `nodes_comment` WHERE `reply` = "'.$c["id"].'"';
        $rf = engine::mysql($query);
        $flag = 0;
        while($df = mysql_fetch_array($rf)){
            $flag = 1;
            $fout1 .= print_comment($df["id"], 1);
        }
        $fout1 .= '</table>';
        if($flag) $fout .= $fout1;
        $fout .= '
            </div>
        </td></tr>';
    }
    return $fout;
}

function print_comments($url){
    $url = trim(str_replace('"', "'", urldecode($url)));
    if(!empty($_POST["comment"])){
        $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
        $text = str_replace("\n", "<br/>", $text);
        $query = 'SELECT * FROM `nodes_comment` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$url.'" AND `user_id` = "'.$_SESSION["user"]["id"].'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(empty($data) && intval($_SESSION["user"]["id"]>0)){
            $query = 'INSERT INTO `nodes_comment` (`url`, `reply`, `user_id`, `text`, `date`) '
            . 'VALUES("'.$url.'", "'.intval($_POST["reply"]).'", "'.$_SESSION["user"]["id"].'", "'.$text.'", "'.date("U").'")';
            engine::mysql($query); 
            $query = 'INSERT INTO `nodes_log`(action, user_id, ip, date, details) '
            . 'VALUES("6", "'.$_SESSION["user"]["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$text.'")';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "send_comments_email"'; 
            $r_conf = engine::mysql($query);
            $d_conf = mysql_fetch_array($r_conf);
            if(intval($d_conf["value"])){
                require_once("engine/core/send_email.php");
                send_email::new_comment($_SESSION["user"]["id"], $url);     
            }
            $fout .= '
            <script>alert("'.lang("Comment submited!").'");</script>
            '; 
        }
    }
    $flag = 0;
    $fout1 .= '<table align=center class="w400">';
    $query = 'SELECT * FROM `nodes_comment` WHERE `url` LIKE "'.$url.'" AND `reply` = 0';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if(intval($data["id"])>0){
            $fout1 .= print_comment($data["id"]);
            $flag = 1;
        }
    }
    $fout1 .= '</table><br/>';
    if(!empty($_SESSION["user"])){
        if(!$flag){
            $fout .= '<br/>'.lang("There is no comments").'<br/><br/>';
        }else{
            $fout .= $fout1;
        }
        $fout .= '<br/>
            <form method="POST">
                <div id="new_comment" class="hidden">
                    <strong>'.lang("Add new comment").'</strong><br/><br/>
                    <textarea name="comment" cols=50 class="comment_textarea"></textarea><br/><br/>
                    <center><input type="submit" class="btn w280" value="'.lang("Submit comment").'" /></center>
                </div>
                <input type="button" class="btn w280" value="'.lang("Add comment").'" onClick=\'document.getElementById("new_comment").style.display="block";this.style.display="none";\' />
            </form>
            ';
    }else{
        $fout .= '<center>'.lang("To post a comment, please").' <a target="_parent" onClick="event.preventDefault(); login();">'.mb_strtolower(lang("sign in")).'</a> '.mb_strtolower(lang("or")).' <a href="'.$_SERVER["DIR"].'/register" target="account">'.mb_strtolower(lang("register now")).'</a>.</center>';
    }
    return $fout;
}