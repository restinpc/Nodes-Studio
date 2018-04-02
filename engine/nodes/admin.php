<?php
/**
* Framework admin class.
* @path /engine/nodes/admin.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
class admin{
public $site;           // Primary Site object.
public $title;          // Page title.
public $content;        // Page HTML data.
public $onload;         // Page executable JavaScript code.
public $statistic;      // Array CMS statistic.
//------------------------------------------------------------------------------
/**
* Admin class constructor.
* @param object $site Admin Site object.
*/
function admin($site){
    $this->site = $site;
    if(!empty($_SESSION["user"]["email"]) && $_SESSION["user"]["admin"]=="1"){
        $this->statistic = array();
        $this->statistic["version"] = "2.0.".$site->configs["version"];
        $query = 'SELECT COUNT(`id`) FROM `nodes_cache` WHERE `title` <> ""';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["pages"] = $d[0];
        $query = 'SELECT COUNT(`id`) FROM `nodes_content`';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["articles"] = $d[0];
        $query = 'SELECT COUNT(`id`) FROM `nodes_product`';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["products"] = $d[0];
        $query = 'SELECT COUNT(`id`) FROM `nodes_comment`';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["comments"] = $d[0];
        $query = 'SELECT COUNT(`id`) FROM `nodes_user` WHERE `id` > 1';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["users"] = $d[0];
        $query = 'SELECT `date` FROM `nodes_attendance` ORDER BY `date` ASC LIMIT 0, 1';
        $res = engine::mysql($query);
        $first = mysql_fetch_array($res);
        $days = round((date("U")-$first["date"])/86400);
        if($days<1)$days=1;
        $query = 'SELECT COUNT(`id`) FROM `nodes_attendance` WHERE `display` = 1';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["views"] = round($d[0]/$days);
        $query = 'SELECT DISTINCT(`token`), `ip` FROM `nodes_attendance` WHERE `display` = 1';
        $res = engine::mysql($query);
        $visitors = array();
        while($d = mysql_fetch_array($res)){
            if(!in_array($d["ip"], $visitors)) array_push($visitors, $d['ip']);
        }
        $this->statistic["visitors"] = round(count($visitors));
        $query = 'SELECT AVG(`script_time`) FROM `nodes_perfomance` WHERE `script_time` > 0';
        $res = engine::mysql($query);
        $d = mysql_fetch_array($res);
        $this->statistic["perfomance"] = round($d[0],2);
        if($site->configs["cron"]){
            $this->statistic["cron"] = 'jQuery ';
        }
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron_exec"';
        $res = engine::mysql($query);
        $exec = mysql_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "cron_done"';
        $res = engine::mysql($query);
        $done = mysql_fetch_array($res);
        if($exec["value"]<date("U")-3600) $this->statistic["cron"] .= lang("Disabled");  
        else if($exec["value"]>$done["value"]+300) $this->statistic["cron"] .= lang("Error");   
        else $this->statistic["cron"] .= lang("Ok");
        if(!empty($_GET["mode"])){
            $this->title = lang(ucfirst($_GET["mode"]));
            $function = 'print_admin_'.$_GET["mode"];
        }else{
            $this->title = lang("Admin");
            $function = 'print_admin';
        }
        $this->content = engine::$function($this);
        $site->title = $this->title." - ".$site->title;
        $site->content = '<div class="profile_menu fs10">
            <div class="container">'.engine::print_admin_navigation($this).'</div>
        </div>
        <div class="admin_content">
            '.$this->content.'
        </div>';
        $site->onload .= 'admin_init(); '.$this->onload;
    }else{
        $this->content = engine::error(401);
    }
}}
