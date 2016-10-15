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
    
    if(!empty($_POST["new_message"]) && !empty($_POST["text"]) && !empty($_SESSION["user"]["id"])){
         engine::send_mail($this->configs["email"], $_SESSION["user"]["email"], $_SERVER["New message from"]." ".$_SERVER["HTTP_HOST"], str_replace("\n", "<br/>", $_POST["text"]));
         $this->activejs .= '
        alert("'.lang("Message sent successfully").'");
             ';
        $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
        . 'VALUES("7", "'.$_SESSION["user"]["id"].'", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "'.$_POST["text"].'")';
        engine::mysql($query);
    }
    
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
        <ul>';
    
    $count = 0;
    if(!empty($_SESSION["products"])){
        foreach($_SESSION["products"] as $key=>$value){
            if($value>0){
                $count++;
            }
        }
    }
    if($count>0){
        $header .= '<li><a href="'.$_SERVER["DIR"].'/order" id="purcases">'.lang("Cart").'<font class="purcases_count"> ('.$count.')</font></a></li>';
    }else{
        $header .= '<li><a href="'.$_SERVER["DIR"].'/order" id="purcases" style="display:none;">'.lang("Cart").'<font class="purcases_count"></font></a></li>';
    }
    
    $header .= '
            <li><a href="'.$_SERVER["DIR"].'/content">'.lang("Content").'</a></li>
            <li><a href="'.$_SERVER["DIR"].'/product">'.lang("Products").'</a></li>
            '; 
    if(empty($_SESSION["user"]["id"])){
        $header .= '<li class="last"><a href="'.$_SERVER["DIR"].'/register" class="btn">'.lang("Sign Up").'</a></li>
            <li class="last" id="last"><a target="_parent" class="btn" onClick="event.preventDefault(); show_login_form();" href="'.$_SERVER["DIR"].'/login">'.lang("Login").'</a></li>';
    }else{
        $header .= '<li class="last"><a href="'.$_SERVER["DIR"].'/account" class="btn">'.lang("My Account").'</a></li>
            <li class="last"  id="last"><a href="#" onClick="logout();" class="btn">'.lang("Logout").'</a></li>';
    }
    $header .= '
        </ul>
    </div>
    <div id="searchIcon" onClick=\'search("'.lang("Search").'");\'>
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAB5lBMVEXExMTExcXNzc3Nz8/R0dHU1NTV1dXX2NjY2NjZ2tra2tra29vc3d3d3d3d3t7e39/f4ODh4eHi4uLi4+Pj5OTk5OTl5eXl5ubm5ubn5+fn6Ojo6Ojo6enq6urq6+vr6+vr7Ozs7Ozt7u7u7u7u7+/v7+/v8PDw8PDw8fHx8fHy8vLz8/P09PT19fX29vb39/f3+Pj4+Pj5+fn6+vr7+/v8/Pz9/f3+/v7////9/f3x8fH19fXh4eHl5eX19fXs7Oz7+/v39/f////f39/z8/PNzc3k5OTz8/Pj4+Pl5eXr6+vs7Oz09PTq6urx8fHh4uLq6urz8/Pr7Ozj4+Pw8PDq6urf39/l5ubs7Ozf39/i4uLc3NzNzs7U1NTs7Ozs7Ozq6+vg4ODa2trc3Nzp6ure39/l5eXr7OzX19fg4ODh4uLl5eXW1tbW19fm5ubY2NjExcXQ0NDR0tLj5OTX19ff39/h4uLc3d3g4eHV1dXY2Njc3d3Nzc3U1NTU1NTY2NjQ0NDV1tbX2NjX19fR0tLS09PS09PPz8/Q0dHOz8/Ozs7Nzc3IysrLzMzFxsbKysrKy8vIyMjLy8vGxsbGx8fLzMzExMTExcXFxcXFxsbGxsbGx8fHx8fHyMjIyMjIycnKy8vLzMwWX0n3AAAAlnRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABBxAVFxccHyAhIiQpKSssLDAwMjM0Nj4/QURFS01aWl5eYmNjZ21vd319fYOEhIuLjY2Pj5CSlZaWmJmgoqWwtbi6vcPExsjIytbY5Onr7PD1+Pn6+/v7/P3+/v5+5tEqAAABi0lEQVQokaXRZ1MTURQG4EUSwCBFMKCBGAKSsCkgIZc9FEWqHRVEQCmCBVQQVHqzAIIUBYU32asU/6m7jMzcjYEvnpkzd+Y8X+55j0THlPR/yMiX4aztHBzouF6UbVcYEzFI7M7crzCg7rxv9kQgoz6Ow3pOigHpsT7d/jDzTX+fUkDEup8A7790xlzZq+vVIhF7tElrdnKqKdHRxkOY8os4CUyTbE06YZFpHPhdIeIXqC1ej8didrjcdznUBhHXsXcjJyv3bB4VBqrXwOtFXAFvkgu88RZfvufmFvg1EWeBMQrkmcyZfnoH7F8W8Rmwc8+VGXcyxX1rD/ho+G2jltzXdm/CKfvD79pWt/MNCb3Ud19883YBCP94TcUiltLo32BDKjavKIb4guR9tKzdRD3oT8ZsGZ0/V/5gaHj4xTzUELoiju1Ic9qyYiS5ZhccG2URmO60psdIGfQK4RC6o6AkXWDKElSsRsPYkkJ2Xzst/sU06bRysZgmgM/R0EaljIJPRqoMCR1Rx+IfQvvbbkQLYs8AAAAASUVORK5CYII=" style="height: 25px;" />
        <form id="search_form" method="GET" action="'.$_SERVER["DIR"].'/search/"><input type="hidden" id="query" name="q" value="" /></form>
    </div>
    <a id="menuIcon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAATBAMAAACJlYuFAAAAD1BMVEUAAAD///+xsbG5ubnMzMxsasSQAAAABXRSTlMAAC5YoFQMGe8AAAAsSURBVAiZY3BBAAcG3BxjBDBgEIQCBhDAzVFCAAU8RqNwUPQQaQ+K24izBwAr9Cer3tlXcgAAAABJRU5ErkJggg==" alt="'.lang("Show navigation").'"></a>
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
        <li><a href="'.$_SERVER["DIR"].'/login" onClick="event.preventDefault(); show_login_form();">'.lang("Login").'</a></li>
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
        . '<input type="submit" name="new_message" class="btn" style="width: 270px;" value="'.lang("Send message").'"  />';
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
<div id="floater" alt="'.lang("Up").'"> </div>';
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