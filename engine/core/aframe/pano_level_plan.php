<?php
/**
* Print level map block.
* @path /engine/core/aframe/pano_level_plan.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param int $level_id @mysql[nodes_vr_level]->id.
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::pano_level_plan(1); </code>
*/
function pano_level_plan($level_id){
    $query = 'SELECT * FROM `nodes_vr_level` WHERE `id` = "'.$level_id.'"';
    $res = engine::mysql($query);
    $level = mysqli_fetch_array($res);
    $fout = '<div style="height:100px;" class="center">'.lang("Drag-n-drop points to locate scenes on level.").'<br/>
        '.lang("Double click to open scene preview").'.</div>
    <div id="level_plan_container" ondragover="allowDrop(event)">
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
        $fout .= '<img draggable="true" onDblClick=\'window.open("'.$_SERVER["DIR"].'/aframe/panorama/'.$scene["id"].'")\' class="dragable" id="camera_icon_'.$scene["id"].'" src="/img/hotpoint.png" width=30 '
                . 't="'.$t.'" l="'.$l.'" g="'.$scene["id"].'"'
                . ' style="position:absolute; top:'.($t+$scene["top"]).'px;'
                . 'left:'.($l+$scene["left"]).'px; cursor: move;" title="ID: '.$scene["id"].'; Name:'.$scene["name"].'" />';
        if(!empty($points)) $points.=',';
    }
    $points = '{"points":['.$points.']}';
    $fout .= '
    </div>
    <div style="height:700px;">&nbsp;</div>
    <form method="POST" id="objects" style="width: 320px; margin:0px auto; text-align:left;"  ENCTYPE="multipart/form-data">
        <input type="hidden" name="action" value="edit_level_plan" />
        <input type="hidden" name="id" value="'.$level_id.'" />
        <input id="points_json" type="hidden" name="json" />
        '.lang("Image rotation").': <br/>
        <input required type="number" step="1" class="input w280" name="rotation" id="level_plan_rotation" value="'.$level["rotation"].'" /><br/>
        <br/>
        '.lang("Image scale").': <br/>
        <input required type="number" step="0.01" class="input w280" name="scale" id="level_plan_scale" value="'.$level["scale"].'" /><br/>
        <br/>
        '.lang("Upload new image").': <br/>
        <input type="file"  class="input w280" id="level_plan_file" name="image"  /><br/>
        <br/>
        <input type="button" onClick=\'level_apply_chages();\' class="btn w280" value="'.lang("Apply chages").'" />
        <br/>
        <input type="submit" class="btn w280" value="'.lang("Save chages").'" />
        <br/>
        <a href="'.$_SERVER["DIR"].'/admin/?mode=panoramas&project_id='.$level["project_id"].'&level_id='.$level_id.'"><input type="button" class="btn w280" value="'.lang("Cancel").'" /></a>
        <br/>
    </form>
    <div class="clear"></div>
    ';
    return $fout;
}
