<?php
/**
* Print account navigation menu.
* @path /engine/core/account/print_navigation.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param object $site Site class object.
* @param string $title Page title.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_navigation($site, lang("Profile")); </code>
*/
function print_navigation($site, $title){
    $fout = '<div class="profile_menu">
        <div class="container">
            <span class="profile_menu_item show_all selected" ><a>'.$title.'</a>
                <div class="fr nav_button" alt="'.lang("Show navigation").'">&nbsp;</div>    
            </span>';
    
    $query = 'SHOW TABLES LIKE "nodes_product"';
    $res = engine::mysql($query);
    $is_products = 0;
    if(mysql_num_rows($res)){
        $is_products = 1;
        $query = 'SELECT * FROM `nodes_order` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" AND `status` > 0';
        $res = engine::mysql($query);
        $pcount = 0;
        while($data = mysql_fetch_array($res)){
            $query = 'SELECT COUNT(*) FROM `nodes_product_order` WHERE `order_id` = "'.$data["id"].'" AND `status` = 1';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $pcount += $d[0];
        }
    }
    $query = 'SELECT COUNT(*) FROM `nodes_transaction` WHERE `status` = 1 AND `user_id` = "'.$_SESSION["user"]["id"].'"';
    $res = engine::mysql($query);
    $fcount = mysql_fetch_array($res);
    $finance_count = '';
    if($fcount[0]>0) $finance_count = ' ('.$fcount[0].')';
    $purcases_count = '';
    if($pcount>0) $purcases_count = ' ('.$pcount.')';
    $button = '<span class="profile_menu_item '.($title == lang("Finances")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_1").click();\'>'
        . '<a id="profile_menu_link_1" href="'.$_SERVER["DIR"].'/account/finances">'.lang("Finances").$finance_count.'</a></span>';
    if($is_products){
    $button .= '<span class="profile_menu_item '.($title == lang("Purchases")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_2").click();\'>'
            . '<a id="profile_menu_link_2" href="'.$_SERVER["DIR"].'/account/purchases">'.lang("Purchases").$purcases_count.'</a></span>';
    }
    $query = 'SELECT COUNT(*) FROM `nodes_inbox` WHERE `to` = "'.$_SESSION["user"]["id"].'" AND `readed` = 0';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $count = '';
    if($data[0]>0) $count = ' ('.$data[0].')';
    $fout .= '
    <span class="profile_menu_item '.($title == lang("Profile")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_0").click();\'>'
    . '<a id="profile_menu_link_0" href="'.$_SERVER["DIR"].'/account">'.lang("Profile").'</a></span>';
    if($_SESSION["user"]["id"]=="1"){
        $fout .= '
            <span class="profile_menu_item" onClick=\'document.getElementById("profile_menu_link_1").click();\'>'
            . '<a id="profile_menu_link_1" href="'.$_SERVER["DIR"].'/admin">'.lang("Admin").'</a></span>
                <span class="profile_menu_item" onClick=\'document.getElementById("profile_menu_link_2").click();\'>'
            . '<a id="profile_menu_link_2" href="http://nodes-studio.com" target="_blank">'.lang("About").'</a></span>';
    }else{
        $fout .= $button;
    }
    $fout .= '
        <span class="profile_menu_item '.($title == lang("Messages")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_5").click();\'>'
        . '<a id="profile_menu_link_5" href="'.$_SERVER["DIR"].'/account/inbox">'.lang("Messages").$count.'</a></span>';
    $fout .= '
        <span class="profile_menu_item '.($title == lang("Settings")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_6").click();\'>'
        . '<a id="profile_menu_link_6" href="'.$_SERVER["DIR"].'/account/settings">'.lang("Settings").'</a></span>';
    $fout .= '<span class="profile_menu_item" onClick=\'document.getElementById("profile_menu_link_7").click();\'>'
            . '<a id="profile_menu_link_7" href="#" onClick="logout();">'.lang("Logout").'</a></span>'
        . '</div>'
    . '</div>';
    return $fout;
}