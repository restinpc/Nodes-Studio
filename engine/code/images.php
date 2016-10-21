<?php
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
if($_SESSION["user"]["id"] != "1") die(lang("Access denied"));
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';
$res = engine::mysql($query);
$data = mysql_fetch_array($res);
$template = $data["value"];
$fout = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="'.$_SERVER["DIR"].'/templates/style.css" rel="stylesheet" type="text/css">
<link href="'.$_SERVER["DIR"].'/templates/'.$template.'/template.css" rel="stylesheet" type="text/css">
</head>
<body style="padding: 0px; margin: 0px; overflow:visible; opacity: 1; min-height: 50px;">';
if(!empty($_GET['id']) && !empty($_GET["pos"])){
    if(!empty($_POST)){
        if(file_exists('img/data/thumb/'.$_POST["file1"])){
            $query = 'SELECT * FROM `nodes_products` WHERE `id` = "'.intval($_GET["id"]).'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $images = explode(";", $data["img"]);
            if(empty($images[0])) $images = array($data["img"]);
            $imgs = array();
            foreach($images as $img){
                $img = trim($img);
                if(!empty($img)){
                    array_push($imgs, $img);
                }
            }
            $i = 0;
            $files = '';
            foreach($imgs as $img){
                $i++;
                if($_GET["pos"]==$i){
                    $files .= $_POST["file1"].';';
                }else{
                    $files .= $img.';';
                }
            }if($_GET["pos"]>$i){
                $files .= $_POST["file1"].';';
            }
            $query = 'UPDATE `nodes_products` SET `img` = "'.$files.'" WHERE `id` = "'.$_GET["id"].'"';
            engine::mysql($query);
            $fout = '<script>parent.hide_photo_editor();</script>';
        }else{
            $fout = "Error. ".$_SERVER["DIR"].'/img/data/thumb/'.$_POST["file1"].' not found';
        }
    }else{
        $fout .= '<form method="POST" id="edit_photos_form"><center>';
        require_once("engine/include/print_uploader.php");
        $fout .= print_uploder(1);
        $fout .= '<script> 
                document.getElementById("uploading_button1").style.display="none"; 
                document.getElementById("new_img1").style.display="block"; 
            </script>
            </center>
        </form>';
    }
}else{
    if(!empty($_POST)){
        $fout .= '<script>
            try{ parent.document.getElementById("delete_image_block").style.display="none"; }catch(e){ };
            parent.hide_photo_editor();
            </script>';
    }else{
        $fout .= '<form method="POST" id="edit_photos_form"><center>';
        require_once("engine/include/print_uploader.php");
        $fout .= print_uploder(1);
        $fout .= '<script> 
        document.getElementById("uploading_button1").style.display="none"; 
        document.getElementById("new_img1").style.display="block"; 
        </script>
        </center></form>';   
    }
}echo $fout.
'</body>
</html>';