<?php
/**
* Backend cardboard control interface file.
* @path /engine/aframe/cardboard.php
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
$page = '/';
if(!empty($_GET["page"])){
    $page = $_GET["page"];
}
$this->content = '
<script src="'.$_SERVER["DIR"].'/script/aframe/cardboard.js" type="text/javascript"></script>
<div id="nodes_headset_content">
    <div class="vr_left_eye">
        <iframe id="nodes_left_frame" src="'.$page.'" frameborder=0 width=100% height=100% onLoad="load_frame();" frameborder=0 ></iframe>
        <div class="vr_preloader" id="vr_left_preloader">
            <div class="vr_start_button">
                <img src="'.$_SERVER["DIR"].'/img/vr/cardboard.png" />
                <br/><br/>
                '.lang("Place insert your phone into cardboard").'<br/>
            </div>
            <div class="vr_countdown" id="vr_left_countdown"></div>
        </div>
    </div>
    <div class="vr_right_eye">
        <iframe id="nodes_right_frame" src="'.$page.'" frameborder=0 width=100% height=100% onLoad="load_frame();" frameborder=0></iframe>
        <div class="vr_preloader" id="vr_right_preloader">
            <div class="vr_start_button">
                <img src="'.$_SERVER["DIR"].'/img/vr/cardboard.png" />
                <br/><br/>
                '.lang("Place insert your phone into cardboard").'<br/>
            </div>
            <div class="vr_countdown" id="vr_right_countdown"></div>
        </div>
    </div>
    <div class="vr_preloader" id="vr_center_preloader">
        <div class="vr_start_button">
            <img src="'.$_SERVER["DIR"].'/img/vr/cardboard.png" />
            <br/><br/>
            '.lang("Place insert your phone into cardboard").'<br/>
        </div>
    </div>
</div>';

$this->onload .= '; load_frame(); startCardboard(); jQuery(window).resize(startCardboard); ';


