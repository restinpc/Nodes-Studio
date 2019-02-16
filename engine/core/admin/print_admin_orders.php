<?php
/**
* Print admin orders page.
* @path /engine/core/admin/print_admin_orders.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $cms->site - Site object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin_orders($cms); </code>
*/
function print_admin_orders($cms){
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
    $fout = '<div class="document640">';
    $query = 'SELECT * FROM `nodes_product` WHERE `user_id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    $flag = 0;
    $orders = array();
    while($data=mysqli_fetch_array($res)){
        $query = 'SELECT * FROM `nodes_product_order` WHERE `product_id` = "'.$data["id"].'" AND `status` < 2 ORDER BY `status` ASC';
        $r = engine::mysql($query);
        while($d = mysqli_fetch_array($r)){
            if(!in_array($d, $orders)) 
                array_push($orders, $d);
        }   
    }
    function cmp($a, $b){
        if ($a["status"] == $b["status"]) {
            if($a["date"] < $b["date"]){
                return -1;
            }else if($a["date"]!=$b["date"]){
                return 1;
            }else return 0;
        }return ($a["status"] < $b["status"]) ? -1 : 1;
    }
    usort($orders, "cmp");
    foreach($orders as $order){
        $print_order = engine::print_order($cms, $order["id"]);
        if(!empty($print_order)){
            $fout .= $print_order;
            $flag = 1;
        } 
    }
    $fout .= '</div>';
    if(!$flag){
        $fout = '<div class="clear_block">'.lang("Orders not found").'</div>';
    }
    return $fout;
}