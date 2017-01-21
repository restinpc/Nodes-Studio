<?php
/**
* Prints content article page.
* @path /engine/core/content/print_article.php
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
* @param int $data @mysql[nodes_content].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_article($site, $data); </code>
*/
function print_article($site, $data){
    if(!empty($data["img"])) 
        $site->img = $_SERVER["DIR"]."/img/data/big/".$data["img"];
    $site->description = $data["text"];
    $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$data["cat_id"].'"';
    $r = engine::mysql($query);
    $d = mysql_fetch_array($r);
    $fout .= '<h1>'.$data["caption"].'</h1><br/>';
    $fout .= '<div class="article">';
    if(!empty($data["img"])){
        $fout .= '
    <div class="article_image>
        <img src="'.$_SERVER["DIR"].'/img/data/big/'.$data["img"].'" class="img" />
    </div>';
    }
    $fout .= '
    <div class="date">'.date("d/m/Y", $data["date"]).' '.date("H:i", $data["date"]).'</div>
    <div class="cr"></div>
    <div class="text">
        '.$data["text"].'
    </div>
    </div>
    <div class="clear"><br/></div>
    <center>'.
    engine::print_comments($_SERVER["REQUEST_URI"])
    .'</center><br/>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '<a href="'.$_SERVER["DIR"].'/admin/?mode=content&cat_id='.$data["cat_id"].'&id='.$data["id"].'&act=edit"><input type="button" class="btn w280" value="'.lang("Edit article").'" /></a><br/><br/>';
    }
    $new_fout .= '<div class="clear"></div><br/>'
        . '<div class="tal pl10 fs21"><b>'.lang("You might also be interested in").'</b><br/></div>
        <div class="preview_blocks">';
    $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` = "'.$data["cat_id"].'" AND `id` <> "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC LIMIT 0, 6';
    $res = engine::mysql($query); 
    $count = 0;
    $urls = array();
    while($d = mysql_fetch_array($res)){
        if($count>5) break;
        $count++;
        $new_fout .= engine::print_preview($site, $d);
        array_push($urls, $d["id"]);
    }
    if($count<6){
        $query = 'SELECT * FROM `nodes_content` WHERE `cat_id` <> "'.$data["cat_id"].'" AND `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() DESC LIMIT 0, '.(6-$count);
        $res = engine::mysql($query); 
        while($d = mysql_fetch_array($res)){
            if(!in_array($d["id"], $urls)){
                if($count>5) break;
                $count++;
                $new_fout .= engine::print_preview($site, $d);
            }
        }
    }
    $new_fout .= '
        <div class="clear"></div>
        </div>';
    if($count) $fout .= $new_fout;
    $fout .= '<div class="clear"><br/></div>
        ';
    return $fout;
}