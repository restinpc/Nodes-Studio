<?php

// TODO - Your code here
//----------------------------

function print_purchase($data){
    $query = 'SELECT * FROM `nodes_product_order` WHERE `order_id` = "'.$data["id"].'"';
    $r = engine::mysql($query);
    while($d = mysql_fetch_array($r)){  
        if($d["count"]>0){
            $query = 'SELECT * FROM `nodes_orders` WHERE `id` = "'.$data["id"].'"';
            $res = engine::mysql($query);
            $order = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$order["shipping"].'"';
            $res = engine::mysql($query);
            $address = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.$d["product_id"].'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$product["user_id"].'"';
            $res = engine::mysql($query);
            $shipping = mysql_fetch_array($res);
            $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$product["user_id"].'"';
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
            if($d["status"]==0){
                $status = lang('Shipment in process');
            }else if($d["status"]==1){
                $status = lang('Sended');
                $button = '<a href="/account/confirm/'.$d["id"].'"><input type="button" class="btn confirm_receipt" value="'.lang("Confirm receipt").'" /></a>';
            }else{
                $status = lang('Finished');
            }
            $fout .= '<div class="print_order">
                <div class="print_order_image mb5" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
                <div>
                    <div class="print_order_date">'.date("d/m/Y", $data["date"]).''
                    . '<br/><strong>'.$status.'</strong></div>
                    <div class="print_order_wrap"></div>
                    <b>'.$product["title"].'</b><br/><br/>
                    <font class="fs18">$ '.$product["price"].'</font><br/><br/>
                    '.lang("Seller").': <a href="'.$_SERVER["DIR"].'/account/inbox/'.$user["id"].'" target="_blank">'.$user["name"].'</a><br/><br/>
                    '.lang("Shipping from").': <a title="'.$shipping["country"].', '.$shipping["state"].', '.$shipping["city"].', '.$shipping["street1"].', '.$shipping["street2"].' ">'.$shipping["country"].'</a>'
                    .'<br/><br/>'
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

