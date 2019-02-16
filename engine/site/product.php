<?php 
/**
* Backend product pages file.
* @path /engine/site/product.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/
if(empty($_GET[0]) || !empty($_GET[2])){
    $this->content = engine::error();
    return; 
}
if(intval($_GET[1])>0){
    $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.intval($_GET[1]).'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($data)){   
        $query = 'SELECT `data`.`value` AS `caption` '
            . 'FROM `nodes_property_data` AS `property` '
            . 'LEFT JOIN `nodes_product_data` AS `data` ON `data`.`id` = `property`.`data_id` '
            . 'WHERE `product_id` = "'.$data["id"].'" AND `property_id` = "1"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        $this->title = $data["title"].' - '.$this->title;
        $this->description = mb_substr($data["text"],0,300);
        $this->content .= engine::print_navigation($this, $d["caption"]);
        $this->content .= engine::print_product($this, $data);
    }else{
        $this->content = engine::error();
        return; 
    }
}else{
    if(!empty($_GET[1])){
        $requery = 'SELECT * FROM `nodes_product_data` WHERE `url` LIKE "'. engine::escape_string(strtolower($_GET[1])).'"';
        $r = engine::mysql($requery);
        $d = mysqli_fetch_array($r);
        if(!empty($d)){
            $title  = $d["value"];
        }
    }else{
        $this->title = lang("Products").' - '.$this->title;
        if(!empty($_POST["request"])){
            $title = lang("Search item");;
        }else{
            $title = lang("Products");
        }
    }
    $this->content .= engine::print_navigation($this, $title);
    $this->content .= engine::print_products($this);
}