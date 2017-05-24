<?php
/**
* Backend main page file.
* @path /engine/site/main.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
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
if(!empty($_GET[0])){
    $this->content = engine::error();
    return; 
}
$this->content = '<div class="lh2">
    <h1><strong>'.$this->configs["name"].'</strong></h1>
    <p class="fs18">'.$this->configs["description"].'</p><br/>
</div>
<div class="document980">';
$query = 'SELECT * FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC LIMIT 0, 6';
$res = engine::mysql($query);
$flag = 0;
$content = '<div class="preview_blocks">';
while($data = mysql_fetch_array($res)){
    $flag = 1;
    $content .= engine::print_preview($this, $data);
}
$content .= '</div>'
        . '<div class="clear h20"></div>'
        . '<a href="'.$_SERVER["DIR"].'/content"><input type="button" class="btn w280" value="'.lang("Show more").' '.mb_strtolower(lang("Articles")).'&nbsp; &raquo;" /></a> '
        . '<div class="clear h20"></div>';
if($flag){
    $this->content .= $content;
}
$query = 'SELECT * FROM `nodes_product` ORDER BY RAND() DESC LIMIT 0, 6';
$res = engine::mysql($query);
$products = '<div class="preview_blocks">';
while($data = mysql_fetch_array($res)){
    if($flag==1){
        $products = '<br/>'.$products;
    }
    $flag = 2;
    $products .= engine::print_product_preview($this, $data);
}
$products .= '</div>'
        . '<div class="clear h20"> </div>'
        . '<a href="'.$_SERVER["DIR"].'/product"><input type="button" class="btn w280" value="'.lang("Show more").' '.  mb_strtolower(lang("Products")).'&nbsp; &raquo;" /></a><br/><br/> '
        . '<div class="clear h20"> </div>';
if($flag==2){
    $this->content .= $products;
}else if(!$flag){
    $this->content .= '<img src="'.$_SERVER["DIR"].'/img/cms/nodes_studio.png" class="nodes_image" /><br/><br/>';
}           
$this->content .= '
</div>';