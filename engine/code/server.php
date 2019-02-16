<?php
/**
* Multiplayer server terminal.
* @path /engine/code/server.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
set_time_limit(60);
if($_GET["mode"]=="reset"){
    $query = 'TRUNCATE nodes_server_lobby';
    engine::mysql($query);
    $query = 'TRUNCATE nodes_server_data';
    engine::mysql($query);
    $query = 'TRUNCATE nodes_server_client';
    engine::mysql($query);
    die(date("U"));
}
if(!empty($_SESSION["user"]["id"]) || !empty($_SESSION["user"]["anonim"])){
    $timestamp = doubleval($_GET["timestamp"]);
    $lobby_id = intval($_GET["lobby_id"]);
    if(isset($_POST["position"]) && intval($_GET["lobby_id"]) > 0){
        $position = engine::escape_string($_POST["position"]);
        $rotation = engine::escape_string($_POST["rotation"]);
        server::upload_data($lobby_id, $timestamp, $position, $rotation);
    }else{
        if($timestamp == -1){
            $fout = server::load_scene($lobby_id);
        }else{
            $fout = server::download_data($lobby_id);
        }
        if(!empty($fout)) echo $fout;
    }
}else engine::error();