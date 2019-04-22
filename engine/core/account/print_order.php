<?php
/**
* Print account order block.
* @path /engine/core/account/print_order.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
* @param int $user_id @mysql[nodes_product_order]->id.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_order($site, 1); </code>
*/
function print_order($site, $order_id){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
        . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "orders" '
        . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
        . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysqli_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.$order_id.'"';
    $r = engine::mysql($query);
    while($d = mysqli_fetch_array($r)){
        if($d["count"]>0){
            $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$d["order_id"].'"';
            $res = engine::mysql($query);
            $order = mysqli_fetch_array($res);
            if($order["status"]=="0") continue;
            $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$order["shipping"].'"';
            $res = engine::mysql($query);
            $address = mysqli_fetch_array($res);
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$d["product_id"].'"';
            $res = engine::mysql($query);
            $product = mysqli_fetch_array($res);
            $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$product["user_id"].'"';
            $res = engine::mysql($query);
            $shipping = mysqli_fetch_array($res);
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$order["user_id"].'"';
            $res = engine::mysql($query);
            $user = mysqli_fetch_array($res);
            $images = explode(";", $product["img"]);
            $addresstr = '';
            if(!empty($address["fname"])) $addresstr .= $address["fname"].' '.$address["lname"].', ';
            if(!empty($address["country"])) $addresstr .= $address["country"].', ';
            if(!empty($address["state"])) $addresstr .= $address["state"].', ';
            if(!empty($address["city"])) $addresstr .= $address["city"].', ';
            if(!empty($address["street1"])) $addresstr .= $address["street1"].', ';
            if(!empty($address["street2"])) $addresstr .= $address["street2"].', ';
            if(!empty($address["zip"])) $addresstr .= "zip ".$address["zip"];
            $addresstr = '<a vr-control id="link-address-'.$d["id"].'" title="'.$addresstr.'" onClick=\'alert(this.title);\'>'.$address["country"].'</a>';
            if($d["status"]==1){
                $status = lang('Sended');
            }else if($d["status"]==0){
                $buttons = '
                <input vr-control id="input-confirm-shipment" type="button" class="btn shipment" value="'.lang('Confirm Shipment').'" onClick=\'confirm_order("'.$d["id"].'", "'.lang("Post track number").'", "'.lang("Shipment is confirmed").'", "'.lang("This item is sold out now?").'");\' />
                ';
                $status = lang('New order');
            }else{
                $buttons = '<input vr-control id="input-archive" type="button" class="btn shipment" value="'.lang('Archive order').'" onClick=\'archive_order("'.$d["id"].'", "'.lang("Archive order").'");\' />';
                $status = lang('Finished');
            }
            $fout .= '<div class="print_order">
            <div class="print_order_image" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
            <div>
                <div class="print_order_date">'.date("d/m/Y", $d["date"]).'<br/>
                <strong>'.$status.'</strong></div>
                <b>'.$product["title"].'</b><br/><br/>
                <font class="print_order_price">$ '.$product["price"].'</font><br/><br/>
                '.lang("Purchaser").': <a vr-control id="link-user-'.$order["user_id"].'-'.$d["id"].'" href="'.$_SERVER["DIR"].'/account/inbox/'.$order["user_id"].'" target="_blank">'.$user["name"].'</a><br/><br/>
                '.lang("Shipping address").': '.$addresstr.'
            </div>
            <div class="clear"></div>
            <div class="print_order_buttons">';
                if($admin_access == 2){
                   $fout .= '
                <form method="POST">'.$buttons.' </form>
                        ';
                }
            $fout .= '
            </div>
            <div class="clear"></div>
            </div>';
        }
    }return $fout;
}