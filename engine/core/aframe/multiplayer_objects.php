<?php
/**
* Generate multiplayer objects data.
* @path /engine/core/aframe/multiplayer_objects.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @param int $scene_id Scene ID.
* @param int $object_id Object ID.
* @return array Returns array with models data.
* @usage <code> engine::multiplayer_objects(1, 1); </code>
*/
function multiplayer_objects($lobby_id, $object_id){
    $query = 'SELECT * FROM `nodes_server_client` WHERE `lobby_id` = "'.$lobby_id.'" AND `object_id` <> "'.$object_id.'"';
    $res = engine::mysql($query);
    $fout = array();
    while($data = mysqli_fetch_array($res)){
        $query = 'SELECT * FROM `nodes_server_object` WHERE `object_id` = "'.$data["object_id"].'"';
        $r = engine::mysql($query);
        $d = mysqli_fetch_array($r);
        array_push($fout, $d);
    }
    return $fout;
}