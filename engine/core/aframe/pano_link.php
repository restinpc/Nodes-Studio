<?php
/**
* Print panorama link entity.
* @path /engine/core/aframe/pano_link.php
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
* @param array $object @mysql[nodes_vr_link].
* @return string Returns content of entity on success, or die with error.
* @usage <code> engine::pano_link($site, $object); </code>
*/
function pano_link($site, $object, $new=0){
    $site->content .= '<a-image transparent="true" look-at="#camera" 
        onClick=\'eval(this.getAttribute("action"));\' 
        action=\' 
            setTimeout(function(id){
                if($id("scene_editor") && $id("scene_editor").style.display=="block"){
                    if(!object_id){
                        jQuery(".vr_object_window").css("display", "none");
                        $id("url_'.$object["id"].'_window").style.display = "block";
                        object_id = "'.$object["id"].'";
                    }
                }else{
                    window.location = "'.$object["url"].'";
                }
            }, 500, "'.$object["id"].'"); \' '
            . 'id="url_'.$object["id"].'" '
            . 'position="'.$object["position"].'" '
            . 'scale="'.$object["scale"].'" '
            . 'rotation="0 0 0"  '
            . ' class="custom_object"'
            . 'opacity="0"'
            . 'width="1" height="1" '
            . 'src="#google"></a-image>';
    if($_SESSION["user"]["id"] == "1"){
        $fout .= '
        <div id="url_'.$object["id"].'_window"  class="vr_object_window">
            <div style="padding-top:10px; padding-bottom:10px; text-align:center; font-weight:bold;">'.lang("Link properties").'</div><br/>
            <form method="POST" id="url_'.$object["id"].'_form">
                <input id="action_'.$object["id"].'" type="hidden" name="action" value="'.($new?'new_url':'edit_url').'" />
                <input type="hidden" name="id" value="'.$object["id"].'" />
                '.lang("Position").':<br/>
                <input required id="url_'.$object["id"].'_position" name="position" type="text" class="input w100p" value="'.$object["position"].'" /><br/>
                    <br/>
                '.lang("Scale").':<br/>
                <input required id="url_'.$object["id"].'_scale" name="scale" type="text" class="input w100p" value="'.$object["scale"].'" /><br/>
                    <br/>   
                URL:<br/>
                <input required id="url_'.$object["id"].'_url" name="url" type="text" class="input w100p" value="'.$object["url"].'" /><br/>
                    <br/> 
                    <br/>
                <input type="button" class="btn w100p" value="'.lang("Apply chages").'" onClick=\'apply_changes_url("'.$object["id"].'");\' /><br/><br/>';
        if(!$new){
            $fout .= '<input type="button" class="btn w100p" value="'.lang("Delete Link").'" onClick=\'delete_url("'.$object["id"].'")\' /><br/><br/>';
        }
        $fout .= '        
                <input type="submit" class="btn w100p" value="'.lang("Submit").'" /><br/><br/>
            </form>
        </div>';
        return $fout;
    }
}