<?php
/**
* Print multiplayer trigger entity.
* @path /engine/core/aframe/multiplayer_trigger.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @return string Returns content of entity.
* @usage <code> engine::multiplayer_trigger(); </code>
*/
function multiplayer_trigger($position, $id){
    $fout = '<a-plane 
        nodes-click="event:trigger" 
        id="'.$id.'" 
        scale="1 1 1" 
        rotation="-90 0 0" 
        width="4.9" height="4.9"
        position="'.$position.'" 
        material="opacity:0.01;" 
        color="white">
    </a-plane>';
    return $fout;
}
