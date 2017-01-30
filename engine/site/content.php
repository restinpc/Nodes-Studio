<?php
/**
* Backend content pages file.
* @path /engine/site/content.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/
if(empty($_GET[0])){
    $this->content = engine::error();
    return; 
}
if($_GET[0]!="content"){ 
    $link = $_GET[0];
    if(!empty($_GET[1])){
        $this->content = engine::error();
        return;
    }
}else{
    $link = $_GET[1];
    if(!empty($_GET[2])){
        $this->content = engine::error();
        return;
    }
}
if(!empty($_POST["from"])) $_SESSION["from"] = $_POST["from"];
if(!empty($_POST["to"])) $_SESSION["to"] = $_POST["to"];
if(!empty($_POST["count"])) $_SESSION["count"] = intval($_POST["count"]);
if(!empty($_POST["page"])) $_SESSION["page"] = intval($_POST["page"]);
if($_SESSION["order"]!="order") $_SESSION["order"] = "order";
if($_SESSION["method"]!="DESC") $_SESSION["method"] = "DESC";
if($_GET[0]!="content" || (!empty($_GET[1]) && $_GET[0]=="content")){      
    $query = 'SELECT * FROM `nodes_catalog` WHERE `url` = "'.$link.'" AND `lang` = "'.$_SESSION["Lang"].'"';
    $res = engine::mysql($query); 
    $data = mysql_fetch_array($res);
    if(!empty($data)){
        $this->title = $data["caption"].' - '.$this->title;
        $this->description = mb_substr(strip_tags($data["text"], 0, 400));
        if(!empty($data["img"])) $this->img = $_SERVER["DIR"]."/img/data/big/".$data["img"];
        $query = 'SELECT COUNT(*) FROM `nodes_content` WHERE `cat_id` = "'.$data["id"].'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        if($data['visible'])$this->content .= engine::print_navigation($this, $data["caption"]);
        $this->content .= '<div class="document980">';
        if($d[0]) $this->content .= engine::print_articles($this, $data);  
        else $this->content .= engine::print_catalog($this, $data);
        $this->content .= '</div>';
    }else{
        $query = 'SELECT * FROM `nodes_content` WHERE `url` = "'.$link.'" AND `lang` = "'.$_SESSION["Lang"].'"';
        $res = engine::mysql($query); 
        $data = mysql_fetch_array($res);
        if(empty($data)){
            engine::error();
            exit();  
        }else{
            $query = 'SELECT * FROM `nodes_catalog` WHERE `id` = "'.$data["cat_id"].'"';
            $r = engine::mysql($query);
            $catalog = mysql_fetch_array($r);
            $this->title = $data["caption"].' - '.$this->title;
            $this->description = mb_substr(strip_tags($data["text"]));
            $this->content .= engine::print_navigation($this, $catalog["caption"]);
            $this->content .= '<div class="document980">';
            $this->content .= engine::print_article($this, $data);
            $this->content .= '</div>';
        }
    }
}else{
    $this->title = lang("Content").' - '.$this->title;
    $this->content .= engine::print_navigation($this, lang("Content"));
    $this->content .= '<div class="document980">';
    $this->content .= engine::print_articles($this);  
    $this->content .= '</div>';
}