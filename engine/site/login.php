<?php
/*
$this->title - Page title
$this->content - Page HTML data
$this->menu - Page HTML navigation
$this->keywords - Page meta keywords
$this->description - Page meta description
$this->img - Page meta image
$this->js - Page JavaScript code
$this->activejs - Page executable JavaScript code
$this->css - Page CSS data
$this->configs - Array MySQL configs
*/

// TODO - Your code here
//----------------------------

if(!empty($_GET[1])){
    $this->content = engine::error();
    return; 
}

$this->title = lang("Login").' - '.$this->title;
$this->content = '<iframe frameborder=0 width=200 height=260 src="'.$_SERVER["DIR"].'/account.php" style="margin-top: 10px; border:0px;"></iframe><br/><br/>';