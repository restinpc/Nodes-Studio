<?php
/**
* Prints see also content block.
* @path /engine/core/content/print_more_articles.php
* 
* @name    Nodes Studio    @version 3.0.0.1
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
* @param string $url Page URL.
* @return string Returns Show more block on article or product page.
* @usage <code> engine::print_show_more($site, "/content/test"); </code>
*/
function print_more_articles($site, $url){
    $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$url.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    // print articles based on [expert] selection
    $brain = engine::match_patterns($url);
    $urls = explode(";", $brain);
    $count = 0;
    foreach($urls as $page_url){
        if($count>5) break;
        if(!empty($page_url)){
            $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$page_url.'" AND `lang` = "'.$_SESSION["Lang"].'"';
            $r = engine::mysql($query); 
            $d = mysqli_fetch_array($r);
            if(!empty($d)){
                $count++;
                $fout .= engine::print_preview($site, $d);
                array_push($urls, $d["id"]);
            }
        }
    }
    // print articles based on [same catalog] and [not in session] selection
    if($count<6){
        $query = 'SELECT `cache`.`url` AS `value`, `att`.`date` AS `date` '
                . 'FROM `nodes_attendance` AS `att` '
                . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
                . 'WHERE `att`.`token` = "'.session_id().'" ORDER BY `att`.`date` ASC';
        $res = engine::mysql($query);
        $arr = array();
        $i = 0;
        while($d = mysqli_fetch_array($res)){
            if($arr[$i][0] != $d["value"]){
                $arr[$i++] = array($d["value"], $d["date"]);
            }
        }
        $pattern_urls = array();
        foreach($arr as $pattern){
            if(engine::is_article($pattern[0])){
                $pattern[0] = str_replace($_SERVER["PUBLIC_URL"].'/', '', $pattern[0]);
                if(strpos($pattern[0], "content/")!==FALSE) $pattern[0] = mb_substr($pattern[0], 8);
                array_push ($pattern_urls, $pattern[0]);
            }
        }
        $pattern_urls = array_unique($pattern_urls);
        $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$data["cat_id"].'" AND `id` <> "'.$data["id"].'" '
                . 'AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC';
        $res = engine::mysql($query); 
        while($d = mysqli_fetch_array($res)){
            if(!in_array($d["id"], $urls) && !in_array($d["url"], $pattern_urls)){
                $count++;
                $fout .= engine::print_preview($site, $d);
                array_push($urls, $d["id"]);
            }
        }
        // print articles based on [any catalog] and [not in session] selection
        if($count<6){
            $query = 'SELECT * FROM `nodes_content` WHERE `id` <> "'.$data["id"].'" '
                    . 'AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC';
            $res = engine::mysql($query); 
            while($d = mysqli_fetch_array($res)){
                if(!in_array($d["id"], $urls) && !in_array($d["url"], $pattern_urls)){
                    if($count>5) break;
                    $count++;
                    $fout .= engine::print_preview($site, $d);
                    array_push($urls, $d["id"]);
                }
            }
        }
        // print articles based on [any catalog] and [in session] selection
        if($count<6){
            $query = 'SELECT * FROM `nodes_content` WHERE `id` <> "'.$data["id"].'" '
                    . 'AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC';
            $res = engine::mysql($query); 
            while($d = mysqli_fetch_array($res)){
                if(!in_array($d["id"], $urls)){
                    if($count>5) break;
                    $count++;
                    $fout .= engine::print_preview($site, $d);
                }
            }
        }
    } return $fout;
}