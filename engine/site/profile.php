<?php
/**
* Backend profile pages file.
* @path /engine/site/profile.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
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
    $this->content .= '<div class="document">'
        . '<div class="clear_block">'
        . '<p>'.lang("Member of").' <b>'.$this->configs["name"].'</b> '.lang("community").'.</p>'
        . '<br/><br/>'.$button
        . '</div>'
        . '</div>';
}