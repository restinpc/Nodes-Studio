<?php
/**
* Print cardboard scene preview block.
* @path /engine/core/function/print_scene_preview.php
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
* @param string $caption Scene caption.
* @param string $url Scene URL.
* @param string $img Scene preview image.
* @param string $text Scene description.
* @return string Returns content of block.
* @usage <code> engine::print_scene_preview($site, "A-Frame", "/aframe", "/img/vr/aframe.jpg", "Cardboard version of website"); </code>
*/
function print_scene_preview($site, $caption, $url, $img, $text){
    $fout = '
        <div class="content_block">
        <div vr-control id="content_'.md5($url).'" class="content_img" style="background-image: url(\''.$_SERVER["DIR"].$img.'\');"
            onClick=\'document.getElementById("'.md5($url).'").click();\'>
            &nbsp;
        </div>
            <a vr-control id="'.md5($url).'" target="_top" href="'.$_SERVER["DIR"].$url.'"><h3>'.$caption.'</h3></a>
            <p class="content_block_text">
            '.$text.'
            </p>
        </div>';
    return $fout;
}