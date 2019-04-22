<?php
/**
* Prints search result block.
* @path /engine/core/function/print_search_result.php
* 
* @name    Nodes Studio    @version 2.0.1.9
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
* @param string $caption Result block caption.
* @param string $html Result block HTML.
* @param string $url Result block URL.
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_search_result($site, "Result 1", "Value 1", "/"); </code>
*/
function print_search_result($site, $caption, $html, $url){
    $request = engine::escape_string(urldecode($_GET[1]));
    $html = preg_replace('#<[^>]+>#', " ", $html);
    $html = trim(preg_replace('#[\s]+#', ' ', $html));
    $pos = strpos($html, $request);
    if($pos){
        if(strlen($html)>180){
            if($pos<90){
                $start = '';
                $from = 0;
            }else{
                $start = '..';
                $from = $pos-90;
            }$html = $start.mb_substr($html, $from, 180).'..';
        }$html = str_replace($_GET[1], "<b>".$_GET[1]."</b>", $html);
        $fout = '<div class="search_result">'
            . '<a vr-control id="link-'.$url.'" href="'.$url.'" target="_blank" class="fs16">'.$caption.'</a>'
            . '<p>'.$html.'</p>'
            . '</div><br/>';
        return $fout;
    }
}