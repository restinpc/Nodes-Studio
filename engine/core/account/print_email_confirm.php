<?php
/**
* Print email confirmation page.
* @path /engine/core/account/print_email_confirm.php
* 
* @name    Nodes Studio    @version 2.0.7
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param object $site Site class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_email_confirm($site); </code>
*/
function print_email_confirm($site){
    $code = '';
    if(!empty($_POST["code"])){
        $code = $_POST["code"];
    }else if(!empty($_GET[1])){
        $code = $_GET[1];
    }
    if(!empty($code)){
        if($code==$_SESSION["user"]["code"]){
            $query = 'UPDATE `nodes_user` SET `confirm` = 1 WHERE `id` = "'.$_SESSION["user"]["id"].'"';
            engine::mysql($query);
            die('<script>window.location = "'.$_SERVER["DIR"].'/account";</script>');
        }else{
            $site->onload .= ' alert("'.lang("Error").'. '.lang("Invalid confirmation code").'"); ';
        }
    }
    $fout = '<div class="document640">
            <h3>'.lang("Account confirmation").'</h3><br/><br/>'
            . '<form method="POST">'
            . '<input type="text" class="input w280" required name="code" placeHolder="'.lang("Confirmation code").'" />'
            . '<br/><br/>'
            . '<input type="submit" class="btn w280" value="'.lang("Submit").'" />'
            . '</form>'
        . '</div>';
    return $fout;
}