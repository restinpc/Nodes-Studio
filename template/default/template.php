<?php
/**
* Default template file.
* @path /template/default/template.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title
* @var $this->content - Page HTML data
* @var $this->keywords - Array meta keywords
* @var $this->description - Page meta description
* @var $this->img - Page meta image
* @var $this->onload - Page executable JavaScript code
* @var $this->configs - Array MySQL configs
*/
$header = '';
$footer = '';
if(!isset($_POST["jQuery"])){
    if(!empty($_POST["new_message"]) && !empty($_POST["text"]) && !empty($_SESSION["user"]["id"])){
         engine::send_mail(
            $this->configs["email"], 
            $_SESSION["user"]["email"], 
            $_SERVER["New message from"]." ".$_SERVER["HTTP_HOST"], 
            str_replace("\n", "<br/>", $_POST["text"])
        );
         $this->onload .= '; alert("'.lang("Message sent successfully").'"); ';
    }
    //  Header Start
    $header = '<header id="mainHead">
    <div class="container">
        <div id="logo">
            <div id="logoImg"><a vr-control id="link-logo" href="'.$_SERVER["DIR"].'/"><img src="'.$_SERVER["DIR"].'/img/logo.png" alt="'.$this->configs["name"].'"></a></div>
        </div>
        <div id="nav">
        <ul>
            <li><a vr-control id="link-content" href="'.$_SERVER["DIR"].'/aframe/panorama">'.lang("Panoramas").'</a></li>
            <li><a vr-control id="link-content" href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
            <li><a vr-control id="link-products" href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>
            '; 
    if(empty($_SESSION["user"]["id"])){
        $header .= '<li class="last"><a vr-control id="link-sign-up" href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a></li>
            <li class="last" id="last"><a vr-control id="link-login" class="btn" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a></li>';
    }else{
        $header .= '<li class="last"><a vr-control id="link-account" class="btn" href="'.$_SERVER["DIR"].'/account">'.lang("My account").'</a></li>';
    }
    $header .= '
        </ul>
    </div>
    <div vr-control id="searchIcon" onClick=\'search("'.lang("Search").'");\'>
        <div class="searchImg"></div>
        <form id="search_form" method="GET" action="'.$_SERVER["DIR"].'/search/"><input type="hidden" id="query" name="q" value="" /></form>
    </div>
    <a vr-control id="menuIcon"><div class="nav_button"></div></a>
    <div vr-control id="langIcon">
        <form method="POST" id="lang_select">
            <select vr-control id="lang-select" name="lang" onChange=\'document.getElementById("lang_select").submit();\'>';
    $languages = explode(";", $this->configs["languages"]);
    foreach($languages as $l){
        $l = strtolower(trim($l));
        if(!empty($l)){
            if($_SESSION["Lang"]==$l) $header .= '<option vr-control id="option-'.$l.'" selected>'.strtoupper($l).'</option>';
            else $header .= '<option vr-control id="option-'.$l.'">'.strtoupper($l).'</option>';
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
        <li><a vr-control id="link-mobile-content" href="'.$_SERVER["DIR"].'/aframe/panorama">'.lang("Panoramas").'</a></li>
        <li><a vr-control id="link-mobile-content" href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
        <li><a vr-control id="link-mobile-products" href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>';
    if(empty($_SESSION["user"]["id"])){
        $header .= '
        <li><a vr-control id="link-mobile-login" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a></li>
        <li><a vr-control id="link-mobile-sign-up" href="'.$_SERVER["DIR"].'/register">'.lang("Sign Up").'</a></li>
        <li class="hidden"><a vr-control id="link-mobile-sitemap" href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></li>';
    }else{ 
        if($_SESSION["user"]["admin"]=="1"){
                $header .= '
        <li><a vr-control id="link-mobile-admin" href="'.$_SERVER["DIR"].'/admin">'.lang("Admin").'</a></li>';
        }$header .= '
        <li><a vr-control id="link-mobile-account" href="'.$_SERVER["DIR"].'/account">'.lang("Account").'</a></li>
        <li><a vr-control id="link-mobile-logout" href="#" onClick="logout();">'.lang("Logout").'</a></li>';
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
<section id="footer" itemscope itemtype="http://schema.org/Organization">
<span class="hidden" itemprop="name">'.$this->configs["name"].'</span>
<div class="hidden" itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
    <img class="hidden" itemprop="image" src="'.$this->img.'"/>
</div>
<div class="container">
    <div class="footer_left">
        <div class="footer_contacts">
            <span>'.lang("Get in Touch").'</span>
            <div class="clear"><br/><br/></div>';
        if(!empty($this->configs["tw_link"]))
            $footer .= '<a itemprop="sameAs" href="'.$this->configs["tw_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" alt="Twitter"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Twitter">Twitter</div>
            </a><div class="clear h20"></div>';
        if(!empty($this->configs["fb_link"]))
            $footer .= '<a itemprop="sameAs" href="'.$this->configs["fb_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" alt="Facebook"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Facebook">Facebook</div>
            </a><div class="clear h20"></div>';
        if(!empty($this->configs["gp_link"]))  
            $footer .= '<a itemprop="sameAs" href="'.$this->configs["gp_link"].'" target="_blank">
                <div class="social_img"><img src="'.$_SERVER["DIR"].'/img/social/gp.png" alt="Google+"/></div>
                <div class="pt7" title="'.lang("Connect us at").' Facebook">Google+</div>
            </a><div class="clear h7"></div>';  
        $footer .= '
        </div>
        <br/>
    </div>
    <div class="footer_right left-center" id="contact_us">
    <span>'.lang("Contact Us").'</span>
        <div class="clear"><br/><br/></div>
        <form method="POST"><textarea name="text" ';
    if(!empty($_SESSION["user"]["id"])){
        $footer .= ' vr-control id="textarea-message" placeHolder="'.lang("Your message here").'"></textarea><br/>'
        . '<input vr-control id="input-send-msg" type="submit" name="new_message" class="btn w270" value="'.lang("Send message").'"  />';
    }else{
        $footer .= 'placeHolder="'.lang("Login to send message").'" disabled></textarea><br/>'
        . '<a id="login-button-footer" vr-control href="/login"><input type="button" class="btn w270" value="'.lang("Login").'" /></a>';
    }$footer .= '
        </form>
        </div>
        <br/><br/>
        <div class="clear"></div>
    </div>
    <div class="clear"><br/><br/></div>
    <div id="copyright">
        <div class="line">
            <span class="text">
                <nobr>'.lang("Copyright").' <a vr-control id="link-footer-host" itemprop="url" href="'.$_SERVER["PUBLIC_URL"].'" title="'.$this->configs["description"].'">'.$_SERVER["HTTP_HOST"].'</a>, 2018.</nobr>
                <nobr>'.lang("All rights reserved").'.</nobr>
            </span>
            <span><a vr-control id="link-footer-home" href="'.$_SERVER["DIR"].'/">'.lang("Home").'</a></span>
            <span><a vr-control id="link-footer-privacy" href="'.$_SERVER["DIR"].'/privacy_policy">'.lang("Privacy Policy").'</a></span>
            <span><a vr-control id="link-footer-terms" href="'.$_SERVER["DIR"].'/terms_and_conditions">'.lang("Terms & Conditions").'</a></span> 
            <span><a vr-control id="link-footer-sitemap" href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></span> 
        </div>
    </div>
</section>';
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