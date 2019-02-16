<?php
/**
* Print panorama scene editor block.
* @path /engine/core/aframe/pano_scene_editor.php
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
* @param array $data @mysql[nodes_vr_scene].
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::pano_scene_editor($data); </code>
*/
function pano_scene_editor($data){
    $query = 'SELECT * FROM `nodes_vr_project` WHERE `id` = "'.$data["project_id"].'"';
    $res = engine::mysql($query);
    $project = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_vr_level` WHERE `id` = "'.$data["level_id"].'"';
    $res = engine::mysql($query);
    $level = mysqli_fetch_array($res);
    $fout = '
    <input id="scene_show_editor" type="button" class="btn" value="'.lang("Show editor").'" onClick=\'show_scene_editor();\' />
    <div id="scene_editor">
    <form method="POST" id="scene_form">
        <input id="act" type="hidden" name="update" value="'.$data["id"].'" />
        <div><b>'.$project["name"].'</b> / <b>'.$level["name"].'</b></div>
            <br/>
        '.lang("Scene name").':<br/>
        <input required name="name" type="text" class="input w100p" value="'.$data["name"].'" /><br/>
            <br/>
        '.lang("Default camera position").':<br/>
        <input required id="camera_position" name="position" type="text" class="input w100p" value="'.$data["position"].'" /><br/>
            <br/>
        '.lang("Default camera rotation").':<br/>
        <input required id="camera_rotation" name="rotation" type="text" class="input w100p" value="'.$data["rotation"].'" /><br/>
            <br/>
        '.lang("Height").':<br/>
        <input required id="height" name="height" type="number" class="input w100p" value="'.$data["height"].'" /><br/>
            <br/>
        '.lang("Latitude").':<br/>
        <input required id="scene_lat" name="lat" type="number" class="input w100p" value="'.$data["lat"].'" /><br/>
            <br/>
        '.lang("Longitude").':<br/>
        <input required id="scene_lng" name="lng" type="number" class="input w100p" value="'.$data["lng"].'" /><br/>
            <br/>
        '.lang("Floor position").':<br/>
        <input required id="floor_position" name="floor_position" type="text" class="input w100p" value="'.$data["floor_position"].'" /><br/>
            <br/>
        '.lang("Floor radius").':<br/>
        <input required id="floor_radius" name="floor_radius" type="number" class="input w100p" value="'.$data["floor_radius"].'" /><br/>
            <br/>
        '.lang("Logo size").':<br/>
        <input required id="logo_size" name="logo_size" type="number" class="input w100p" value="'.$data["logo_size"].'" /><br/>
            <br/>
        <input type="button" class="btn w100p" value="'.lang("Apply changes").'" onClick=\'apply_scene_changes();\' />
            <br/><br/>
        <input type="button" class="btn w100p" value="'.lang("Load default setting").'" onClick=\'default_settings();\' />
            <br/><br/>
        <input type="submit" class="btn w100p" value="'.lang("Save scene settings").'" />
            <br/><br/>
    </form>
    </div>
    <div style="position:absolute; top:10px; right: 10px; width: 180px; display:none;" id="add_area">        
        <input type="button" class="btn w100p" value="'.lang("Add new object").'" onClick=\'add_object();\' />
        <br/><br/>
        <input type="button" class="btn w100p" value="'.lang("Add new navigation").'" onClick=\'add_navigation();\' />
        <br/><br/>
        <input type="button" class="btn w100p" value="'.lang("Add new link").'" onClick=\'add_url();\' />
        <br/><br/>
        <input type="button" class="btn w100p" value="'.lang("Reset scene objects").'" onClick=\'reset_scene_object('.$data["id"].');\' />
    </div>';
    return $fout;
}

