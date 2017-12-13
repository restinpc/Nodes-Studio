<?php
/**
* Image resize library.
* @path /engine/core/image.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @example <code>
*  $img = new image("/img/1.jpg"); 
*  $img->crop(10,10,200,200); 
*  $img->resize(100, 100); 
*  $img->save("/img/", "1", "jpg", true, 100); 
* </code> crops /img/1.jpg and saves selection.
*/
class image{
private $image;
private $width; 
private $height;
private $type;
//------------------------------------------------------------------------------
/**
* Class constructor
* @param string $file Source image.
*/
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
//------------------------------------------------------------------------------
/**
* Resizes an image
*/
function resize($width = false, $height = false){
    if(is_numeric($width) && is_numeric($height) && $width > 0 && $height > 0){
            $newSize = $this->getSizeByFramework($width, $height);
    }else if(is_numeric($width) && $width > 0){
            $newSize = $this->getSizeByWidth($width);
    }else if(is_numeric($height) && $height > 0){
            $newSize = $this->getSizeByHeight($height);
    }else $newSize = array($this->width, $this->height);
    $newImage = imagecreatetruecolor($newSize[0], $newSize[1]);
    imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, 
        $newSize[0], $newSize[1], $this->width, $this->height);
    $this->image = $newImage;
    $this->setSize();
    return $this;
}
//------------------------------------------------------------------------------
/**
* Crops an image.
*/
function crop($x0 = 0, $y0 = 0, $w = false, $h = false){
    if(!is_numeric($x0) || $x0 < 0 || $x0 >= $this->width) $x0 = 0;
    if(!is_numeric($y0) || $y0 < 0 || $y0 >= $this->height) $y0 = 0;
    if(!is_numeric($w) || $w <= 0 || $w > $this->width - $x0) $w = $this->width - $x0;
    if(!is_numeric($h) || $h <= 0 || $h > $this->height - $y0) $h = $this->height - $y0;
    return $this->cropSave($x0, $y0, $w, $h);
}
//------------------------------------------------------------------------------
/**
* Crops an image and returs canvas.
*/
private function cropSave($x0, $y0, $w, $h){
    $newImage = imagecreatetruecolor($w, $h);
    imagecopyresampled($newImage, $this->image, 0, 0, $x0, $y0, $w, $h, $w, $h);
    $this->image = $newImage;
    $this->setSize();
    return $this;
}
//------------------------------------------------------------------------------
/**
* Saves image to file.
* 
* @usage <code>
*  $img = new image("/img/1.jpg");  
*  $img->save("/img/", "2", "jpg", true, 100);
* </code> copies /img/1.jpg to /img/2.jpg
*/
function save($path = '', $fileName, $type = false, $rewrite = false, $quality = 80){
    if(trim($fileName) == '' || $this->image === false) return false;
    $type = strtolower($type);
    $savePath = $path.trim($fileName).".".$type;
    if(!$rewrite && @file_exists($savePath)) return false;
    if($type == "jpeg") $type = "jpg";
    switch($type){
        case 'jpg':
            if(!is_numeric($quality) || $quality < 0 || $quality > 100) $quality = 100;
            imagejpeg($this->image, $savePath, $quality);
            chmod($savePath, 0755);
            return $savePath;
        case 'png':
            imagepng($this->image, $savePath);
            chmod($savePath, 0755);
            return $savePath;
        case 'gif':
            imagegif($this->image, $savePath);
            chmod($savePath, 0755);
            return $savePath;
        default: return false;
    }
}
//------------------------------------------------------------------------------
/**
* Parsed image type based on mime.
*/
private function setType($file){
    $size = getimagesize($file);
    $mime = strtolower(mb_substr($size['mime'], strpos($size['mime'], '/')+1));
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
        default: return false;
    }
}
//------------------------------------------------------------------------------
/**
* Calculates an image size.
*/
private function setSize(){
    $this->width = imagesx($this->image);
    $this->height = imagesy($this->image);
}
//------------------------------------------------------------------------------
/**
* Gets an image size based on arguments.
*/
private function getSizeByFramework($width, $height){
    if($this->width <= $width && $this->height <= height) 
        return array($this->width, $this->height);
    if($this->width / $width > $this->height / $height){
        $newSize[0] = $width;
        $newSize[1] = round($this->height * $width / $this->width);
    }else{
        $newSize[1] = $height;
        $newSize[0] = round($this->width * $height / $this->height);
    }return $newSize;
}
//------------------------------------------------------------------------------
/**
* Gets an image size by width.
*/
private function getSizeByWidth($width){
    if($width >= $this->width) return array($this->width, $this->height);
    $newSize[0] = $width;
    $newSize[1] = round($this->height * $width / $this->width);
    return $newSize;
}
//------------------------------------------------------------------------------
/**
* Gets an image size by height.
*/
private function getSizeByHeight($height){
    if($height >= $this->height) return array($this->width, $this->height);
    $newSize[1] = $height;
    $newSize[0] = round($this->width * $height / $this->height);
    return $newSize;
}
//------------------------------------------------------------------------------
/**
* Copies and resize an image.
* 
* @param string $src Source image path.
* @param string $dest Destination image path.
* @param int $width Destination image width in px.
* @param int $height Destination image height in px.
* @param hex $rgb Destination image background color from 0x000 to 0xfff.
* @param int $quality Destination image quality in % from 0 to 100.
* @param bool $proportions Flag to save image proportions while resizing.
* @return bool Returns TRUE on success, FALSE on failure.
* @usage <code> 
*  image::resize_image('img/1.jpg', 'img/2.jpg', 800, 600, 0xfff, 100, 0); 
* </code>
*/
static function resize_image($src, $dest, $width, $height, $rgb=0x1d1d1d, $quality=80, $proportions=0){
    if (!file_exists($src)) return false;
    $size = getimagesize($src);
    if ($size === false) return false;
    $format = strtolower(mb_substr($size['mime'], strpos($size['mime'], '/')+1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc)) return false;
    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
    imagefill($idest, 0, 0, $rgb);
    if($proportions){
        $x_ratio = $width / $size[0];
        $y_ratio = $height / $size[1];
        $ratio       = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);
        $new_width   = $use_x_ratio  ? $width  : round($size[0] * $ratio);
        $new_height  = !$use_x_ratio ? $height : round($size[1] * $ratio);
        $new_left    = $use_x_ratio  ? 0 : round(($width - $new_width) / 2);
        $new_top     = !$use_x_ratio ? 0 : round(($height - $new_height) / 2);
        imagecopyresampled($idest,$isrc,$new_left,$new_top,0,0,$new_width,$new_height,$size[0],$size[1]);  
    }else{
        imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
    }
    imagejpeg($idest, $dest, $quality);
    imagedestroy($isrc);
    imagedestroy($idest);
    return true;
}
//------------------------------------------------------------------------------
/**
* Saves base64 encoded image string to jpg file.
* 
* @param string $base64_string Base64 encoded image string.
* @param string $output_file Destination image path.
* @usage <code> 
*  image::base64_to_jpg('data:image/png;base64,iVBORw0KGgo..', 'img/file.jpg'); 
* </code>
*/
static function base64_to_jpg($base64_string, $output_file) {
    $ifp = fopen( $output_file, 'wb' ); 
    $data = explode( ',', $base64_string );
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    fclose( $ifp ); 
    return $output_file; 
}
}