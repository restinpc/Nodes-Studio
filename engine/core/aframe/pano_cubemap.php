<?php
/**
* Print cubmap image entity.
* @path /engine/core/aframe/pano_cubemap.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @param int $scene_id @mysql[nodes_vr_scene]->id.
* @param string $id Entity ID.
* @param string $class Entity class name.
* @param string $scale Entity scale.
* @return string Returns content of entity on success, or die with error.
* @usage <code> engine::pano_cubemap(1, "cubemap_0", "mesh", "1.01 1.01 1.01", 1, 512, 1); </code>
*/
function pano_cubemap($scene_id, $id, $class, $scale, $q, $s, $t ){
    $sides = Array("pz", "nz", "px", "nx", "py", "ny");
    $rotations = Array("0 0 0", "0 -180 0", "0 -90 0", "0 90 0", "90 0 0", "-90 0 0");
    $fout = '<a-entity id="'.$id.'" position="0 0 0" scale="'.$scale.'">';    
    $w = $s/2;
    $x = $q*$s/2;
    for($l = 0; $l< count($sides); $l++){
        $side = $sides[$l];
        $rotation_img = $rotations[$l];
        for($i = 0; $i < $q; $i++){
            for($j = 0; $j < $q; $j++){
                $id = ($i*$q+$j);
                if($side == "pz"){
                    $i_1 = (-$x+$i*$s+$w);
                    $j_1 = ($x-$j*$s-$w);
                }else if($side == "nz"){
                    $i_1 = ($x-$i*$s-$w);
                    $j_1 = ($x-$j*$s-$w);
                }else if($side == "px"){
                    $i_1 = ($x-$j*$s-$w);
                    $j_1 = (-$x+$i*$s+$w);
                }else if($side == "nx"){
                    $i_1 = ($x-$j*$s-$w);
                    $j_1 = ($x-$i*$s-$w);
                }else if($side == "py"){
                    $i_1 = (-$x+$i*$s+$w);
                    $j_1 = ($x-$j*$s-$w);
                }else if($side == "ny"){
                    $i_1 = (-$x+$i*$s+$w);
                    $j_1 = (-$x+$j*$s+$w);
                }
                $positions = Array(
                    $i_1.' '.$j_1.' -'.$x,
                    $i_1.' '.$j_1.' '.$x,
                    ''.$x.' '.$i_1.' '.$j_1,
                    '-'.$x.' '.$i_1.' '.$j_1,
                    $i_1.' '.$x.' '.$j_1,
                    $i_1.' -'.$x.' '.$j_1
                );
                $position_img = $positions[$l];
                if($q == 1){
                    $src = 'src="'.$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/f_'.$t.'_'.$side.'_'.$id.'.png"';
                }else{
                    $src = 'src="#pixel" xsrc="'.$_SERVER["DIR"].'/img/scenes/'.$scene_id.'/f_'.$t.'_'.$side.'_'.$id.'.png"';
                }
                $fout .= '<a-image zoom="'.$t.'" class="'.$class.'"  side="'.$side.'"  id="cubemap_'.$side.'_'.$t.'_'.$id.'" position="'.$position_img.'" rotation="'.$rotation_img.'" width="'.$s.'" height="'.$s.'" side="front" '.$src.'></a-image>';
            }
        }
    }
    $fout .= '</a-entity>';
    return $fout;
}

