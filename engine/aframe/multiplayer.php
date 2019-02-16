<?php
/**
* Backend cardboard multiplayer screne file.
* @path /engine/aframe/multiplayer.php
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
$scene_id = 1;
$query = 'SELECT * FROM `nodes_server_scene` WHERE `id` = "'.$scene_id.'"';
$res = engine::mysql($query);
$scene = mysqli_fetch_array($res);
$scene_max_radius = $scene["max_radius"];
$scene_radius = $scene["radius"];
$lobby = server::create_lobby($scene_id);
$player = engine::multiplayer_player($lobby["id"]);
$models = engine::multiplayer_objects($lobby["id"], $player["object_id"]);
$this->title .= ' #'.$lobby["id"];
$this->content = '<script src="'.$_SERVER["DIR"].'/script/aframe/multiplayer.js"></script>';
$this->content .= '<div id="nodes_vr_scene" style="opacity: 1;">
<a-scene
    embedded="true"
    nodes-scene="default" 
    nodes-lobby="'.$lobby["id"].'" 
    timeout="10000" 
    id="nodes_scene" 
    nodes-state="loading"
    auto-enter-vr="enabled: false"
    vr-mode-ui="enabled: true;" 
    background="color: #000;" 
    fog="
        type: linear; 
        density: 0.0001; 
        color: black; 
        far: '.($scene_max_radius*1.5).'; 
        near: '.$scene_radius.';
    ">
    '.engine::multiplayer_assets().'
    '.engine::multiplayer_user($player).'
    '.aframe::default_light().'
    '.aframe::sky_entity("sky", "#moon", "2048", "1024", $scene_max_radius, "#fff").'
    '.aframe::terrain_entity($scene_id).'
    '.aframe::collada_entity("temple", "#temple", "0 1.5 0", "0 0 0", "10 10 10").'
    <a-plane position="0 1 -10" rotation="-90 0 0" color="#55a" width="57" depth="85" height="80"></a-plane>';
foreach($models as $model){
    $this->content .= engine::multiplayer_model($model, $lobby["id"]);
}
$this->content .= '</a-scene>
</div>';
