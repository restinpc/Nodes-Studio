<?php
/**
* Print multiplayer user entity.
* @path /engine/core/aframe/multiplayer_user.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @param object $player @mysql->nodes_server_object.
* @return string Returns content of entity.
* @usage <code> engine::multiplayer_user({player}); </code>
*/
function multiplayer_user($player){
    $fout = '<a-entity id="rig" 
    position="'.$player["position"].'"  
    rotation="'.$player["rotation"].'" 
    scale="'.$player["scale"].'">
    <a-camera id="camera" 
            nodes-camera 
            position="0 0 0" 
            rotation="0 0 0"
            data-aframe-default-camera 
            look-controls 
            wasd-controls="acceleration: 500"
            universal-controls="movementControls: hmd; rotationControls: touch-rotation">
            <a-cursor id="cursor"
                position="0 0 -3" material="color: white; shader: flat; opacity: 0.1;"
                geometry="primitive: ring; radiusInner: 0.08; radiusOuter: 0.1; theta-length: 360;">
                <a-animation id="nodes_fuse" begin="cursor-fusing" attribute="geometry.radiusInner" dur="2000" from="0.08" to="0.01"></a-animation>
                <a-animation id="nodes_unfuse" begin="cursor-unfusing" attribute="geometry.radiusInner" dur="200" to="0.08"></a-animation>
           </a-cursor>
            <a-entity position="0 3 0" 
                id="model-to-terrain" 
                raycaster="showLine: false; far: 100" 
                line="color: orange; opacity: 0.5" 
                rotation="-90 0 0">
            </a-entity>
        </a-camera>
        <a-entity
            id="user_model" 
            collada-model="'.$player["collada-model"].'" 
            position="0 -10 0"  
            rotation="0 0 0">
            <a-entity position="0 3 0" 
                id="model-forward" 
                raycaster="showLine: false; far: 100" 
                line="color: orange; opacity: 1" 
                rotation="0 90 0">
            </a-entity>
        </a-entity> 
    </a-entity>';
    return $fout;
}

