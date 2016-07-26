<?php

// TODO - Your code here
//----------------------------

function print_search_result($caption, $html, $url){
    $html = preg_replace('#<[^>]+>#', " ", $html);
    $html = trim(preg_replace('#[\s]+#', ' ', $html));
    $pos = strpos($html, $_GET[1]);
    if($pos){
        if(strlen($html)>180){
            if($pos<90){
                $start = '';
                $from = 0;
            }else{
                $start = '..';
                $from = $pos-90;
            }$html = $start.substr($html, $from, 180).'..';
        }$html = str_replace($_GET[1], "<b>".$_GET[1]."</b>", $html);
        $fout = '<div style="margin: 0px auto; text-align:left;">'
            . '<a href="'.$url.'" target="_blank">'.$caption.'</a>'
            . '<p style="font-size: 12px;">'.$html.'</p>'
            . '</div><br/>';
        return $fout;
    }
}