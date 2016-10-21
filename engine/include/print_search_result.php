<?php

// TODO - Your code here
//----------------------------

function print_search_result($caption, $html, $url){
    $html = preg_replace('#<[^>]+>#', " ", $html);
    $html = trim(preg_replace('#[\s]+#', ' ', $html));
    $pos = mb_strpos($html, $_GET[1]);
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
            . '<a href="'.$url.'" target="_blank" class="fs16">'.$caption.'</a>'
            . '<p>'.$html.'</p>'
            . '</div><br/>';
        return $fout;
    }
}