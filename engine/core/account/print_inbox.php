<?php
/**
* Print account inbox page.
* @path /engine/core/account/print_inbox.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param object $site Site class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_inbox($site); </code>
*/
function print_inbox($site, $target_id){
    $fout = '<div class="document980">';
    if(!empty($_GET[2]) || $target_id!=0){
        if(!$target_id) $target_id = $_GET[2];
        if(!$site->configs["free_message"]){
            $query = 'SELECT COUNT(*) FROM `nodes_inbox` WHERE (`to` = "'.intval($target_id).'" AND `from` = "'.intval($_SESSION["user"]["id"]).'") OR '
                    . '(`from` = "'.intval($target_id).'" AND `to` = "'.intval($_SESSION["user"]["id"]).'")';
            $r = engine::mysql($query);
            $d = mysqli_fetch_array($r);
            if(!intval($d[0]) && $_SESSION["user"]["id"]!="1" && $target_id!="1") return engine::error(401);
        }
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$target_id.'"';
        $res = engine::mysql($query);
        $target= mysqli_fetch_array($res);
        if(empty($target)) return engine::error();
        if($target["ban"]==1) return '<div class="clear_block">'.lang("User banned").'</div>';
        $site->onload .= '
            refresh_chat("'.$target_id.'");
            setInterval(refresh_chat, 10000, "'.$target_id.'");
            ';
        $fout .= '<div id="nodes_chat"></div>';
        $query = 'UPDATE `nodes_inbox` SET `readed` = "'.date("U").'" WHERE `to` = "'.$_SESSION["user"]["id"].'" AND `readed` = 0';
        engine::mysql($query);
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = '.$_SESSION["user"]["id"];
        $res = engine::mysql($query);
        $u = mysqli_fetch_array($res);
        if($target["online"] > date("U")-300){
            $online = '<br/><font class="chat_font">'.lang("online").'</font>';
        }else{
            $online = '<br/><font class="chat_font">'.lang("offline").'</font>'; 
        }
        $fout .= '<div class="chat_user_left">
                <img src="'.$_SERVER["DIR"].'/img/pic/'.$u["photo"].'" width=50 /><br/>
                <div class="chat_user_name">
                    <font class="chat_user_name_font">'.$u["name"].'</font>
                    <font class="chat_font">'.lang("online").'</font>
                </div>
            </div>
            <div vr-control id="div-user-'.$target["id"].'" class="chat_user_right" onClick=\'document.getElementById("target").click();\'>
                <img src="'.$_SERVER["DIR"].'/img/pic/'.$target["photo"].'" width=50 /><br/>
                <div class="chat_user_name">
                    <a vr-control href="'.$_SERVER["DIR"].'/profile/'.$target["id"].'" id="target" class="chat_user_name_font">'.$target["name"].'</a>
                    '.$online.'
                </div>
            </div>
            <div class="chat_center">
                <textarea vr-control name="text" id="nodes_message_text" class="input" placeHolder="'.lang("Your message here").'"
                onkeypress=\'if(event.keyCode==13&&!event.shiftKey){ event.preventDefault(); post_message("'.$target_id.'"); } \'
                ></textarea><br/>
                <input vr-control id="send-message" type="button" onClick=\'post_message("'.$target_id.'");\' class="btn" value="'.lang("Send message").'"  />
            </div>
            <div class="clear"></div><br/>';
    }else{
        $query = 'SELECT * FROM `nodes_user` WHERE `id` <> "'.$_SESSION["user"]["id"].'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(empty($data)){
            $fout .= '<div class="clear_block">'.lang("There is no users, you can send a message").'</div>';
        }else{
            $query = 'SELECT * FROM `nodes_user` WHERE `id` <> "'.$_SESSION["user"]["id"].'" AND `ban` <= 0';
            $res = engine::mysql($query);
            $count = 0;
            while($u = mysqli_fetch_array($res)){
                if(!$site->configs["free_message"]){               
                    $query = 'SELECT COUNT(*) FROM `nodes_inbox` WHERE (`to` = "'.intval($u["id"]).'" AND `from` = "'.intval($_SESSION["user"]["id"]).'") OR '
                            . '(`from` = "'.intval($u["id"]).'" AND `to` = "'.intval($_SESSION["user"]["id"]).'")';
                    $r = engine::mysql($query);
                    $d = mysqli_fetch_array($r);
                    if(!intval($d[0]) && $_SESSION["user"]["id"]!="1" && intval($u["id"]) != 1) continue;
                }
                $count++;
                $query = 'SELECT COUNT(*) FROM `nodes_inbox` WHERE `to` = "'.intval($_SESSION["user"]["id"]).'" AND `readed` = 0 AND `from` = "'.$u["id"].'"';
                $r = engine::mysql($query);
                $d = mysqli_fetch_array($r);
                if($d[0] > 0){ 
                    if($d[0] == 1) $new = '<span class="new_message">'.lang("New message").'</span><br/>';
                    else $new = '<span class="new_message">'.$d[0].' '.lang("new messages").'</span><br/>';
                }else $new = '';
                if($u["online"] > date("U")-300){
                    $online = '<font class="chat_font">'.lang("online").'</font>';
                }else{
                    $online = '<font class="chat_font">'.lang("offline").'</font>'; 
                }
                $fout .= '<a vr-control id="link-user-'.$u["id"].'" href="'.$_SERVER["DIR"].'/account/inbox/'.$u["id"].'">'
                        . '<div class="user_block">'
                        . '<div class="user_block_image">
                                <img src="'.$_SERVER["DIR"].'/img/pic/'.$u["photo"].'" width=50 />
                            </div>
                            <div class="user_block_name">'.$u["name"].'</div>'.$new.$online
                        . '</div></a>';
            }
            if($count == 1){
                die('<script>window.location = "'.$_SERVER["DIR"].'/account/inbox/1";</script>');
            }
            $fout .=  '<div class="clear"></div>'
                    . '<br/>';  
        }$fout .= '<br/>';
        $site->onload .= ' setTimeout(function(){window.location.reload();}, 60000); ';
    }
    $fout .= '</div>';
    return $fout;
}