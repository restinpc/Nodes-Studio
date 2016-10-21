<?php

// TODO - Your code here
//----------------------------

require_once ("engine/nodes/language.php");
function print_uploder($count=1){
    for($i = 1; $i <= $count; $i++){
        $fout .= '<div id="new_img'.$i.'" class="uploader">
            <input type="hidden" id="result_file'.$i.'" name="file'.$i.'" value="" />
            <div id="result'.$i.'" class="scroll-x">
                <iframe id="f1'.$i.'" frameborder=0 src="'.$_SERVER["DIR"].'/uploader.php?id='.$i.'" class="uploader_frame" scrolling="no"></iframe>
            </div>
        </div>';
        }
    $fout .= '
    <div class="clear"></div><br/>
    <input type="button" class="btn w280" id="uploading_button1" value="'.lang("Upload new image").'" onClick=\' try{  
        parent.document.getElementById("uploading_button1").style.display="none"; 
        parent.document.getElementById("new_img1").style.display="block"; 
    }catch(e){ } \' /><div class="clear"></div>
    ';
    return $fout;
}

