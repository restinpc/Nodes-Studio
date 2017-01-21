<?php
/**
* Print product page.
* @path /engine/core/product/print_product.php
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
* @param array $data @mysql[nodes_product].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_product($site, $data); </code>
*/
function print_product($site, $data){
    $images = explode(";", $data["img"]);
    $fout = '<div class="document980">
    <div class="two_columns product_details">
        <section class="left_column">
            '.engine::print_image_rotator($site, $images).'
        </section>
        <section class="right_column">
            <div class="right_column_block">
                <h1>'.$data["title"].'</h1>
                <br/><br/>
                <p>'.$data["text"].'</p>
            </div>
            <div class="right_column_block pt0">';
    $list .= '<ul>';
    $query = 'SELECT `data`.`value` AS `value`, '
            . '`product_property`.`value` AS `name` '
            . 'FROM `nodes_property_data` AS `property_data` '
            . 'LEFT JOIN `nodes_product_data` AS `data` ON `property_data`.`data_id` = `data`.`id` '
            . 'LEFT JOIN `nodes_product_property` AS `product_property` ON `product_property`.`id` = `property_data`.`property_id` '
            . 'WHERE `property_data`.`product_id` = "'.$data["id"].'" AND `product_property`.`id` > 1 '
            . 'ORDER BY `product_property`.`id` ASC';
    $res = engine::mysql($query);
    $flag = 0;
    while($d = mysql_fetch_array($res)){
        $flag = 1;
        $list .= '<li>'.lang($d["name"]).': <b>'.lang($d["value"]).'</b></li>';
    }
    $list .= '</ul><br/>';
    if($flag) $fout .= $list;
    $fout .= '<div class="btn w280" ';
        if($data["user_id"]==$_SESSION["user"]["id"]){
            $fout .= ' onClick=\'alert("'.lang("Unable to purchase your own product").'")\' ';  
        }else{
            $fout .= ' onClick=\'buy_now('.$data["id"].', '
                    . '"'.lang("A new item has been added to your Shopping Cart").'", '
                    . '"'.lang("Continue").'", '
                    . '"'.lang("Checkout").'");\' ';
        }
        $fout .= ' >
                    <div class="detail_buy_now">
                        <div class="label_1">'.lang("Buy Now").'&nbsp;</div> 
                        <div class="label_2 cart_img">&nbsp;</div>
                        <div class="label_3">&nbsp;$'.intval($data["price"]).'</div>    
                    </div>
                </div>
                <br/><br/>
                <a href="#comments" onClick=\'document.getElementById("comments_block").style.display="block";\'><button class="btn w280" >'.lang("Show comments").'</button></a>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '<br/><br/>
                <a href="'.$_SERVER["DIR"].'/admin/?mode=products&action=edit&id='.$data["id"].'"><button class="btn w280">'.lang("Edit product").'</button></a>';
    }
    $fout .= '</div>
            <div class="clear"><br/></div>
        </section>
        <div class="clear"><br/></div>';
    $fout .= '<div id="comments_block">
        <a name="comments"></a>
        <div class="tal pl10 fs21"><b>'.lang("Latest comments").'</b><br/><br/></div>
        '.engine::print_comments("/product/".$data["id"]).'<br/>
        </div>
    </div>';
    $new_fout .= '<br/>
        <div class="tal pl10 fs21"><b>'.lang("You might also be interested in").'</b><br/><br/></div>
        <div class="preview_blocks">';
    $query = 'SELECT * FROM `nodes_product` WHERE `id` <> "'.$data["id"].'" AND `status` = "1" ORDER BY RAND() DESC LIMIT 0, 6';
    $res = engine::mysql($query);
    $count = 0;
    while($d = mysql_fetch_array($res)){
        $count++;
        $new_fout .= engine::print_product_preview($site, $d);
    }
    $new_fout .= '
        <div class="clear"></div>
        </div>';
    if($count) $fout .= $new_fout;
    $fout .= '<div class="clear"><br/></div>';
    $fout .= '
    </div>';
    return $fout;
}

