<?php
/**
* Backend cardboard visualizer file.
* @path /engine/aframe/visualizer.php
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
$this->content = '
<script src="'.$_SERVER["DIR"].'/script/aframe/visualizer.js" crossorigin="anonymous"></script>
<a-scene class="vr_fullscreen" vr-mode-ui="enabled: true;" embedded style="background: #000;">
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
            src="#equirectangular_1" id="sphere" color="" radius="1" position="0 0 -50">
            <!-- <a-animation attribute="rotation"
                dur="100000"
                fill="forwards"
                to="-1440 0 0"
                repeat="indefinite"></a-animation> -->
        </a-sphere>
    </a-camera>
    
    <a-curve id="track1" curve="">
        <a-curve-point position="-16 38 -48" curve-point=""></a-curve-point>
        <a-curve-point position="38 23 -57" curve-point=""></a-curve-point>
        <a-curve-point position="87 21 5" curve-point=""></a-curve-point>
        <a-curve-point position="22 27 91" curve-point=""></a-curve-point>
        <a-curve-point position="29 16 91" curve-point=""></a-curve-point>
        <a-curve-point position="60 40 70" curve-point=""></a-curve-point>
        <a-curve-point position="-56 17.0 79" curve-point=""></a-curve-point>
        <a-curve-point position="-16 38 -48" curve-point=""></a-curve-point>
    </a-curve>
    <a-entity clone-along-curve="curve: #track1; spacing: 0.2; rotation: 0 0 0;" geometry="primitive:box; height:0.001; width:0.002; depth:0.001"></a-entity>
    
    <a-sky id="sky"
        position="0 0 0"
        geometry="primitive: sphere; radius: 100; phiLength: 360; phiStart: 0; thetaLength: 180;"
        material="shader: flat; side: back; height: 960; width: 1920; opacity: 1;"
        src="#equirectangular_1">
        <a-animation attribute="rotation"
            dur="100000"
            fill="forwards"
            to="36000 0 0"
            repeat="indefinite"></a-animation>
    </a-sky> 
</a-scene>';

$this->onload .= 'submit_patterns = 0;'
        . 'window.addEventListener(\'touchstart\', function() { start_visualizer(); } );'
        . 'window.addEventListener(\'click\', function() { start_visualizer(); } );';