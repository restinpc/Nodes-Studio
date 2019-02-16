<?php
/**
* Generate multiplayer player data.
* @path /engine/core/aframe/multiplayer_player.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @param int $lobby_id @mysql->nodes_server_lobby.
* @return object Returns player data.
* @usage <code> engine::multiplayer_player(1); </code>
*/
function multiplayer_player($lobby_id){
    $query = 'SELECT * FROM `nodes_server_lobby` WHERE `id` = "'.$lobby_id.'"';
    $res = engine::mysql($query);
    $lobby = mysqli_fetch_array($res);
    if(!empty($_SESSION["user"]["id"])){
        $user_id = $_SESSION["user"]["id"];
    }else{
        $user_id = -$_SESSION["user"]["anonim"];
    }
    $query = 'SELECT * FROM `nodes_server_client` WHERE `lobby_id` = "'.$lobby_id.'" AND `user_id` = "'.$user_id.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_server_object` WHERE `object_id` = "'.$user["object_id"].'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    $position = explode(" ", $data["position"]);
    $rotation = explode(" ", $data["rotation"]);
    $scale = explode(" ", $data["scale"]);
    $player = array(
        "user_id" => intval($user_id),
        "object_id" => $data["object_id"],
        "position" => ($position[0])." ".($position[1]+10)." ".($position[2]),
        "model-position" => $data["position"],
        "rotation" => $data["rotation"],
        "scale" => "1 1 1",
        "collada-model" => $data["collada-model"]
    );
    return $player;
}