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
    $fout = '<div class="article">';
    if(!empty($data["img"])){
        $fout = '<div class="article">
            <div class="article_image">
                <img src="'.$_SERVER["DIR"].'/img/data/big/'.$data["img"].'" class="img" />
            </div>';
        if(!$data["visible"]){
            $fout .= '<h1>'.$data["caption"].'</h1><br/>';
        }
        $fout .= '<div class="text">'.$data["text"].'</div>
        </div>';
    }else{
        if(!$data["visible"]){
            $fout .= '<h1>'.$data["caption"].'</h1><br/>';
        }
        $fout .= '<div class="text">'.$data["text"].'</div>
        </div>';
    }
    $fout .= '<div class="clear"></div>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'"><input type="button" class="btn w280" value="'.lang("Add article").'" /></a>'
            . '<br/><br/><a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["id"].'&act=edit"><input type="button" class="btn w280" value="'.lang("Edit directory").'" /></a><br/><br/>';
    }
    return $fout;
}