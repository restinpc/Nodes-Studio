<?php
/**
* API file
* @path /engine/code/api.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once("engine/nodes/session.php");
header('Content-Type: application/json; charset=utf-8');
$error = 'Errors not found';
$fout = null;
$status = null;
if(!empty($_POST["email"]) && !empty($_POST["pass"])){
    $email = strtolower(str_replace('"', "'", $_POST["email"]));
    $pass = trim(strtolower($_POST["pass"]));
    $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'" AND `pass` = "'.$pass.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    if(!empty($user)){
        if($_GET["mode"] == "send_message"){
            $name = engine::escape_string($_POST["sender_name"]);
            $email = strtolower(engine::escape_string($_POST["sender_email"]));
            $subject = "Message from ".$_SERVER["HTTP_HOST"];
            $query = 'SELECT * FROM `nodes_config` WHERE `name` = "email"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            engine::send_mail(
               $data["value"],
               '"'.$name.'" <'.$email.'>', 
               $subject, 
               str_replace("\n", "<br/>", $_POST["message"])
           );
            $status = "Ok";
        }else{
            $status = "Error";
            $error = 'Request is not specified';
        } 
    }else{
        $status = "Error";
        $error = 'Invalid email or password';
    }
}else if($_GET["mode"] == "reset_password"){
    if(!empty($_POST["email"])){
        $email = str_replace('"', "'", $_POST["email"]);
        $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'"';
        $res = engine::mysql($query);
        $data = mysqli_fetch_array($res);
        if(!empty($data)){
            $code = mb_substr(md5($email.date("Y-m-d")), 0, 4);
            email::restore_password($data["email"], $code);
            $status = "Ok"; 
        }else{
            $status = "Error";
            $error = 'User not found'; 
        }
    }else{
        $status = "Error";
        $error = 'Email is not specified'; 
    }
}else if($_GET["mode"] == "userinfo"){
    $email = strtolower(str_replace('"', "'", $_POST["email"]));
    $pass = md5(trim(strtolower($_POST["password"])));
    $query = 'SELECT * FROM `nodes_user` WHERE `email` = "'.$email.'" AND `pass` = "'.$pass.'"';
    $res = engine::mysql($query);
    $user = mysqli_fetch_array($res);
    if(!empty($user)){
        $fout .= '<user>';
        foreach($user as $key=>$value){
            if(!intval($key) && $key != '0'){
                $fout .= '<'.$key.'>'.$value.'</'.$key.'>';
            }
        }
        $fout .= '</user>';
        $status = "Ok";
    }else{
        $status = "Error";
        $error = 'User not found';
    }
}else{
    $status = "Error";
    $error = 'Email or password are not specified';
}
$type = $_GET["mode"];
$date = date("Y-m-d H:i:s");
$string = <<<XML
<?xml version='1.0'?> 
<response>
    <type>$type</type>
    <status>$status</status>
    <data>$fout</data>
    <error>$error</error>
    <time>$date</time>
</response>
XML;
$xml = simplexml_load_string($string);
$json = json_encode($xml);
$json = str_replace('{}', 'null', $json);
echo $json;