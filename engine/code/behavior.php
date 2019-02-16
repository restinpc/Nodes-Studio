<?php
/**
* Behavioral information input script.
* @path /engine/code/behavior.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
//------------------------------------------------------------------------------
//  Receiving user patterns to MySQL DB
if(!empty($_POST["patterns"])){
    for($i = 0; $i<count($_POST["patterns"]); $i++){
        if(!empty($_POST["patterns"][$i][5])){
            $query = 'SELECT `id` FROM `nodes_attendance` WHERE `token` = "'.session_id().'" ORDER BY `id` DESC LIMIT 0, 1';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            $query = 'INSERT INTO `nodes_pattern`(`attendance_id`, `action`, `x`, `y`, `top`, `width`, `height`, `date`) '
                . 'VALUES("'.$data["id"].'", "'.$_POST["patterns"][$i][0].'", "'.$_POST["patterns"][$i][1].'", "'.$_POST["patterns"][$i][2].'", '
                . '"'.$_POST["patterns"][$i][3].'", "'.$_POST["patterns"][$i][4].'", "'.$_POST["patterns"][$i][5].'", "'.(date("U")-(10-intval($_POST["patterns"][$i][6]))).'")';
            engine::mysql($query);
        }
    }
}else engine::error(404);