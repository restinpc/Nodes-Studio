<?php
/**
* Print admin products page.
* @path /engine/core/admin/print_admin_products.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
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
* @usage <code> engine::print_admin_products($cms); </code>
*/
function print_admin_products($cms){
    $cms->onload .= '; tinymce_init(); ';
    if($_GET["action"]=="add"){
        if(!empty($_POST["file1"]) && !empty($_SESSION["user"]["id"])){
            $_SESSION["photos"] = '';
            foreach($_POST as $key=>$file){
                if(!empty($file) && strpos(" ".$key, "file")){
                    $_SESSION["photos"] .= trim($file).';';
                }
            }
        }
        if(!empty($_POST["product"]) && !empty($_SESSION["user"]["id"])){
            $title = trim(htmlspecialchars($_POST["title"]));
            $text = trim(htmlspecialchars($_POST["text"]));
            $price = doubleval($_POST["price"]);
            $description = trim(mysql_real_escape_string($_POST["description"]));
            $date = date("U");
            $query = 'INSERT INTO `nodes_product`(`user_id`, `title`, `text`, `description`, `img`, `price`, `date`, `status`, `views`) '
                    . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$title.'", "'.$text.'", "'.$description.'", "'.$_SESSION["photos"].'", "'.$price.'", "'.$date.'", "1", "0")';
            engine::mysql($query);
            $query = 'SELECT * FROM `nodes_product` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" AND `date` = "'.date("U").'"';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            $_SESSION["product"] = $data["id"];
            foreach($_POST as $key=>$value){
                if(strpos($key, 'property_')!=="false"){
                    $key = str_replace('property_', '', $key);
                }if(intval($key) > 0){
                    $value = intval($_POST["property_".$key]);
                    if($value==-1){
                        $new_value = trim(mysql_real_escape_string($_POST["new_value_".$key]));
                        $query = 'SELECT * FROM `nodes_product_data` WHERE `cat_id` = "'.$key.'" AND `value` = "'.$new_value.'"';
                        $res = engine::mysql($query);
                        $data = mysql_fetch_array($res);
                        if(!empty($data)){
                           $value = $data["id"]; 
                        }else{
                            $query = 'INSERT INTO `nodes_product_data`(cat_id, value) VALUES("'.$key.'", "'.$new_value.'")';
                            engine::mysql($query);
                            $value = mysql_insert_id();
                        }
                    }
                    $query = 'SELECT * FROM `nodes_property_data` WHERE `product_id` = "'.$_SESSION["product"].'" AND `property_id` = "'.$key.'" AND `data_id` = "'.$value.'"';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                    if(empty($data)){
                        $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                                . 'VALUES("'.$_SESSION["product"].'", "'.$key.'", "'.$value.'")';
                        engine::mysql($query);
                    }
                }
            }
            if(!empty($_POST["new_property"]) && !empty($_POST["new_value"])){
                $value = trim(mysql_real_escape_string($_POST["new_value"]));
                $property = trim(mysql_real_escape_string($_POST["new_property"]));
                $query = 'INSERT INTO `nodes_product_property`(cat_id, value) VALUES(0, "'.$property.'")';
                engine::mysql($query);
                $id = mysql_insert_id();
                $query = 'INSERT INTO `nodes_product_data`(cat_id, value, url) VALUES('.$id.', "'.$value.'", "")';
                engine::mysql($query);
                $data_id = mysql_insert_id();
                $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                        . 'VALUES("'.$_SESSION["product"].'", "'.$id.'", "'.$data_id.'")';
                engine::mysql($query);
            }
        }
        if(!empty($_POST["shipping"]) && !empty($_SESSION["user"]["id"])){
            $country = htmlspecialchars($_POST["country"]);
            $state = htmlspecialchars($_POST["state"]);
            $city = htmlspecialchars($_POST["city"]);
            $zip = htmlspecialchars($_POST["zip"]);
            $street1 = htmlspecialchars($_POST["street1"]);
            $street2 = htmlspecialchars($_POST["street2"]);
            $phone = htmlspecialchars($_POST["phone"]);
            $query = 'SELECT * FROM `nodes_shipping` WHERE '
                    . '`user_id` = "'.$_SESSION["user"]["id"].'" AND '
                    . '`country` = "'.$country.'" AND '
                    . '`state` = "'.$state.'" AND '
                    . '`city` = "'.$city.'" AND '
                    . '`zip` = "'.$zip.'" AND '
                    . '`street1` = "'.$street1.'" AND '
                    . '`street2` = "'.$street2.'" AND '
                    . '`phone` = "'.$phone.'" '
                    . 'ORDER BY `id` DESC LIMIT 0, 1';
            $res = engine::mysql($query);
            $data = mysql_fetch_array($res);
            if(empty($data)){
                $query = 'INSERT INTO `nodes_shipping`(user_id, country, state, city, zip, street1, street2, phone)'
                        . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$country.'", "'.$state.'", "'.$city.'", "'.$zip.'", "'.$street1.'", "'.$street2.'", "'.$phone.'")';
                engine::mysql($query);
                $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
            }
            $query = 'UPDATE `nodes_product` SET `status` = "1", `shipping` = "'.$data["id"].'" WHERE `id` = "'.$_SESSION["product"].'"';
            engine::mysql($query);
            $id = $_SESSION["product"];
            unset($_SESSION["product"]);
            unset($_SESSION["photos"]);
            $fout = '<script type="text/javascript">window.location = "'.$_SERVER["DIR"].'/admin/?mode=products";</script>';
            return $fout;
        }
        if(empty($_SESSION["photos"])){
            $fout = '
                <div class="add_product">
                <form method="POST">';
                    for($i = 1; $i<5; $i++){
                        $fout .= '<div class="new_photo" id="new_photo_'.$i.'" title="none">
                        <input type="hidden" name="file'.$i.'" id="file'.$i.'" value="" />
                        </div>';
                    }$fout .= '
                        <div class="clear"><br/></div>
                    <input type="button" id="upload_btn" value="'.lang("Upload new image").'" class="btn w280"  onClick=\'show_photo_editor(0,0);\' /><br/><br/><br/>
                    <input type="hidden" name="product" value="1" />
                <div class="add_product_left">
                    '.lang("Please, describe this item").'<br/><br/>
                    <input type="text" placeHolder="'.lang("Title").'" class="input w280" name="title" required /><br/><br/>
                    <textarea class="input w280 h100" name="text" placeHolder="'.lang("Description (e.g. Blue Nike Vapor Cleats Size 10. Very comfortable and strong ankle support.)").'" required></textarea><br/><br/>
                    <input type="text" class="input w280" name="price" placeHolder="$ 0.00" required /><br/><br/>';
                $query = 'SELECT * FROM `nodes_product_property` ORDER BY `id` ASC';
                $res = engine::mysql($query);
                while($data=  mysql_fetch_array($res)){
                    $flag = 0;
                    $select = '<select class="input w280" style="margin-bottom: 15px;" name="property_'.$data["id"].'" onChange=\'if(this.value=="-1"){document.getElementById("new_value_'.$data["id"].'").style.display="block"; this.style.display="none";}\'>'
                    . '<option disabled selected>'.$data["value"].'</option>';
                    $query = 'SELECT * FROM `nodes_product_data` WHERE `cat_id` = "'.$data["id"].'"';
                    $r = engine::mysql($query);
                    while($d = mysql_fetch_array($r)){
                        $flag = 1;
                        $select .= '<option value="'.$d["id"].'">'.$d["value"].'</option>';
                    }$select .= '
                        <option value="-1">'.lang("New value").'</option>
                        </select><input type="text" class="input w280" style="display:none; margin: 0px auto;" name="new_value_'.$data["id"].'" id="new_value_'.$data["id"].'" placeHolder="'.$data["value"].'" />
                        <br/>';
                    if($flag){
                        $fout .= $select;
                    }
                }
                $fout .= '
                    <div id="nodes_new_properties">
                        <input type="text" name="new_property" class="input w280" placeHolder="'.lang("Property").'" /><br/><br/>
                        <input type="text" name="new_value" class="input w280" placeHolder="'.lang("Value").'" /><br/>
                    </div>
                    <input type="button" value="'.lang("Add new property").'" class="btn small w280" 
                        onClick=\'document.getElementById("nodes_new_properties").style.display="block";
                        this.style.display="none";\' 
                    /><br/><br/>';
                $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
            $fout .= '
                </div>
                <div class="add_product_right">
                    '.lang("Please, confirm item shipping address").'<br/><br/>
                    <input type="hidden" name="shipping" value="1" />
                    <input type="text" class="input w280" placeHolder="'.lang("Country").'" id="country_selector" name="country" required value="'.$data["country"].'" /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("State").'" name="state" required value="'.$data["state"].'" /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("City").'" name="city" required value="'.$data["city"].'"  /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("Zip code").'" name="zip" required value="'.$data["zip"].'"  /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("Street").' 1" name="street1" required value="'.$data["street1"].'"  /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("Street").' 2" name="street2" value="'.$data["street2"].'"  /><br/><br/>
                    <input type="text" class="input w280" placeHolder="'.lang("Phone number").'" name="phone" required value="'.$data["phone"].'"  /><br/><br/>
                    <br/>
                </div>
                <div class="clear"><br/></div>
                <div class="w600 m0a">
                    <textarea id="editable" name="description" placeHolder="'.lang("Complete item description").'"></textarea>
                    <br/><br/>
                </div>
                <input type="submit" class="btn w280" value="'.lang("Submit").'" /><br/>
            </form>
            </div>
            <style>.country-select{width: 280px;}</style>';  
            $cms->onload .= '; jQuery("#country_selector").countrySelect({ defaultCountry: "us" }); ';
        }
    }else if($_GET["action"]=="edit"){
        if(!empty($_GET["id"])){
            if(!empty($_POST["title"])){
                $query = 'DELETE FROM `nodes_property_data` WHERE `product_id` = "'.$_GET["id"].'"';
                engine::mysql($query);
                foreach($_POST as $key=>$value){
                    if(strpos(' '.$key, 'property_')){
                        $key = str_replace('property_', '', $key);
                    }if(intval($key) > 0){
                        $value = intval($_POST["property_".$key]);
                        $new_value = trim(mysql_real_escape_string($_POST["new_value_".$key]));
                        if(!empty($new_value) && $value == "-1"){
                            $query = 'SELECT * FROM `nodes_product_data` WHERE `value` = "'.$new_value.'"';
                            $r = engine::mysql($query);
                            $d = mysql_fetch_array($r);
                            if(!empty($d)){
                                $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                                        . 'VALUES("'.$_GET["id"].'", "'.$key.'", "'.$d["id"].'")';
                                engine::mysql($query);
                            }else{
                                $query = 'INSERT INTO `nodes_product_data`(cat_id, value) VALUES("'.$key.'", "'.$new_value.'")';
                                $rr = engine::mysql($query);
                                $dd = mysql_fetch_array($rr);
                                $id = mysql_insert_id();
                                $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                                        . 'VALUES("'.$_GET["id"].'", "'.$key.'", "'.$id.'")';
                                engine::mysql($query);
                            }
                        }else{
                            $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                                    . 'VALUES("'.$_GET["id"].'", "'.$key.'", "'.$value.'")';
                            engine::mysql($query);
                        }
                    }
                }
                if(!empty($_POST["new_property"]) && !empty($_POST["new_value"])){
                    $value = trim(mysql_real_escape_string($_POST["new_value"]));
                    $property = trim(mysql_real_escape_string($_POST["new_property"]));
                    $query = 'INSERT INTO `nodes_product_property`(cat_id, value) VALUES(0, "'.$property.'")';
                    engine::mysql($query);
                    $id = mysql_insert_id();
                    $query = 'INSERT INTO `nodes_product_data`(cat_id, value, url) VALUES('.$id.', "'.$value.'", "")';
                    engine::mysql($query);
                    $data_id = mysql_insert_id();
                    $query = 'INSERT INTO `nodes_property_data`(product_id, property_id, data_id) '
                            . 'VALUES("'.$_GET["id"].'", "'.$id.'", "'.$data_id.'")';
                    engine::mysql($query);
                }
                $title = trim(htmlspecialchars($_POST["title"]));
                $text = trim(htmlspecialchars($_POST["text"]));
                $price = doubleval($_POST["price"]);
                $description = mysql_real_escape_string($_POST["description"]);
                $query = 'UPDATE `nodes_product` SET '
                        . '`title` = "'.$title.'", '
                        . '`text` = "'.$text.'", '
                        . '`price` = "'.$price.'", '
                        . '`description` = "'.$description.'" '
                        . 'WHERE `id` = "'.intval($_GET["id"]).'"';
                engine::mysql($query);
                $country = htmlspecialchars($_POST["country"]);
                $state = htmlspecialchars($_POST["state"]);
                $city = htmlspecialchars($_POST["city"]);
                $zip = htmlspecialchars($_POST["zip"]);
                $street1 = htmlspecialchars($_POST["street1"]);
                $street2 = htmlspecialchars($_POST["street2"]);
                $phone = htmlspecialchars($_POST["phone"]);
                $query = 'SELECT * FROM `nodes_shipping` WHERE '
                        . '`user_id` = "'.$_SESSION["user"]["id"].'" AND '
                        . '`country` = "'.$country.'" AND '
                        . '`state` = "'.$state.'" AND '
                        . '`city` = "'.$city.'" AND '
                        . '`zip` = "'.$zip.'" AND '
                        . '`street1` = "'.$street1.'" AND '
                        . '`street2` = "'.$street2.'" AND '
                        . '`phone` = "'.$phone.'" '
                        . 'ORDER BY `id` DESC LIMIT 0, 1';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(empty($data)){
                    $query = 'INSERT INTO `nodes_shipping`(user_id, country, state, city, zip, street1, street2, phone)'
                            . 'VALUES("'.$_SESSION["user"]["id"].'", "'.$country.'", "'.$state.'", "'.$city.'", "'.$zip.'", "'.$street1.'", "'.$street2.'", "'.$phone.'")';
                    engine::mysql($query);
                    $query = 'SELECT * FROM `nodes_shipping` WHERE `user_id` = "'.$_SESSION["user"]["id"].'" ORDER BY `id` DESC LIMIT 0, 1';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                }
                $query = 'UPDATE `nodes_product` SET `status` = "1", `shipping` = "'.$data["id"].'" WHERE `id` = "'.intval($_GET["id"]).'"';
                engine::mysql($query);
            }  
            $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.intval($_GET["id"]).'"';
            $res = engine::mysql($query);
            $product = mysql_fetch_array($res);
            $fout = '<div class="document edit_product">
                <form method="POST" id="edit_product_form">
                <section class="one_column_block">';
                $images = explode(";", $product["img"]);
                $fout .= '<table align=center><tr>';
                    $i=0;
                    foreach($images as $img){
                        $img = trim($img);
                        if(!empty($img)){
                            $i++;    
                            if($i>5) break;
                            $fout .= '<td id="block_'.$i.'" class="product_preview_image_small">'
                            . '<img class="img" src="'.$_SERVER["DIR"].'/img/data/thumb/'.$img.'" onClick=\'select_image("'.$i.'", "'.$img.'");\'/><br/>
                                <div class="new_small_photo" onClick=\'show_photo_editor('.$product["id"].', '.$i.');\' > </div>';
                            if($i>1){
                                $fout .= '<input class="btn small del_button" type="button" value="'.lang("Delete").'" onClick=\'delete_image("'.$product["id"].'", "'.$i.'");\' />';
                            }
                            $fout .= '</td>';
                        }
                    }
                    if($i<4){
                        $fout .= '<td class="add_td">'
                                . '<div class="new_add_photo" onClick=\'show_photo_editor('.$product["id"].', '.(++$i).');\' > </div>
                                </td>';
                    }
                $fout .= '</tr>
                </table>
            </section>
                <div class="double_column">
                    <section class="double_column_block">
                        <input type="hidden" name="product" value="1" />
                        '.lang("Please, describe this item").'<br/><br/>
                        <input type="text" placeHolder="'.lang("Title").'" title="'.lang("Title").'"  class="input w280" name="title" required value="'.$product["title"].'" /><br/><br/>
                        <textarea class="input w280 h100" name="text" title="'.lang("Description").'" placeHolder="'.lang("Description (e.g. Blue Nike Vapor Cleats Size 10. Very comfortable and strong ankle support.)").'" required>'.$product["text"].'</textarea><br/><br/>'
                        . '<input type="text" class="input w280" name="price" title="'.lang("Price").'" placeHolder="$ 0.00" required value="'.$product["price"].'" /><br/>
                        <br/>';
                    $query = 'SELECT * FROM `nodes_product_property` ORDER BY `id` ASC';
                    $res = engine::mysql($query);
                    while($fdata=  mysql_fetch_array($res)){
                        $flag = 0;
                        $fout .= '<select class="input w280" name="property_'.$fdata["id"].'" title="'.$fdata["value"].'" onChange=\'if(this.value=="-1"){document.getElementById("new_value_'.$fdata["id"].'").style.display="block"; this.style.display="none";}\' >'
                                . '<option value="0">'.$fdata["value"].'</option>';
                        $query = 'SELECT * FROM `nodes_product_data` WHERE `cat_id` = "'.$fdata["id"].'"';
                        $r = engine::mysql($query);
                        while($d = mysql_fetch_array($r)){
                            $flag = 1;
                            $query = 'SELECT * FROM `nodes_property_data` WHERE `product_id` = "'.$product["id"].'" '
                                    . 'AND `property_id` = "'.$fdata["id"].'" and `data_id` = "'.$d["id"].'"';
                            $rr = engine::mysql($query);
                            $dd = mysql_fetch_array($rr);
                            if(!empty($dd)){
                                $fout .= '<option selected value="'.$d["id"].'">'.$d["value"].'</option>'; 
                            }else{
                                $fout .= '<option value="'.$d["id"].'">'.$d["value"].'</option>';
                            }
                        }$fout .= '<option value="-1">'.lang("New value").'</option>
                        </select><input type="text" class="input w280" style="display:none; margin: 0px auto;" name="new_value_'.$fdata["id"].'" id="new_value_'.$fdata["id"].'" placeHolder="'.$fdata["value"].'" />'
                                . '<br/><br/>';
                    }
                    $fout .= '
                        <div id="nodes_new_properties">
                            <input type="text" name="new_property" class="input w280" placeHolder="'.lang("Property").'" /><br/><br/>
                            <input type="text" name="new_value" class="input w280" placeHolder="'.lang("Value").'" /><br/>
                        </div>
                        <input type="button" value="'.lang("Add new property").'" class="btn small w280" 
                            onClick=\'document.getElementById("nodes_new_properties").style.display="block";
                            this.style.display="none";\' 
                        /><br/><br/>
                    </section>
                    <section class="double_column_block">';
                        $query = 'SELECT * FROM `nodes_shipping` WHERE `id` = "'.$product["shipping"].'" ORDER BY `id` DESC LIMIT 0, 1';
                        $res = engine::mysql($query);
                        $data = mysql_fetch_array($res);
                        $fout .= lang("Please, confirm item shipping address").'<br/><br/>
                        <input type="hidden" name="shipping" value="1" />
                        <input type="text" class="input w280" placeHolder="'.lang("Country").'" id="country_selector" name="country" required value="'.$data["country"].'" /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("State").'" name="state" required value="'.$data["state"].'" /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("City").'" name="city" required value="'.$data["city"].'"  /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("Zip code").'" name="zip" required value="'.$data["zip"].'"  /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("Street").' 1" name="street1" required value="'.$data["street1"].'"  /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("Street").' 2" name="street2" value="'.$data["street2"].'"  /><br/><br/>
                        <input type="text" class="input w280" placeHolder="'.lang("Phone number").'" name="phone" required value="'.$data["phone"].'"  /><br/><br/>
                    </section>
                    <div class="clear"></div>
                    <div class="w600 tal">
                        <textarea class="w100p" id="editable" name="description">'.$product["description"].'</textarea>
                        <br/><br/>
                    </div>
                    <input type="submit" class="btn w280" value="'.lang("Save changes").'" /><br/><br/>
                    <a href="'.$_SERVER["DIR"].'/admin/?mode=products"><input type="button" class="btn w280" value="'.lang("Back to products").'" /></a><br/>
                </div>
                </form>
            </div>
            <style>.country-select{width: 280px;}</style>';  
            $cms->onload .= '; jQuery("#country_selector").countrySelect({ defaultCountry: "us" }); ';
        }else{
            if(!empty($_POST["new_property"])){
                $prop = trim(mysql_real_escape_string($_POST["new_property"]));
                $query = 'SELECT * FROM `nodes_product_property` WHERE `value` LIKE "'.$_POST["new_property"].'"';
                $res = engine::mysql($query);
                $data = mysql_fetch_array($res);
                if(empty($data)){
                    $query = 'INSERT INTO `nodes_product_property`(value) VALUES("'.$prop.'")';
                    engine::mysql($query);
                }
            }else if(!empty($_POST["action"]) && !empty($_POST["id"])){
                if($_POST["action"] == "cat_delete"){
                    $id = intval($_POST["id"]);
                    $query = 'DELETE FROM `nodes_product_data WHERE `id` = "'.$id.'"';
                    engine::mysql($query);
                }else if($_POST["action"] == "add"){
                    $cat_id = intval($_POST["id"]);
                    $value = mysql_real_escape_string($_POST["value"]);
                    if(!empty($_POST["url"])){
                        $url = engine::url_translit($_POST["url"]);
                    }else{
                        $url = engine::url_translit($value);
                    }
                    $query = 'SELECT COUNT(*) FROM `nodes_product_data` WHERE `cat_id` = "'.$cat_id.'" AND `value` LIKE "'.$value.'"';
                    $res = engine::mysql($query);
                    $data = mysql_fetch_array($res);
                    if(!$data[0]){
                        if($cat_id=="1"){
                            $query = 'INSERT INTO `nodes_product_data`(cat_id, value, url) '
                                . 'VALUES ("'.$cat_id.'", "'.$value.'", "'.$url.'")';
                        }else{
                            $query = 'INSERT INTO `nodes_product_data`(cat_id, value) '
                                . 'VALUES ("'.$cat_id.'", "'.$value.'")';
                        }engine::mysql($query);
                    }
                }else if($_POST["action"] == "edit_cat"){
                    $id = intval($_POST["id"]);
                    $value = mysql_real_escape_string($_POST["value"]);
                    $url = engine::url_translit($_POST["url"]);
                    $query = 'UPDATE `nodes_product_data` SET `value` = "'.$value.'", `url` = "'.$url.'" WHERE `id` = "'.$id.'"';
                    engine::mysql($query);
                }else if($_POST["action"] == "delete"){
                    $id = intval($_POST["id"]);
                    $query = 'DELETE FROM `nodes_product_property` WHERE `id` = "'.$id.'"';
                    engine::mysql($query);
                }else if($_POST["action"] == "save_property"){
                    $id = intval($_POST["id"]);
                    $value = mysql_real_escape_string($_POST["value"]);
                    $query = 'UPDATE `nodes_product_property` SET `value` = "'.$value.'" WHERE `id` = "'.$id.'"';
                    engine::mysql($query);
                }
            }
            $f = 0;
            $fout = '<div class="document">
                <div class="properties">
                ';
            $query = 'SELECT * FROM `nodes_product_property`';
            $res = engine::mysql($query);
            while($data=mysql_fetch_array($res)){
                $f = 1;
                $fout .= '<form id="category_'.$data["id"].'" method="POST">'
                        . '<input type="hidden" id="category_id_'.$data["id"].'" name="id" value="" />'
                        . '<input type="hidden" id="category_action_'.$data["id"].'" name="action" value="" />'
                        . '<input type="hidden" id="category_value_'.$data["id"].'" name="value" value="" />'
                        . '<input type="hidden" id="category_url_'.$data["id"].'" name="url" value="" />'
                        . '</form>'
                        . '<span id="value_'.$data["id"].'">'.$data["value"].'</span> '
                        . '<span id="input_'.$data["id"].'" class="hidden">'
                        . ' <input type="text" class="input" id="save_value_'.$data["id"].'" value="'.$data["value"].'" />
                            <input type="button" class="btn small" value="'.lang("Save").'" onClick=\'
                        document.getElementById("category_id_'.$data["id"].'").value = "'.$data["id"].'";
                        document.getElementById("category_action_'.$data["id"].'").value = "save_property";
                        document.getElementById("category_value_'.$data["id"].'").value = document.getElementById("save_value_'.$data["id"].'").value;
                        document.getElementById("category_'.$data["id"].'").submit();
                        \' /> 
                            </span>'
                        . '<select id="select_'.$data["id"].'" class="input" name="action" onChange=\'
                            if(this.value=="1"){
                                document.getElementById("li_'.$data["id"].'").style.display = "block"; 
                            }else if(this.value=="2"){
                                document.getElementById("input_'.$data["id"].'").style.display = "block";
                                document.getElementById("value_'.$data["id"].'").style.display = "none";
                                document.getElementById("select_'.$data["id"].'").style.display = "none";    
                            }else if(this.value=="3"){
                                if(confirm("'.lang("Are you sure?").'")){
                                    document.getElementById("category_id_'.$data["id"].'").value = "'.$data["id"].'";
                                    document.getElementById("category_action_'.$data["id"].'").value = "delete";
                                    document.getElementById("category_'.$data["id"].'").submit();
                                }
                            }
                            \'>'
                        . '<option selected disabled>'.lang("Select option").'</option>'
                        . '<option value="1">'.lang("Add value").'</option>'
                        . '<option value="2">'.lang("Edit property").'</option>';         
                $query = 'SELECT * FROM `nodes_product_data` WHERE `cat_id` = "'.$data["id"].'"';
                $r = engine::mysql($query);
                $flag = 0;
                while($d = mysql_fetch_array($r)){
                    if(!$flag){ 
                        $fout .= '</select>
                        <ul class="pl15 lh2">
                        <li class="hidden" id="li_'.$data["id"].'">'
                    . '<input type="text" id="xcv_'.$d["id"].'" name="value" class="input" placeHolder="'.lang("New value").'" />';
                        if($data["id"]=="1"){
                            $fout .= '<input type="text" id="cat_url_'.$d["id"].'" name="value" class="input" placeHolder="URL" />';
                        }
                        $fout .= '<input type="button" class="btn small" value="'.lang("Add").'" onClick=\'
                        document.getElementById("category_id_'.$data["id"].'").value = "'.$data["id"].'";
                        document.getElementById("category_action_'.$data["id"].'").value = "add";';
                        if($data["id"]=="1"){
                            $fout .= '
                        try{
                            document.getElementById("category_url_'.$data["id"].'").value = document.getElementById("cat_url_'.$d["id"].'").value;
                        }catch(err){;};';
                        }
                        $fout .= '
                        document.getElementById("category_value_'.$data["id"].'").value = document.getElementById("xcv_'.$d["id"].'").value;
                        document.getElementById("category_'.$data["id"].'").submit();
                        \' /></li>';
                    }
                    $flag = 1;
                    $fout .= '<li>
                            <span id="category_public_'.$d["id"].'">'.$d["value"].' '.
                            '<input type="button" class="btn small" value="'.lang("Edit").'" onClick=\'
                                document.getElementById("category_edit_'.$d["id"].'").style.display = "block";
                                document.getElementById("category_public_'.$d["id"].'").style.display = "none";
                                \' /> ';

                    $fout .= '<input type="button" class="btn small" value="'.lang("Delete").'" onClick=\'if(confirm("'.lang("Are you sure?").'")){
                                document.getElementById("category_id_'.$data["id"].'").value = "'.$d["id"].'";
                                document.getElementById("category_action_'.$data["id"].'").value = "cat_delete";
                                document.getElementById("category_'.$data["id"].'").submit();
                            }\' />';

                    $fout .= '</span>'
                            . '<span id="category_edit_'.$d["id"].'" class="hidden">'
                            . '<input type="text" name="value" id="cat_val_'.$d["id"].'" class="input" value="'.$d["value"].'" />';
                        if($data["id"]=="1"){
                            $fout .= '<input type="text" id="edit_cat_url_'.$d["id"].'" name="value" value="'.$d["url"].'" class="input" placeHolder="URL" />';
                        }
                        $fout .= '<input type="button" class="btn small" value="'.lang("Save").'"  onClick=\'
                        document.getElementById("category_id_'.$data["id"].'").value = "'.$d["id"].'";
                        document.getElementById("category_action_'.$data["id"].'").value = "edit_cat";
                        document.getElementById("category_value_'.$data["id"].'").value = document.getElementById("cat_val_'.$d["id"].'").value;';
                        if($data["id"]=="1"){
                            $fout .= '
                        try{
                            document.getElementById("category_url_'.$data["id"].'").value = document.getElementById("edit_cat_url_'.$d["id"].'").value;
                        }catch(err){;};
                        ';
                        }
                        $fout .= 'document.getElementById("category_'.$data["id"].'").submit();
                                \' />'
                            . '</span></li>';
                }if(!$flag){
                    if($data["id"]!="1"){
                        $fout .= '<option value="3">'.lang("Delete property").'</option>';
                    }
                    $fout .= '</select><ul class="pl15 lh2">
                        <li class="hidden" id="li_'.$data["id"].'">'
                    . '<input type="text" id="cv_'.$data["id"].'" name="value" class="input" placeHolder="'.lang("New value").'" />';
                        if($data["id"]=="1"){
                            $fout .= '<input type="text" id="cat_url_'.$d["id"].'" name="value" class="input" placeHolder="URL" />';
                        }
                        $fout .= '<input type="button" class="btn small" value="'.lang("Add").'" onClick=\'
                        document.getElementById("category_id_'.$data["id"].'").value = "'.$data["id"].'";
                        document.getElementById("category_action_'.$data["id"].'").value = "add";
                        document.getElementById("category_value_'.$data["id"].'").value = document.getElementById("cv_'.$data["id"].'").value;
                        document.getElementById("category_'.$data["id"].'").submit();
                        \' /></li></ul>';
                }else{
                    $fout .= '</ul>';
                }
            }
            $fout .= '<br/></div>
                <input type="button" class="btn w280 mb10" value="'.lang("Add new property").'" 
                    onClick=\'document.getElementById("new_prop").style.display = "block"; this.style.display = "none";\' 
                />';
            $fout .= '
                <form method="POST" class="hidden" id="new_prop">
                    <strong>'.lang("Add new property").'</strong><br/><br/>
                    <input type="text" name="new_property" placeHolder="'.lang("Value").'" class="input w280" /><br/><br/>
                    <input type="submit" class="btn w280" value="'.lang("Submit").'" /><br/><br/>
                </form>
            </div>
            <a href="'.$_SERVER["DIR"].'/admin/?mode=products"><input type="button" class="btn w280" value="'.lang("Back to products").'" /></a><br/>';
        }
    }else{
        
        if($_SESSION["order"]=="id") $_SESSION["order"] = "date";
        $arr_count = 0;    
        $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
        $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
        $query = 'SELECT * FROM `nodes_product` ORDER BY `'.$_SESSION["order"].'` '.$_SESSION["method"].' LIMIT '.($from-1).', '.$_SESSION["count"];
        $requery = 'SELECT COUNT(*) FROM `nodes_product`';
        $fout = '<div class="document980">';
        $table = '
            <div class="table">
            <table width=100% id="table">
            <thead>
            <tr>';
                $array = array(
                    "title" => "Title",
                    "price" => "Price",
                    "date" => "Date",
                    "status" => "Status"
                ); foreach($array as $order=>$value){
                    $table .= '<th>';
                    if($_SESSION["order"]==$order){
                        if($_SESSION["method"]=="ASC") $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "DESC"; submit_search_form();\'>'.lang($value).'&nbsp;&uarr;</a>';
                        else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'&nbsp;&darr;</a>';
                    }else $table .= '<a class="link" href="#" onClick=\'document.getElementById("order").value = "'.$order.'"; document.getElementById("method").value = "ASC"; submit_search_form();\'>'.lang($value).'</a>';
                    $table .= '</th>';
                }
                $table .= '
            <th></th>
            </tr>
            </thead>';
        $res = engine::mysql($query);
        while($data = mysql_fetch_array($res)){
            $arr_count++;
            if($data["status"]) $status = lang("Enabled");
            else $status = lang("Disabled");
            $imgs = explode(';', $data["img"]);
            if(empty($imgs)) $imgs = array($data["imgs"]);
            $table .= '<tr>
                <td align=left valign=middle class="min-w150"><a href="'.$_SERVER["DIR"].'/product/'.$data["id"].'" target="_blank">'.$data["title"].'</a></td>
                <td align=left valign=middle>$'.$data["price"].'</td>
                <td align=left valign=middle>'.date("d/m/Y H:i", $data["date"]).'</td>
                <td align=left valign=middle>'.$status.'</td>
                <td width=60 align=left valign=middle>
                    <form method="POST" id="edit_product_form_'.$data["id"].'" action="'.$_SERVER["DIR"].'/admin/?mode=products&action=edit&id='.$data["id"].'" >
                        <input type="hidden" name="edit" value="1" />
                        <select name="act" class="input" onChange=\'document.getElementById("edit_product_form_'.$data["id"].'").submit();\'>
                            <option>'.lang("Choose action").'</option>
                            <option value="1">'.lang("Edit item").'</option>';

            if($data["status"]) $table .= '<option value="2">'.lang("Deactivate item").'</option>';
            else $table .= '<option value="3">'.lang("Activate item").'</option>';

            $table .= '
                        </select>
                    </form>
                </td>
            </tr>';
        }$table .= '</table>
    </div>
    <br/>';

        if($arr_count){
            $fout .= $table.'
        <form method="POST"  id="query_form"  onSubmit="submit_search();">
        <input type="hidden" name="page" id="page_field" value="'.$_SESSION["page"].'" />
        <input type="hidden" name="count" id="count_field" value="'.$_SESSION["count"].'" />
        <input type="hidden" name="order" id="order" value="'.$_SESSION["order"].'" />
        <input type="hidden" name="method" id="method" value="'.$_SESSION["method"].'" />
        <input type="hidden" name="reset" id="query_reset" value="0" />

        <div class="total-entry">';
        $res = engine::mysql($requery);
        $data = mysql_fetch_array($res);
        $count = $data[0];
        if($to > $count) $to = $count;
        if($data[0]>0){
            $fout .= '<p class="p5">'.lang("Showing").' '.$from.' '.lang("to").' '.$to.' '.lang("from").' '.$count.' '.lang("entries").', 
                <nobr><select class="input" onChange=\'document.getElementById("count_field").value = this.value; submit_search_form();\' >
                 <option'; if($_SESSION["count"]=="20") $fout .= ' selected'; $fout .= '>20</option>
                 <option'; if($_SESSION["count"]=="50") $fout .= ' selected'; $fout .= '>50</option>
                 <option'; if($_SESSION["count"]=="100") $fout .= ' selected'; $fout .= '>100</option>
                </select> '.lang("per page").'.</nobr></p>';
        }$fout .= '
        </div><div class="cr"></div>';
        if($count>$_SESSION["count"]){
           $fout .= '<div class="pagination" >';
                $pages = ceil($count/$_SESSION["count"]);
               if($_SESSION["page"]>1){
                    $fout .= '<span onClick=\'goto_page('.($_SESSION["page"]-1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Previous").'</a></span>';
                }$fout .= '<ul>';
               $a = $b = $c = $d = $e = $f = 0;
               for($i = 1; $i <= $pages; $i++){
                   if(($a<2 && !$b && $e<2)||
                       ($i >=( $_SESSION["page"]-2) && $i <=( $_SESSION["page"]+2) && $e<5)||
                   ($i>$pages-2 && $e<2)){
                       if($a<2) $a++;
                       $e++; $f = 0;
                       if($i == $_SESSION["page"]){
                           $b = 1; $e = 0;
                          $fout .= '<li class="active-page">'.$i.'</li>';
                       }else{
                           $fout .= '<li onClick=\'goto_page('.($i).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.$i.'</a></li>';
                       }
                   }else if((!$c||!$b) && !$f && $i<$pages){
                       $f = 1; $e = 0;
                       if(!$b) $b = 1;
                       else if(!$c) $c = 1;
                       $fout .= '<li class="dots">. . .</li>';
                   }
               }if($_SESSION["page"]<$pages){
                   $fout .= '<li class="next" onClick=\'goto_page('.($_SESSION["page"]+1).');\'><a hreflang="'.$_SESSION["Lang"].'" href="#">'.lang("Next").'</a></li>';
               }$fout .= '
         </ul>
        </div>';
             }$fout .= '<div class="clear"></div>
    </form>
    </div>
    ';

        }else{
            $fout = '<div class="clear_block">'.lang("Products not found").'</div>';
        }
        $fout .= '<br/>
            <a href="'.$_SERVER["DIR"].'/admin/?mode=products&action=add"><input type="button" class="btn w280" value="'.lang("List new Item").'" ></a><br/><br/>'
            . '<a href="'.$_SERVER["DIR"].'/admin/?mode=products&action=edit"><input type="button" class="btn w280" value="'.lang("Edit Properties").'" ></a><br/>';
    }
    return $fout;
}   