<?php
/**
* Print admin navigation menu.
* @path /engine/core/admin/print_admin_navigation.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
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
* @usage <code> engine::print_admin_navigation($cms); </code>
*/
function print_admin_navigation($cms){
    $i=1;
    $fout = '<span class="profile_menu_item show_all selected" ><a>'.$cms->title.'</a>
            <div class="fr nav_button" alt="'.lang("Show navigation").'">&nbsp;</div>     
        </span><span class="profile_menu_item '.($cms->title == lang("Admin")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_0").click();\'>'
        . '<a id="profile_menu_link_0" href="'.$_SERVER["DIR"].'/admin">'.lang("Admin").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Pages")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=pages">'.lang("Pages").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Content")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=content">'.lang("Content").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Products")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=products">'.lang("Products").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Users")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=users">'.lang("Users").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Orders")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=orders">'.lang("Orders").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Finance")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=finance">'.lang("Finance").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Language")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=language">'.lang("Language").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Attendance")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=attendance">'.lang("Attendance").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Files")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=files">'.lang("Files").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Config")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=config">'.lang("Config").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Backend")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=backend">'.lang("Backend").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Templates")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=templates">'.lang("Templates").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Perfomance")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=perfomance">'.lang("Perfomance").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Outbox")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=outbox">'.lang("Outbox").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Logs")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=logs">'.lang("Logs").'</a></span>';
    $fout .= '<span class="profile_menu_item '.($cms->title == lang("Errors")?'selected':'').'" '
        . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode=errors">'.lang("Errors").'</a></span>';
    return $fout;
}
