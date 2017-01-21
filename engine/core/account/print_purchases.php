<?php
/**
* Print account purchases page.
* @path /engine/core/account/print_purchases.php
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
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_purchases($site); </code>
*/
function print_purchases($site){
    $fout = '<div class="document640">';
    $query = 'SELECT * FROM `nodes_order` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" AND `status` > 0 ORDER BY `date` DESC';
    $res = engine::mysql($query);
    $flag = 0;
    while($data = mysql_fetch_array($res)){
        if($data["status"]=="1"){
            $site->onload .= '
                alert("'.lang("Thank you for your order! Shipment in process now.").'");
                document.getElementById("purcases_count").innerHTML = "";
                document.getElementById("purcases").style.display = "none";
                ';
            $query = 'UPDATE `nodes_order` SET `status` = "2" WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
        }
        $flag = 1;
        $fout .= engine::print_purchase($site, $data);
    }if(!$flag){
        $fout .= '<div class="clear_block">'.lang("There is no purchases").'</div>';
    }$fout .= '</div>';
    return $fout;
}