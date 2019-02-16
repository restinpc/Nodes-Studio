<?php
/**
* Print panorama perview block.
* @path /engine/core/aframe/pano_preview.php
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
* @param array $data @mysql[nodes_vr_scene].
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::pano_preview($site, $data); </code>
*/
function pano_preview($site, $data){
    $query = 'SELECT * FROM `nodes_vr_project` WHERE `id` = "'.$data["project_id"].'"';
    $res = engine::mysql($query);
    $project = mysqli_fetch_array($res);
    $query = 'SELECT * FROM `nodes_vr_project` WHERE `id` = "'.$data["project_id"].'"';
    $res = engine::mysql($query);
    $level = mysqli_fetch_array($res);
    $text = strip_tags($project["name"].' / '.$level["name"]);
    if(strlen($text)>70) $text = mb_substr($text, 0 ,70).'..';
    $fout = '
        <div class="content_block">
        <div vr-control id="content_'.md5('pano-'.$data["id"]).'" class="content_img" style="background-image: url(\''.$_SERVER["DIR"].'/img/scenes/'.$data["id"].'/f_1_pz_0.png\');"
            onClick=\'document.getElementById("pano-'.$data["id"].'").click();\'>
            &nbsp;
        </div>
            <a vr-control target="_top" id="pano-'.$data["id"].'" href="'.$_SERVER["DIR"].'/aframe/panorama/'.$data["id"].'"><h3>'.mb_substr(strip_tags($data["name"]),0,100).'</h3></a>
            <p class="content_block_text">
            '.$text.'
            </p>
        </div>';
    return $fout;
}
