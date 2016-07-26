<?php

// TODO - Your code here
//----------------------------

require_once("engine/include/print_comments.php");
function print_content($data){
    $fout = '
<div id="article">';
    if(!empty($data["img"])){
        $fout .= '
    <div style="float:left; margin-right: 10px; margin-left: 10px;">
        <img src="'.$_SERVER["DIR"].'/img/data/big/'.$data["img"].'" class="img" />
    </div>';
    }
    $fout .= '
    <div class="date">'.date("d.m.Y", $data["date"]).' at '.date("H:i", $data["date"]).'</div>
    <div style="clear:right;"></div>
    <div class="text">
        '.$data["text"].'
    </div>
</div>
<div style="clear:both;height: 10px;"></div>
<center>'.
print_comments($_SERVER["REQUEST_URI"])
.'</center><br/>';
    return $fout;
}