<?php
/** 
* Framework updater.
* @path /engine/code/update.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
$echo_to = 0;
if(isset($fout)) $echo_to = 1;
function update(){
    array_push($_SERVER["CONSOLE"], "update()");
    $output = '';
    $url = "http://nodes-studio.com/source/";
    $query = 'UPDATE `nodes_config` SET `value` = "'.date("U").'" WHERE `name` = "checkupdate"';
    engine::mysql($query);
    if(!empty($_POST["version"])){
        $version = intval($_POST["version"]);
    }else{
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $email = $data["value"];
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "version"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $old_version = $data["value"];
        $version = file_get_contents($url."updater.php?version=".$old_version.'&host='.$_SERVER["HTTP_HOST"].'&email='.$email);
        if($version<=$old_version) return;
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "version"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $v = $data["value"];
    $output .= "<b>".lang("Updating engine from version")." 2.0.".$v." ".lang("to")." 2.0.".$version.".</b><br/>";
    $filelist = '';
    $output .= lang("Downloading files").'.<br/>';
    $files = file_get_contents($url."updater.php?version=".$version."&mode=file&file=/engine.txt");
    $filelist = $files;
    $arr = explode("\n", $files);
    foreach($arr as $a){
        $a = trim(str_replace("\r", "", $a));
        if(!empty($a)){
            $output .= lang("Receiving")." ".$a.'.. ';
            $file = file_get_contents($url."updater.php?version=".$version."&no_encode=0&mode=engine&file=/".$a);
            if($file!="error" && !empty($file)){
                $a = "temp/".$a;
                $temp = @fopen($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.$a, 'w');
                if(!$temp){
                    $dirs = explode("/", $a);
                    $fullpath = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/temp/';
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
                $temp = @fopen($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$a, 'w');
                if(!$temp){
                    $dirs = explode("/", $a);
                    $fullpath = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/temp/';
                    foreach($dirs as $d){
                        $d = trim($d);
                        if(!empty($d)&&!strpos($d, ".")&&$d!="temp"){
                            mkdir($fullpath.$d);
                            $fullpath .= $d."/";
                        }
                    }$temp = fopen($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/".$a, 'w');
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
        $fullpath = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/';
        foreach($dirs as $d){
            $d = trim($d);
            if(!empty($d)&&!strpos($d, ".")){
                mkdir($fullpath.$d);
                $fullpath .= $d."/";
            }
        }
        if(!empty($file)){
            if(is_file($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].$file)){
                $output .= lang("File")." ".$file." ".lang("already exist").".<br/>";
                continue;
            }else if(!copy($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'."temp/".$file, $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/'.$file)){
                return($output.lang("Error").".<br/>".lang("Update aborted").".".$error_output);
            }}
    }$output .= "Done.<br/>";
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
}
if($echo_to){ 
    $fout .= update();
}else{ 
    echo update();
}
$path = $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/temp/';
if(file::delete($path)){
    mkdir($path, 0755);
}