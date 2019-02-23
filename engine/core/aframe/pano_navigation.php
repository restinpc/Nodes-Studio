<?php
/**
* Print panorama navigation entity.
* @path /engine/core/aframe/pano_navigation.php
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
* @param object $site Site class object.
* @param array $object @mysql[nodes_vr_navigation].
* @return string Returns content of entity on success, or die with error.
* @usage <code> engine::pano_navigation($site, $object); </code>
*/
function pano_navigation($site, $object, $new=0){
    $site->content .= '<a-image transparent="true" class="hotpoint" look-at="#camera" 
        onClick=\'eval(this.getAttribute("action"));\' 
        action=\' 
            setTimeout(function(id){
                if('.($_SESSION["user"]["id"]==1?'1':'0').' && $id("scene_editor").style.display=="block"){
                    if(!object_id){
                        jQuery(".vr_object_window").css("display", "none");
                        $id("point_'.$object["id"].'_window").style.display = "block";
                        object_id = "'.$object["id"].'";
                    }
                }else{
                    '.($object["target"]?'load_scene("'.$object["target"].'", "point_'.$object["id"].'")':'').'
                }
            }, 500, "'.$object["id"].'"); 
                \' '
            . 'id="point_'.$object["id"].'" '
            . 'position="'.$object["position"].'" '
            . 'scale="'.$object["scale"].'" '
            . 'rotation="0 0 0" '
            . 'opacity="0" '
            . 'width="1" height="1" '
            . 'src="#hotspot"></a-image>';
    if($_SESSION["user"]["id"] == "1"){
        $fout .= '
        <div id="point_'.$object["id"].'_window"  class="vr_object_window">
            <div style="padding-top:10px; padding-bottom:10px; text-align:center; font-weight:bold;">'.lang("Point properties").'</div><br/>
            <form method="POST" id="object_'.$object["id"].'_form">
                <input id="action_'.$object["id"].'" type="hidden" name="action" value="'.($new?'new_point':'edit_point').'" />
                <input type="hidden" name="id" value="'.$object["id"].'" />
                '.lang("Position").':<br/>
                <input required id="point_'.$object["id"].'_position" name="position" type="text" class="input w100p" value="'.$object["position"].'" /><br/>
                    <br/>
                '.lang("Scale").':<br/>
                <input required id="point_'.$object["id"].'_scale" name="scale" type="text" class="input w100p" value="'.$object["scale"].'" /><br/>
                    <br/>   
                '.lang("Target").':<br/>
                <select required id="point_'.$object["id"].'_target" name="target"  class="input w100p lh2">';
        $query = 'SELECT * FROM `nodes_vr_scene` WHERE `level_id` = "'.$object["level_id"].'" AND `id` <> "'.$object["scene_id"].'"';
        $res = engine::mysql($query);
        while($data = mysqli_fetch_array($res)){
            if($object["target"] == $data["id"]){
                $fout .= '<option value="'.$data["id"].'" selected>'.$data["name"].':'.$data["id"].'</option>';
            }else{
                $fout .= '<option value="'.$data["id"].'">'.$data["name"].':'.$data["id"].'</option>';
            }
        }
        $fout .= '</select>
                    <br/>
                    <br/>
                <input type="button" class="btn w100p" value="'.lang("Apply chages").'" onClick=\'apply_changes_navigation("'.$object["id"].'");\' /><br/><br/>';
        if(!$new){
            $fout .= '<input type="button" class="btn w100p" value="'.lang("Delete point").'" onClick=\'delete_navigation("'.$object["id"].'")\' /><br/><br/>';
        }
        $fout .= '        
                <input type="submit" class="btn w100p" value="'.lang("Submit").'" /><br/><br/>
            </form>
        </div>';
        return $fout;
    }
}