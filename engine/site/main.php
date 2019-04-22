<?php
/**
* Backend main page file.
* @path /engine/site/main.php
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
 
$this->content = '<div class="lh2 p10 pt20">
    <h1><strong>'.$this->configs["name"].'</strong></h1>
    <p class="fs18">'.$this->configs["description"].'</p>
    <form method="POST" id="template_selector">
        <select class="input w280 mt15" name="template" onChange=\'$id("template_selector").submit();\'>
            <option value="default" '.($_SESSION["template"]=="default"?'selected':'').'>Default Frontend Template</option>
            <option value="android" '.($_SESSION["template"]=="android"?'selected':'').'>Android Frontend Template</option>
            <option value="bootstrap" '.($_SESSION["template"]=="bootstrap"?'selected':'').'>Bootstrap Frontend Template</option>
        </select>
    </form>
</div>
<div class="document980">';

$query = 'SELECT * FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'" ORDER BY `id` DESC LIMIT 0, 6';
$res = engine::mysql($query);
$content = '
    <div class="tal p10"><h2 class="fs21">'.lang("Latest Articles").'</h2></div>
    <div class="preview_blocks">';
while($data = mysqli_fetch_array($res)){
    $flag = 2;
    $content .= engine::print_preview($this, $data);
}
$content .= '</div>'
        . '<div class="clear h20"></div>'
        . '<a vr-control id="show-more-article" href="'.$_SERVER["DIR"].'/content"><input type="button" class="btn w280" value="'.lang("Show more").' '.mb_strtolower(lang("Articles")).'&nbsp; &raquo;" /></a> '
        . '<div class="clear h20"></div>';
if($flag == 2){
    $this->content .= $content;
}
$query = 'SELECT * FROM `nodes_product` ORDER BY `id` DESC LIMIT 0, 6';
$res = engine::mysql($query);
$products = '
    <div class="tal p10"><h2 class="fs21">'.lang("Popular goods").'</h2></div>
    <div class="preview_blocks">';
while($data = mysqli_fetch_array($res)){
    $flag = 3;
    $products .= engine::print_product_preview($this, $data);
}
$products .= '</div>'
        . '<div class="clear h20"> </div>'
        . '<a vr-control id="show-more-products" href="'.$_SERVER["DIR"].'/product"><input type="button" class="btn w280" value="'.lang("Show more").' '.  mb_strtolower(lang("Products")).'&nbsp; &raquo;" /></a><br/><br/> '
        . '<div class="clear h20"> </div>';
if($flag==3){
    $this->content .= $products;
}else if(!$flag){
    $this->content .= '<img src="'.$_SERVER["DIR"].'/img/cms/nodes_studio.png" class="nodes_image" /><br/><br/>';
}           
$this->content .= '
</div>';