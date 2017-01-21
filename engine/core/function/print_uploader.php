<?php
/**
* Prints image uploader form.
* @path /engine/core/function/print_uploder.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*
* @param int $count Images count.
* @return string Returns content of form on success, or die with error.
* @usage <code> engine::print_uploader(1); </code>
*/
require_once ("engine/nodes/language.php");
function print_uploader($count=1){
    for($i = 1; $i <= $count; $i++){
        $fout .= '<div id="new_img'.$i.'" class="uploader">
            <input type="hidden" id="result_file'.$i.'" name="file'.$i.'" value="" />
            <div id="result'.$i.'"  style="overflow:hidden; overflow-x:auto;">
                <iframe id="f1'.$i.'" frameborder=0 src="'.$_SERVER["DIR"].'/uploader.php?id='.$i.'" class="uploader_frame" scrolling="no"></iframe>
            </div>
        </div>';
        }
    $fout .= '
    <div class="clear"></div><br/>
    <input type="button" class="btn w280" id="uploading_button1" value="'.lang("Upload new image").'" onClick=\' try{  
        parent.document.getElementById("uploading_button1").style.display="none"; 
        parent.document.getElementById("new_img1").style.display="block"; 
    }catch(e){ } \' />
    ';
    return $fout;
}

