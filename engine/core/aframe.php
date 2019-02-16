<?php
/**
* Aframe core library.
* @path /engine/core/aframe.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
* 
* @example <code> $color = aframe::pattern_color(); </code>
*/
class aframe{
//------------------------------------------------------------------------------
/**
* Print aframe collada model.
*
* @param string $id Site class object.
* @param string $collada_model Scene caption.
* @param string $position Scene URL.
* @param string $rotation Scene preview image.
* @param string $scale Model scale.
* @return string Returns content of entity.
* @usage <code> engine::aframe_collada_entity(1, "/res/models/*.collada"); </code>
*/
static function collada_entity(
        $id, 
        $collada_model,
        $position="0 0 0", 
        $rotation="0 0 0",
        $scale="1 1 1"
    ){
    $fout = '<a-entity 
        id="'.$id.'" 
        raycaster="objects: .collidable;"
        collada-model="'.$collada_model.'"
        rotation="'.$rotation.'"
        position="'.$position.'"
        scale="'.$scale.'">
    </a-entity>';
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Print aframe camera entity.
* 
* @return string Returns content of entity.
* @usage <code> engine::aframe_default_camera(); </code>
*/
static function default_camera(){
    $fout = '
    <a-camera id="camera" 
        nodes-camera 
        position="0 0 0" 
        rotation="0 0 0"
        data-aframe-default-camera 
        look-controls 
        wasd-controls="acceleration: 500"
        universal-controls="movementControls: hmd; rotationControls: touch-rotation"
        animation="property: position; dur: 1000; to: 0 0 0; startEvents: respawn;">
        <a-cursor id="cursor"
            position="0 0 -3" material="color: white; shader: flat; opacity: 0.1;"
            geometry="primitive: ring; radiusInner: 0.08; radiusOuter: 0.1; theta-length: 360;">
            <a-animation id="nodes_fuse" begin="cursor-fusing" attribute="geometry.radiusInner" dur="2000" from="0.08" to="0.01"></a-animation>
            <a-animation id="nodes_unfuse" begin="cursor-unfusing" attribute="geometry.radiusInner" dur="200" to="0.08"></a-animation>
       </a-cursor>
    </a-camera>';
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Print aframe light entity.
*
* @return string Returns content of entity.
* @usage <code> engine::aframe_default_light(); </code>
*/
static function default_light(){
    $fout = '<a-light color="white" type="ambient"  intensity="0.7"></a-light>
            <a-light color="white" type="point" position="10 15 10" intensity="0.3"></a-light>';
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Print aframe spherical sky entity.
* 
* @param string $id Entity ID.
* @param string $src Image URL.
* @param int $width Image width.
* @param int $height Image height.
* @param int $radius Sphere radius.
* @param rgb $color Image color.
* @return string Returns content of entity.
* @usage <code> engine::aframe_sky_entity(1, "/img/vr/sky.jpg"); </code>
*/
static function sky_entity($id, $src, $width="2048", $height="1024", $radius=150, $color=""){
    $fout = '<a-sky id="'.$id.'"
        geometry="primitive: sphere; radius: '.$radius.'; phiLength: 360; phiStart: 0; thetaLength: 180;"
        material="fog: false; shader: flat; side: back; height: '.$height.'; width: '.$width.'; opacity: 1;"
        src="'.$src.'"></a-sky>';
    return $fout;
}
//------------------------------------------------------------------------------
/**
* Print aframe meshed terrain entity.
* 
* @param string $id Entity ID.
* @param string $src Image URL.
* @return string Returns content of entity.
* @usage <code> afrane::terrain_entity(1, 300); </code>
*/
static function terrain_entity($scene_id, $step=50){
    $query = 'SELECT * FROM `nodes_server_scene` WHERE `id` = "'.$scene_id.'"';
    $res = engine::mysql($query);
    $scene = mysqli_fetch_array($res);
    $map = $scene["terrain"];
    $scene_max_radius = $scene["max_radius"];
    if(empty($map)){
        $terratin = array();
        for($i = 0; $i<=$scene_max_radius*2; $i+=$step){
            for($j = 0; $j<=$scene_max_radius*2; $j+=$step){
                $mod = $scene_max_radius*2-($i+$j);
                if($mod < 0) $mod *= -1;
                $mod /= $scene_max_radius;
                $mod *= 2;
                $magic = 2.01;
                if($i < $scene_max_radius-$scene_max_radius/$magic || $i>$scene_max_radius+$scene_max_radius/$magic 
                    || $j < $scene_max_radius-$scene_max_radius/$magic || $j > $scene_max_radius+$scene_max_radius/$magic){

                    if($i > 0 && $j > 0){
                        $f = $terratin[$i][$j-$step]+
                            $terratin[$i-$step][$j]+
                            $terratin[$i-$step][$j-$step];
                        $terratin[$i][$j] = floatval(($f/3)+rand(-10, rand(0,30))*$mod);
                    }else{
                       $terratin[$i][$j] = rand(-5, 10);
                    }
                }else{
                    $terratin[$i][$j] = rand(-5, 10);
                }
            }
        }
        $map = '{';
        $c = 1;
        for($i = 0; $i < $scene_max_radius*2-1; $i+=$step){
            for($j = 0; $j < $scene_max_radius*2-1; $j+=$step){
                $h1 = $terratin[$i][$j];
                $h2 = $terratin[$i+$step][$j];
                $h3 = $terratin[$i][$j+$step];
                $h4 = $terratin[$i+$step][$j+$step];
                if($map != "{") $map .= ' , ';
                $map .= '"'.$c++.'": [ '
                        . '['.(0+$i).', '.(0+$j).', '.$h1.'], ['.(0+$i).', '.($step+$j).', '.$h3.'], ['.($step+$i).', '.(0+$j).', '.$h2.'], '
                        . '['.($step+$i).', '.(0+$j).', '.$h2.'], ['.($step+$i).', '.($step+$j).', '.$h4.'], ['.(0+$i).', '.($step+$j).', '.$h3.'] ]';
            }
        }
        $map .= '}';
        $query = 'UPDATE `nodes_server_scene` SET `terrain` = "'.str_replace('"', '\"', $map).'" WHERE `id` = '.$scene_id;
        engine::mysql($query);
    }
    return '<a-entity position="-500 -10 500" rotation="-90 0 0" nodes-terrain color="white" scale="1 1 1" style="display:none;">'.$map.'</a-entity>';
}
}

