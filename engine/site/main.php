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

if(!empty($_GET[0])){
    $this->content = engine::error();
    return; 
}

$this->title .= '';
$this->content = '
<section id="topSection">
    <h1><strong>'.$this->configs["name"].'</strong></h1><br/>
    <p>'.$this->configs["description"].'</p><br/>
</section>
<div id="mainSection">
    <img src="'.$_SERVER["DIR"].'/img/cms/nodes_studio.png" class="nodes_image" />
</div>';