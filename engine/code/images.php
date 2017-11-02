<?php
/** 
* Backend file.
* @path /engine/code/images.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
if(empty($_SESSION["user"]["id"])) die(engine::error(401));
$fout = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css">
<link href="'.$_SERVER["DIR"].'/template/'.$_SESSION["template"].'/template.css" rel="stylesheet" type="text/css">
</head>
<body class="body_images nodes">';
if(!empty($_GET['id']) && !empty($_GET["pos"])){
    if(!empty($_POST)){
        if(file_exists('img/data/thumb/'.$_POST["file1"])){
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.intval($_GET["id"]).'"';
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
            $query = 'UPDATE `nodes_product` SET `img` = "'.$files.'" WHERE `id` = "'.$_GET["id"].'"';
            engine::mysql($query);
            $fout = '<script>top.document.getElementById("edit_product_form").submit();</script>';
        }else{
            $fout = "Error. ".$_SERVER["DIR"].'/img/data/thumb/'.$_POST["file1"].' not found';
        }
    }else{
        $fout .= '<form method="POST" id="edit_photos_form"><center>';
        $fout .= engine::print_uploader(1);
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
            try{ 
                parent.document.getElementById("delete_image_block").style.display="none"; 
            }catch(e){ };
            try{ 
                parent.document.getElementById("new_profile_picture").value="'.$_POST["file1"].'"; 
                parent.document.getElementById("edit_profile_form").submit();
            }catch(e){ };
            top.js_hide_wnd();
            </script>';
    }else{
        $fout .= '<form method="POST" id="edit_photos_form"><center>';
        $fout .= engine::print_uploader(1);
        $fout .= '<script> 
        document.getElementById("uploading_button1").style.display="none"; 
        document.getElementById("new_img1").style.display="block"; 
        </script>
        </center></form>';   
    }
}echo $fout.
'</body>
</html>';