<?php
/**
* Print admin templates page.
* @path /engine/core/admin/print_admin_templates.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @var $cms->site - Site object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin_templates($cms); </code>
*/
function print_admin_templates($cms){
    if(!empty($_POST["template"])){
        $query = 'UPDATE `nodes_config` SET `value` = "'.$_POST["template"].'" WHERE `name` = "template"';
        engine::mysql($query);
        die('<script>window.location = "'.$_SERVER["DIR"].'/admin/?mode=templates&'.rand(0,1000).'";</script>');
    }else if(!empty($_POST["new_template"])){
        $php = '<?php
/**
* '.ucfirst($_POST["new_template"]).' template file.
* @path /template/'.$_POST["new_template"].'/template.php
*
* @name    Nodes Studio    @version 2.0.2
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
$header = \'<header>
<ul>
    <li><a href="\'.$_SERVER["DIR"].\'/">\'.lang("Home").\'</a></li>
    <li><a href="\'.$_SERVER["DIR"].\'/content">\'.lang("Content").\'</a></li>
    <li><a href="\'.$_SERVER["DIR"].\'/product">\'.lang("Products").\'</a></li>
    <li><a href="\'.$_SERVER["DIR"].\'/privacy_policy">\'.lang("Privacy Policy").\'</a></li>
    <li><a href="\'.$_SERVER["DIR"].\'/terms_and_conditions">\'.lang("Terms & Conditions").\'</a></li>\';
if(!empty($_SESSION["user"]["id"])){
    $header .= \'<li><a href="\'.$_SERVER["DIR"].\'/account">\'.lang("My Account").\'</a></li>\';
}else{
    $header .= \'<li><a href="\'.$_SERVER["DIR"].\'/register">\'.lang("Sign Up").\'</a></li>
    <li><a href="\'.$_SERVER["DIR"].\'/login" target="_parent" onClick="event.preventDefault(); login();">\'.lang("Login").\'</a></li>\';
}
$header .= \'</ul>
</header>
<!-- content -->\';
//  Header End  
//------------------------------------------------------------------------------
//  Footer Start
$footer = \'
<!-- /content -->
<footer>
    <p><nobr>\'.lang("Copyright").\' <a href="http://\'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].\'">\'
    .$_SERVER["HTTP_HOST"].\'</a>, 2016.</nobr> <nobr>\'.lang("All rights reserved").\'</nobr>.</p>
</footer>\';
//  Footer End
} $this->content = $header.$this->content.$footer;';
        
        $css = '/**
* '.ucfirst($_POST["new_template"]).' template stylesheets file.
* @path /template/'.$_POST["new_template"].'/template.css
*
* @name    Nodes Studio    @version 2.0.2
* @license http://nodes-studio.com/license.txt GNU Public License
*/
@charset "UTF-8";
html {
    width: 100%;
    height: 100%;
    -ms-touch-action: manipulation;
    touch-action: manipulation; 
}
.nodes #content{
    background: #fff;
}
.nodes a{ 
    color: #4473ba; 
}
.nodes a:hover{ 
    color: #333; 
}
.nodes header{
    padding-bottom: 20px;
    padding-top: 15px;
    background: #efefef;
}
.nodes header ul{
    display: table;
    margin: 0px auto;
}
.nodes header li{
    display: table-cell;
    text-align: center;
    padding: 10px;
}
.nodes footer{
    padding-bottom: 20px;
    padding-top: 20px; 
    background: #333;
    color: #fff;
}
.nodes footer p{
    padding: 5px;
    line-height: 2.0;
}
/* Device Layout */
@media (max-width: 980px) { 
    /* Netbook */
}
@media (max-width: 640px) {
    /* Tablet */
    .nodes header li{
        display: block;
    }
}
@media (max-width: 320px) { 
    /* Smartphone */
}';
        
        $js = '/** 
*  '.ucfirst($_POST["new_template"]).' template JavaScript file.
* @path /template/'.$_POST["new_template"].'/template.js
*
* @name    Nodes Studio    @version 2.0.2
* @license http://nodes-studio.com/license.txt GNU Public License
*/
(function() {
"use strict";
    // TODO your code here
    if(window.jQuery){
        jQuery(function() {
            // TODO your jQuery code here
        });
    }
}());';
        mkdir("template/".$_POST["new_template"]);
        $file = @fopen("template/".$_POST["new_template"]."/template.php", 'w');
        fwrite($file, $php);
        fclose($file);
        $file = @fopen("template/".$_POST["new_template"]."/template.css", 'w');
        fwrite($file, $css);
        fclose($file);
        $file = @fopen("template/".$_POST["new_template"]."/template.js", 'w');
        fwrite($file, $js);
        fclose($file);
    }else if(!empty($_POST["name"])){
        require_once("engine/core/manage_files.php");
        manage_files::delete('template/'.$_POST["name"]);
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';   
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $template = $data["value"];
    $fout .= '<div class="document420">
<div class="table">
<form method="POST" id="template"><input type="hidden" name="template" value="'.$template.'" id="new_template" /></form>
<table id="table" class="lh2">
        ';
    $i = 0;
    $dirct = "template/";
    $hdl = opendir($dirct) or die("can't open direct");
    while ($file_name = readdir($hdl)){
        if (($file_name != ".") && ($file_name != "..") && is_dir($dirct.$file_name)){
            $i++;
            if($file_name==$template) $name = '<b>'.$file_name.'</b>';
            else $name = '<a href="#" onClick=\'document.getElementById("new_template").value="'.$file_name.'"; document.getElementById("template").submit();\'>'.$file_name.'</a>';
            $fout .= '<tr><td align=left class="fs16"><form method="POST" id="form_'.$i.'"><input type="hidden" name="name" value="'.$file_name.'" /></form>'.$name;
            $fout .= '</td>'
                    . '<td align=left>'
                    . '<select class="input w100p" onChange=\'if(this.value==1){if(confirm("'.lang("Are you sure?").'")){document.getElementById("form_'.$i.'").submit();}}else if(this.value!=0){show_editor(this.value);}\'>
                        <option value="0">'.lang("Select an action").'</option>';
            if($file_name!="default"){
                $fout .= '<option value="1">'.lang("Delete template").'</option>';
            }
            $fout .= '  <option value="template/'.$file_name.'/template.php">'.lang("View source").' PHP</option>
                        <option value="template/'.$file_name.'/template.js">'.lang("View source").' JS</option>
                        <option value="template/'.$file_name.'/template.css">'.lang("View source").' CSS</option>
                    </select></td></tr>';
        }
     }
     if(!$i){
        $fout = '<div class="clear_block">'.lang("There is no templates").'</div>';
     }else{
        $fout .= '
</table><br/>
</div>';
     }
     $fout .= '<input id="button" type="button" name="load" value="'.lang("New template").'" class="btn w280" onClick=\'this.style.display="none";document.getElementById("form").style.display="block";\' />
    <form method="POST" ENCTYPE="multipart/form-data" id="form" class="hidden">
        '.lang("New template").'<br/><br/>
        <input type="text" class="input w280 pointer" required placeHolder="'.lang("Template name").'" title="'.lang("Template name").'" name="new_template" /><br/><br/>
        <input type="submit" class="btn w280" value="'.lang("Submit").'" />
    </form><br/>
    </div>
    ';
    return $fout;
}