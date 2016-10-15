<?php

// TODO - Your code here
//----------------------------

require_once ("engine/nodes/language.php");
function print_uploder($count=1){
    if(isset($count)){
        for($i = 1; $i <= $count; $i++){
            $fout .= '<div id="new_img'.$i.'" style="display:none; margin: 3px; overflow:hidden;">
                <input type="hidden" id="result_file'.$i.'" name="file'.$i.'" value="" />
                <div id="result'.$i.'" style="overflow:visible; overflow-x: auto;">
                    <iframe id="f1'.$i.'"  frameborder=0 src="'.$_SERVER["DIR"].'/uploader.php?id='.$i.'" style="height: 250px; width: 300px; margin-left: 0px;" scrolling="no"></iframe>
                </div>
            </div>';
            }
        $fout .= '
        <div style="clear:both;"></div><br/>
        <input type="button" class="btn"  style="width:280px;" id="uploading_button1" value="'.lang("Upload new image").'" onClick=\' try{  
            parent.document.getElementById("uploading_button1").style.display="none"; 
            parent.document.getElementById("new_img1").style.display="block"; 
        }catch(e){ } \' /><div style="clear:both;"></div>
        ';
    }return $fout;
}

