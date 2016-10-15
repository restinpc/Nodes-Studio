<?php

// TODO - Your code here
//----------------------------

function print_product_filter($id, $url){
    $filter = '<form method="POST" id="filer_form_'.$id.'">
    <input type="hidden" name="details" value="1" />
    <input type="hidden" name="reset" value="0" id="reset" />
    <select class="input" style="width: 210px;" name="category" id="category" onChange=\'
        document.getElementById("filer_form_'.$id.'").action = "'.$_SERVER["DIR"].'/product/"+this.value; 
        document.getElementById("filer_form_'.$id.'").submit();\'>
        <option value="">'.lang("Category").'</option>';
    $query = 'SELECT * FROM `nodes_category` WHERE `cat_id` = 1 ORDER BY `id` ASC';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if($url==$data["url"]){
            $filter .= '<option value="'.$data["url"].'" selected>'.$data["value"].'</option>';
        }else{
            $filter .= '<option value="'.$data["url"].'">'.$data["value"].'</option>';
        }
    }$filter .= '
        </select><br/><br/>';
    
    $query = 'SELECT * FROM `nodes_properties` WHERE `id` > 1 ORDER BY `id` ASC';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        $filter .= '<select class="input" style="width: 210px;" name="'.$data["id"].'" onChange=\'document.getElementById("filer_form_'.$id.'").submit();\'>
        <option value="0">'.$data["value"].'</option>';
        $query = 'SELECT * FROM `nodes_category` WHERE `cat_id` = "'.$data["id"].'"';
        $r = engine::mysql($query);
        while($cat = mysql_fetch_array($r)){
                if($_SESSION["details"][$data["id"]]==$cat["id"]){
                    $filter .= '<option value="'.$cat["id"].'" selected>'.$cat["value"].'</option>';
                }else{
                    $filter .= '<option value="'.$cat["id"].'">'.$cat["value"].'</option>';
                }
        }$filter .= '
            </select><br/><br/>';
    }
    if(!empty($_SESSION["details"])){
        $filter .= '
        <input type="button" class="btn" style="width: 210px;" value="'.lang("Reset Filter").'" onClick=\'
        document.getElementById("reset").value="1";
        document.getElementById("filer_form_'.$id.'").action = "'.$_SERVER["DIR"].'/product";
        document.getElementById("filer_form_'.$id.'").submit();\' /><br/><br/>';
    }
    $filter .= '</form>';
    return $filter;
}
