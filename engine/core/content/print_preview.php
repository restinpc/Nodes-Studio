<?php
/**
* Print content perview block.
* @path /engine/core/content/print_preview.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
* @param array $data @mysql[nodes_content].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_preview($site, $data); </code>
*/
function print_preview($site, $data){
    $text = strip_tags($data["text"]);
    if(strlen($text)>70) $text = mb_substr($text, 0 ,70).'..';
    $fout = '
        <div class="content_block">
        ';
    if(!empty($data["img"])){
        $fout .= '
        <div vr-control id="content_'.md5($data["url"]).'" class="content_img" style="background-image: url(\''.$_SERVER["DIR"].'/img/data/thumb/'.$data["img"].'\');"
            onClick=\'document.getElementById("'.$data["url"].'").click();\'>
            &nbsp;
        </div>';
    }else{
        $fout .= '
        <div vr-control id="content_'.md5($data["url"]).'" class="content_img" style="background-image: url(\''.$_SERVER["DIR"].'/img/no-image.jpg\');"
            onClick=\'document.getElementById("'.$data["url"].'").click();\'>
            &nbsp;
        </div>';
    }
    $fout .= '
            <a vr-control id="'.$data["url"].'" href="'.$_SERVER["DIR"].'/content/'.$data["url"].'"><h3>'.mb_substr(strip_tags($data["caption"]),0,100).'</h3></a>
            <p class="content_block_text">
            '.$text.'
            </p>
        </div>';
    return $fout;
}