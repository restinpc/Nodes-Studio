<?php

// TODO - Your code here
//----------------------------

function print_preview($data){
    $fout = '
        <div class="content_block">
        ';
    if(!empty($data["img"])){
        $fout .= '
        <div class="content_img" style="background: url(\''.$_SERVER["DIR"].'/img/data/thumb/'.$data["img"].'\') no-repeat;background-size: cover;"
            onClick=\'document.getElementById("'.$data["url"].'").click();\'>
            &nbsp;
        </div><br/>';
    }else{
        $fout .= '
        <div class="content_img" style="background: url(\''.$_SERVER["DIR"].'/img/no-image.jpg\') no-repeat;background-size: cover;"
            onClick=\'document.getElementById("'.$data["url"].'").click();\'>
            &nbsp;
        </div><br/>';
    }
    $fout .= '
            <a id="'.$data["url"].'" href="'.$_SERVER["DIR"].'/content/'.$data["url"].'"><h2>'.substr(strip_tags($data["caption"]),0,100).'</h2></a>
            <p class="content_block_text">
            '.substr(strip_tags($data["text"]), 0 ,100).'...
            </p>
        </div>';
    return $fout;
}