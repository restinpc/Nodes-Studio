<?php
/*
$this->title - Page title
$this->content - Page HTML data
$this->menu - Page HTML navigation
$this->keywords - Page meta keywords
$this->description - Page meta description
$this->img - Page meta image
$this->js - Page JavaScript code
$this->activejs - Page executable JavaScript code
$this->css - Page CSS data
$this->configs - Array MySQL configs
*/

// TODO - Your code here
//---------------------------- 

if(!isset($_POST["jQuery"])){
    
    //  Header Start
    $header = '<header id="mainHead">
    <div class="container">
        <div id="logo">
            <div id="logoOne"><a href="'.$_SERVER["DIR"].'/"><img src="'.$_SERVER["DIR"].'/img/logo.png" style="height: 65px;" alt="'.$this->configs["name"].'"></a></div>
            <div id="logoTwo">
                <div style="float:left; padding-right: 5px;"><a href="'.$_SERVER["DIR"].'/"><img src="'.$_SERVER["DIR"].'/img/favicon.png" title="'.$this->configs["description"].'" /></a></div>
                <div id="title" class="site_title">'.$this->title.'</div>
            </div>
        </div>
        <div id="nav">
        <ul>
            <li><a href="'.$_SERVER["DIR"].'/">'.lang("Home").'</a></li>
            <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
            '; 
    if(empty($_SESSION["user"]["id"])){
        $header .= '<li class="last"><a href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a></li>
            <li class="last" id="last"><a target="_parent" class="btn" onClick="show_login_form();" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a></li>';
    }else{
        $header .= '<li class="last"><a href="'.$_SERVER["DIR"].'/account" class="btn">'.lang("My Account").'</a></li>
            <li class="last"  id="last"><a href="#" onClick="logout();" class="btn">'.lang("Logout").'</a></li>';
    }
    $header .= '
        </ul>
    </div>
    <div id="searchIcon" onClick=\'search("'.lang("Search").'");\'>
        <img src="'.$_SERVER["DIR"].'/img/search.png" style="height: 25px;" />
        <form id="search_form" method="GET" action="'.$_SERVER["DIR"].'/search/"><input type="hidden" id="query" name="q" value="" /></form>
    </div>
    <a id="menuIcon"><img src="'.$_SERVER["DIR"].'/img/menu.png" alt="'.lang("Show navigation").'"></a>
    <div id="langIcon">
        <form method="POST" id="lang_select">
            <select name="lang" onChange=\'document.getElementById("lang_select").submit();\'>';
    
    $languages = explode(";", $this->configs["languages"]);
    foreach($languages as $l){
        $l = strtolower(trim($l));
        if(!empty($l)){
            if($_SESSION["Lang"]==$l) $header .= '<option selected>'.strtoupper($l).'</option>';
            else $header .= '<option>'.strtoupper($l).'</option>';
        }
    }
    
    $header .= '
            </select>
        </form>
    </div>
</div>
</header>
<section id="bigNav">
<div class="container">
    <ul>
        <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>';
    if(empty($_SESSION["user"]["id"])){
        $header .= '
        <li><a href="#" onClick="show_login_form();">'.lang("Login").'</a></li>
        <li><a href="'.$_SERVER["DIR"].'/register">'.lang("Sign Up").'</a></li>
        <li style="display:none;"><a href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></li>';
    }else{ 
        if($_SESSION["user"]["id"]=="1"){
                $header .= '
        <li><a href="'.$_SERVER["DIR"].'/admin">'.lang("Admin").'</a></li>';
        }$header .= '
        <li><a href="'.$_SERVER["DIR"].'/account">'.lang("Account").'</a></li>
        <li><a href="#" onClick="logout();">'.lang("Logout").'</a></li>';
    }$header .= '
    </ul>
</div>
</section>
<div id="content">
<!-- content -->
';  //  Header End  
    
    //  Footer Start  
    $footer = '
<!-- /content -->
</div>
<section id="footer">
<div class="container">
    <div class="footer_left">
        <div class="footer_contacts">
            <span>'.lang("Get in Touch").'</span>
            <div style="clear:both; height: 25px;"></div>
            <a href="https://twitter.com/" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" alt="Twitter"/></div>
                <div style="padding-top: 7px;" title="'.lang("Connect us at").' Twitter">Twitter</div>
            </a>
            <div style="clear:both; height: 20px;"></div>
            <a href="https://www.facebook.com/" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" alt="Facebook"/></div>
                <div style="padding-top: 7px;" title="'.lang("Connect us at").' Facebook">Facebook</div>
            </a>
            <div style="clear:both; height: 10px;"></div>
        </div>
    </div>
    <div class="footer_right left-center" id="contact_us">
    <span>'.lang("Contact Us").'</span>
        <div style="clear:both; height: 20px;"></div>
        <form method="POST"><textarea name="text" ';
    if(!empty($_SESSION["user"]["id"])){
        $footer .= 'placeHolder="'.lang("Your message here").'"></textarea><br/>'
        . '<input type="submit" name="new_message" onClick=\'send_message();\' class="btn" style="width: 270px;" value="'.lang("Send message").'"  />';
    }else{
        $footer .= 'placeHolder="'.lang("Login to send message").'" disabled></textarea><br/>'
        . '<input type="button" class="btn" style="width: 270px;" value="'.lang("Login").'" onClick="show_login_form();"  />';
    }$footer .= '
        </form>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div id="copyright">
        <span>'.lang("Copyright").' <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'" title="'.$this->configs["description"].'">'
        .$_SERVER["HTTP_HOST"].'</a>, 2015. </span>
        <span>'.lang("All rights reserved").'.</span></div>
    <div style="clear:both;"></div>
</section>
<script language="JavaScript" type="text/javascript"> if(!window.jQuery) document.write(unescape(\'<script language="JavaScript" type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.js">%3C/script%3E\')); </script>
<div id="floater"><img src="'.$_SERVER["DIR"].'/img/up_button.png" alt="'.lang("Up").'"></div>';
    //  Footer End       
}

if(!empty($this->menu)){ 
    $header .= '
<nav><div id="submenu">'.$this->menu.'</div></nav>
<section id="contentSection" style="padding-top: 10px;">';  
}else{ 
    $header .= ' 
<section id="contentSection">';
}$this->content = 
    $header.'
<div class="container">
    '.$this->content.'
</div>
</section>'.
    $footer;