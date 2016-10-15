<?php
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once ("engine/nodes/language.php");
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
//------------------------------------------------------------------------------------------------------------
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
    }
}
//------------------------------------------------------------------------------------------------------------
class acResizeImage{
    private $image;
    private $width; 
    private $height;
    private $type;
    function __construct($file){
            if (@!file_exists($file)) exit("File does not exist");
            if(!$this->setType($file)) exit("File is not an image");
            if($this->type == "png"){
                $this->image = @imagecreatefrompng($file);  
            }else{
                $this->image = @imagecreatefromjpeg($file);
            }
            $this->setSize();
    }
    function resize($width = false, $height = false){
            if(is_numeric($width) && is_numeric($height) && $width > 0 && $height > 0){
                    $newSize = $this->getSizeByFramework($width, $height);
            }else if(is_numeric($width) && $width > 0){
                    $newSize = $this->getSizeByWidth($width);
            }else if(is_numeric($height) && $height > 0){
                    $newSize = $this->getSizeByHeight($height);
            }else $newSize = array($this->width, $this->height);
            $newImage = imagecreatetruecolor($newSize[0], $newSize[1]);
            imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize[0], $newSize[1], $this->width, $this->height);
            $this->image = $newImage;
            $this->setSize();
            return $this;
    }
    function crop($x0 = 0, $y0 = 0, $w = false, $h = false){
            if(!is_numeric($x0) || $x0 < 0 || $x0 >= $this->width) $x0 = 0;
            if(!is_numeric($y0) || $y0 < 0 || $y0 >= $this->height) $y0 = 0;
            if(!is_numeric($w) || $w <= 0 || $w > $this->width - $x0) $w = $this->width - $x0;
            if(!is_numeric($h) || $h <= 0 || $h > $this->height - $y0) $h = $this->height - $y0;
            return $this->cropSave($x0, $y0, $w, $h);
    }
    function cropSquare($x0 = false, $y0 = false, $size = false){
        if(!is_numeric($size) || $size <= 0) $size = false;
        if(!is_numeric($x0) && !is_numeric($y0)){
            if($this->width < $this->height){
                $x0 = 0;
                if(!$size || $size > $this->width){
                    $size = $this->width;
                    $y0 = round(($this->height - $size) / 2);
                }else $y0 = 0;
            }else{
                $y0 = 0;
                if(!$size || $size > $this->height){
                    $size = $this->height;
                    $x0 = round(($this->width - $size) / 2);
                }else $x0 = 0;
            }
        }else{
            if(!is_numeric($x0) || $x0 <= 0 || $x0 >= $this->width) $x0 = 0;
            if(!is_numeric($y0) || $y0 <= 0 || $y0 >= $this->height) $y0 = 0;
            if(!$size || $this->width < $size + $x0) $size = $this->width - $x0;
            if(!$size || $this->height < $size + $y0) $size = $this->height - $y0;
        }return $this->cropSave($x0, $y0, $size, $size);
    }
    private function cropSave($x0, $y0, $w, $h){
            $newImage = imagecreatetruecolor($w, $h);
            imagecopyresampled($newImage, $this->image, 0, 0, $x0, $y0, $w, $h, $w, $h);
            $this->image = $newImage;
            $this->setSize();
            return $this;
    }
    function thumbnail($width, $height, $c = 2){
            if(!is_numeric($width) || $width <= 0) $width = $this->width;
            if(!is_numeric($height) || $height <= 0) $height = $this->height;
            if(!is_numeric($c) || $c <= 1) $c = 2;
            $newSize = $this->getSizeByThumbnail($width, $height, $c);
            $newImage = imagecreatetruecolor($newSize[0], $newSize[1]);
            imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize[0], $newSize[1], $this->width, $this->height);
            $this->image = $newImage;
            $this->setSize();
            return $this;
    }
    function save($path = '', $fileName, $type = false, $rewrite = false, $quality = 100){
        if(trim($fileName) == '' || $this->image === false) return false;
        $type = strtolower($type);
        $savePath = $path.trim($fileName).".".$type;
        if(!$rewrite && @file_exists($savePath)) return false;
        if($type == "jpeg") $type = "jpg";
        switch($type){
                case 'jpg':
                        if(!is_numeric($quality) || $quality < 0 || $quality > 100) $quality = 100;
                        imagejpeg($this->image, $savePath, $quality);
                        return $savePath;
                case 'png':
                        imagepng($this->image, $savePath);
                        return $savePath;
                case 'gif':
                        imagegif($this->image, $savePath);
                        return $savePath;
                default:
                        return false;
        }
    }

    private function setType($file){
        $mime = strtolower(array_pop(explode(".", $file)));
        switch($mime){
            case 'jpg':
                $this->type = "jpg";
                return true;
            case 'jpeg':
                $this->type = "jpg";
                return true; 
            case 'png':
                $this->type = "png";
                return true;
            case 'gif':
                $this->type = "gif";
                return true;
            default:
                return false;
        }
    }

    private function setSize(){
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    private function getSizeByFramework($width, $height)
    {
            if($this->width <= $width && $this->height <= height) 
                    return array($this->width, $this->height);
            if($this->width / $width > $this->height / $height)
            {
                    $newSize[0] = $width;
                    $newSize[1] = round($this->height * $width / $this->width);
            }
            else
            {
                    $newSize[1] = $height;
                    $newSize[0] = round($this->width * $height / $this->height);
            }
            return $newSize;
    }

    private function getSizeByWidth($width)
    {
            if($width >= $this->width) return array($this->width, $this->height);
            $newSize[0] = $width;
            $newSize[1] = round($this->height * $width / $this->width);
            return $newSize;
    }

    private function getSizeByHeight($height)
    {
            if($height >= $this->height) return array($this->width, $this->height);
            $newSize[1] = $height;
            $newSize[0] = round($this->width * $height / $this->height);
            return $newSize;
    }

    private function getSizeByThumbnail($width, $height, $c)
    {
            if($this->width <= $width && $this->height <= height) 
                    return array($this->width, $this->height);
            $realW = $this->width;
            $realH = $this->height;

            $rotate = false;
            if($width / $realW <= $height / $realH)
            {
                    $t = $realH;
                    $realH = $realW;
                    $realW = $t;
                    $t = $width;
                    $width = $height;
                    $height = $t;
                    $rotate = true;
            }

            $limX = $c * $width;
            $limY = $c * $height;
            $possH = $realH * $width / $realW;

            if($realW > $width)
            {
                    if($possH <= $limY)
                    {
                            $newSize[0] = $width;
                            $newSize[1] = round($possH);
                    }
                    else
                    {
                            if($possH <= 2 * $limY)
                            {
                                    $newSize[1] = $limY;
                                    $newSize[0] = $realW * $newSize[1] / $realH;
                            }
                            else
                            {
                                    $newSize[0] = $width / 2;
                                    $newSize[1] = $realH * $newSize[0] / $realW;
                            }
                    }
            }
            else
            {
                    if($realH <= $limY)
                    {
                            $newSize[0] = $realW;
                            $newSize[1] = $realH;
                    }
                    else
                    {
                            if($realH <= 2 * $limY)
                            {
                                    if($realW * $limY / $realH >= $width / 2)
                                    {
                                            $newSize[1] = $limY;
                                            $newSize[0] = $realW * $limY / $realH;
                                    }
                                    else
                                    {
                                            $newSize[0] = $width / 2;
                                            $newSize[1] = $realH * $newSize[0] / $realW;
                                    }
                            }
                            else
                            {
                                    $newSize[0] = $width / 2;
                                    $newSize[1] = $realH * $newSize[0] / $realW;
                            }
                    }
            }
            if($rotate)
            {
                    $t = $newSize[0];
                    $newSize[0] = $newSize[1];
                    $newSize[1] = $t;
            }
            return $newSize;
    }
}
function resize_image($src, $dest, $width, $height, $rgb=0x1d1d1d, $quality=100){
    if (!file_exists($src)) return false;
    $size = getimagesize($src);
    if ($size === false) return false;
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;
    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    imagejpeg($idest, $dest, $quality);
    imagedestroy($isrc);
    imagedestroy($idest);
    return true;
}
echo '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style>
.btn{
    color: #FFFFFF;
    padding: 10px 15px;
    display: inline-block;
    transition: all .2s ease-in;
    -webkit-transition: all .2s ease-in;
    -moz-transition: all .2s ease-in;
    border: none;
    font-size: 19px;
    text-align: center;
    cursor: pointer;
    -webkit-font-smoothing: antialiased;
    font-weight:normal;
    background: #c0c0c0;
    color: #fff;
    border-radius: 2px;
}

.btn:hover{
    background:#cacaca;
}

button, .button{
    border-radius: 4px;
    padding: 4px;
    cursor:pointer;
    border: #DDD 1px solid;
    border-bottom: #aaa 1px solid;
    border-right: #aaa 1px solid;
    background: #EEE;
    color:#4e5d8e;
    font-weight: bold;
    border-collapse:collapse;
}
.button:hover, button:hover{
    background: #3d85be;
    color: #fff;
    border: #3d85be 1px solid;
}
.button:active, button:active{
    background: #2e3d6e;
    color: #fff;
    border: #2e3d6e 1px solid;
}
.input{
    background: #fff;
    border: #b2bcc4 1px solid;
    padding: 3px;
    font-family: Sans-Serif;
    -webkit-font-smoothing: antialiased;
    font-size: 14px;
}
.input:-moz-placeholder { 
    color: #c0c0c0; 
}
.input::-webkit-input-placeholder { 
    color: #c0c0c0; 
}
</style>
<script type="text/javascript">     
function addHandler(object, event, handler, useCapture) {
     if (object.addEventListener) {
         object.addEventListener(event, handler, useCapture ? useCapture : false);
     } else if (object.attachEvent) {
         object.attachEvent(\'on\' + event, handler);
     } else alert("Add handler is not supported");
}
var width='.$THUMB_WIDTH.';
var height='.$THUMB_HEIGHT.';
var posx = 30;
var posy = 30;';

if(!empty($_FILES["img"]) || (!empty($_POST["url"])) || !empty($_POST["new_image"])){
    echo "
addHandler(window, 'load', load);
addHandler(window, 'touchmove', drag);
addHandler(window, 'mousemove', drag);
addHandler(window, 'mouseup', undrag);
addHandler(top, 'mouseup', undrag);
addHandler(window, 'dblclick', submit_img);
    ";
}

echo '
var drag_mode = 0;
var fx = 0;
var fy = 0;
function drag(e){
    if (!e) var e = window.event;
    if (e.pageX || e.pageY) 	{
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY) 	{
    posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
    posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    try{
        var touchobj = e.changedTouches[0] ;
        posx = parseInt(touchobj.clientX);
        posy = parseInt(touchobj.clientY);
    }catch(e){}
    if(drag_mode == 1){
        if(fx==0){
            fx = posx-parseInt(document.getElementById("frame").style.left);
        }if(fy==0){
            fy = posy-parseInt(document.getElementById("frame").style.top);
        }
        posx -= fx;
        posy -= fy;
        if(posx>document.getElementById("img").clientWidth-document.getElementById("frame").clientWidth+34) 
            posx = document.getElementById("img").clientWidth-document.getElementById("frame").clientWidth+34;
        if(posy>document.getElementById("img").clientHeight-document.getElementById("frame").clientHeight+34) 
            posy = document.getElementById("img").clientHeight-document.getElementById("frame").clientHeight+34;
        if(posy<34) posy = 34; 
        if(posx<34) posx = 34;
        document.getElementById("frame").style.left = (posx-6)+"px";
        document.getElementById("frame").style.top = (posy-6)+"px";
        document.getElementById("t").value = posy-32;
        document.getElementById("l").value = posx-32;
    }else if(drag_mode == 2){
        var width1 = posx - parseInt(document.getElementById("frame").style.left);
        if(width1+parseInt(document.getElementById("frame").style.left)>document.getElementById("img").clientWidth+24){
            width1 = document.getElementById("img").clientWidth-parseInt(document.getElementById("frame").style.left)+24;
        }
        var height1 = width1 * '.($THUMB_HEIGHT/$THUMB_WIDTH).';

        if(
            (width1-26 <= document.getElementById("img").clientWidth-parseInt(document.getElementById("frame").style.left))
            &&
            (height1-26 <= document.getElementById("img").clientHeight-parseInt(document.getElementById("frame").style.top))
            && 
            height1 >= '.($THUMB_HEIGHT/2).' 
            && 
            width1 >= '.($THUMB_WIDTH/2).'
        ){
            width = width1+4;
            height = height1+4;
            document.getElementById("frame").style.width = width+"px";
            document.getElementById("frame").style.height = height+"px";   
            document.getElementById("w").value = width;
            document.getElementById("h").value = height;
        }
    }else{
        fx = 0;
        fy = 0;
    }
}

function submit_img(){
    if(confirm("'.lang("Upload selection as thumb?").'")){
        document.getElementById("form").submit();
    }
}
function load(){
    if(document.getElementById("img").clientHeight < height){
        height = document.getElementById("img").clientHeight;
        width = parseInt('.($THUMB_WIDTH/$THUMB_HEIGHT).'*height);
    }else if(document.getElementById("img").clientWidth < width){
        width = document.getElementById("img").clientWidth
        height = parseInt('.($THUMB_HEIGHT/$THUMB_WIDTH).'*width);
    }
    document.getElementById("frame").style.width = width;
    document.getElementById("frame").style.height = height;
    document.getElementById("frame").style.display = "block"; 
}   
function undrag(){
    drag_mode=0;
}
        </script>
    </head>';

    if(!empty($_POST["name"])){
        if(!empty($_POST["url"])){         
            $ext = strtolower(array_pop(explode(".", $_POST["url"])));
            $name = md5($_POST["filename"]+date("U"));
            $img = new acResizeImage($_POST["url"]); 
            $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/', $name, $ext, true, 100);
            $img->crop($_POST["l"], $_POST["t"], $_POST["w"], $_POST["h"]); 
            $img->resize($THUMB_WIDTH, $THUMB_HEIGHT); 
            $path = $img->save($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/thumb/', $name, $ext, true, 100); 
            echo '<img src="'.$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.'" />'
                    . '<script type="text/javascript">'
                    . 'parent.document.getElementById("'.$result_file.'").value="'. $name.'.'.$ext.'";'
                    . 'var df_img = document.createElement("img"); df_img.id = "d_img";
                    df_img.src = "http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.'";';
            if(!empty($_GET["id"]) && $_GET["id"]<6){
                echo 'var z = parent.document.getElementById("new_img'.(intval($_GET["id"])+1).'"); if(z) z.style.display = "block";';
                echo 'parent.document.getElementById("new_img'.(intval($_GET["id"])).'").style.width = "'.$THUMB_WIDTH.'px";';
            }
            echo 'parent.document.getElementById("'.$result.'").style.display = "none"; ';
            echo 'parent.document.getElementById("'.$new_img.'").appendChild(df_img);'
                . 'parent.document.getElementById("'.$f1.'").style.width=('.($THUMB_WIDTH+20).'+"px");'
                . 'parent.document.getElementById("'.$f1.'").style.height=('.($THUMB_HEIGHT+20).'+"px");
                    try{
                        var ii = 0;
                        for(var i = 1; i < 5; i++){
                            ii = i+1;
                            try{
                                var el = parent.parent.document.getElementById("new_photo_"+i);
                                if(el.style.display == "none"){
                                    el.style.background = "url('.$_SERVER["DIR"].'/img/data/thumb/'. $name.'.'.$ext.') center no-repeat";
                                    el.style.display = "block";
                                    top.document.getElementById("file"+i).value = "'. $name.'.'.$ext.'";
                                    break;
                                }
                            }catch(e){ break; };
                        }if(ii>0){
                            var new_photo_el = top.document.getElementById("file"+ii);
                            if(!new_photo_el) try{ top.document.getElementById("upload_btn").style.display = "none"; }catch(e){ };
                        }
                    }catch(e){ };
                    try{
                        var img = parent.document.getElementById("result_file1");
                        if(img.value != ""){
                            parent.document.getElementById("edit_photos_form").submit();
                        }
                    }catch(e){ };
                    try{ top.hide_photo_editor(); }catch(e){  };
                        '
            .'</script>';                
            die($fout);
        }else if(!empty($_POST["new_image"])){
            $file = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/img/data/big/'.$_POST["new_image"];
            $size = getimagesize($file);
            if($size[0]<$THUMB_WIDTH||$size[1]<$THUMB_HEIGHT) 
                return lang("Image too small. Minimal size is 400x400").'.'
                . '<script type="text/javascript">setTimeout(function(){window.location="'.$_SERVER["DIR"].'/uploader.php?id='.$_GET["id"].'";}, 1000);</script>';
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
                        resize_image($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $width, $height);
                    }else{
                        $height = MAX_IMG_HEIGHT;
                        $width = $size[0]*(MAX_IMG_HEIGHT/$size[1]);
                        resize_image($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name, $width, $height);
                    }
                }
                $size = getimagesize($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$f_name);
                $width = intval($size[0]);
                $height = intval($size[1]);

                $fout = '<body style="padding:28px; padding-bottom: 0px; margin:0px; background: #fff; overflow: scroll;" title="'.lang("For uploading selected area use double click").'">'
                        . '<div id="image" style="background: url('.$_SERVER["DIR"].'/'.$f_name.') top left no-repeat;"/>'
                            . '<img id="img" src="'.$_SERVER["DIR"].'/'.$f_name.'" style="visibility:hidden;" />'
                        . '</div>'
                        . '<div id="frame" style="position: absolute;top: 28px;left: 28px;width:'.$THUMB_WIDTH.'px; height: '.$THUMB_HEIGHT.'px; display:block;">'
                            . '<table cellpadding=0 cellspacing=0 style="width: 100%; height: 100%; padding:0px; margin: 0px; border: #f00 2px dotted; cursor:pointer; background: rgba(200, 80, 80, 0.2); "'
                        . 'onMouseDown=\'if(drag_mode!=3)drag_mode=1;\'>'
                            . ' <tr>
                                <td align=left valign=top></td>
                                </tr>'
                            . '</table>'
                        . '   <div style="float:right; margin-right: -10px; margin-top: -14px; padding: 10px;cursor: se-resize;" id="bottom_dot" onMouseDown=\'drag_mode=2;\' draggable="false">'
                        . '   <div style="width: 4px; height: 4px; background: #f00;"> </div></div>'
                        . '</div>'
                        . '<br/>'
                        . '<form method="POST" id="form" style="text-align:center;">'
                        . '<input type="hidden" name="name" value="'.$_POST["name"].'"/>'
                        . '<input type="hidden" name="url" value="'.$_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.($f_name).'"/>'
                        . '<input type="hidden" name="thumb" value="1"/>'
                        . '<input type="hidden" name="filename" value="'.$a.'"/>'
                        . '<input type="hidden" id="t" name="t" value="0"/>'
                        . '<input type="hidden" id="l" name="l" value="0"/>'
                        . '<input type="hidden" id="w" name="w" value="'.$THUMB_WIDTH.'"/>'
                        . '<input type="hidden" id="h" name="h" value="'.$THUMB_HEIGHT.'"/>'
                        . '<input type="submit" class="btn" style="width: 280px;" value="'.lang("Crop image").'" />'
                        . '</form><script type="text/javascript">
                            addHandler(document.getElementById("frame"), "touchstart", function(){ drag_mode=1; });
                            addHandler(document.getElementById("frame"), "touchend", function(){ drag_mode=0; });
                            addHandler(document.getElementById("bottom_dot"), "touchend", function(){ drag_mode=0; });
                            addHandler(document.getElementById("bottom_dot"), "touchstart", function(){ drag_mode=2; });
                            window.parent.document.getElementById("'.$f1.'").style.width="'.($width+60).'px";
                            window.parent.document.getElementById("'.$f1.'").style.height="'.($height+120).'px";
                            </script>';
                    die($fout.'</body></html>');
            }  die(lang("Error").'<script type="text/javascript">setTimeout(function(){window.location="'.$_SERVER["DIR"].'/uploader.php?id='.$_GET["id"].'";}, 1000);</script>');
        }
    }
    
    $fout = '<body> 
<style>
body{
    background: #fff; 
    border: #b2bcc4 0px solid; 
    padding: 6px; 
    text-align:center;
}
h1, h2{
    font-size: 1.5em;
    font-weight: normal;
}
h2{
    font-size: 1.3em;
}
legend{
    font-weight: bold;
    color: #333;
}
#filedrag{
    display: none;
    font-weight: bold;
    text-align: center;
    padding: 1em 0;
    margin: 1em 0;
    color: #555;
    border: 2px dashed #555;
    border-radius: 7px;
    cursor: pointer;
    width: 100%;
    height: 100%;
    padding-top: 70px;
    padding-bottom: 70px;
}
#filedrag.hover{
    color: #f00;
    border-color: #f00;
    border-style: solid;
    box-shadow: inset 0 3px 4px #888;
}
img{
    max-width: 100%;
}
pre{
    width: 95%;
    height: 8em;
    font-family: monospace;
    font-size: 0.9em;
    padding: 1px 2px;
    margin: 0 0 1em auto;
    border: 1px inset #666;
    background-color: #eee;
    overflow: auto;
}
#messages{
    padding: 0 10px;
    margin: 1em 0;
    border: 1px solid #999;
    display:none;
}
#progress p{
    display: block;
    width: 240px;
    padding: 2px 5px;
    margin: 2px 0;
    border: 1px inset #446;
    border-radius: 5px;
    background: #eee url("progress.png") 100% 0 repeat-y;
}
#progress p.success{
    background: #0c0 none 0 0 no-repeat;
}
#progress p.failed{
    background: #c00 none 0 0 no-repeat;
}
#fileselect{
    display:none;
}
</style>

<form id="upload" method="POST" enctype="multipart/form-data">
    <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="10485760" />
    <div>
        <input type="file" id="fileselect" name="fileselect" onChange=\'document.getElementById("upload").submit();\' />
        <div id="filedrag" onClick=\'document.getElementById("fileselect").click();\'>'.lang("Drop file here").'</div>
    </div>
    <div id="submitbutton">
        <button type="submit">'.lang("Upload Files").'</button>
    </div>
    <input type="hidden" name="name" value="'.lang("Uploaded").' '.date("Y-m-d H:i:s").'" />
</form>

<form method="POST" id="new_image_form">
    <input type="hidden" name="name" value="'.lang("Uploaded").' '.date("Y-m-d H:i:s").'" />
    <input type="hidden" name="new_image" value="" id="new_image" />
</form>
<div id="messages">
</div>

<script>
(function() {

	function $id(id) {
            return document.getElementById(id);
	}

	function Output(msg) {
            var m = $id("messages");
            m.innerHTML = msg + m.innerHTML;
	}

	function FileDragHover(e) {
            e.stopPropagation();
            e.preventDefault();
            e.target.className = (e.type == "dragover" ? "hover" : "");
	}

	function FileSelectHandler(e) {
            FileDragHover(e);
            var files = e.target.files || e.dataTransfer.files;
            for (var i = 0, f; f = files[i]; i++) {
                ParseFile(f);
            }
	}

	function ParseFile(file) {
            if (file.type.indexOf("image") == 0) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    UploadFile(file);
                }
                reader.readAsDataURL(file);
            }
	}
        
	function UploadFile(file) {
            if (location.host.indexOf("sitepointstatic") >= 0) return;
            if( $id("fileselect").value != "") return;
            var xhr = new XMLHttpRequest();
            if (xhr.upload && (file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/gif" || file.type == "image/png") && file.size <= $id("MAX_FILE_SIZE").value) {
                var fname = file.name;
                xhr.onreadystatechange = function(e) {
                    if (xhr.readyState == 4) {
                        if(xhr.responseText == "error"){
                            document.getElementById("fileselect").style.display="block";
                            document.getElementById("filedrag").style.display="none";
                            alert("'.lang("Error! Drag-n-drop disabled on this server").'");
                        }else{
                            document.getElementById("new_image").value=fname;
                            document.getElementById("new_image_form").submit();
                        }
                    }
                };
                xhr.open("POST", "'.$_SERVER["DIR"].'/uploader.php?dragndrop=1", true);
                xhr.setRequestHeader("X_FILENAME", file.name);
                xhr.setRequestHeader("HTTP_X_FILENAME", file.name);
                xhr.send(file);
                document.getElementById("filedrag").innerHTML = "'.lang("Uploading").'.."; 
            }
	}

	function Init() {
            var fileselect = $id("fileselect"),
                filedrag = $id("filedrag"),
                submitbutton = $id("submitbutton");
            fileselect.addEventListener("change", FileSelectHandler, false);
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                filedrag.addEventListener("dragover", FileDragHover, false);
                filedrag.addEventListener("dragleave", FileDragHover, false);
                filedrag.addEventListener("drop", FileSelectHandler, false);
                filedrag.style.display = "block";
                submitbutton.style.display = "none";
            }
	}

	if (window.File && window.FileList && window.FileReader) {
            Init();
	}
})();
</script>
';
    
    echo $fout.'
</body>
</html>';