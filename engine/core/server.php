<?php
/**
* Web server.
* @path /engine/core/server.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
class server{
static $max_timestamp_range = 60;
static $lobby_lifetime = 600;
//------------------------------------------------------------------------------
static function create_lobby($scene_id){
    $query = 'SELECT * FROM `nodes_server_lobby` WHERE `last` > "'.(date("U")-server::$lobby_lifetime).'" AND `active` = 1 AND `scene_id` = "'.$scene_id.'"';
    $res = engine::mysql($query);
    $lobby = mysqli_fetch_array($res);
    if(!empty($_SESSION["user"]["id"])){
        $user_id = $_SESSION["user"]["id"];
    }else{
        $user_id = -$_SESSION["user"]["anonim"];
    }
    if(empty($lobby)){
        $query = 'INSERT INTO `nodes_server_lobby`(`scene_id`, `user_id`, `date`, `last`, `active`) '
                . 'VALUES("1", "'.$user_id.'", "'.date("U").'", "'.date("U").'", 1)';
        engine::mysql($query);
        $lobby_id = mysqli_insert_id($_SERVER["sql_connection"]);
        $query = 'SELECT * FROM `nodes_server_object` WHERE `scene_id` = "'.$scene_id.'" AND `type` = 1 ORDER BY `object_id` ASC';
        $res = engine::mysql($query);
        $model = mysqli_fetch_array($res);
        $query = 'INSERT INTO `nodes_server_client`(`lobby_id`, `user_id`, `object_id`, `online`) '
                . 'VALUES("'.$lobby_id.'", "'.$user_id.'", "'.$model["object_id"].'", "'.date("U").'")';
        engine::mysql($query);
        $query = 'SELECT * FROM `nodes_server_lobby` WHERE `id` = "'.$lobby_id.'"';
        $res = engine::mysql($query);
        $lobby = mysqli_fetch_array($res);
    }else{
        $query = 'SELECT * FROM `nodes_server_client` WHERE `lobby_id` = "'.$lobby["id"].'" AND `user_id` = "'.$user_id.'"';
        $res = engine::mysql($query);
        $client = mysqli_fetch_array($res);
        if(empty($client)){
            $query = 'SELECT * FROM `nodes_server_object` WHERE `scene_id` = "'.$scene_id.'" AND `type` = 1 ORDER BY `id` ASC';
            $res = engine::mysql($query);
            $object_id = 0;
            while($data = mysqli_fetch_array($res)){
                $query = 'SELECT * FROM `nodes_server_client` WHERE `object_id` = "'.$data["object_id"].'"';
                $r = engine::mysql($query);
                $d = mysqli_fetch_array($r);
                if(empty($d)){
                    $object_id = $data["object_id"];
                    break;
                }
            }
            $query = 'INSERT INTO `nodes_server_client`(`lobby_id`, `user_id`, `object_id`, `online`) '
                    . 'VALUES("'.$lobby["id"].'", "'.$user_id.'", "'.$object_id.'", "'.date("U").'")';
            engine::mysql($query);
        }
        $query = 'SELECT * FROM `nodes_server_lobby` WHERE `id` = "'.$lobby["id"].'"';
        $res = engine::mysql($query);
        $lobby = mysqli_fetch_array($res);
    }
    return $lobby;
}
//------------------------------------------------------------------------------
/**
* Upload data from client to database
*/
static function upload_data($lobby_id, $timestamp, $position, $rotation){
    $query = 'SELECT * FROM `nodes_server_data` WHERE `lobby_id` = "'.$lobby_id.'" ORDER BY `timestamp` DESC LIMIT 0, 1';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(!empty($_SESSION["user"]["id"])){
        $user_id = $_SESSION["user"]["id"];
    }else{
        $user_id = -$_SESSION["user"]["anonim"];
    }
    if($data["timestamp"] <= $timestamp+server::$max_timestamp_range){
        $query = 'INSERT INTO `nodes_server_data`(lobby_id, user_id, position, rotation, timestamp) '
                . 'VALUES("'.$lobby_id.'", "'.$user_id.'", "'.$position.'", "'.$rotation.'", "'.$timestamp.'")';
        engine::mysql($query);
    } die(microtime(1));
}
//------------------------------------------------------------------------------
/**
* Load scene from database to client
*/
static function load_scene($lobby_id){
    $fout = '';
    $objects = array();
    $objects["cmd"] = "load";
    $objects["timestamp"] = microtime(1);
    $objects["count"] = 0;
    if(!empty($_SESSION["user"]["id"])){
        $user_id = $_SESSION["user"]["id"];
    }else{
        $user_id = -$_SESSION["user"]["anonim"];
    }
    $query = 'SELECT * FROM `nodes_server_lobby` WHERE `id` = "'.$lobby_id.'"';
    $res = engine::mysql($query);
    $lobby = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_server_data` WHERE `lobby_id` = "'.$lobby_id.'" ORDER BY `timestamp` ASC';
    $res = engine::mysql($query);
    while($data = mysqli_fetch_array($res)){
        if(!is_array($objects["objects"])){
            $objects["objects"]=array();
        }
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$data["user_id"].'"';
        $r = engine::mysql($query);
        $user = mysqli_fetch_array($r);
        $query = 'SELECT * FROM `nodes_server_client` WHERE `lobby_id` = "'.$lobby_id.'" AND `user_id` = "'.$user["id"].'"';
        $r = engine::mysql($query);
        $client = mysqli_fetch_array($r);
        $query = 'SELECT * FROM `nodes_server_object` WHERE `object_id` = "'.$client["object_id"].'"';
        $r = engine::mysql($query);
        $dd = mysqli_fetch_array($r);
        $position = explode(' ', $data["position"]);
        if($client["user_id"] != $user_id){
            $object_id = $dd["id"];
        }else{
            $object_id = 'rig';
        }
        array_push($objects["objects"], array(
            "user_id"=>$user["id"],
            "object_id"=>$object_id, 
            "position"=>array($position[0], $position[1], $position[2]), 
            "rotation"=>$data["rotation"]
        ));
    }
    $objects["temp"] = array();
    for($i = 0; $i < count($objects["objects"]); $i++){
        $model = $objects["objects"][$i]["object_id"];
        $objects["temp"][$model] = $objects["objects"][$i];
    }
    $objects["fout"] = array();
    foreach($objects["temp"] as $key=>$value){
        array_push($objects["fout"], $value);
    }
    unset($objects["objects"]);
    unset($objects["temp"]);
    $objects["count"] = count($objects["fout"]);
    $fout = json_encode($objects);
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Download data from database to client
*/
static function download_data($lobby_id, $timestamp){
    $fout = '';
    $query = 'SELECT * FROM `nodes_server_lobby` WHERE `id` = "'.$lobby_id.'"';
    $res = engine::mysql($query);
    $lobby = mysqli_fetch_array($res);
    $objects = array();
    $objects["cmd"] = "action";
    $objects["timestamp"] = microtime(1);
    $objects["fout"] = array();
    if(!empty($_SESSION["user"]["id"])){
        $user_id = $_SESSION["user"]["id"];
    }else{
        $user_id = -$_SESSION["user"]["anonim"];
    }
    $query = 'SELECT DISTINCT(`user_id`) FROM `nodes_server_data` '
                . 'WHERE `lobby_id` = "'.$lobby_id.'" '
                . 'AND `timestamp` >= "'.$timestamp.'" '
                . 'AND `user_id` <> "'.$user_id.'" ';
    $rr = engine::mysql($query);
    while($dd = mysqli_fetch_array($rr)){
        $query = 'SELECT * FROM `nodes_server_data` '
                . 'WHERE `user_id` = "'.$dd["user_id"].'" '
                . 'ORDER BY `timestamp` DESC LIMIT 0, 1';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        $query = 'SELECT * FROM `nodes_server_client` WHERE `user_id` = "'.$data["user_id"].'" AND `lobby_id` = "'.$lobby_id.'"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        $query = 'SELECT * FROM `nodes_server_object` WHERE `object_id` = "'.$d["object_id"].'"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        $position = explode(' ', $data["position"]);
        array_push($objects["fout"], array(
            "user_id"=>$data["user_id"],
            "object_id"=>$d["id"],
            "position"=>array($position[0], $position[1], $position[2]), 
            "rotation"=>$data["rotation"]
        ));
    }
    $objects["count"] = count($objects["fout"]);
    $fout = json_encode($objects);
    return $fout;
}
//------------------------------------------------------------------------------
}