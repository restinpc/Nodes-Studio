<?php
/**
* Backend login page file.
* @path /engine/site/login.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
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
if(!empty($_GET[1])){
    $this->content = engine::error();
    return; 
}
$this->title = lang("Login").' - '.$this->title;
$this->content = '<iframe frameborder=0 width=200 height=260 class="login_frame" src="'.$_SERVER["DIR"].'/account.php"></iframe>';