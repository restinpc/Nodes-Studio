<?php

// TODO - Your code here
//----------------------------

require_once("engine/include/print_comments.php");
function print_content($data){
    $fout = '
<div id="article">';
    if(!empty($data["img"])){
        $fout .= '
    <div class="article_image>
        <img src="'.$_SERVER["DIR"].'/img/data/big/'.$data["img"].'" class="img" />
    </div>';
    }
    $fout .= '
    <div class="date">'.date("d.m.Y", $data["date"]).' at '.date("H:i", $data["date"]).'</div>
    <div class="cr"></div>
    <div class="text">
        '.$data["text"].'
    </div>
</div>
<div class="clear"><br/></div>
<center>'.
print_comments($_SERVER["REQUEST_URI"])
.'</center><br/>';
    return $fout;
}