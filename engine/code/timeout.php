<?php
/**
 * Timeout page generator.
 * @path /engine/code/timeout.php
 *
 * @name    Nodes Studio    @version 2.0.1.9
 * @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
require_once("engine/nodes/session.php");
if(!empty($_COOKIE["token"]) && !isset($_SERVER["CRON"])){
    $_SESSION["display"] = "1";
    $query = 'SELECT `id`, `display`, `ref_id` FROM `nodes_attendance` WHERE `token` = "'.$_COOKIE["token"].'" ORDER BY `id` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!$data["display"]){
        $query = 'UPDATE `nodes_attendance` SET `display` = "1" WHERE `id` = "'.$data["id"].'"';
        engine::mysql($query);
    }
    if(!$data["ref_id"] && !empty($_GET["ref"])){
        $ref = engine::escape_string(urldecode($_GET["ref"]));
        if(mb_strpos($ref, $_SERVER["HTTP_HOST"]) === FALSE){
            $query = 'INSERT INTO `nodes_referrer`(name) VALUES("'.$ref.'")';
            engine::mysql($query);
            $ref_id = mysqli_insert_id($_SERVER["sql_connection"]);
            $query = 'UPDATE `nodes_attendance` SET `ref_id` = "'.$ref_id.'" WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
        }
    }
}engine::error(504);

