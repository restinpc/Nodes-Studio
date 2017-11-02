<?php
/**
 * Timeout page generator.
 * @path /engine/code/timeout.php
 *
 * @name    Nodes Studio    @version 2.0.3
 * @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
 * @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
 */
require_once("engine/nodes/session.php");
if(!empty($_COOKIE["token"]) && !isset($_SERVER["CRON"])){
    $_SESSION["display"] = "1";
    $query = 'SELECT `id`, `display` FROM `nodes_attendance` WHERE `token` = "'.$_COOKIE["token"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!$data["display"]){
        $query = 'UPDATE `nodes_attendance` SET `display` = "1" WHERE `id` = "'.$data["id"].'"';
        engine::mysql($query);
    }
}engine::error(504);

