<?php
if(!empty($_GET['id']) && !empty($_GET["pos"])){
    $fout .= '<html><body style="padding: 0px; margin: 0px; overflow:visible;">';
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
            die('<img src="'.$_SERVER["DIR"].'/img/data/thumb/'.$_POST["file1"].'" />');
        }else{
            die("Error. ".$_SERVER["DIR"].'/img/data/thumb/'.$_POST["file1"].' not found');
        }
    }else{
        $fout .= '<form method="POST" id="edit_photos_form"><center>';
        require_once("engine/include/print_uploader.php");
        $fout .= print_uploder(1);
        $fout .= '<script> 
              document.getElementById("uploading_button1").style.display="none"; 
              document.getElementById("new_img1").style.display="block"; 
              </script>'
        . '</form>';
    }
    $fout .= '</body></html>';
}else{
        $fout .= '<center>';
        require_once("engine/include/print_uploader.php");
        $fout .= print_uploder(1);
        $fout .= '<script> 
            document.getElementById("uploading_button1").style.display="none"; 
            document.getElementById("new_img1").style.display="block"; 
            </script>
            </center>';   
}echo $fout;