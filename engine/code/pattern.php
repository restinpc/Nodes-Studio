<?php
/**
* Playbacker of user sessions.
* @path /engine/code/pattern.php
*
* @name    Nodes Studio    @version 2.0.7
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
if(!empty($_GET["token"]) && $_SESSION["user"]["id"]=="1"){
?>
<!DOCTYPE html> <!-- Powered by Nodes Studio -->
<html>
<head>
<style>
#wind{
    width: 1px; 
    height: 1px; 
    padding-bottom: 20px;
    position: fixed;
    top: 0px;
    left: 0px;
}
#cursor{
    position: fixed; 
    top: -100px; 
    left: -100px;
}
#action{
    position: fixed; 
    padding: 5px; 
    left: 0px; 
    right: 0px; 
    background: #fff; 
    color: #000; 
    bottom: 0px; 
    border-top: #eee 1px solid;
}
.blind{
    position: fixed; 
    top: 0px; 
    left: 0px; 
    right: 0px; 
    left: 0px; 
    bottom: 0px; 
    background: rgba(0,0,0,0); 
    z-index: 3;
}
body{
    margin: 0px;
}
</style>
</head>
<body>
<?php
    $last_page = 0;
    $session_time = 0;
    $page_time = 0;
    $function_id = 1;
    $min_time = 0;
    $max_time = 0;
    $max = 0;
    $script = '
        var iframe = document.getElementById("wind");
        var cursor = document.getElementById("cursor");
        var cursor_img = document.getElementById("cursor_img");
        var console = document.getElementById("action");
        var t;
        ';
    $fout = '<iframe id="wind" frameborder=0></iframe>
        <div id="cursor"><img id="cursor_img" src="/img/cms/arrow.png"></div>
        <div id="action"></div>
        <div class="blind">&nbsp</div>';
    $query = 'SELECT * FROM `nodes_attendance` WHERE `token` = "'.$_GET["token"].'" ORDER BY `date` ASC';
    $res = engine::mysql($query);
    while($data = mysql_fetch_array($res)){
        if($page_time == 0) 
            $page_time = $data["date"];
        $query = 'SELECT * FROM `nodes_cache` WHERE `id` = "'.$data["cache_id"].'"';
        $r = engine::mysql($query);
        $page = mysql_fetch_array($r);
        $script_x = '
            function function'.($function_id).'(){
            ';
        $script_flag = 0;
        $query = 'SELECT * FROM `nodes_pattern` WHERE `attendance_id` = "'.$data["id"].'" ORDER BY `date` ASC';
        $rr = engine::mysql($query);
        $session_time = 0;
        while($d = mysql_fetch_array($rr)){
            if($min_time == 0){
                $min_time = $d["date"];
            }
            if($d["date"]>$max_time){ 
                $max_time = $d["date"];
            }
            if($session_time == 0){
                $session_time = $d["date"];
            }
            $script_x .= '
                setTimeout( function(){
                    iframe.contentWindow.scrollTo(0, '.$d["top"].');
                    iframe.style.width = "'.$d["width"].'px";
                    iframe.style.height = "'.$d["height"].'px";
                    cursor.style.top = "'.$d["y"].'px";
                    cursor.style.left = "'.$d["x"].'px";
                ';
            if($d["action"] == "1"){
                $script_x .= 'cursor_img.src="/img/cms/pointer.png";
                    console.innerHTML = "'.lang("Click to").' '.$d["x"].'x'.$d["y"].' at "+iframe.src;
                    ';
            }else{
                $script_x .= 'cursor_img.src="/img/cms/arrow.png";
                    console.innerHTML = "'.lang("Mouse move to").' '.$d["x"].'x'.$d["y"].' at "+iframe.src;
                    ';
            }
            $script_x .= '
                        }, '.(($d["date"]-$session_time)*1000).'
                );
                ';
            $max = $d["date"]-$session_time;
            $script_flag++;
        }
        if($script_flag){
            $script .= $script_x.'
                setTimeout( function(){
                    console.innerHTML = "'.lang("Actions are finished at").' "+iframe.src;
                    }, '.(($max*1000)+1000).');
                }
                setTimeout( function(){
                            console.innerHTML = "Loading '.$page["url"].'?lang='.$page["lang"].'..";
                            iframe.onload = function'.($function_id++).';
                            iframe.src="'.$page["url"].'?lang='.$page["lang"].'";
                        }, '.(($data["date"]-$page_time)*1000).'
                    );
                ';
        }
    }
    $fout .= '<script>'.$script.'
        setTimeout( function(){
            console.innerHTML = "'.lang("Session finished").'";
        }, '.((($max_time - $min_time)*1000)+3000).');
        </script>';
    echo $fout;
?>
</body>
</html>
<?php
}else engine::error();