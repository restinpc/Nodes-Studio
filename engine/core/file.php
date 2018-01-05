<?php
/**
* File managment library.
* @path /engine/core/file.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @example <code>
*  if(file::zip('/temp', '/temp/files.zip')){
*      file::delete('/temp');
*  }
* </code>
*/
class file{
//------------------------------------------------------------------------------
/**
* Backup a file, or recursively copy a folder and its contents.
* 
* @param string $source Source path.
* @param string $dest Destination path.
* @param int $permissions New folder creation permissions.
* @return bool Returns TRUE on success, FALSE on failure.
* @usage <code> file::copy("/img", "/temp"); </code>
*/
static function copy($source, $dest, $permissions = 0755){
    if (is_link($source)) return symlink(readlink($source), $dest);
    if (is_file($source)){ 
        $res = copy($source, $dest);
        chmod($dest, $permissions);
        return $res;
    }
    if (!is_dir($dest)) mkdir($dest, $permissions);
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        if ($entry == '.' || 
            $entry == '..' || 
            $entry == 'backup' || 
            $entry == 'session') 
                continue;
        self::copy("$source/$entry", "$dest/$entry", $permissions);
    }
    $dir->close();
    return true;
}
//------------------------------------------------------------------------------
/**
* Recursively delete a directory.
* 
* @param string $dir Destination path.
* @return bool Returns TRUE on success, FALSE on failure.
* @usage <code> file::delete("/temp"); </code>
*/
static function delete($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) 
            self::delete("$dir/$file");
        else unlink("$dir/$file");
    }rmdir($dir);
    return true;
}
//------------------------------------------------------------------------------
/**
* Uploading file from FILES request data to server.
* 
* @param string $filename Source input name.
* @param string $path Destination path.
* @param bool $md5 MD5 name modification.
* @return string Returns filename on success, 'error' on failure.
* @usage <code> file::upload("new_image", "/img", true); </code>
*/
static function upload($filename, $path, $md5=0){
    if(!is_array($_FILES[$filename]["name"])){
        if (is_uploaded_file($_FILES[$filename]['tmp_name'])){
            if(!$md5){
                $a = $_FILES[$filename]["name"];
            }else{
                $a = md5($_FILES[$filename]["name"].date("U")).".".strtolower(array_pop(explode(".", $_FILES[$filename]["name"])));;
            }$f_name = $path."/".$a;
            if (move_uploaded_file($_FILES[$filename]["tmp_name"], $f_name)){
                return $a;
            } return 'error';
        } return 'error';
    }else{
        $fout = '';
        for($i = 0; $i < count($_FILES[$filename]['tmp_name']); $i++){
            if (is_uploaded_file($_FILES[$filename]['tmp_name'][$i])){
                if(!$md5){
                    $a = $_FILES[$filename]["name"][$i];
                }else{
                    $a = md5($_FILES[$filename]["name"][$i].date("U")).".".strtolower(array_pop(explode(".", $_FILES[$filename]["name"][$i])));;
                }
                $f_name = $path."/".$a;
                if (move_uploaded_file($_FILES[$filename]["tmp_name"][$i], $f_name)){
                    $fout .= $a.';';
                }
            } 
        } return $fout;
    }
 }
//------------------------------------------------------------------------------
/**
* Add files and sub-directories in a folder to zip file. 
* 
* @param string $folder Input directory
* @param ZipArchive $zipFile Link to file
* @param int $exclusiveLength Number of text to be exclusived from the file path. 
*/ 
private static function zip_folder($folder, &$zipFile, $exclusiveLength) { 
    $handle = opendir($folder); 
    while (false !== $f = readdir($handle)) { 
      if ($f != '.' && $f != '..') { 
        $filePath = "$folder/$f";
        $localPath = mb_substr($filePath, $exclusiveLength); 
        if (is_file($filePath)) { 
          $zipFile->addFile($filePath, $localPath); 
        } elseif (is_dir($filePath)) { 
          $zipFile->addEmptyDir($localPath); 
          self::zip_folder($filePath, $zipFile, $exclusiveLength); 
        } 
      } 
    } 
    closedir($handle); 
} 
//------------------------------------------------------------------------------
/**
* Zip a folder (include itself). 
* 
* @param string $sourcePath Path of directory to be zip. 
* @param string $outZipPath Path of output zip file. 
* @usage <code> file::zip('/img', '/backup/img.zip'); </code> 
*/ 
static function zip($sourcePath, $outZipPath) { 
    $pathInfo = pathInfo($sourcePath); 
    $parentPath = $pathInfo['dirname']; 
    $dirName = $pathInfo['basename']; 
    $z = new ZipArchive(); 
    $z->open($outZipPath, ZIPARCHIVE::CREATE); 
    $z->addEmptyDir($dirName); 
    self::zip_folder($sourcePath, $z, strlen("$parentPath/")); 
    $z->close(); 
    return is_file($outZipPath);
} 
}