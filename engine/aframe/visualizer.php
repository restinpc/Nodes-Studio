<?php
/**
* Backend cardboard visualizer file.
* @path /engine/aframe/visualizer.php
*
* @name    Nodes Studio    @version 3.0.0.2
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
$this->content = '
<script src="'.$_SERVER["DIR"].'/script/aframe/visualizer.js" crossorigin="anonymous"></script>
<a-scene style="background: #000;" id="nodes_scene">
    <a-assets> 
        <img id="equirectangular_1" src="/img/vr/audio_1.jpg" crossorigin="anonymous" /> 
        <img id="equirectangular_2" src="/img/vr/audio_2.jpg" crossorigin="anonymous" /> 
        <img id="equirectangular_3" src="/img/vr/audio_3.jpg" crossorigin="anonymous" /> 
        <img id="equirectangular_4" src="/img/vr/audio_4.jpg" crossorigin="anonymous" /> 
        <img id="equirectangular_5" src="/img/vr/audio_5.jpg" crossorigin="anonymous" />
    </a-assets>
    <a-camera id="camera" 
        nodes-camera 
        position="0 0 0" 
        rotation="0 0 0"     
        wasd-controls-enabled="false">
        <a-sphere  
            material="shader: flat; height: 1024; width: 2048; opacity: 1;" opacity="1"
            src="#equirectangular_1" id="sphere" color="" radius="5" position="0 0 -50">
        </a-sphere>
    </a-camera>
    <a-sky id="sky"
        position="0 0 0" color="grey"
        geometry="primitive: sphere; radius: 100; phiLength: 360; phiStart: 0; thetaLength: 180;"
        material="shader: flat; side: back; height: 960; width: 1920; opacity: 1;"
        src="#equirectangular_1">
    </a-sky> 
</a-scene>
<style>.a-enter-vr-button{ position: fixed; bottom: 20px; right: 20px;}</style>';
$this->onload .= 'submit_patterns = 0;'
    . 'window.addEventListener(\'touchstart\', function() { start_visualizer(); } );'
    . 'window.addEventListener(\'click\', function() { start_visualizer(); } );';