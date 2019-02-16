<?php
/**
* Print multiplayer assets.
* @path /engine/core/aframe/multiplayer_assets.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @return string Returns content of entity.
* @usage <code> engine::multiplayer_assets(); </code>
*/
function multiplayer_assets(){
    $fout = '<a-assets>
        <img id="anon_image" src="'.$_SERVER["PUBLIC_URL"].'/img/pic/anon.jpg" crossorigin="anonymous" />
        <img id="moon" src="'.$_SERVER["PUBLIC_URL"].'/img/vr/moon.jpg" crossorigin="anonymous" />
        <a-asset-item 
            id="temple" 
            src="'.$_SERVER["PUBLIC_URL"].'/res/models/temple.dae">
        </a-asset-item>
        <a-asset-item
            id="red-dummy-model" 
            src="'.$_SERVER["PUBLIC_URL"].'/res/models/red.dae">
        </a-asset-item>
        <a-asset-item
            id="green-dummy-model" 
            src="'.$_SERVER["PUBLIC_URL"].'/res/models/green.dae">
        </a-asset-item>
        <a-asset-item
            id="blue-dummy-model" 
            src="'.$_SERVER["PUBLIC_URL"].'/res/models/blue.dae">
        </a-asset-item>
        <a-asset-item
            id="purple-dummy-model" 
            src="'.$_SERVER["PUBLIC_URL"].'/res/models/purple.dae">
        </a-asset-item>
    </a-assets>';
    return $fout;
}
