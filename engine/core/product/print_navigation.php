<?php
/**
* Print product navigation menu.
* @path /engine/core/product/print_navigation.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
* @usage <code> engine::print_navigation($site, lang("Product")); </code>
*/
function print_navigation($site, $title){
    $i = 0;
    $fout = '<div class="profile_menu">
        <div class="container">
            <span vr-control id="span-show-nav" class="profile_menu_item show_all selected" ><a>'.$title.'</a>
                <div class="fr nav_button" alt="'.lang("Show navigation").'">&nbsp;</div>    
            </span>';
        $fout .= '
        <span vr-control id="profile_menu_span_'.$i.'" class="profile_menu_item '.($title == lang("Products")?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a vr-control id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/product">'. lang("Products").'</a></span>';
    $query = 'SELECT * FROM `nodes_product_data` WHERE `cat_id` = "1" AND `url` <> "" ORDER BY `order` DESC';
    $res = engine::mysql($query);
    while($data = mysqli_fetch_array($res)){
        $fout .= '
        <span vr-control id="profile_menu_span_'.$i.'" class="profile_menu_item '.($title == $data["value"]?'selected':'').'" onClick=\'document.getElementById("profile_menu_link_'.$i.'").click();\'>'
        . '<a vr-control id="profile_menu_link_'.$i++.'" href="'.$_SERVER["DIR"].'/product/'.$data["url"].'">'.$data["value"].'</a></span>';
    }
        $fout .= '</div>'
    . '</div>';
    if($i) return $fout;
}   
