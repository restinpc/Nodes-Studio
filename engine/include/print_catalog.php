<?php

// TODO - Your code here
//----------------------------

function print_catalog($data){
    if(!empty($data["img"])){
        $fout = '<div id="article">
            <div class="article_image">
                <img src="'.$_SERVER["DIR"].'/img/data/big/'.$data["img"].'" class="img" />
            </div>
            <div class="text">
                '.$data["text"].'
            </div>
        </div>';
    }else{
        $fout = '<div id="article">
            <div class="text">
                '.$data["text"].'
            </div>
        </div>';
    }return $fout;
}