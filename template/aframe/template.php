<?php
/**
* Template file.
* @path /template/empty/template.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title
* @var $this->content - Page HTML data
* @var $this->keywords - Array meta keywords
* @var $this->description - Page meta description
* @var $this->img - Page meta image
* @var $this->onload - Page executable JavaScript code
* @var $this->configs - Array MySQL configs
*/
if(!isset($_POST["jQuery"])){
//  Header Start
$header = '
<div id="content">
<!-- content -->';
//  Header End  
//------------------------------------------------------------------------------
//  Footer Start
$footer = '
<!-- /content -->
</div>';
//  Footer End
}
$this->content = $header.$this->content.$footer;