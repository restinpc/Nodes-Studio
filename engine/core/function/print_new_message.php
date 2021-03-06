<?php
/**
* Prints new message block.
* @path /engine/core/function/print_new_message.php
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
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_new_message($site); </code>
*/
function print_new_message($site){
    $fout = '';
    $query = 'SELECT * FROM `nodes_inbox` WHERE `to` = "'.intval($_SESSION["user"]["id"]).'" '
            . 'AND `readed` = 0 AND `inform` = 0 ORDER BY `date` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data)){
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["from"].'"';
        $res = engine::mysql($query);
        $user = mysqli_fetch_array($res);
        if(strlen($data["text"])>100) $data["text"] = mb_substr($data["text"], 0, 100)."..";
        if($user["online"] > date("U")-300) $online = '<span class="fs11">'.lang("online").'</span>';
        $fout .= '
            <div id="nodes_message">
                <div vr-control id="new_msg_img" class="new_msg_img" onClick=\'window.location="'.$_SERVER["DIR"].'/account/inbox/'.$data["from"].'";\'>
                    <img src="'.$_SERVER["DIR"].'/img/pic/'.$user["photo"].'" width=50 /><br/>'.$online.'
                </div>
                <div class="new_msg_close">
                    <div vr-control id="new_msg_close" class="close_image" title="'.lang("Close window").'" onClick=\'document.getElementById("nodes_message").style.display="none";\'> </div>
                </div>
                <div vr-control id="new_msg_name"  class="pointer" onClick=\'window.location="'.$_SERVER["DIR"].'/account/inbox/'.$data["from"].'";\'>
                    <div class="new_msg_name">'.$user["name"].'</div>'
                    .$data["text"].'
                </div>
            </div>
            <script>jQuery(\'body\').append(\'<audio id="sound" autoplay preload><source src="'.$_SERVER["DIR"].'/res/sounds/notify.wav" type="audio/wav"></audio>\');</script>';
        $query = 'UPDATE `nodes_inbox` SET `inform` = "1" WHERE `to` = "'.intval($_SESSION["user"]["id"]).'" AND `from` = "'.$data["from"].'"';
        engine::mysql($query);
    }
    return $fout;
}

