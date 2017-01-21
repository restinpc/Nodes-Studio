<?php
/**
* Default template file.
* @path /template/default/template.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @var $this->title - Page title
* @var $this->content - Page HTML data
* @var $this->keywords - Array meta keywords
* @var $this->description - Page meta description
* @var $this->img - Page meta image
* @var $this->onload - Page executable JavaScript code
* @var $this->configs - Array MySQL configs
*/
if(!isset($_POST["jQuery"])){
    if(!empty($_POST["new_message"]) && !empty($_POST["text"]) && !empty($_SESSION["user"]["id"])){
         engine::send_mail(
            $this->configs["email"], 
            $_SESSION["user"]["email"], 
            $_SERVER["New message from"]." ".$_SERVER["HTTP_HOST"], 
            str_replace("\n", "<br/>", $_POST["text"])
        );
         $this->onload .= '
        alert("'.lang("Message sent successfully").'");
             ';
        $query = 'INSERT INTO `nodes_log`(action, user_id, ip, date, details) '
        . 'VALUES("7", "'.$_SESSION["user"]["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$_POST["text"].'")';
        engine::mysql($query);
    }
    //  Header Start
    $header = '<header id="mainHead">
    <div class="container">
        <div id="logo">
            <div id="logoOne"><a href="'.$_SERVER["DIR"].'/"><img src="'.$_SERVER["DIR"].'/img/logo.png" alt="'.$this->configs["name"].'"></a></div>
            <div id="logoTwo">
                <div class="favicon"><a href="'.$_SERVER["DIR"].'/"><img src="'.$_SERVER["DIR"].'/img/favicon.png" title="'.$this->configs["description"].'" /></a></div>
                <div id="title">'.$this->configs["name"].'</div>
            </div>
        </div>
        <div id="nav">
        <ul>
            <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
            <li><a href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>
            '; 
    if(empty($_SESSION["user"]["id"])){
        $header .= '<li class="last"><a href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a></li>
            <li class="last" id="last"><a target="_parent" class="btn"  onClick="event.preventDefault(); login();" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a></li>';
    }else{
        $header .= '<li class="last"><a class="btn" href="'.$_SERVER["DIR"].'/account">'.lang("My account").'</a></li>';
    }
    $header .= '
        </ul>
    </div>
    <div id="searchIcon" onClick=\'search("'.lang("Search").'");\'>
        <div class="searchImg"></div>
        <form id="search_form" method="GET" action="'.$_SERVER["DIR"].'/search/"><input type="hidden" id="query" name="q" value="" /></form>
    </div>
    <a id="menuIcon"><div class="nav_button"></div></a>
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
        <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
        <li><a href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>';
    if(empty($_SESSION["user"]["id"])){
        $header .= '
        <li><a href="'.$_SERVER["DIR"].'/login" target="_parent" onClick="event.preventDefault(); login();">'.lang("Login").'</a></li>
        <li><a href="'.$_SERVER["DIR"].'/register">'.lang("Sign Up").'</a></li>
        <li class="hidden"><a href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></li>';
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
<!-- content -->';  
//  Header End  
//------------------------------------------------------------------------------
//  Footer Start  
$footer = '
<!-- /content -->
</div>
<div class="clear"></div>
<section id="footer">
<div class="container">
    <div class="footer_left">
        <div class="footer_contacts">
            <span>'.lang("Get in Touch").'</span>
            <div class="clear h20"> </div>';
        if(!empty($this->configs["tw_link"]))
            $footer .= '<a href="'.$this->configs["tw_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" alt="Twitter"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Twitter">Twitter</div>
            </a><div class="clear h5"></div>';
        if(!empty($this->configs["fb_link"]))
            $footer .= '<a href="'.$this->configs["fb_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" alt="Facebook"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Facebook">Facebook</div>
            </a><div class="clear h5"></div>';
        if(!empty($this->configs["gp_link"]))  
            $footer .= '<a href="'.$this->configs["gp_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" alt="Google+"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Facebook">Google+</div>
            </a><div class="clear h5"></div>';  
        if(!empty($this->configs["vk_link"]))    
            $footer .= '<a href="'.$this->configs["vk_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" alt="Vkontakte"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Facebook">Vkontakte</div>
            </a><div class="clear h5"></div>';  
        $footer .= '
        </div>
    </div>
    <div class="footer_right left-center" id="contact_us">
    <span>'.lang("Contact Us").'</span>
        <div class="clear h20"> </div>
        <form method="POST"><textarea name="text" ';
    if(!empty($_SESSION["user"]["id"])){
        $footer .= 'placeHolder="'.lang("Your message here").'"></textarea><br/>'
        . '<input type="submit" name="new_message" class="btn w270" value="'.lang("Send message").'"  />';
    }else{
        $footer .= 'placeHolder="'.lang("Login to send message").'" disabled></textarea><br/>'
        . '<input type="button" class="btn w270" value="'.lang("Login").'"  onClick="event.preventDefault(); login();" />';
    }$footer .= '
        </form>
        </div>
        <div class="clear"></div>
    </div>
    <div id="copyright">
        <div class="line">
            <span class="text">
                <nobr>'.lang("Copyright").' <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'" title="'.$this->configs["description"].'">'.$_SERVER["HTTP_HOST"].'</a>, 2017.</nobr>
                <nobr>'.lang("All rights reserved").'.</nobr>
            </span>
            <span><a href="'.$_SERVER["DIR"].'/">'.lang("Home").'</a></span>
            <span><a href="'.$_SERVER["DIR"].'/privacy_policy">'.lang("Privacy Policy").'</a></span>
            <span><a href="'.$_SERVER["DIR"].'/terms_and_conditions">'.lang("Terms & Conditions").'</a></span> 
            <span><a href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></span> 
        </div>
    </div>
</section>
    ';
    $count = 0;
    if(!empty($_SESSION["products"])) foreach($_SESSION["products"] as $key=>$value) if($value>0) $count++;
    if($count) $footer .= engine::print_cart($count);
    $footer .= '
<div id="floater" alt="'.lang("Up").'"> </div>';
    //  Footer End       
}
$header .= '<section id="contentSection"><div class="container">';
$footer = '</div></section>'.$footer;
$this->content = $header.$this->content.$footer;