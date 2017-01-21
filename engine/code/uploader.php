<?php
/**
* Image croper & uploader.
* @path /engine/code/uploader.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/language.php");
require_once("engine/core/image_resize.php");
if(empty($_SESSION["user"]["id"])) die(engine::error(401));
define("MAX_IMG_WIDTH", 1000);
define("MAX_IMG_HEIGHT", 1000);
$THUMB_WIDTH = 400;
if(!empty($_GET["width"])) $THUMB_WIDTH = $_GET["width"];
$THUMB_HEIGHT = 400;
if(!empty($_GET["height"])) $THUMB_HEIGHT = $_GET["height"];
$f1 = "f1";
if(!empty($_GET["id"])) $f1 .= $_GET["id"];
$result_file = "result_file";   
if(!empty($_GET["id"])) $result_file .= $_GET["id"];
$result_caption = "result_caption";
if(!empty($_GET["id"])) $result_caption .= $_GET["id"];
$new_img = "new_img";
if(!empty($_GET["id"])) $new_img .= $_GET["id"];
$result = "result";
if(!empty($_GET["id"])) $result .= $_GET["id"];
if(!empty($_GET["dragndrop"])||!empty($_FILES)){
    $fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
    if ($fn) {
        if(file_put_contents(
                $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/' . $fn,
                file_get_contents('php://input')
        )){
            die(lang('Ok'));
        }else{
            die(lang('Error'));
        }
    }else if(isset($_FILES['fileselect'])) {
        $files = $_FILES['fileselect'];
        $ext = explode('.', $files['name']);
        $fn = md5($files['name']).'.'.$ext[count($ext)-1];
        if(copy(
            $files['tmp_name'],
            $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/' . $fn
        )){
            die('<form method="POST" id="new_image_form">
            <input type="hidden" name="name" value="'.lang("Uploaded").' '.date("Y-m-d H:i:s").'" />
            <input type="hidden" name="new_image" value="'.$fn.'" id="new_image" />
            </form><script>document.getElementById("new_image_form").submit();</script>');
        }else{
            die(lang('Error'));
        }
    }die();
}
$query = 'SELECT * FROM `nodes_config` WHERE `name` = "template"';
$res = engine::mysql($query);
$data = mysql_fetch_array($res);
$template = $data["value"];
echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="'.$_SERVER["DIR"].'/template/nodes.css" rel="stylesheet" type="text/css">
<link href="'.$_SERVER["DIR"].'/template/uploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript">     
    var width = twidth='.$THUMB_WIDTH.';
    var height = theight='.$THUMB_HEIGHT.';
    var no_drag_dpop = "'.lang("Error! Drag-n-drop disabled on this server").'";  
    var uploading = "'.lang("Uploading").'";
    var confirm_upload = "'.lang("Upload selection as thumb?").'";
    var post_new_image = '.(!empty($_POST["new_image"])?'1':'0').';
    var dir = "'.$_SERVER["DIR"].'";
    var posx = posy = 30;
    var fx = fy = drag_mode = 0;
</script>
<script src="'.$_SERVER["DIR"].'/script/uploader.js" type="text/javascript"></script>
</head>';

if(!empty($_POST["name"])){
    if(!empty($_POST["url"])){
        $ext = strtolower(array_pop(explode(".", $_POST["url"])));
        $name = md5($_POST["filename"]+date("U"));
        $img = new image_resize($_POST["url"]); 
        $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/', $name, $ext, true, 100);
        unlink($_POST["url"]);
        $img->crop($_POST["l"], $_POST["t"], $_POST["w"], $_POST["h"]); 
        $img->resize($THUMB_WIDTH, $THUMB_HEIGHT); 
        $path = $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/thumb/', $name, $ext, true, 100); 
        $fout .= '<body class="nodes result_body">
            <img src="'.$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.'" />
            <script type="text/javascript">
                try{
                    parent.document.getElementById("'.$result_file.'").value="'. $name.'.'.$ext.'";
                    var df_img = document.createElement("img"); 
                    df_img.id = "d_img";
                    df_img.src = "//'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.'";';
        
        if(!empty($_GET["id"]) && $_GET["id"]<6){
            $fout .= '
                    var z = parent.document.getElementById("new_img'.(intval($_GET["id"])+1).'"); 
                    if(z) z.style.display = "block";
                    parent.document.getElementById("new_img'.(intval($_GET["id"])).'").style.width = "'.$THUMB_WIDTH.'px";';
        }
        $fout .= '
                    parent.document.getElementById("'.$result.'").style.display = "none";
                    parent.document.getElementById("'.$new_img.'").appendChild(df_img);
                    parent.document.getElementById("'.$f1.'").style.width=('.($THUMB_WIDTH+20).'+"px");
                    parent.document.getElementById("'.$f1.'").style.height=('.($THUMB_HEIGHT+20).'+"px");
                }catch(e){ console.log("error 1"); }     
                try{
                    var ii = 0;
                    for(var i = 1; i < 5; i++){
                        ii = i+1;
                        try{
                            var el = top.document.getElementById("new_photo_"+i);
                            if(el.title == "none"){
                                el.style.background = "url('.$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.') center no-repeat";
                                el.style.display = "block";
                                el.title = "";
                                top.document.getElementById("file"+i).value = "'. $name.'.'.$ext.'";
                                break;
                            }
                        }catch(e){ console.log("error 2"); break; };
                    }if(ii>0){
                        var new_photo_el = top.document.getElementById("file"+ii);
                        if(!new_photo_el) try{ top.document.getElementById("upload_btn").style.display = "none"; }catch(e){ };
                    }
                }catch(e){ console.log("error 3");  };
                try{
                    var img = parent.document.getElementById("result_file1");
                    if(img.value != ""){
                        parent.document.getElementById("edit_photos_form").submit();
                    }
                }catch(e){ console.log("error 4"); };
            </script>
        </body>
        </html>';                
        die($fout);
    }else if(!empty($_POST["new_image"])){
        $file = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/'.$_POST["new_image"];
        $size = getimagesize($file);
        if($size[0]<$THUMB_WIDTH||$size[1]<$THUMB_HEIGHT) 
            die('<script type="text/javascript">alert("'.lang("Image too small. Minimal size is 400x400").'."); window.location="'.$_SERVER["DIR"].'/uploader.php?id='.$_GET["id"].'";</script></html>');
        $f_name = "";
        $a = md5(date('U').$file);
        $ext = strtolower(array_pop(explode(".", $file)));
        if($ext != "jpeg" && $ext != "jpg" && $ext != "png" && $ext != "gif"){
             die(lang("Error").'<script type="text/javascript">setTimeout(function(){window.location="'.$_SERVER["DIR"].'/uploader.php?id='.$_GET["id"].'";}, 1000);</script>'); 
        }if($ext == "jpeg") $ext = "jpg";
        $f_name = "img/data/big/".$a.".".$ext;
        $thumb_name = "img/data/thumb/".$a.".".$ext;
        if (rename($file, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name)){
            $size = getimagesize($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name);
            if($size[0] > MAX_IMG_WIDTH || $size[1] > MAX_IMG_HEIGHT){
                if($size[0]/MAX_IMG_WIDTH > $size[1]/MAX_IMG_HEIGHT){
                    $width = MAX_IMG_WIDTH;
                    $height = $size[1]*(MAX_IMG_WIDTH/$size[0]);
                    image_resize::resize_image($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $width, $height);
                }else{
                    $height = MAX_IMG_HEIGHT;
                    $width = $size[0]*(MAX_IMG_HEIGHT/$size[1]);
                    image_resize::resize_image($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $width, $height);
                }
            }
            $size = getimagesize($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name);
            $width = intval($size[0]);
            $height = intval($size[1]);
            $fout = '<body class="nodes uploader_body" draggable="false" title="'.lang("For uploading selected area use double click").'">
                <div id="image" draggable="false" style="background: url('.$_SERVER["DIR"].'/'.$f_name.') top left no-repeat;" >
                    <img id="img" draggable="false" src="'.$_SERVER["DIR"].'/'.$f_name.'" />
                </div>
                <div id="frame" draggable="false" style="width:'.$THUMB_WIDTH.'px; height:'.$THUMB_HEIGHT.'px;position: absolute;top: 28px;left: 28px;display:block;">
                    <table draggable="false" cellpadding=0 cellspacing=0 onMouseDown=\'if(drag_mode!=3)drag_mode=1;\'>
                    <tr><td align=left valign=top></td></tr></table>
                    <div id="bottom_dot" onMouseDown=\'drag_mode=2;\' draggable="false">
                        <div class="dot" draggable="false"> </div>
                    </div>
                </div><br/>
                <form method="POST" id="form">
                    <input type="hidden" name="name" value="'.$_POST["name"].'"/>
                    <input type="hidden" name="url" value="'.$_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.($f_name).'"/>
                    <input type="hidden" name="thumb" value="1"/>
                    <input type="hidden" name="filename" value="'.$a.'"/>
                    <input type="hidden" id="t" name="t" value="0"/>
                    <input type="hidden" id="l" name="l" value="0"/>
                    <input type="hidden" id="w" name="w" value="'.$THUMB_WIDTH.'"/>
                    <input type="hidden" id="h" name="h" value="'.$THUMB_HEIGHT.'"/>
                    <input type="submit" class="btn w280" value="'.lang("Crop image").'" />
                </form>
                <script type="text/javascript">
                    addHandler(document.getElementById("frame"), "touchstart", function(){ drag_mode=1; });
                    addHandler(document.getElementById("frame"), "touchend", function(){ drag_mode=0; });
                    addHandler(document.getElementById("bottom_dot"), "touchend", function(){ drag_mode=0; });
                    addHandler(document.getElementById("bottom_dot"), "touchstart", function(){ drag_mode=2; });
                    try{
                        window.parent.document.getElementById("'.$f1.'").style.width="'.($width+60).'px";
                        window.parent.document.getElementById("'.$f1.'").style.height="'.($height+120).'px";
                    }catch(e){}
                </script>
            </body>
            </html>';
            die($fout);
        }die(lang("Error").'<script type="text/javascript">setTimeout(function(){window.location="'.$_SERVER["DIR"].'/uploader.php?id='.$_GET["id"].'";}, 5000);</script></html>');
    }
}else{
    echo '<body class="nodes dragndrop_body"> 
<form id="upload" method="POST" enctype="multipart/form-data">
    <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="10485760" />
    <div style="height: 100%;">
        <input type="file" id="fileselect" name="fileselect" onChange=\'document.getElementById("upload").submit();\' />
        <div id="filedrag" onClick=\'document.getElementById("fileselect").click();\'>'.lang("Drop file here").'</div>
    </div>
    <div id="submitbutton">
        <button class="btn" type="submit">'.lang("Upload Files").'</button>
    </div>
    <input type="hidden" name="name" value="'.lang("Uploaded").' '.date("Y-m-d H:i:s").'" />
</form>
<form method="POST" id="new_image_form">
    <input type="hidden" name="name" value="'.lang("Uploaded").' '.date("Y-m-d H:i:s").'" />
    <input type="hidden" name="new_image" value="" id="new_image" />
</form>
<div id="messages"></div>
<script>(function(){if(window.File&&window.FileList&&window.FileReader){Init();}})();</script>
</body>
</html>';
}