<?php
/**
* Print admin files page.
* @path /engine/core/admin/print_admin_files.php
* 
* @name    Nodes Studio    @version 2.0.8
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
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
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
        . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "files" '
        . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
        . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysql_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    if(!empty($_FILES)){
        file::upload("photo", "file");
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
            $fout .= '<form method="POST" id="form_'.$i.'"><input type="hidden" name="name" value="'.$file_name.'" /></form><a href="'.$_SERVER["DIR"].'/file/'.$file_name.'" target="_blank">'.$file_name.'</a> ';
            if($admin_access == 2){
                $fout .= '<div class="close_image ml3 fl" onClick=\'document.getElementById("form_'.$i.'").submit();\' title="'.lang("Delete").'"> </div>';
            }
            $fout .= '<br/><br/>';
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
     if($admin_access == 2){
        $fout .= '<input id="button" type="button" name="load" value="'.lang("Upload files").'" class="btn w280" onClick=\'this.style.display="none";document.getElementById("form").style.display="block"; jQuery("#form").removeClass("hidden");\' /><br/>
       <form method="POST" ENCTYPE="multipart/form-data" id="form" class="w280 m0a hidden">
           <input id="file" type="file" onChange=\'document.getElementById("form").submit();\' required placeHolder="'.lang("File").'" title="'.lang("File").'" name="photo[]" multiple class="input pointer w280" /><br/><br/>
       </form>
       ';
     }
    return $fout;
}

