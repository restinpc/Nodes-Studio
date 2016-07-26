<?php
/*
$this->title - CMS page title
$this->content - CMS page HTML data
$this->menu - CMS page HTML navigation
*/

$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=content">'.lang("Content").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=users">'.lang("Users").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=language">'.lang("Language").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=attendance">'.lang("Attendance").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=files">'.lang("Files").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=config" >'.lang("Config").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=backend" >'.lang("Backend").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=templates" >'.lang("Templates").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=logs">'.lang("Logs").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=updates">'.lang("Updates").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=catch">'.lang("Catch").'</a> ';
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=errors">'.lang("Errors").'</a> ';
/*  
TODO - Your code here
----------------------------
$this->menu .= '<a class="mdl-navigation__link" href="'.$_SERVER["DIR"].'/admin/?mode=custom_url">Custom title</a>';
----------------------------
*/

function admin_main_page(){
    return '<div style="width: 100%; margin: auto; max-width: 610px; min-width: 280px;">'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=content"><img src="'.$_SERVER["DIR"].'/img/cms/content.jpg" /><br/>'.lang("Content").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=users"><img src="'.$_SERVER["DIR"].'/img/cms/users.jpg" /><br/>'.lang("Users").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=language"><img src="'.$_SERVER["DIR"].'/img/cms/language.jpg" /><br/>'.lang("Language").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=attendance"><img src="'.$_SERVER["DIR"].'/img/cms/attendance.jpg" /><br/>'.lang("Attendance").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=files"><img src="'.$_SERVER["DIR"].'/img/cms/files.jpg" /><br/>'.lang("Files").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=config"><img src="'.$_SERVER["DIR"].'/img/cms/config.jpg" /><br/>'.lang("Config").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=backend"><img src="'.$_SERVER["DIR"].'/img/cms/backend.jpg" /><br/>'.lang("Backend").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=templates"><img src="'.$_SERVER["DIR"].'/img/cms/templates.jpg" /><br/>'.lang("Templates").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=logs"><img src="'.$_SERVER["DIR"].'/img/cms/logs.jpg" /><br/>'.lang("Logs").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=updates"><img src="'.$_SERVER["DIR"].'/img/cms/updates.jpg" /><br/>'.lang("Updates").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=catch"><img src="'.$_SERVER["DIR"].'/img/cms/catch.jpg" /><br/>'.lang("Catch").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=errors"><img src="'.$_SERVER["DIR"].'/img/cms/errors.jpg" /><br/>'.lang("Errors").'</a></div>'
    . '</div><div style="clear:both; height: 10px;"></div>';
}
/*  
TODO - Your code here
----------------------------
function admin_custom(){
    $fout = '';
    //.............
    return $fout;
}
----------------------------
*/

switch($_GET["mode"]){
/*  
TODO - Your code here
----------------------------
case "custom_url":
    $this->title = "Custom title";
    $this->content = admin_custom(); 
    break;
----------------------------
*/
case "content":
    $this->title = lang("Content");
    $this->content = $this->admin_content(); 
    break;
case "users":       
    $this->title = lang("Users");
    $this->content = $this->admin_users(); 
    break;
case "language":
    $this->title = lang("Language");
    $this->content = $this->admin_language(); 
    break;
case "attendance":       
    $this->title = lang("Attendance");
    $this->content = $this->admin_attendance(); 
    break;
case "files":
    $this->title = lang("Files");
    $this->content = $this->admin_files(); 
    break;
case "config":
    $this->title = lang("Config");
    $this->content = $this->admin_config(); 
    break;
case "backend":
    $this->title = lang("Backend");
    $this->content = $this->admin_backend(); 
    break;
case "templates":
    $this->title = lang("Templates");
    $this->content = $this->admin_templates(); 
    break;
case "logs":
    $this->title = lang("Logs");
    $this->content = $this->admin_logs(); 
    break;
case "updates":
    $this->title = lang("Updates");
    $this->content = $this->admin_updates(); 
    break;
case "catch":
    $this->title = lang("Catch");
    $this->content = $this->admin_catch(); 
    break;
case "errors":
    $this->title = lang("Errors");
    $this->content = $this->admin_errors(); 
    break;
default:
    $this->title = lang("Admin");
    $this->content = admin_main_page(); 
}