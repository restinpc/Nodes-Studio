<?php

function print_level_plan($level_id){
    $query = 'SELECT * FROM `vr_level` WHERE `id` = "'.$level_id.'"';
    $res = engine::mysql($query);
    $level = mysqli_fetch_array($res);
    $fout = '<div id="level_plan_container">
    <img src="'.$level["image"].'" id="level_plan_img" style="
        -webkit-transform : rotate('.$level["rotation"].'deg) scale('.$level["scale"].'); 
        -ms-transform     : rotate('.$level["rotation"].'deg) scale('.$level["scale"].'); 
        transform         : rotate('.$level["rotation"].'deg) scale('.$level["scale"].');
    " />';
    $query = 'SELECT * FROM `nodes_vr_scene` WHERE `level_id` = "'.$level_id.'"';
    $res = engine::mysql($query);
    $scenes = array();
    $left = null;
    $top = null;
    $left = null;
    $right = null;
    while($data = mysqli_fetch_array($res)){
        array_push($scenes, $data);
        if($data["lat"] < $left || $left == null) $left = $data["lat"];
        if($data["lat"] > $right || $right == null) $right = $data["lat"];
        if($data["lng"] < $top || $top == null) $top = $data["lng"];
        if($data["lng"] > $bottom || $bottom == null) $bottom = $data["lng"];
    }
    $points = '';
    $bt = 550/($bottom-$top);
    $rl = 550/($right-$left);
    if($bt > $rl){
        $rl *= ($rl/$bt);
    }else{
        $bt *= ($bt/$rl);
    }
    foreach($scenes as $scene){
        $t = (5+($scene["lng"]-$top)*($bt));
        $l = (5+($right-$scene["lat"])*($rl));
        $fout .= '<img id="camera_icon_'.$scene["id"].'" src="/img/hotpoint.png" width=30 '
                . ' style="position:absolute; top:'.($t+$scene["top"]).'px;'
                . 'left:'.($l+$scene["left"]).'px; cursor: pointer;" title="'.$scene["name"].'" 
                    onClick=\'parent.window.location="/aframe/'.$scene["id"].'";\'
                    />';
    }
    $points = '{"points":['.$points.']}';
    $fout .= '
    </div>';
    return $fout;
}

