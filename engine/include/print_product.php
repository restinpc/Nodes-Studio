<?php

// TODO - Your code here
//----------------------------

function print_product($data){

    $query = 'SELECT * FROM `nodes_users` WHERE `id` = "'.$data["user_id"].'"';
    $res = engine::mysql($query);
    $user = mysql_fetch_array($res);
    
    $rating=0;
    $images = explode(";", $data["img"]);
    
    $query = 'SELECT * FROM `nodes_category` WHERE `id` = "'.$data["category"].'"';
    $r = engine::mysql($query);
    $d = mysql_fetch_array($r);
    $category = $d["value"];
    $category_url = $d["url"];
    
    $fout = '
<div class="product_header">
    <a href="'.$_SERVER["DIR"].'/product">'.lang("All Items").'</a> › '.$data["title"].'
</div>
<br/>
<div class="product_center">
    <div class="product_left">
        <div class="product_galery">
        <div class="main_image">
            <img id="product_main_image" src="'.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].'" width=100% /><br/>
        </div>';
    
        $table = '<table><tr>';
            $i=0;
            foreach($images as $img){
                $img = trim($img);
                if(!empty($img)){
                    $i++;    
                    if($i>5) break;
                    $table .= '<td id="block_'.$i.'" class="product_preview_image_small">'
                    . '<img class="img" style="';
                    if($img == $images[0]){
                        $table .= ' border: #ee4e00 1px solid;';
                    }
                    $table .= '" src="'.$_SERVER["DIR"].'/img/data/thumb/'.$img.'" onClick=\'select_image("'.$i.'", "'.$img.'");\'/><br/>
                        <div class="new_small_photo" onClick=\'show_photo_editor('.$data["id"].', '.$i.');\' > </div>';
                    if($i>1){
                        $table .= '<input class="btn small del_button" style="display:none;" type="button" value="'.lang("Delete").'" onClick=\'delete_image("'.$data["id"].'", "'.$i.'");\' />';
                    }
                    $table .= '</td>';
                }
            }
            if($i<4){
                $table .= '<td class="add_td">'
                        . '<div class="new_add_photo" onClick=\'show_photo_editor('.$data["id"].', '.(++$i).');\' > </div>
                        </td>';
            }
        $table .= '</tr>
        </table>';
        if($i>1) $fout .= $table;
        $fout .= '<br/>
            </div>
    </div>
    <div class="product_right">
        <div class="block">
        <div id="details_button">';
            if($data["user_id"]!=$_SESSION["user"]["id"]){
                if($data["status"]=="1"){
                    $fout .= '<button class="btn" style="width: 280px;" onClick=\'buy_now("'.$data["id"].'", "'.lang("Checkout order?").'");\'>'.lang("Buy Now!").'</button><br/><br/>';
                }
            }
            
            $fout .= '
        </div>
        <div id="edit_details">
            <form method="POST" id="edit_details_form">
                '.lang("Title").':<br/>
                <input class="input" type="text" id="product_title" name="title" value="'.$data["title"].'" style="width: 280px;" /><br/><br/>
                '.lang("Description").': <br/>
                <textarea class="input" id="product_text" name="text" style="width: 280px;" >'.$data["text"].'</textarea><br/><br/>
                '.lang("Ask price").': <br/>
                <input class="input" type="text" id="product_price" name="price" value="'.$data["price"].'" style="width: 280px;" /><br/><br/>';

            preg_match_all('#\[([^,]+),([^\]]+)\]#', $data["properties"], $properties);
            $pro = array();
            for($j = 0; $j < count($properties[1]); $j++){
                $pro[$properties[1][$j]] = $properties[2][$j];
            }
            
            $query = 'SELECT * FROM `nodes_properties` ORDER BY `id` ASC';
            $res = engine::mysql($query);
            while($fdata=  mysql_fetch_array($res)){
                $flag = 0;
                $select = $fdata["value"].':<br/>'
                        . '<select class="input" style="width: 280px;" name="property_'.$fdata["id"].'">'
                        . '<option></option>';
                $query = 'SELECT * FROM `nodes_category` WHERE `cat_id` = "'.$fdata["id"].'"';
                $r = engine::mysql($query);
                while($d = mysql_fetch_array($r)){
                    $flag = 1;
                    if($pro[$fdata["id"]]==$d["id"]){
                        $select .= '<option value="'.$d["id"].'" selected>'.$d["value"].'</option>';
                    }else{
                        $select .= '<option value="'.$d["id"].'">'.$d["value"].'</option>';
                    }
                }$select .= '</select><br/><br/>';
                if($flag){
                    $fout .= $select;
                }
            }
            
            $fout .= '
                </select><br/>
                <button class="btn" style="width: 280px;" onClick=\'edit_product();\'>'.lang("Save changes").'</button>
                <br/>
            </form>
        </div>
        <div style="text-align:left;">
            <div id="public_details">
                <h1>'.$data["title"].'</h1>
                <br/>
                <p style="line-height: 1.2;">'.mb_substr($data["text"],0,300).'</p>
                </br>
                '.lang("Ask price").': <b>$'.$data["price"].'</b><br/><br/>';
            
            foreach($pro as $key=>$value){
                if(intval($key)>0 && intval($value)>0){
                    $query = 'SELECT * FROM `nodes_properties` WHERE `id` = "'.intval($key).'"';
                    $r = engine::mysql($query);
                    $prop = mysql_fetch_array($r);
                    $query = 'SELECT * FROM `nodes_category` WHERE `id` = "'.intval($value).'"';
                    $r = engine::mysql($query);
                    $cat = mysql_fetch_array($r);
                    $fout .= $prop["value"].": ".$cat["value"].'<br/><br/>';
                }
            }
            $fout .= '
            '.lang("Uploaded").': <b>'.date("Y-m-d H:i", $data["date"]).'</b><br/><br/>
            ';
            if($data["user_id"]==$_SESSION["user"]["id"]){
                $fout .= '<br/><button id="edit_button" class="btn" style="width: 280px;" onClick=\'edit_product("'.lang("Save Changes").'");\'>'.lang("Edit product").'</button><br/><br/>';
                if($data["status"]=="1"){
                    $fout .= '<button class="btn" style="width: 280px;" onClick=\'deactivate("'.$data["id"].'");\' >'.lang("Click to Deactivate").'</button><br/><br/>';     
                }else{
                    $fout .= '<button class="btn" style="width: 280px;" onClick=\'activate("'.$data["id"].'");\'>'.lang("Click to Activate").'</button><br/><br/>';
                }
            }
            $fout .= '
            </div>
        </div>
        </div>
        <br/>
    </div>   
<div style="clear:both;"></div>
<div class="product_comments">';
    require_once("engine/include/print_comments.php");
    $fout .= print_comments("/product/".$data["id"]);
    $fout .= '<br/>
</div>
<div style="clear:both;"></div>
<div style="text-align:left;">';
    $products = '';
    $query = 'SELECT * FROM `nodes_products` WHERE `id` <> "'.$data["id"].'" AND `status` = "1" ORDER BY RAND() DESC LIMIT 0, 3';
    $res = engine::mysql($query);
    $count = 0;
    require_once('engine/include/print_product_preview.php');
    while($d = mysql_fetch_array($res)){
        $count++;
        $products .= print_product_preview($d);
    }
    $query = 'SELECT * FROM `nodes_content` WHERE `lang` = "'.$_SESSION["Lang"].'" ORDER BY RAND() LIMIT 0,'.(6-$count);
    $res = engine::mysql($query); 
    require_once ("engine/include/print_preview.php");
    while($d = mysql_fetch_array($res)){
        $count++;
        $products .= print_preview($d);
    }
    if($count) $fout .= '<h6 style="padding-left: 10px;">'.lang("You might also be interested in").':</h6><br/>'.$products;
    $fout .= '
</div><div style="clear:both;"></div>
</div>';
    return $fout;
}

