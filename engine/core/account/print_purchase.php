<?php
/**
* Print account purchase block.
* @path /engine/core/account/print_purchase.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
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
* @param array $data @mysql[nodes_product_order].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_purchase($site, $data); </code>
*/
function print_purchase($site, $data){
    $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$data["id"].'"';
    $r = engine::mysql($query);
    while($d = mysql_fetch_array($r)){  
        if($d["count"]>0){
            $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$data["id"].'"';
            $res = engine::mysql($query);
            $order = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$order["shipping"].'"';
            $res = engine::mysql($query);
            $address = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$d["product_id"].'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$product["user_id"].'"';
            $res = engine::mysql($query);
            $shipping = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$product["user_id"].'"';
            $res = engine::mysql($query);
            $user = mysql_fetch_array($res);
            $images = explode(";", $product["img"]);
            if(!empty($d["track"])) $track = lang("Tracking number").": ".$d["track"]."<br/><br/>";
            $addresstr = '';
            if(!empty($address["fname"])) $addresstr .= $address["fname"].' '.$address["lname"].', ';
            if(!empty($address["country"])) $addresstr .= $address["country"].', ';
            if(!empty($address["state"])) $addresstr .= $address["state"].', ';
            if(!empty($address["city"])) $addresstr .= $address["city"].', ';
            if(!empty($address["street1"])) $addresstr .= $address["street1"].', ';
            if(!empty($address["street2"])) $addresstr .= $address["street2"].', ';
            if(!empty($address["zip"])) $addresstr .= "zip ".$address["zip"];
            $addresstr = '<a title="'.$addresstr.'" onClick=\'alert(this.title);\'>'.$address["country"].'</a>';
            if($d["status"]==0){
                $status = lang('Shipment in process');
            }else if($d["status"]==1){
                $status = lang('Sended');
                $button = '<a href="'.$_SERVER["DIR"].'/account/confirm/'.$d["id"].'"><input type="button" class="btn confirm_receipt" value="'.lang("Confirm receipt").'" /></a>';
            }else{
                $status = lang('Finished');
            }
            $fout .= '<div class="print_order">
                <div class="print_order_image"><img src="'.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].'" width=150 /></div>
                <div>
                    <div class="fl pb5"><b>'.$product["title"].'</b></div>
                    <div class="print_order_date">'.date("d/m/Y", $data["date"]).''
                    . '<br/><strong>'.$status.'</strong></div>
                    <div class="print_order_wrap"></div>
                    <div class="cr"></div>
                    <font class="fs18">$ '.$product["price"].'</font><br/><br/>
                    '.lang("Seller").': <a href="'.$_SERVER["DIR"].'/account/inbox/'.$user["id"].'" target="_blank">'.$user["name"].'</a><br/><br/>'
                    .$track.
                    lang("Shipping address").': '.$addresstr.'<br/>
                </div>
                <div class="clear"></div>
                <div class="pt10 center">
                    <form method="POST">'.$button.'</form>
                </div>
                <div class="clear"></div>
            </div>';
        }
    }return $fout;
}