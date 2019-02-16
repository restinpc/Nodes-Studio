<?php
/**
* Print multiplayer model entity.
* @path /engine/core/aframe/multiplayer_model.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @param object $model @mysql->nodes_server_object.
* @param int $lobby_id @mysql->nodes_server_lobby->id.
* @return string Returns content of entity.
* @usage <code> engine::multiplayer_model({object}, [1;2;3]); </code>
*/
function multiplayer_model($model, $lobby_id){
    $fout.= '<a-entity ';
    foreach($model as $key=>$value){
        if(intval($key) > 0){ 
            continue;
        }else if($key != "0"){
            if($key == "rotation"){
                $rotation = explode(" ", $value);
                $model_rotation = ($rotation[0])." ".(intval($rotation[1])-90)." ".($rotation[2]);
                $fout .= ' rotation="'.$model_rotation.'"';
            }else{
               $fout .= ' '.$key.'="'.$value.'"';
            }
        }
    }
    $query = 'SELECT * FROM `nodes_server_client` WHERE `lobby_id` = "'.$lobby_id.'" AND `object_id` = "'.$model["object_id"].'"';
    $res = engine::mysql($query);
    $d = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$d["user_id"].'"';
    $res = engine::mysql($query);
    $nodes_user = mysqli_fetch_array($res);
    $model_name = "Dummy";
    if(!empty($nodes_user)){
        $fout .= ' user_id="'.$nodes_user["id"].'"';
        $model_name = $nodes_user["name"];
    }
    $fout .= '>
        <a-text 
            id="'.$model["id"].'_text" 
            value="'.$model_name.'" 
            position="0.5 9 -2" 
            scale="5 5 5"
            opacity="1"
            rotation="0 -90 0"
            color="white">
        </a-text>
        <a-plane
            id="'.$model["id"].'_image" 
            position="0.26 5.65 -0.135"
            rotation="0 270 0"
            height="0.8" 
            width="0.7"
            src="#anon_image"
            material="shader:flat;opacity:1">
        </a-plane>
    </a-entity>';
    return $fout;
}
