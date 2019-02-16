<?php /* Nodes Studio 2.0.8 script. Do not edit directly. 10/11/2018 */
/**
* Bootstrap template file.
* @path /template/bootstrap/template.php
*
* @name    Nodes Studio    @version 2.0.8
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
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
$header = '
<nav class="navbar navbar-fixed-top" id="sectionsNav">
    <div class="container">
        <div class="navbar-header">
            <noindex>
                <button type="button" class="navbar-toggle" data-toggle="collapse">
                <span class="sr-only">'.lang("Toggle navigation").'</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </noindex>
            <a class="navbar-brand" '.($_SERVER["app"]=="TRUE"?'':' href="/"').'><img class="logo-image" src="'.$_SERVER["DIR"].'/img/logo.png" /></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="'.$_SERVER["DIR"].'/aframe/panorama" title="'.lang("Panoramas").'">
                        <noindex class="material-icons">3d_rotation</noindex> 
                        <span class="navigation_caption">'.lang("Panoramas").'</span>
                    </a>
                </li>
                <li>
                    <a href="'.$_SERVER["DIR"].'/content" title="'.lang("Content").'">
                        <noindex class="material-icons">apps</noindex> 
                        <span class="navigation_caption">'.lang("Content").'</span>
                    </a>
                </li>
                <li>
                    <a href="'.$_SERVER["DIR"].'/product" title="'.lang("Products").'">
                        <noindex class="material-icons">credit_card</noindex> 
                        <span class="navigation_caption">'.lang("Products").'</span>
                    </a>
                </li>
                <li id="menu_5" class="dropdown">
                    <a onClick="dropdown_menu(5);" class="dropdown_item" title="'.lang("Language").'">
                        <noindex class="material-icons">language</noindex> 
                        <span class="navigation_caption">'.lang("Language").'</span> 
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="?lang=en" target="_parent">'.($_SESSION["Lang"]=="en"?'<noindex class="material-icons">done</noindex>':'').' English </a></li>
                        <li><a href="?lang=ru" target="_parent">'.($_SESSION["Lang"]=="ru"?'<noindex class="material-icons">done</noindex>':'').' Русский </a></li>
                    </ul>
                </li>';
if(empty($_SESSION["user"]["id"])){
    $header .= '<li>
                    <a onClick="hide_menu();" href="'.$_SERVER["DIR"].'/register" id="b1" class="btn btn-round">
                        <noindex class="material-icons">person_add</noindex> '.lang("Sign Up").'
                    </a>
                </li>
                <li>
                    <a target="_parent" onClick="event.preventDefault(); login(); hide_menu();" id="b2" href="'.$_SERVER["DIR"].'/login" class="btn btn-round">
                        <noindex class="material-icons">account_circle</noindex> '.lang("Login").'
                    </a>
                </li>
                ';
}else{
    if($_SESSION["user"]["admin"] != 1){
        $header .= '<li>
                    <a href="'.$_SERVER["DIR"].'/account"  onClick="hide_menu();"  id="b1" class="btn btn-round">
                        <noindex class="material-icons">person</noindex> '.lang("Account").'
                    </a>
                </li>';
    }else{
        $header .= '<li>
                    <a href="'.$_SERVER["DIR"].'/admin"  onClick="hide_menu();"  id="b1" class="btn btn-round">
                        <noindex class="material-icons">person</noindex> '.lang("Admin").'
                    </a>
                </li>';  
    }
    $header .= '
                <li>
                    <a target="_parent" id="b2"  onClick="hide_menu(); logout();" class="btn btn-round">
                        <noindex class="material-icons">directions_run</noindex> '.lang("Logout").'
                    </a>
                </li>
                ';

    if($_SERVER["app"]=="TRUE"){
            $header .= '
                <li>
                    <a target="_top" id="b3" onClick=\'window.location = "/#close";\'>
                        <noindex class="material-icons">close</noindex> '.lang("Close Application").'
                    </a>
                </li>
                ';
    }
}                     
$header .= '
            <li class="search-bar">
                <form class="navbar-form navbar-right" role="search" onSubmit="search(event);">
                    <div id="search_form" class="form-group form-white is-empty">
                      <input type="text" class="form-control" placeholder="'.lang("Search").'" id="search_text">
                    <span class="material-input"></span></div>
                    <button type="submit" class="btn btn-white btn-raised btn-fab btn-fab-mini" id="search_button">
                        <noindex class="material-icons">search</noindex>
                    </button>
                </form>
            </li>
            </ul>
        </div>
    </div>
</nav>
<div class="wrapper"></div>
<div id="content">
<!-- content -->';
//  Header End  
//------------------------------------------------------------------------------
//  Footer Start
$footer = '
<!-- /content -->
</div>';

$footer .= '
<footer  id="footer" class="footer footer-gray">
    <div class="container footer_top">
        <a class="footer-brand" href="/">'.$this->configs["name"].'</a>
        <ul class="pull-center">
            <li><a href="'.$_SERVER["DIR"].'/aframe/panorama">'.lang("Panoramas").'</a></li> 
            <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>  
            <li><a href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>    
            <li><a href="'.$_SERVER["DIR"].'/privacy_policy">'.lang("Privacy Policy").'</a></li>
            <li><a href="'.$_SERVER["DIR"].'/terms_and_conditions">'.lang("Terms and Conditions").'</a></li>
            <li class="hidden"><a href="'.$_SERVER["DIR"].'/sitemap.php" target="_blank">'.lang("Sitemap").'</a></li>
        </ul>
        <ul class="social-buttons pull-right">';
        if(!empty($this->configs["tw_link"])){
            $footer .= '
            <li>
                <a href="'.$this->configs["tw_link"].'" title="Twitter" target="_blank" class="btn btn-just-icon btn-simple">
                    <i class="fa fa-twitter"></i>
                </a>
            </li>';
        }
        if(!empty($this->configs["fb_link"])){
           $footer .= ' 
            <li>
                <a href="'.$this->configs["fb_link"].'" title="Facebook" target="_blank" class="btn btn-just-icon btn-simple">
                    <i class="fa fa-facebook-square"></i>
                </a>
            </li>';
        }
        if(!empty($this->configs["gp_link"])){
           $footer .= ' 
            <li>
                <a href="'.$this->configs["gp_link"].'" title="Google+" target="_blank" class="btn btn-just-icon btn-simple">
                    <i class="fa fa-google-plus"></i>
                </a>
            </li>';
        }
        $footer .= '
        </ul>
    </div>
    <div class="footer_bottom">
        <div class="container">
            <div class="copyright pull-center">'.lang("Copyright").' &copy; 2018. '.lang("All Rights Reserved").'.</div>
        </div>
    </div>
</footer>';
//  Footer End
}
$this->content = $header.$this->content.$footer;