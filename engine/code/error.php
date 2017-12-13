<?php
/**
* Error file.
* @path /engine/code/error.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once("engine/nodes/session.php");
$fout .= '
<style>
#div{
    text-align: center; 
    color: #2e3137;  
    width: 100%; 
    border: 0px solid; 
    padding-top: 150px;
    font-family: Sans-Serif;
    line-height: 1.0;
}  
#caption{
    z-index: 0; 
    height: 40px; 
    z-index: 0;  
    padding-top: 10px;
    min-width: 100px;
}
#robot{
    float:right;
    margin-right: 15%;
}
#redirect a{
    font-size: 16px; 
    color: #349ac5;
    text-decoration: none;
}
.error_code{
    font-size: 120px;
}
.error_text{
    font-size: 28px;
}
.clear{
    clear:both;
    height: 100px;
}
@media (max-width: 680px) {
    #robot{
        display: none;
    }
    #div{
        padding-top: 100px;
    }
}
</style>
<div id="div">
    <div id="robot"><img src="data:image/png;base64,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" /></div>
    <div id="caption">';
        if(isset($_GET["504"])){
            $fout .= '
        <span class="error_code">504</span><br/><br/>
        <span class="error_text">'.lang("Gateway Timeout").'</span>
                ';  
        }else if(isset($_GET["204"])){
            if(empty($_POST["jQuery"])){
                header("HTTP/1.0 204 No Content");
            }
            $fout .= '
        <span class="error_code">204</span><br/><br/>
        <span class="error_text">'.lang("Under construction").'</span>
                ';  
        }else if(isset($_GET["401"])){
            if(empty($_POST["jQuery"])){
                header("HTTP/1.0 401 No Content");
            }
            $fout .= '
        <span class="error_code">401</span><br/><br/>
        <span class="error_text">'.lang("Access denied").'</span>
        <br/><br/><br/>
        <span id="redirect"><a href="'.$_SERVER["DIR"].'/login">'.lang("Login to website").'</a></span><br/>
                ';  
        }else if(isset($_GET["500"])){
            if(empty($_POST["jQuery"])){
                header('HTTP/1.1 500 Internal Server Error' );
            }
            $fout .= '
        <span class="error_code">500</span><br/><br/>
        <span class="error_text">'.lang("Internal Server Error").'</span>
        <br/><br/><br/>
        <span id="redirect"><a href="'.$_SERVER["DIR"].'/">'.lang("Back to Home Page").'</a></span><br/>
<!--
MySQL -> '.mysql_error().'; 
PHP -> '.print_r(error_get_last(), 1).';
-->
        ';  
        }else{
            if(empty($_POST["jQuery"])){
                header("HTTP/1.0 404 Not Found");
            }
            $fout .= '
        <span class="error_code">404</span><br/><br/>
        <span class="error_text">'.lang("Page not found").'</span>
        <br/><br/><br/>
        <span id="redirect"><a href="'.$_SERVER["DIR"].'/">'.lang("Back to Home Page").'</a></span><br/>
                ';
        }
        $fout .= '
    </div>
</div>
<div class="clear">&nbsp;</div>';
echo str_replace("  ", " ", str_replace("
", "", $fout));


