<?php /* Nodes Studio 2.0.2 script. Do not edit directly. 09/11/2018 */
/**
* Android template file.
* @path /template/android/template.php
*
* @name    Nodes Studio    @version 2.0.4
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
//  Header Start
$header = '<form method="POST" id="lang_select"><input type="hidden" id="lang_value" name="lang" value="" /></form>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
<div class="android-header mdl-layout__header mdl-layout__header--waterfall">
    <div class="mdl-layout__header-row">
        <span class="mdl-layout-title android-title">
            <div id="logo"><a href="/"><img class="android-logo-image" src="'.$_SERVER["DIR"].'/img/logo.png" /></a></div>
        </span>
    <div class="android-header-spacer mdl-layout-spacer"></div>
    <div class="android-search-box mdl-textfield mdl-js-textfield mdl-textfield--expandable mdl-textfield--floating-label mdl-textfield--align-right mdl-textfield--full-width">
    <label class="mdl-button mdl-js-button mdl-button--icon" for="search-field" onClick="search(event);">
        <noindex class="material-icons">search</noindex>
    </label>
    <div class="mdl-textfield__expandable-holder">
        <form onSubmit="search(event);"><input class="mdl-textfield__input" type="text" id="search-field"></form>
    </div>
</div>
<!-- Navigation -->
    <div class="android-navigation-container">
        <nav class="android-navigation mdl-navigation">
            <a class="mdl-navigation__link mdl-typography--text-uppercase" href="'.$_SERVER["DIR"].'/aframe/panorama">'.lang("Panoramas").'</a>
            <a class="mdl-navigation__link mdl-typography--text-uppercase" href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a>
            <a class="mdl-navigation__link mdl-typography--text-uppercase" href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a>
            '; 
    if(empty($_SESSION["user"]["id"])){
        $header .= '<a class="mdl-navigation__link mdl-typography--text-uppercase" href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a>
            <a class="mdl-navigation__link mdl-typography--text-uppercase" target="_parent" class="btn" onClick="event.preventDefault(); login();" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a>';
    }else{
        $header .= ' <a class="mdl-typography--text-uppercase header_user" href="'.$_SERVER["DIR"].'/account"><img src="'.$_SERVER["DIR"].'/img/pic/'.$_SESSION["user"]["photo"].'" /> &nbsp;'.$_SESSION["user"]["name"].'</a>';
    }
    $header .= '
        </nav>
    </div>
    <span class="mdl-layout-title android-mobile-title">
        <a href="/"><img class="android-logo-image" src="'.$_SERVER["DIR"].'/img/logo.png" /></a>
    </span>
    <button class="android-more-button mdl-button mdl-js-button mdl-button--icon mdl-js-ripple-effect" id="more-button">
        <noindex class="material-icons">language</noindex>
    </button>
    <ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right mdl-js-ripple-effect" for="more-button">';
$languages = explode(";", $this->configs["languages"]);
foreach($languages as $l){
    $l = strtolower(trim($l));
    if(!empty($l)){
        if($_SESSION["Lang"]==$l) $header .= '<li disabled class="mdl-menu__item">'.strtoupper($l).'</li>';
        else $header .= '<li class="mdl-menu__item" onClick=\'setLang("'. strtolower($l).'");\'>'.strtoupper($l).'</li>';
    }
}
$header .= '
    </ul>
</div>
</div>
<div class="android-drawer mdl-layout__drawer">
    <span class="mdl-layout-title">'.$this->configs["name"].' </span>
    <nav class="mdl-navigation">
            '; 
    if(!empty($_SESSION["user"]["id"])){
        $header .= ' <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/account"><img src="'.$_SERVER["DIR"].'/img/pic/'.$_SESSION["user"]["photo"].'" style="width: 26px; " /> &nbsp;'.$_SESSION["user"]["name"].'</a>'
                . '<!-- <div class="android-drawer-separator"></div> -->';
    }
    $header .= '
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/">'.lang("Home").'</a>
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/aframe/panorama">'.lang("Panoramas").'</a>
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a>
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a>';
    if(empty($_SESSION["user"]["id"])){
        $header .= '
            <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a>
            <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a>';
    }
    $header .= '
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/privacy_policy">'.lang("Privacy Policy").'</a>  
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/terms_and_conditions">'.lang("Terms & Conditions").'</a>
        <a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a>
    </nav>
</div>
<div class="android-content mdl-layout__content">
    <div class="mdl-typography--text-center" id="content">
    <!-- content -->';  
//  Header End  
//------------------------------------------------------------------------------
//  Footer Start  
$footer = '
    <!-- /content -->
    </div>
    <footer class="android-footer mdl-mega-footer">
        <div class="mdl-mega-footer--top-section">
            <div class="mdl-mega-footer--left-section">';
        if(!empty($this->configs["tw_link"]))
            $footer .= '<a href="'.$this->configs["tw_link"].'" target="_blank" title="'.lang("Connect us at").' Twitter">'
                . '<img src="'.$_SERVER["DIR"].'/img/social/tw.png" alt="Twitter"/></a> &nbsp; ';
        if(!empty($this->configs["fb_link"]))
            $footer .= '<a href="'.$this->configs["fb_link"].'" target="_blank" title="'.lang("Connect us at").' Facebook">'
                . '<img src="'.$_SERVER["DIR"].'/img/social/fb.png" alt="Facebook"/></a> &nbsp; ';
        if(!empty($this->configs["gp_link"]))    
            $footer .= '<a href="'.$this->configs["gp_link"].'" target="_blank" title="'.lang("Connect us at").' Google+">'
                . '<img src="'.$_SERVER["DIR"].'/img/social/gp.png" alt="Google+"/></a> &nbsp; ';
        $footer .= '
        </div>
        <div class="mdl-mega-footer--right-section floater">
            <noindex class="material-icons">expand_less</noindex><br/>'.lang("Back to Top").'
        </div>
    </div>
    <div class="mdl-mega-footer--middle-section">
        <p class="mdl-typography--font-light">
        '.lang("Copyright").' <a href="http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'" title="'.$this->configs["description"].'">'
        .$_SERVER["HTTP_HOST"].'</a>, 2018.<br/><br/>'.lang("All rights reserved").'.
        </p><br/>
    </div>
    <div class="mdl-mega-footer--bottom-section">
        <a class="android-link android-link-menu mdl-typography--font-light" href="'.$_SERVER["DIR"].'/">'.lang("Home").'</a>
        <a class="android-link android-link-menu mdl-typography--font-light" href="'.$_SERVER["DIR"].'/privacy_policy">'.lang("Privacy Policy").'</a>
        <a class="android-link android-link-menu mdl-typography--font-light" href="'.$_SERVER["DIR"].'/terms_and_conditions">'.lang("Terms & Conditions").'</a>
        <a class="android-link android-link-menu mdl-typography--font-light" href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a>
    </div>';
$count = 0;
if(!empty($_SESSION["products"])) foreach($_SESSION["products"] as $key=>$value) if($value>0) $count++;
if($count) $footer .= engine::print_cart($count);
$footer .= '
    </footer>
  </div>
</div>'; 
//  Footer End   
}$this->content = $header.$this->content.$footer;