<?php
/**
* Backend account pages file.
* @path /engine/site/account.php
*
* @name    Nodes Studio    @version 2.0.1.9
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
if(!empty($_GET[3])){
    $this->content = engine::error();
    return; 
}
if(!empty($_SESSION["user"]["id"])){
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    if(!$user["confirm"]){
        $this->title = lang("Account confirmation").' - '.$this->title;
        $this->content .= engine::print_email_confirm($this);
        return;
    }else if(!empty($_GET[1])){
        if($_GET[1] == "settings"){
            if(!empty($_GET[3])){ $this->content = engine::error(); return; }
            $title = lang("Settings");
            $this->title = $title.' - '.$this->title;
            $this->content .= engine::print_navigation($this, $title);
            $this->content .= engine::print_settings($this);
        }else if($_GET[1]=="confirm"){
            if(!empty($_GET[3]) || empty($_GET[2])){ $this->content = engine::error(); return; }
            $title = lang("Delivery confirmation");
            $this->title = $title.' - '.$this->title;
            $this->content .= engine::print_navigation($this, $title);
            $this->content .= engine::print_order_confirm($this);
        }else if($_GET[1]=="purchases"){            
            if(!empty($_GET[2])){ $this->content = engine::error(); return; }
            $title = lang("Purchases");
            $this->title = $title.' - '.$this->title;
            $this->content .= engine::print_navigation($this, $title);
            $this->content .= engine::print_purchases($this);
        }else if($_GET[1]=="inbox"){
            if(!empty($_GET[3])){ $this->content = engine::error(); return; }
            $title = lang("Messages");
            $this->title = $title.' - '.$this->title;
            $this->content .= engine::print_navigation($this, $title);
            $this->content .= engine::print_inbox($this);
        }else if($_GET[1]=="finances"){
            if(!empty($_GET[3])){ $this->content = engine::error(); return; }
            $title = lang("Finances");
            $this->title = $title.' - '.$this->title;
            $this->content .= engine::print_navigation($this, $title);
            $this->content .= engine::print_finances($this);
        }else{ $this->content = engine::error(); return; }
    }else{
        $title = lang("Profile");
        $this->title = $user["name"].' - '.$this->title;
        $this->content = engine::print_header($this, intval($_SESSION["user"]["id"]));
        $this->content .= engine::print_navigation($this, $title);
        $this->content .= '<div class="document">'
        . '<div class="clear_block">'
        . '<p>'.lang("Member of").' <b>'.$this->configs["name"].'</b> '.lang("community").'.</p>'
        . '</div>'
        . '</div>';
    }
}else{
    $this->content = engine::error(401);
}