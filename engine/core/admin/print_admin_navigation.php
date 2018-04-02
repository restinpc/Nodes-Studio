<?php
/**
* Print admin navigation menu.
* @path /engine/core/admin/print_admin_navigation.php
* 
* @name    Nodes Studio    @version 2.0.8
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
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
    $query = 'SELECT `admin`.*, `access`.`access` FROM `nodes_access` AS `access` '
            . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`id` = `access`.`admin_id` '
            . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
            . 'ORDER BY `admin`.`id` ASC';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if($data["access"]){
            $fout .= '<span class="profile_menu_item '.($cms->title == lang($data["name"])?'selected':'').'" '
            . 'onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
            . '<a id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/admin/?mode='.$data["url"].'">'.lang($data["name"]).'</a></span>';
        }
    }
    return $fout;
}
