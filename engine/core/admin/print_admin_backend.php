<?php
/**
* Print admin backend page.
* @path /engine/core/admin/print_admin_backend.php
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
* @usage <code> engine::print_admin_backend($cms); </code>
*/
function print_admin_backend($cms){
    if(isset($_POST["id"])){
        $id = intval($_POST["id"]);
        $mode = trim(htmlspecialchars($_POST["mode"]));
        $file = trim(htmlspecialchars($_POST["file"]));
        $query = 'UPDATE `nodes_backend` SET `mode`="'.$mode.'", `file`="'.$file.'" WHERE `id` = "'.$id.'"';
        engine::mysql($query);
        $fout = '<script type="text/javascript">window.location = document.referrer;</script>';
        return $fout;
    }else if(!empty($_POST["mode"]) && !empty($_POST["file"])){
        $mode = trim(htmlspecialchars($_POST["mode"]));
        $mode = str_replace('/', '', $mode);
        $file = trim(htmlspecialchars($_POST["file"]));
        if(strpos($file, ".php")===FALSE) $file .= '.php';
        $fname = "engine/site/".$file;
        $fname = fopen($fname, 'w') or die("can't open file");
        $code = "<?php
/**
* Backend '.$mode.' page file.
* @path /engine/site/'.$file.'
*
* @name    Nodes Studio    @version 2.0.3
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var \$this->title - Page title.
* @var \$this->content - Page HTML data.
* @var \$this->keywords - Array meta keywords.
* @var \$this->description - Page meta description.
* @var \$this->img - Page meta image.
* @var \$this->onload - Page executable JavaScript code.
* @var \$this->configs - Array MySQL configs.
*/
if(!empty($_GET[1])){
    \$this->content = engine::error();
    return; 
}
\$this->content = '<div class=\"document\">
    <img src=\"'.\$_SERVER[\"DIR\"].'/img/cms/nodes_studio.png\" class=\"nodes_image\" />
</div>';";
        $query = 'INSERT INTO nodes_backend(mode, file) VALUES("'.$mode.'", "'.$file.'")';
        engine::mysql($query);
        fwrite($fname, $code);
        fclose($fname);
    }else if($_GET["act"]=="edit" && !empty($_GET["id"]) && !empty($_GET["target"]) && !empty($_GET["value"])){
        $id = intval($_GET["id"]);
        $value = urldecode($_GET["value"]);
        $target = trim(htmlspecialchars($_GET["target"]));
        $query = 'UPDATE `nodes_backend` SET `'.$target.'`="'.$value.'" WHERE `id` = "'.$id.'"';
        engine::mysql($query);
    }else if(intval($_GET["delete"])>0){
        $query = 'SELECT * FROM `nodes_backend` WHERE `id` = "'.intval($_GET["delete"]).'"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $query = 'DELETE FROM `nodes_backend` WHERE `id` = "'.intval($_GET["delete"]).'"';
        engine::mysql($query);
        unlink('engine/site/'.$data["file"]);
    }
    $arr_count = 0;    
    $from = ($_SESSION["page"]-1)*$_SESSION["count"]+1;
    $to = ($_SESSION["page"]-1)*$_SESSION["count"]+$_SESSION["count"];
    $query = 'SELECT * FROM `nodes_backend` ORDER BY `id` ASC';
    $requery = 'SELECT COUNT(*) FROM `nodes_backend` ORDER BY `id` ASC';
    $table = '
        <div class="table">
        <table width=100% id="table">
        <thead>
        <tr>';
            $array = array(
                "mode" => "Path",
                "file" => "File"
            ); foreach($array as $order=>$value){
                $table .= '<th>';
                $table .= lang($value);
                $table .= '</th>';
            } $table .= '<th>'.lang("Action").'</th>
        </tr>
        </thead>
        <tbody>';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if($data["file"] == "main.php" 
        || $data["file"] == "site.php"
        || $data["file"] == "register.php"
        || $data["file"] == "account.php"
        || $data["file"] == "search.php"
        || $data["file"] == "admin.php"
        || $data["file"] == "content.php"
        || $data["file"] == "login.php"){
            $table .= '
                    <tr>
                        <td width=35% align=left><a href="/'.$_SERVER["DIR"].$data["mode"].'" target="_blank">/'.$data["mode"].'</a></td>
                        <td width=35% align=left >'.$data["file"].'</td>';
            if($data["file"] != "site.php"){
                $table .= '<td width=30% align=left>
                    <select class="input w100p" onChange=\'if(this.value==1){show_editor("engine/site/'.$data["file"].'");}\'>
                        <option>'.lang("Select an action").'</option>
                        <option value="1">'.lang("View source").'</option>
                    </select>
                    </td>';
            } 
            $table .= '
                    </tr>
                ';
        }else{
            $table .= '
                    <tr>
                        <td width=35% align=left>
                        <a title="'.lang("Edit").'" onClick=\'var s = prompt("'.lang("Edit").':", "'.$data["mode"].'"); if(s.length > 0 && s != "'.$data["mode"].'"){window.location="'.$_SERVER["DIR"].'/admin/?mode=backend&act=edit&id='.$data["id"].'&target=mode&value="+encodeURI(s);}\'>/'.$data["mode"].'</a></td>
                        <td width=35%  align=left>'.$data["file"].'</td>
                        <td width=30% align=left >
                        <select class="input w100p" onChange=\'if(this.value==1){show_editor("engine/site/'.$data["file"].'");}else if(this.value==2 && confirm("'.lang("Are you sure?").'")){window.location="'.$_SERVER["DIR"].'/admin/?mode=backend&delete='.$data["id"].'";}\'>
                            <option>'.lang("Select an action").'</option>
                            <option value="1">'.lang("View source").'</option>
                            <option value="2">'.lang("Delete file").'</option>
                        </select>
                        </td>
                    </tr>
                ';
        }
    }
$table .= '</tbody>
    </table><br/>
    <form method="POST" id="default">
        '.lang("Default file").': 
        <select name="default" class="input" onChange=\'document.getElementById("default").submit();\'>';
    if(!empty($_POST["default"])){
        $query = 'UPDATE `nodes_config` SET `value` = "'.$_POST["default"].'" WHERE `name` = "default"';
        engine::mysql($query);
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `name` = "default"';   
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    $default = $data["value"];
    $query = 'SELECT * FROM `nodes_backend` ORDER BY `id` ASC';   
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if($data["file"] != "admin.php"){
            if($data["file"]!=$default){
                $table .= '<option value="'.$data["file"].'">'.$data["file"].'</option>'; 
            }else{
                $table .= '<option selected disabled value="'.$data["file"].'">'.$data["file"].'</option>';     
            }
        }
    }        
$table .= '
        </select>
    </form>
</div>
';
        $fout .= '<div class="document640">'.$table.'
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
    }$fout .= '</div><div class="cr"></div>';
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
         }
         $fout .= '
    </form>
    <div class="clear"></div>
    <input type="button" class="btn w280" value="'.lang("New file").'" onClick=\' this.style.display = "none"; document.getElementById("new_file").style.display = "block";\' />
    <div id="new_file" class="hidden">
        <form method="POST">
        '.lang("Path").': <input required placeHolder="'.lang("Path").'" type="text" class="input" name="mode" /><br/><br/>
        '.lang("File").': <input required placeHolder="'.lang("File").'" type="text" class="input" name="file" /><br/><br/>
         <input type="submit" class="btn w280" value="'.lang("Submit").'" />
        </form><br/>
    </div>
    </div>';
    return $fout;
}

