<?php
/**
* Print product page.
* @path /engine/core/product/print_product.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
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
    $rating = number_format(($data["rating"]/$data["votes"]),2);
    $fout = '<div class="document980">
    <div class="two_columns product_details">
        <section class="left_column">
            '.engine::print_image_rotator($site, $data["title"], $images).'
        </section>
        <section class="right_column">
            <div class="right_column_block">
                <h1>'.$data["title"].'</h1>
                <br/>
                <div class="profile_star fl">
                    <div class="profile_stars">
                        <div class="baseimage" style="margin-top: -'.(160-round($rating)*32).'px;" ></div>
                    </div>
                    <div class="votes">
                       '.$rating.' / 5.00 ('.$data["votes"].' '.lang("votes").')
                    </div>
                </div>
                <div class="share_block"><div>'.lang("Share friends").'</div><br/>'.
                    engine::print_share($_SERVER["PUBLIC_URL"].'/product/'.$data["id"]).'</div>
                <div class="clear"></div>
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
                </div><a href="#comments" onClick=\'document.getElementById("comments_block").style.display="block"; this.style.display="none";\'><button class="btn w280 mt15" >'.lang("Show comments").'</button></a>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '<br/><br/>
                <a href="'.$_SERVER["DIR"].'/admin/?mode=products&action=edit&id='.$data["id"].'"><button class="btn w280">'.lang("Edit product").'</button></a>';
    }
    $fout .= '</div>
            <div class="clear"><br/></div>
        </section>
        <div class="clear"><br/></div>';
        if(!empty($data["description"])){ $fout .= $data["description"].'<br/>'; }
    $fout .= '<div id="comments_block">
        <a name="comments"></a>
        <div class="tal pl10 fs21"><b>'.lang("Latest comments").'</b><br/><br/></div>
        '.engine::print_comments("/product/".$data["id"]).'<br/>
        </div>
    </div>';
    $blocks = engine::print_more_products($site, $data["id"]);
    if(!empty($blocks)){
        $new_fout .= '<div class="clear"></div><br/>'
            . '<div class="tal pl10 fs21"><b>'.lang("You might also be interested in").'</b><br/></div>
            <div class="preview_blocks">'
            .$blocks.
            '<div class="clear"></div>
            </div>';
    }
    $fout .= $new_fout;
    $fout .= '<div class="clear"><br/></div>';
    $fout .= '
    </div>';
    return $fout;
}

