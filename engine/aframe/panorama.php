<?php
/**
* Backend VR panoramas file.
* @path /engine/aframe/panorama.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $this->title - Page title.
* @var $this->content - Page HTML data.
* @var $this->keywords - Array meta keywords.
* @var $this->description - Page meta description.
* @var $this->img - Page meta image.
* @var $this->onload - Page executable JavaScript code.
* @var $this->configs - Array MySQL configs.
*/    
$_SESSION["redirect"] = $_SERVER["SCRIPT_URI"];
if(!empty($_GET[2])){
    $id = intval($_GET[2]);
    $query = 'SELECT * FROM `nodes_vr_scene` WHERE `id` = "'.$id.'"';
    $res = engine::mysql($query);
    $data = mysqli_fetch_array($res);
    if(empty($data)) engine::error();
    if(!empty($_POST["update"]) && $_SESSION["user"]["id"] == 1){
        $id = intval($_POST["update"]);
        $name = engine::escape_string($_POST["name"]);
        $position = engine::escape_string($_POST["position"]);
        $rotation = engine::escape_string($_POST["rotation"]);
        $lat = floatval($_POST["lat"]);
        $lng = floatval($_POST["lng"]);
        $height = floatval($_POST["height"]);
        $floor_position = engine::escape_string($_POST["floor_position"]);
        $floor_radius = floatval($_POST["floor_radius"]);
        $logo_size = floatval($_POST["logo_size"]);
        $query = 'UPDATE `nodes_vr_scene` SET '
                . '`name` = "'.$name.'", '
                . '`position` = "'.$position.'", '
                . '`rotation` = "'.$rotation.'", '
                . '`height` = "'.$height.'", '
                . '`lat` = "'.$lat.'", '
                . '`lng` = "'.$lng.'", '
                . '`floor_position` = "'.$floor_position.'", '
                . '`floor_radius` = "'.$floor_radius.'", '
                . '`logo_size` = "'.$logo_size.'" '
                . 'WHERE `id` = "'.$id.'"';
        engine::mysql($query);
        $query = 'SELECT * FROM `nodes_vr_scene` WHERE `id` = "'.$id.'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
    }else if(!empty($_POST["default"]) && $_SESSION["user"]["id"] == 1){
        $id = intval($_POST["default"]);
        $query = 'UPDATE `nodes_vr_scene` SET '
                . '`position` = "0 3 0", '
                . '`rotation` = "0 0 0", '
                . '`floor_position` = "0 -2 0", '
                . '`floor_radius` = "20", '
                . '`logo_size` = "3" '
                . 'WHERE `id` = "'.$id.'"';
        engine::mysql($query);
        $query = 'SELECT * FROM `nodes_vr_scene` WHERE `id` = "'.$id.'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
    }else if(!empty($_POST["action"]) && !empty($_POST["id"]) && $_POST["action"] == "edit_object"){
        $id = intval($_POST["id"]);
        $text = $_POST["text"];
        $color = engine::escape_string($_POST["color"]);
        $position = engine::escape_string($_POST["position"]);
        $rotation = engine::escape_string($_POST["rotation"]);
        $scale = engine::escape_string($_POST["scale"]);
        $request = $_SERVER["PUBLIC_URL"].'/text.php?text='.urlencode($text);
        $img = file_get_contents($request);  
        $image = getimagesizefromstring($img);
        $width = $image[0];
        $height = $image[1];
        $base64 = base64_encode($img);
        $query = 'UPDATE `nodes_vr_object` SET'
                . '`text` = "'.$text.'", '
                . '`color` = "'.$color.'", '
                . '`position` = "'.$position.'", '
                . '`rotation` = "'.$rotation.'", '
                . '`scale` = "'.$scale.'", '
                . '`width` = "'.$width.'", '
                . '`height` = "'.$height.'", '
                . '`base64` = "'.$base64.'" '
                . 'WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && !empty($_POST["id"]) && $_POST["action"] == "delete_object"){
        $id = intval($_POST["id"]);
        $query = 'DELETE FROM `nodes_vr_object` WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && $_POST["action"] == "new_object"){
        $text = $_POST["text"];
        $color = engine::escape_string($_POST["color"]);
        $position = engine::escape_string($_POST["position"]);
        $rotation = engine::escape_string($_POST["rotation"]);
        $scale = engine::escape_string($_POST["scale"]);
        $request = $_SERVER["PUBLIC_URL"].'/text.php?text='.urlencode($text);
        $img = engine::curl_get_query($request);
        $base64 = base64_encode($img);
        $uri = 'data://application/octet-stream;base64,' . $base64;
        $image = getimagesize($uri);
        $width = $image[0];
        $height = $image[1];
        $query = 'INSERT INTO `nodes_vr_object`(`project_id`, `level_id`, `scene_id`, `text`, `width`, `height`, `color`, `base64`, `position`, `rotation`, `scale`) '
                . 'VALUES("'.$data["project_id"].'", "'.$data["level_id"].'", "'.$data["id"].'", "'.$text.'", "'.$width.'", "'.$height.'", "'.$color.'", "'.$base64.'", "'.$position.'", "'.$rotation.'", "'.$scale.'")';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && $_POST["action"] == "new_point"){
        $position = engine::escape_string($_POST["position"]);
        $scale = engine::escape_string($_POST["scale"]);
        $target = intval($_POST["target"]);
        $query = 'INSERT INTO `nodes_vr_navigation`(`project_id`, `level_id`, `scene_id`, `target`, `position`, `scale`) '
                . 'VALUES("'.$data["project_id"].'", "'.$data["level_id"].'", "'.$data["id"].'", "'.$target.'", "'.$position.'", "'.$scale.'")';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && $_POST["action"] == "edit_point" && !empty($_POST["id"])){
        $id = intval($_POST["id"]);
        $position = engine::escape_string($_POST["position"]);
        $scale = engine::escape_string($_POST["scale"]);
        $target = intval($_POST["target"]);
        $query = 'UPDATE `nodes_vr_navigation` SET '
                . '`position` = "'.$position.'", '
                . '`scale` = "'.$scale.'", '
                . '`target` = "'.$target.'" '
                . 'WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && !empty($_POST["id"]) && $_POST["action"] == "delete_point"){
        $id = intval($_POST["id"]);
        $query = 'DELETE FROM `nodes_vr_navigation` WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && $_POST["action"] == "new_url"){
        $position = engine::escape_string($_POST["position"]);
        $scale = engine::escape_string($_POST["scale"]);
        $url = engine::escape_string($_POST["url"]);
        $query = 'INSERT INTO `nodes_vr_link`(`project_id`, `level_id`, `scene_id`, `url`, `position`, `scale`) '
                . 'VALUES("'.$data["project_id"].'", "'.$data["level_id"].'", "'.$data["id"].'", "'.$url.'", "'.$position.'", "'.$scale.'")';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && $_POST["action"] == "edit_url" && !empty($_POST["id"])){
        $id = intval($_POST["id"]);
        $position = engine::escape_string($_POST["position"]);
        $scale = engine::escape_string($_POST["scale"]);
        $url = engine::escape_string($_POST["url"]);
        $query = 'UPDATE `nodes_vr_link` SET '
                . '`position` = "'.$position.'", '
                . '`scale` = "'.$scale.'", '
                . '`url` = "'.$url.'" '
                . 'WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(!empty($_POST["action"]) && !empty($_POST["id"]) && $_POST["action"] == "delete_url"){
        $id = intval($_POST["id"]);
        $query = 'DELETE FROM `nodes_vr_link` WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }
    
    $query = 'SELECT COUNT(*) FROM `nodes_vr_navigation` WHERE `scene_id` = "'.$data["id"].'"';
    $r = engine::mysql($query);
    $d = mysqli_fetch_array($r);
    if(!$d[0]){
        $data["floor_radius"] = 0;
    }
    $this->content = '<script src="'.$_SERVER["DIR"].'/script/aframe/panorama.js" type="text/javascript"></script>
    <div id="nodes_vr_scene" style="opacity: 0.5;">
    <a-scene id="nodes_scene" scene-id="'.$data["id"].'" vr-mode-ui="enabled: false;" background="color: #fff;" embedded="true">
        <a-assets>
            <img id="floor_logo" src="'.$_SERVER["PUBLIC_URL"].'/img/vr/vr_logo.png" crossorigin="anonymous" />
            <img id="hotspot" src="'.$_SERVER["PUBLIC_URL"].'/img/vr/hotpoint.png" crossorigin="anonymous" />
            <img id="google" src="'.$_SERVER["PUBLIC_URL"].'/img/vr/gsv.png" crossorigin="anonymous" />
            <img id="pixel" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" />
        </a-assets>
        <a-entity id="rig" 
            position="'.$data["position"].'" 
            rotation="'.$data["rotation"].'">
            <a-camera id="camera" 
                look-controls
                nodes-camera   
                wasd-controls-enabled="false">
                '.engine::pano_vr_cursor().'
            </a-camera>
        </a-entity>
        <a-sky id="sky_back" position="0 0 0" rotation="0 0 0" radius="500" opacity="1"></a-sky>';
    $this->content .= engine::pano_cubemap(intval($_GET[2]), "cubemap_0", "mesh", "1.01 1.01 1.01", 1, 512, 1);
    $this->content .= engine::pano_cubemap(intval($_GET[2]), "cubemap_1", "mesh load_later", "1.01 1.01 1.01", 2, 256, 2);
    $this->content .= engine::pano_cubemap(intval($_GET[2]), "cubemap_2", "mesh load_later", "1.01 1.01 1.01", 4, 128, 3);
    $this->content .= engine::pano_cubemap(intval($_GET[2]), "cubemap_3", "mesh load_later", "1.00 1.00 1.00", 8, 64, 4);
    $this->content .= '
        </a-entity> 
            <a-entity id="virtual_scene">
            ';
    $objects = '';
    $navigation = '';
    $gsv = '';
    $query = 'SELECT * FROM `nodes_vr_object` WHERE `scene_id` = "'.$data["id"].'"';
    $res = engine::mysql($query);
    while($d = mysqli_fetch_array($res)){
        $objects .= engine::pano_object($this, $d);
    }
    $new_obj = array();
    $new_obj["id"] = "new_obj";
    $new_obj["level_id"] = $data["level_id"];
    $new_obj["position"] = "0 -100 0";
    $new_obj["rotation"] = "30 30 0";
    $new_obj["color"] = "white";
    $new_obj["scale"] = "10 10 10";
    $new_obj["text"] = "";
    $objects .= engine::pano_object($this, $new_obj, 1);
    //---------------
    $query = 'SELECT * FROM `nodes_vr_navigation` WHERE `scene_id` = "'.$data["id"].'"';
    $res = engine::mysql($query);
    while($d = mysqli_fetch_array($res)){
        $navigation .= engine::pano_navigation($this, $d);
    }
    $new_nav = array();
    $new_nav["id"] = "new_nav";
    $new_nav["position"] = "0 -100 0";
    $new_nav["level_id"] = $data["level_id"];
    $new_nav["scale"] = "10 10 10";
    $navigation .= engine::pano_navigation($this, $new_nav, 1);
    //---------------
    $query = 'SELECT * FROM `nodes_vr_link` WHERE `scene_id` = "'.$data["id"].'"';
    $res = engine::mysql($query);
    while($d = mysqli_fetch_array($res)){
        $gsv .= engine::pano_link($this, $d);
    }
    $new_nav = array();
    $new_nav["id"] = "new_google";
    $new_nav["position"] = "0 -100 0";
    $new_nav["level_id"] = $data["level_id"];
    $new_nav["scale"] = "10 10 10";
    $gsv .= engine::pano_link($this, $new_nav, 1);
    //---------------
    $this->content .= '
        </a-entity>
        <a-circle id="floor" position="'.$data["floor_position"].'" rotation="-90 0 0" color="white" radius="'.$data["floor_radius"].'" opacity="0"></a-circle>
        <a-circle id="move_point" onClick=\'eval(this.getAttribute("action"));\' action=\'navigate();\' position="0 0.01 0" rotation="-90 0 0" color="white" radius="1" opacity="0" ></a-circle>
        <a-image class="vr_hidden" transparent="true" id="vr_logo" position="0 0.02 0" rotation="-90 0 0"  width="'.$data["logo_size"].'" height="'.$data["logo_size"].'" src="#floor_logo"></a-image>
    </a-scene>
    <audio id="vr-sound" preload><source src="'.$_SERVER["DIR"].'/res/sounds/vr-load.wav" preload type="audio/wav"></audio>';
    $query = 'SELECT * FROM `nodes_vr_level` WHERE `id` = "'.$data["level_id"].'"';
    $r = engine::mysql($query);
    $level = mysqli_fetch_array($r);
    if(!empty($level["image"])){
        $this->content .= '<input id="scene_map" type="button" class="btn" value="'.lang("Show map").'" onClick=\'show_map('.$data["level_id"].');\' />';
    }else{
        $this->content .= '<style>#scene_show_editor{ top: 10px !important; }</style>';
    }
    if($_SESSION["user"]["id"] == "1"){
        $this->content .= engine::pano_scene_editor($data);
    }
    $this->content .= '</div>'
            . '<div id="temp_data">';
    $this->content .= $objects;
    $this->content .= $navigation;
    $this->content .= $gsv;
    $this->content .= '</div>'
            . '<div id="vr-block"></div>';
    $this->onload .= 'vr_load();';
}else{
    $this->content .= '<div class="document980">'
            . '<h1>'.lang("Panoramas").'</h1><br/><br/>';
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_vr_scene` ORDER BY `id` DESC LIMIT '.($from-1).', '.$_SESSION["count"];
    $requery = 'SELECT COUNT(*) FROM `nodes_vr_scene`';
    $res = engine::mysql($requery);
    $data = mysqli_fetch_array($res);
    $count = $data[0];
    $res = engine::mysql($query);
    $table = '<div class="preview_blocks">';
    $flag = 0;
    while($d = mysqli_fetch_array($res)){
        $flag = 1;
        $table .= engine::pano_preview($site, $d);
    }
    $table .= '</div><div class="clear"></div><br/>';
    if($flag){
        $this->content .= $table.'
        <form method="POST"  id="query_form"  onSubmit="submit_search();">
        <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
        <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
        <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
        <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
        <input type="hidden" name="reset" id="query_reset" value="0" />
        <div class="total-entry">';
        if($to > $count) $to = $count;
        if($data[0]>0){
            $this->content .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
                <nobr><select vr-control id="select-pagination" class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
                 <option vr-control id="option-pagination-20"'; if($_SESSION["count"]=="20") $this->content.= ' selected'; $this->content.= '>20</option>
                 <option vr-control id="option-pagination-50"'; if($_SESSION["count"]=="50") $this->content.= ' selected'; $this->content.= '>50</option>
                 <option vr-control id="option-pagination-100"'; if($_SESSION["count"]=="100") $this->content.= ' selected'; $this->content.= '>100</option>
                </select> '.lang("per page").'.</nobr></p>';
        }$this->content .= '
        </div><div class="cr"></div>';
        if($count>$_SESSION["count"]){
           $this->content .= '<div class="pagination" >';
                $pages = ceil($count/$_SESSION["count"]);
               if($_SESSION["page"]>1){
                    $this->content .= '<span vr-control id="page-prev" onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
                }$this->content .= '<ul>';
               $a = $b = $c = $d = $e = $f = 0;
               for($i = 1; $i <= $pages; $i++){
                   if(($a<2 && !$b && $e<2)||
                       ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
                   ($i>$pages-2 && $e<2)){
                       if($a<2) $a++;
                       $e++; $f = 0;
                       if($i == $_SESSION["page"]){
                           $b = 1; $e = 0;
                          $this->content .= '<li class="active-page">'.$i.'</li>';
                       }else{
                           $this->content .= '<li vr-control id="page-'.$i.'" onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                       }
                   }else if((!$c||!$b) && !$f && $i<$pages){
                       $f = 1; $e = 0;
                       if(!$b) $b = 1;
                       else if(!$c) $c = 1;
                       $this->content .= '<li class="dots">. . .</li>';
                   }
               }if($_SESSION["page"]<$pages){
                   $this->content .= '<li vr-control id="page-next" class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
               }$this->content .= '
         </ul>
        </div>';
        }
        $this->content .= '</form>'
            . '<div class="clear"></div>';
    }
    if(!$count){
        $this->content .= '<div class="clear_block">'.lang("Panoramas not found").'</div>';
    }$this->content .= '<br/>';
    $this->content .= '</div>';
}