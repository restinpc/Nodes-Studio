<?php

// TODO - Your code here
//----------------------------

require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
function print_comment($id, $noreply = 0){
    $query = 'SELECT * FROM `nodes_comments` WHERE `id` = "'.intval($id).'"';
    $rc = engine::mysql($query);
    $c = mysql_fetch_array($rc);
    $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$c["user_id"].'"';
    $rd = engine::mysql($query);
    $d = mysql_fetch_array($rd);
    if(!empty($c)){
        $fout .= '<tr><td align=left valign=top style="padding: 10px; padding-bottom: 0px; padding-left: 0px;">
                <div style="float:left;">
                    <img src="'.$d["photo"].'" width=50  style="border: #d0d0d0 4px solid; border-radius: 4px;" />
                </div>
                <div style="border: 0px solid; margin-left: 60px;">
                    <strong>'.$d["name"].'</strong> <small>'.date("d.m.Y H:i", $c["date"]).'</small>
                    <div style="padding-top: 5px; padding-bottom: 5px;">'.$c["text"].'</div>';
        
        if($_SESSION["user"]["id"]=="1" && !$noreply){
            $fout .= '
                <a onClick=\'document.getElementById("comment_'.$c["id"].'").style.display ="block";'
                    .'this.style.display = "none";\'>'.lang("Reply").'</a><br/>
                <div id="comment_'.$c["id"].'" style="display:none; float:left;">
                ';
            if(empty($_SESSION["user"])){
                $fout .= '<center>'.lang("To post a comment, please").' <a href="#" onClick="show_login_form();">'.mb_strtolower(lang("auth")).'</a> '.mb_strtolower(lang("or")).' <a href="/register" target="account">'.mb_strtolower(lang("register now")).'</a>.</center></div>';
            }else{
                $fout .= '
            <form method="POST">
                <input type="hidden" name="reply" value="'.$c["id"].'" />
                <textarea name="comment" cols=50 style="height: 80px; width: 100%; max-width: 500px;"></textarea>
                <br/><br/>
                <input type="submit" class="btn"  value="'.lang("Add comment").'" />
            </form>
            </div>';
            }
        }
        $fout1 .= '<table align=center style="border: 0px solid; width: 100%; max-width: 500px; margin-top: 5px;">';
        $query = 'SELECT * FROM `nodes_comments` WHERE `reply` = "'.$c["id"].'"';
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
    }return $fout;
}function print_comments($url){
    $url = trim(str_replace('"', "'", urldecode($url)));
    if(!empty($_POST["comment"])){
        $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
        $text = str_replace("\n", "<br/>", $text);
        $query = 'SELECT * FROM `nodes_comments` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$url.'" AND `user_id` = "'.$_SESSION["user"]["id"].'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        if(empty($data) && intval($_SESSION["user"]["id"]>0)){
            $query = 'INSERT INTO `nodes_comments` (`url`, `reply`, `user_id`, `text`, `date`) '
            . 'VALUES("'.$url.'", "'.intval($_POST["reply"]).'", "'.$_SESSION["user"]["id"].'", "'.$text.'", "'.date("U").'")';
            engine::mysql($query); 
            $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
            . 'VALUES("6", "'.$_SESSION["user"]["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$text.'")';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "send_comments_email"'; 
            $r_conf = engine::mysql($query);
            $d_conf = mysql_fetch_array($r_conf);
            if(intval($d_conf["value"])){
                $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"'; 
                $r_email = engine::mysql($query);
                $d_email = mysql_fetch_array($r_email);
                $message = 'User '.$_SESSION["user"]["name"].' add new comment!<br/>'
                        . '<a href="'.$_SERVER["SCRIPT_URI"].'">'.$_SERVER["SCRIPT_URI"].'</a><br/>'
                        . '<br/>Comment:<br/>-----------------------------<br/>'.$text;
                engine::send_mail($d_email["value"], "no-reply@".$_SERVER["HTTP_HOST"], 
                        "New comment at ".$_SERVER["HTTP_HOST"], $message);
            }
            $fout .= '
            <script>alert("'.lang("Comment submited!").'");</script>
            '; 
        }
    }
    $flag = 0;
    $fout1 .= '<table align=center style="width: 100%; max-width: 500px; font-size: 14px;">';
    $query = 'SELECT * FROM `nodes_comments` WHERE `url` LIKE "'.$url.'" AND `reply` = 0';
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
            $fout .= lang("There is no comments").'<br/><br/>';
        }else{
            $fout .= $fout1;
        }
        $fout .= '
            <form method="POST">
                <div id="new_comment" style="display:none;">
                    <strong>'.lang("Add new comment").'</strong><br/><br/>
                    <textarea name="comment" cols=50 style="height: 80px; width: 100%; max-width: 500px;"></textarea><br/><br/>
                    <center><input type="submit" class="btn"  value="'.lang("Submit comment").'" style="width: 280px;" /></center>
                </div>
                <input type="button" class="btn"  value="'.lang("Add comment").'" style="width: 280px;" onClick=\'document.getElementById("new_comment").style.display="block";this.style.display="none";\' />
            </form>
            ';
    }else{
        $fout .= '<center>'.lang("To post a comment, please").' <a href="#" onClick="show_login_form();">'.mb_strtolower(lang("auth")).'</a> '.mb_strtolower(lang("or")).' <a href="'.$_SERVER["DIR"].'/register" target="account">'.mb_strtolower(lang("register now")).'</a>.</center>';
    }return $fout;
}