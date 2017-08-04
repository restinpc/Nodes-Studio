<?php
/**
* Print admin main page.
* @path /engine/core/admin/print_admin.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $cms->site - Site class object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin($cms); </code>
*/
function print_admin($cms){
    return '
<div class="document980">
<div class="two_columns">
    <section class="top_right_column">
        <div class="right_column_block single_block admin_main_page_right">
            <div class="admin_main_page_right_block">
                <b class="utc_date" alt="'.date("U").'">'.date("d/m/Y H:i").' '.  date_default_timezone_get().'</b><br/>
                '.lang("Engine version").': <b>'.$cms->statistic["version"].'</b><br/>
                '.lang("Total pages").': <b>'.$cms->statistic["pages"].'</b><br/>
                '.lang("Total articles").': <b>'.$cms->statistic["articles"].'</b><br/>
                '.lang("Total products").': <b>'.$cms->statistic["products"].'</b><br/>
                '.lang("Total users").': <b>'.$cms->statistic["users"].'</b><br/>
                '.lang("Total comments").': <b>'.$cms->statistic["comments"].'</b><br/>
                '.lang("Visitors per day").': <b>'.$cms->statistic["visitors"].'</b><br/>
                '.lang("Views per day").': <b>'.$cms->statistic["views"].'</b><br/>
                '.lang("Perfomance").': <b>'.$cms->statistic["perfomance"].'</b><br/>
                '.lang("Cron status").': <b>'.$cms->statistic["cron"].'</b><br/>
            </div>
        </div>
    </section>
    <section class="left_column b0">'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=pages"><img src="'.$_SERVER["DIR"].'/img/cms/pages.jpg" /><br/>'.lang("Pages").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=content"><img src="'.$_SERVER["DIR"].'/img/cms/content.jpg" /><br/>'.lang("Content").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=products"><img src="'.$_SERVER["DIR"].'/img/cms/products.jpg" /><br/>'.lang("Products").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=users"><img src="'.$_SERVER["DIR"].'/img/cms/users.jpg" /><br/>'.lang("Users").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=orders"><img src="'.$_SERVER["DIR"].'/img/cms/orders.jpg" /><br/>'.lang("Orders").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=finance"><img src="'.$_SERVER["DIR"].'/img/cms/finance.jpg" /><br/>'.lang("Finance").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=language"><img src="'.$_SERVER["DIR"].'/img/cms/language.jpg" /><br/>'.lang("Language").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=attendance"><img src="'.$_SERVER["DIR"].'/img/cms/attendance.jpg" /><br/>'.lang("Attendance").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=files"><img src="'.$_SERVER["DIR"].'/img/cms/files.jpg" /><br/>'.lang("Files").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=config"><img src="'.$_SERVER["DIR"].'/img/cms/config.jpg" /><br/>'.lang("Config").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=backend"><img src="'.$_SERVER["DIR"].'/img/cms/backend.jpg" /><br/>'.lang("Backend").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=templates"><img src="'.$_SERVER["DIR"].'/img/cms/templates.jpg" /><br/>'.lang("Templates").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=perfomance"><img src="'.$_SERVER["DIR"].'/img/cms/perfomance.jpg" /><br/>'.lang("Perfomance").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=outbox"><img src="'.$_SERVER["DIR"].'/img/cms/outbox.jpg" /><br/>'.lang("Outbox").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=logs"><img src="'.$_SERVER["DIR"].'/img/cms/logs.jpg" /><br/>'.lang("Logs").'</a></div>'
    . '<div class="admin_menu_icon"><a href="'.$_SERVER["DIR"].'/admin/?mode=errors"><img src="'.$_SERVER["DIR"].'/img/cms/errors.jpg" /><br/>'.lang("Errors").'</a></div>'
    . '</section>
    <div class="clear"></div>
</div>
</div>';
}

