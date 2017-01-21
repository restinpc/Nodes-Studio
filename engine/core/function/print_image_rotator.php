<?php
/**
* Prints an image rotator block.
* @path /engine/core/function/print_cart.php
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
* @param array $images Array with images for rotation.
* @return string Returns content of block on success, or die with error.
* @usage <code> 
*   $images = array("/img/1.jpg", "/img/2.jpg"); 
*   engine::print_image_rotator($site, $images); 
* </code>
*/
function print_image_rotator($site, $images){
    $images = array_filter($images, function($element) {
     return !empty($element);
    });
    $site->onload .= ' show_rotator(); ';
    $fout = '
    <div id="jssor_1" style="position: relative; margin: 0 auto; left: 0px; width: 600px; height: 500px; overflow: hidden; visibility: hidden;">
        <div data-u="slides" id="slider_block" style="cursor: default; position: relative; width: 600px; top: 0px; left: 0px; height: 500px; overflow: hidden;">
            <div> <img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[0].'"  /> </div>';
    for($i = 1; $i<count($images); $i++){
        if(!empty($images[$i])){
            if($i==count($images)-1){
                $fout .= '<div style="display:none;"> <img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'"  /> </div>';
            }else{
                $fout .= '<div style="display:none;"> <img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'" /> </div>';
            }
        }
    }
    $fout .= '                  
        </div>
        <div data-u="navigator" class="jssorb13" style="bottom:16px;right:16px;" data-autocenter="1">
            <div data-u="prototype" style="width:21px;height:21px;"></div>
        </div>
    </div>';
    return $fout;
}