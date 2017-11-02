<?php
/**
* Backend profile pages file.
* @path /engine/site/profile.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/
if(empty($_GET[1])||!empty($_GET[2])){
    $this->content = engine::error();
    return; 
}else if($_SESSION["user"]["id"] == intval($_GET[1])){
    $this->content = '<script>window.location = "'.$_SERVER["DIR"].'/account";</script>';
    return;
}
$query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.intval($_GET[1]).'"';
$res = engine::mysql($query);
$user = mysql_fetch_array($res);
if(empty($user)){
    $this->content = engine::error();
    return;    
}else{
    $this->title = $user["name"].' - '.$this->title;
    $this->content = engine::print_header($this, intval($_GET[1]));
    if($this->configs["free_message"]){
        if(empty($_SESSION["user"]["id"])){
            $button = '<a target="_parent" onClick="event.preventDefault(); login();" href="'.$_SERVER["DIR"].'/login"><input type="button" class="btn w280" value="'.lang("Login to Send message").'" /><br/><br/>';
        }else{
            $button = '<a href="'.$_SERVER["DIR"].'/account/inbox/'.$user["id"].'"><input type="button" class="btn w280" value="'.lang("Send message").'" /><br/><br/>';
        }
    }
    $rating = number_format(($user["rating"]/$user["votes"]),2);
    $this->content .= '
        <div class="profile_star m10 fl">
            <div class="profile_stars">
                <div class="baseimage" style="margin-top: -'.(160-round($rating)*32).'px;" ></div>
            </div>
            <div class="votes">
               '.$rating.' / 5.00 ('.$user["votes"].' '.lang("votes").')
            </div>
        </div>
        <div class="share_block fr m15"><div>'.lang("Share friends").'</div><br/>'.
            engine::print_share($_SERVER["PUBLIC_URL"].'/product/'.$data["id"]).'</div>
        <div class="clear"></div>
        <div class="document">
            <div class="clear_block">
                <p>'.lang("Member of").' <b>'.$this->configs["name"].'</b> '.lang("community").'.</p>
                <br/><br/>'.$button.'<br/>
            </div>
        </div>';
}