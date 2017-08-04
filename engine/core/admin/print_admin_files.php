<?php
/**
* Print admin files page.
* @path /engine/core/admin/print_admin_files.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $cms->site - Site object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin_files($cms); </code>
*/
function print_admin_files($cms){
    if(!empty($_FILES)){
        for($i = 1; $i < 2; $i++){
            if(!empty($_FILES["photo_".$i])){
                $_FILES["photo_".$i]["name"];
                $arr = explode(".", $_FILES["photo_".$i]["name"]);
                if( $arr[count($arr)-1] == "exe" || 
                    $arr[count($arr)-1] == "bat" || 
                    $arr[count($arr)-1] == "php" || 
                    $arr[count($arr)-1] == "cgi" ||
                    $arr[count($arr)-1] == "js"){
                    die("Unable to upload *.exe, *.bat, *.php, *.cgi, *.js files");
                }
                $f1 = file::upload("photo_".$i, 'file/', 1);
                if($f1=="error"){ 
                    $fout .= '<script>alert("'.lang("Error").'");</script>';
                }
                $_FILES["photo_".$i] = "";
            }
        }
    }if(!empty ($_POST["name"])){
        $name = trim(htmlspecialchars($_POST["name"]));
        $images = "file/".$name;
        if(is_file($images)){
            @unlink($images);
        }else if(is_file($_SERVER["DOCUMENT_ROOT"]."/file/".$name)){
            $images = $_SERVER["DOCUMENT_ROOT"]."/file/".$name;
            @unlink($images);
        }
    }          
    $fout .= '<div class="document640">
<div class="table">
<table id="table">
<tr><td align=left>
        ';
    $i = 0;
    $dirct = "file/";
    $hdl = opendir($dirct) or die("can't open direct");
    while ($file_name = readdir($hdl)){
        if (($file_name != ".") && ($file_name != "..") && is_file($dirct.$file_name)){
            $i++;
        $fout .= '<form method="POST" id="form_'.$i.'"><input type="hidden" name="name" value="'.$file_name.'" /></form><a href="'.$_SERVER["DIR"].'/file/'.$file_name.'" target="_blank">'.$file_name.'</a> '
                . '<div class="close_image ml3 fl" onClick=\'document.getElementById("form_'.$i.'").submit();\' title="'.lang("Delete").'"> </div><br/><br/>';
        }
     }if(!$i){
        $fout = '<div class="clear_block">'.lang("There is no files").'</div>';
     }else{
        $fout .= '
        </td>
</tr>
</table></div>
</div>';
     }
     $fout .= '<input id="button" type="button" name="load" value="'.lang("Upload files").'" class="btn w280" onClick=\'this.style.display="none";document.getElementById("form").style.display="block";\' /><br/>
    <form method="POST" ENCTYPE="multipart/form-data" id="form" class="hidden">
        <input id="file" type="file" onChange=\'document.getElementById("form").submit();\' required placeHolder="'.lang("File").'" title="'.lang("File").'" name="photo_1" class="input pointer w280" /><br/><br/>
    </form>
    ';
    return $fout;
}

