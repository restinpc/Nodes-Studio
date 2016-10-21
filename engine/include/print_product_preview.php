<?php

// TODO - Your code here
//----------------------------

function print_product_preview($data, $right=0){
    $images = explode(";", $data["img"]);
    $fout = '<div class="product_preview ';
    if(!empty($right)){
        $fout .= 'right';
    }
    $fout .= '">
    <a href="'.$_SERVER["DIR"].'/product/'.$data["id"].'">
        <div class="product_preview_image">
            <img src="'.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].'" width=100% />
        </div>
        <div class="title">
            <b>'.$data["title"].'</b>
        </div>
    </a>
    <div class="details">';
        if($data["status"]){
            $fout .= '
            <div class="buy_now"';
            if($data["user_id"]==$_SESSION["user"]["id"]){
                $fout .= ' onClick=\'alert("'.lang("Unable to purchase your own product").'")\' ';  
            }else{
                $fout .= ' onClick=\'buy_now('.$data["id"].', "'.lang("Checkout order?").'");\' ';
            }
            $fout .= '>
                <div class="price">$'.intval($data["price"]).'</div>';
            $fout .= '
                <center class="grabit">'.lang("Buy Now").'</center>        </div>
                ';
        }
        $fout .= '
        <div class="clear"></div>
    </div>
<br/> 
</div>';
    return $fout;
}