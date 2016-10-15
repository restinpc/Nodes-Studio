<?php
require_once("engine/nodes/engine.php");
require_once("engine/nodes/headers.php");
require_once("engine/nodes/language.php");
function removeDirectory($dir) {
  if ($objs = glob($dir."/*")) {
     foreach($objs as $obj) {
       is_dir($obj) ? removeDirectory($obj) : unlink($obj);
     }
  }rmdir($dir);
}
function update(){
$output = '';
$url = "http://nodes-studio.com/source/";
if(!empty($_POST["version"])||!empty($GLOBALS["auto"])){
    $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "checkupdate"';
    engine::mysql($query);
    if(!empty($_POST["version"])){
        $version = intval($_POST["version"]);
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "version"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $old_version = $data["value"];
        $version = file_get_contents($url."updater.php?version=".$old_version);
        if($version<=$old_version) die();
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "version"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $v = $data["value"];
    
    $output .= "<b>".lang("Updating engine from version")." 2.0.".$v." ".lang("to")." 2.0.".$version.".</b><br/>";
    $filelist = '';
    
    $query = 'SELECT `value` FROM `nodes_config` WHERE `name` = "encoding"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $no_encode = 1-intval($data["value"]);
    
    $output .= lang("Downloading files").'.<br/>';
    $files = file_get_contents($url."updater.php?version=".$version."&mode=file&file=/engine.txt");
    $filelist = $files;
    $arr = explode("\n", $files);
    foreach($arr as $a){
        $a = trim(str_replace("\r", "", $a));
        if(!empty($a)){
            $output .= lang("Receiving")." ".$a.'.. ';
            $file = file_get_contents($url."updater.php?version=".$version."&no_encode=".$no_encode."&mode=engine&file=/".$a);
            if($file!="error" && !empty($file)){
                $a = "temp/".$a;
                $temp = @fopen($a, 'w');
                if(!$temp){
                    $dirs = explode("/", $a);
                    $fullpath = 'temp/';
                    foreach($dirs as $d){
                        $d = trim($d);
                        if(!empty($d)&&!strpos($d, ".")&&$d!="temp"){
                            mkdir($fullpath.$d);
                            $fullpath .= $d."/";
                        }
                    }$temp = fopen($a, 'w');
                }
                if(!$temp){
                    return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
                }
                fwrite($temp, $file);
                fclose($temp);
                $output .= "Ok.";
            }else{
                return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
            }$output .= '<br/>';
        }
    }
    
    $files = file_get_contents($url."updater.php?version=".$version."&mode=file&file=/files.txt");
    $filelist .= '
'.$files;
    $arr = explode("\n", $files);
    foreach($arr as $a){
        $a = trim(str_replace("\r", "", $a));
        if(!empty($a)){
            $output .= lang("Receiving")." ".$a.'.. ';
            $file = file_get_contents($url.$version."/".$a);
            if($file!="error" && !empty($file)){
                $a = "temp/".$a;
                $temp = @fopen($a, 'w');
                if(!$temp){
                    $dirs = explode("/", $a);
                    $fullpath = 'temp/';
                    foreach($dirs as $d){
                        $d = trim($d);
                        if(!empty($d)&&!strpos($d, ".")&&$d!="temp"){
                            mkdir($fullpath.$d);
                            $fullpath .= $d."/";
                        }
                    }$temp = fopen($a, 'w');
                }
                if(!$temp){
                    return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
                }
                fwrite($temp, $file);
                fclose($temp);
                $output .= "Ok.";
            }else{
                return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
            }$output .= '<br/>';
        }
    }
    
    $output .= lang("Replacing downloaded files from")." /temp.. <br/>";
    $files = explode("\n", $filelist);
    foreach($files as $file){
        $file = trim($file);
        $dirs = explode("/", $file);
        $fullpath = '';
        foreach($dirs as $d){
            $d = trim($d);
            if(!empty($d)&&!strpos($d, ".")){
                mkdir($fullpath.$d);
                $fullpath .= $d."/";
            }
        }
        if(!empty($file)){
            if(is_file($file)){ 
                $output .= lang("File")." ".$file." ".lang("already exist").".<br/>";
                continue;
            }else if(!copy("temp/".$file, $file)){
                return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
            }
        }
    }$output .= "Ok.<br/>";
    
    $output .= lang("Receiving MySQL data").".. ";
    $sql = file_get_contents($url."updater.php?version=".$version."&mode=file&file=/mysql.txt");
    $arr = explode(";"."\n", $sql);
    $flag = 0;
    foreach($arr as $a){
        $a = trim($a);
        if(!empty($a)){
            @mysql_query("SET NAMES utf8");
            mysql_query($a) or die(mysql_error());
            $flag++;
        }
    }if($flag) $output .= "Ok.<br/>".lang("Executed")." ".$flag." MySQL ".lang("commands").".</br>";
    else if(empty($arr)) $output .= "Ok. File is empty.<br/>";
    else return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);

    $output .= lang("Updating to version")." 2.0.".$version." ".lang("is complete")."!<br/>";
    
    $query = 'INSERT INTO `nodes_logs`(action, user_id, ip, date, details) '
            . 'VALUES("8", "1", "'.$_SERVER["REMOTE_ADDR"].'", "'.date("U").'", "2.0.'.$version.'")';
    engine::mysql($query);
    
    $query = 'UPDATE `nodes_config` SET `value` = "'.$version.'" WHERE `name` = "version"';
    engine::mysql($query);
    
    $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "lastupdate"';
    engine::mysql($query);
    
    
    $new_version = file_get_contents($url."updater.php?version=".$version);
    if($new_version>$version){
        $output .= '
            <script>
            function new_update(){
                jQuery("#content").animate({opacity: 0}, 300);
                document.getElementById("post_form").submit();
            }setTimeout(new_update, 5000);
            </script>
            <form method="POST" id="post_form">
                <input type="hidden" name="version" value="'.intval($new_version).'" />
                '.lang("Updating engine from version").' 2.0.'.$version.' '.lang("to").' 2.0.'.$new_version.' '.lang("after 5 seconds").'.<br/>
            </form>';
    }
    return($output);
}else{
    if(isset($_POST["autoupdate"])){
        $query = 'UPDATE `nodes_config` SET `value` = "'.$_POST["autoupdate"].'" WHERE `name` = "autoupdate"';
        engine::mysql($query);
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "version"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $version = $data["value"];
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "autoupdate"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $value = $data["value"];
    $output = '<center>
    <form id="autoupdate" method="POST">
        '.lang("Autoupdate").': <select class="input" name="autoupdate" onChange=\'document.getElementById("autoupdate").submit();\'>';
            if($value){
                $output .= '<option value="1" selected>'.lang("Enabled").'</option>
                <option value="0">'.lang("Disabled").'</option>';
            }else{
                $output .= '<option value="1">'.lang("Enabled").'</option>
                <option value="0" selected>'.lang("Disabled").'</option>'; 
            }
            $output .= '
        </select><br/>
    </form>
    <form method="POST" id="post_form">
        '.lang("Current version").': 2.0.'.$version.'<br/>';
    
    $new_version = file_get_contents($url."updater.php?version=".$version);
    if($new_version>$version){
        $output .= '
            <input type="hidden" name="version" value="'.intval($new_version).'" />
            '.lang("New updates available").'!<br/><br/>
            <input type="button" class="btn" onClick=\'jQuery("#content").animate({opacity: 0}, 300); document.getElementById("post_form").submit();\' value="'.lang("Update Now").'" style="width: 280px;" />';
    }else{
       $output .= lang("No updates available").'.<br/><br/>'; 
    }
    
    $output .= '
    </form>
    </center>';
}return $output;
}$fout = update();
removeDirectory("temp");
@mkdir("temp");