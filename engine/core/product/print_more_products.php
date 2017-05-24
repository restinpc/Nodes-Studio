<?php
/**
* Prints see also product block.
* @path /engine/core/product/print_more_products.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
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
* @param string $url Page URL.
* @return string Returns Show more block on article or product page.
* @usage <code> engine::print_more_products($site, 1); </code>
*/
function print_more_products($site, $id){
    $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$id.'"';
    $res = engine::mysql($query);
    $product = mysql_fetch_array($res);
    // print articles based on [expert] selection
    $brain = engine::match_patterns('product/'.$id);
    $urls = explode(";", $brain);
    $count = 0;
    foreach($urls as $page_url){
        if($count>5) break;
        if(!empty($page_url)){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$page_url.'"';
            $r = engine::mysql($query); 
            $d = mysql_fetch_array($r);
            if(!empty($d)){
                $count++;
                $fout .= engine::print_product_preview($site, $d);
                array_push($urls, $d["id"]);
            }
        }
    }
    // print products based on [same catalog] and [not in session] selection
    if($count<6){
        $query = 'SELECT `data_id` FROM `nodes_property_data` WHERE `product_id` = "'.$id.'" AND `property_id` = "1"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $data_id = $data["data_id"];
        $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date` '
                . 'FROM `nodes_attendance` AS `att` '
                . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
                . 'WHERE `att`.`token` = "'.session_id().'" ORDER BY `att`.`date` ASC';
        $res = engine::mysql($query);
        $arr = array();
        $i = 0;
        while($data = mysql_fetch_array($res)){
            if($arr[$i][0] != $data["value"]){
                $arr[$i++] = array($data["value"], $data["date"]);
            }
        }
        $pattern_urls = array();
        foreach($arr as $pattern){
            if(engine::is_product($pattern[0])){
                $pattern[0] = str_replace($_SERVER["PUBLIC_URL"].'/', '', $pattern[0]);
                if(strpos($pattern[0], "product/")!==FALSE) $pattern[0] = mb_substr($pattern[0], 8);
                array_push ($pattern_urls, $pattern[0]);
            }
        }
        $pattern_urls = array_unique($pattern_urls);
        $query = 'SELECT `product`.* FROM `nodes_product` AS `product` '
            . 'LEFT JOIN `nodes_property_data` AS `data` ON `data`.`product_id` = `product`.`id` '
            . 'WHERE `product`.`id` <> "'.$product["id"].'" AND `product`.`status` = "1" AND `data`.`data_id` = "'.$data_id.'" '
            . 'ORDER BY RAND() DESC';
        $res = engine::mysql($query); 
        while($d = mysql_fetch_array($res)){
            if(!in_array($d["id"], $urls) && !in_array($d["id"], $pattern_urls)){
                $count++;
                $fout .= engine::print_product_preview($site, $d);
                array_push($urls, $d["id"]);
            }
        }
        // print products based on [any catalog] and [not in session] selection
        if($count<6){
            $query = 'SELECT `product`.* FROM `nodes_product` AS `product` '
            . 'WHERE `product`.`id` <> "'.$product["id"].'" AND `product`.`status` = "1" '
            . 'ORDER BY RAND() DESC';
            $res = engine::mysql($query); 
            while($d = mysql_fetch_array($res)){
                if(!in_array($d["id"], $urls) && !in_array($d["id"], $pattern_urls)){
                    if($count>5) break;
                    $count++;
                    $fout .= engine::print_product_preview($site, $d);
                    array_push($urls, $d["id"]);
                }
            }
        }
        // print articles based on [any catalog] and [not in session] selection
        if($count<6){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` <> "'.$product["id"].'" ORDER BY RAND() DESC';
            $res = engine::mysql($query); 
            while($d = mysql_fetch_array($res)){
                if(!in_array($d["id"], $urls)){
                    if($count>5) break;
                    $count++;
                    $fout .= engine::print_product_preview($site, $d);
                }
            }
        }
    } return $fout;
}