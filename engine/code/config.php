<?php
/**
* Config.php generator.
* @path /engine/code/config.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
if(!empty($_POST["admin_email"])){
    if(mysql_connect($_POST["server"], 
        $_POST["login"],
        $_POST["pass"])){
        if(mysql_select_db($_POST["db"])){ 
            if(!empty($_SERVER["HTTP_HOST"])&&
                !empty($_SERVER["DOCUMENT_ROOT"])){
                $query = 'SELECT * FROM `nodes_user` WHERE `id` = "1"';
                $res = mysql_query($query);
                $data = mysql_fetch_array($res);
                $email = strtolower($_POST["admin_email"]);
                $pass = md5(trim(strtolower($_POST["admin_pass"]))); 
                if($email==$data["email"]&&$pass==$data["pass"]){
                    $fname = "engine/nodes/config.php";
                    $fname = fopen($fname, 'w') or die("Error. Can't open file engine/nodes/config.php");
                        $code = '<?php
/**
* Config file.
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
';
$source = '/**'."\n".'
* Framework config file'."\n".'
*/'."\n".'
$_SERVER["config"] = array('."\n".'
    "name" => "'. mysql_real_escape_string($_POST["name"]).'",'."\n".'
    "sql_server" => "'. mysql_real_escape_string($_POST["mysql_server"]).'",'."\n".'
    "sql_login" => "'. mysql_real_escape_string($_POST["mysql_login"]).'",'."\n".'
    "sql_pass" => "'. mysql_real_escape_string($_POST["mysql_pass"]).'",'."\n".'
    "sql_db" => "'. mysql_real_escape_string($_POST["mysql_db"]).'"'."\n".'
);';     
                    if(intval($_POST["encoding"])){
                        $encode = base64_encode($source);
                        $code .= 'eval(base64_decode("'.$encode.'"));';
                    }else{
                        $code .= $source;
                    }
                    fwrite($fname, $code);
                    fclose($fname);
                    $fout = '<center>Config file updated! Redirect after 5 seconds.</center>
                        <script>setTimeout(function(){window.location = "'.$_SERVER["DIR"].'/";}, 5000);</script>';
                }else{
                    $fout = '<center>Error. Invalid login or password.</center>
                    <script>setTimeout(function(){window.location = "'.$_SERVER["DIR"].'/";}, 5000);</script>';  
                }
            }
        }else{
            $fout = '<center>Error while selecting to MySQL DB. Redirect after 5 seconds.</center>
            <script>setTimeout(function(){window.location = "'.$_SERVER["DIR"].'/";}, 5000);</script>';  
        }
    }else{
        $fout = '<center>Error while connecting to MySQL server. Redirect after 5 seconds.</center>
        <script>setTimeout(function(){window.location = "'.$_SERVER["DIR"].'/";}, 5000);</script>'; 
    }
}else{
    if(function_exists('exec')&&function_exists('base64_decode')&&function_exists('base64_encode')){
        $options = '<option value="1" selected>Yes</option><option value="0">No</option>';
    }else{
        $options = '<option value="1">Yes</option><option value="0" selected>No</option>';   
    }
    $fout = '<div style="width: 280px; margin: 0px auto; margin-top: 20px;">
    <center><h1>Configuration</h1></center><br/>
    <form method="POST" id="post_form" onSubmit=\'if(confirm("Do you want to rewrite engine/nodes/config.php file?")){return 1;}else{event.preventDefault();}\'>
        <div style="width: 110px; float: left; margin-right: 10px;">Admin email</div> <input class="input" required="required" type="text" name="admin_email" ><br/><br/>
        <div style="width: 110px; float: left; margin-right: 10px;">Admin pass</div> <input class="input" required="required" type="text" name="admin_pass" ><br/><br/>
        <div style="width: 110px; float: left; margin-right: 10px;">Mysql server</div> <input id="server" class="input" type="text" required="required" name="mysql_server" value="localhost" ><br/><br/>
        <div style="width: 110px; float: left; margin-right: 10px;">Mysql login</div> <input id="login" class="input" type="text" required="required" name="mysql_login" value="root" ><br/><br/>
        <div style="width: 110px; float: left; margin-right: 10px;">Mysql pass</div> <input id="pass" class="input" type="text" name="mysql_pass" ><br/><br/>
        <div style="width: 110px; float: left; margin-right: 10px;">Mysql DB</div> <input id="db" class="input" type="text" required="required" name="mysql_db" value="" ><br/><br/>
        <div style="width: 210px; float: left; margin-right: 10px;">Encode file</div> <select class="input" type="text" name="encoding">'.$options.'</select><br/>
        <br/><input type="submit" value="Update file" style="width: 270px; padding: 5px;" />
    </form>
    </div>';
}echo $fout;