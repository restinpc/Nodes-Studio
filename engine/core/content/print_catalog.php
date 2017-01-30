<?php
/**
* Prints content catalog page.
* @path /engine/core/content/print_catalog.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
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
* @param int $data @mysql[nodes_catalog].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_catalog($site, $data); </code>
*/
function print_catalog($site, $data){
    $fout = '<div class="article" itemscope  itemtype="http://schema.org/Article">
        <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
            <img class="hidden" itemprop="logo" src="'.$site->img.'" />
            <meta itemprop="name" content="'.$site->configs["name"].'" />
        </div>
        <meta itemprop="datePublished" content="2017-01-27" />
        <meta itemprop="dateModified" content="2017-01-27" />
        <meta itemprop="author" content="'.$site->configs["email"].'" />
          ';
    if(!empty($data["img"])){
        $fout .= '<div class="article_image">
            <img itemprop="image" src="'.$_SERVER["PUBLIC_URL"].'/img/data/big/'.$data["img"].'" class="img" />
        </div>';
    }else{
        $fout .= '<img class="hidden" itemprop="image" src="'.$site->img.'" />';    
    }
    $fout .= '<h1 itemprop="headline" '.($data["visible"]?'class="hidden"':'').'>'.$data["caption"].'</h1><br/>
        <div itemprop="articleBody" class="text">'.$data["text"].'</div>
    </div>
    <div class="clear"></div>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'"><input type="button" class="btn w280" value="'.lang("Add article").'" /></a>'
            . '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'&act=edit"><input type="button" class="btn w280" value="'.lang("Edit directory").'" /></a><br/><br/>';
    }
    return $fout;
}