<?php
/**
* Print account settings page.
* @path /engine/core/account/print_settings.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
* @usage <code> engine::print_settings($site); </code>
*/
function print_settings($site){
    if($_GET[2] == "delete"){
        $query = 'DELETE FROM `nodes_comment` WHERE `user_id` = "'.$_SESSION["user"]["id"].'"';
        engine::mysql($query);
        $query = 'DELETE FROM `nodes_inbox` WHERE `from` = "'.$_SESSION["user"]["id"].'" OR `to` = "'.$_SESSION["user"]["id"].'"';
        engine::mysql($query);
        $query = 'UPDATE `nodes_user` SET `pass` = "" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
        engine::mysql($query);
        unset($_SESSION["user"]);
        $fout = '<script language="JavaScript">window.location = "'.$_SERVER["DIR"].'/";</script>';
    }else{
        $fout .= '<div class="document640">';
        if(!empty($_POST["name"])){
            $name = strip_tags(engine::escape_string($_POST["name"]));
            $email = strip_tags(strtolower(engine::escape_string($_POST["email"])));
            $bulk_ignore = intval($_POST["bulk_ignore"]);
            $query = 'SELECT `id` FROM `nodes_user` WHERE `email` = "'.$email.'" AND `id` <> "'.$_SESSION["user"]["id"].'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if(!empty($data)){
                $site->onload .= ' alert("'.lang("Sorry, this email already registered").'"); ';
            }else{
                $query = 'UPDATE `nodes_user` SET `email` = "'.$email.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
            }
            $query = 'UPDATE `nodes_user` SET `name` = "'.$name.'", `bulk_ignore` = "'.$bulk_ignore.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
            engine::mysql($query);
            $_SESSION["user"]["name"] = $name;
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["bulk_ignore"] = $bulk_ignore;
            if(!empty($_POST["new_profile_picture"])){
                image::resize_image('img/data/thumb/'.$_POST["new_profile_picture"], 'img/pic/'.$_POST["new_profile_picture"], 100, 100, 1);
                $query = 'UPDATE `nodes_user` SET `photo` = "'.$_POST["new_profile_picture"].'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
                engine::mysql($query);
                $_SESSION["user"]["photo"] = $_POST["new_profile_picture"];
            }
        }if(!empty($_POST["pass"])){
            $password = md5(trim(strtolower($_POST["pass"]))); 
            $query = 'UPDATE `nodes_user` SET `pass` = "'.$password.'" WHERE `id` = "'.$_SESSION["user"]["id"].'"';
            engine::mysql($query);
        }
        if(empty($_SESSION["user"]["email"])){
            $fout .= '<p>'.lang("Enter your email and password to continue").'</p>';
        }
        $fout .= '
        <form method="POST" id="edit_profile_form"> 
        <input type="hidden" name="new_profile_picture" id="new_profile_picture" />
        <table class="w400 m0a" align="center">
            <tr>
                <td align=left colspan=2>
                    <div class="user_photo_block"><img src="'.$_SERVER["DIR"].'/img/pic/'.$_SESSION["user"]["photo"].'" width=80 /></div>
                    <div class="ml100">
                        '.lang("Profile image").':
                        <br/><br/>
                        <input vr-control id="change-picture" type="button" class="btn w280" value="'.lang("Change picture").'" onClick=\'show_photo_editor(0, 0);\' /><br/>
                    </div>
                </td>
            </tr>
            <tr>
                <td align=right class="settings_caption">'.lang("Name").':</td>
                <td class="pb10"><input vr-control id="input-name" type="text" name="name" value="'.$_SESSION["user"]["name"].'" class="input w280" /></td>
            </tr>';

        if(!empty($_SESSION["user"]["email"])){
            $fout .= '
            <tr>
                <td align=right class="settings_caption">'.lang("Email").':</td>
                <td class="pb10"><input vr-control id="input-email" type="text" name="email" value="'.$_SESSION["user"]["email"].'" class="input w280" /></td>
            </tr>
            <tr>
                <td align=right class="settings_caption">'.lang("Password").':</td>
                <td class="pb10"><input vr-control id="input-password" type="password" name="pass" value="" placeHolder="'.lang("New password").'" class="input w280" /></td>
            </tr>';
        }else{
            $fout .= '
            <tr>
                <td align=right class="settings_caption">'.lang("Email").':</td>
                <td class="pb10"><input vr-control id="input-email" required type="text" name="email" placeHolder="'.lang("Enter your email").'" class="input w280" /></td>
            </tr>
            <tr>
                <td align=right class="settings_caption">'.lang("Password").':</td>
                <td class="pb10"><input vr-control id="input-password" required type="password" name="pass" value="" placeHolder="'.lang("Enter your password").'" class="input w280" /></td>
            </tr>'; 
        }
        $fout .= '
        <tr>
            <td align=right class="settings_caption">'.lang("Subscription").':</td>
            <td class="pb10">
                <select vr-control id="select-subscription" name="bulk_ignore" class="input w280" >
                    <option vr-control id="option-enabled" value="0">'.lang("Enabled").'</option>
                    <option vr-control id="option-disabled" value="1" '.($_SESSION["user"]["bulk_ignore"]?'selected':'').'>'.lang("Disabled").'</option>
                </select>
            </td>
        </tr> 
        <tr>
        ';
    if(empty($_SESSION["user"]["url"])){
        $fout .= '<td colspan=2 class="p5">';
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "vk_id"';
        $res = engine::mysql($query);
        $vk = mysqli_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "fb_id"';
        $res = engine::mysql($query);
        $fb_id = mysqli_fetch_array($res);
        $query = 'SELECT * FROM `nodes_config` WHERE `name` = "tw_key"';
        $res = engine::mysql($query);
        $tw_key = mysqli_fetch_array($res);
        if(!empty($fb_id["value"])||
            !empty($tw_key["value"])||
            !empty($vk["value"])){
            $fout .= '<div class="settings_social">'.lang("Connect with social network").'<br/><br/>';
            if(!empty($fb_id["value"])) $fout .= '<a rel="nofollow" target="_parent"  href="'.$_SERVER["DIR"].'/account.php?mode=social&method=fb" class="settings_fb"><img src="'.$_SERVER["DIR"].'/img/social/fb.png" title="Facebook"/></a>';
            if(!empty($tw_key["value"])) $fout .= '<a rel="nofollow" target="_parent" href="'.$_SERVER["DIR"].'/account.php?mode=social&method=tw" class="m15"><img src="'.$_SERVER["DIR"].'/img/social/tw.png" title="Twitter"/></a>';
            if(!empty($vk["value"])) $fout .= '<a rel="nofollow" target="_parent" href="https://oauth.vk.com/authorize?client_id='.$vk["value"].'&scope=notify&redirect_uri='.  urlencode($_SERVER["PUBLIC_URL"].'/account.php?mode=social&method=vk').'&display=page&response_type=token" class="settings_vk"><img src="'.$_SERVER["DIR"].'/img/social/vk.png" title="VK"/></a>';
            $fout .= '</div>';
        }
    }else{
        $fout .= ' <td align=right  class="settings_caption">'.lang("Site").':</td>
        <td align=left class="pl7"><div class="settings_url"><a href="'.$_SESSION["user"]["url"].'" target="_blank">'.str_replace('/', ' / ', str_replace("http://", '', $_SESSION["user"]["url"])).'</a></div><br/>';
    }
    $fout .= '<br/></td>
            </tr>
            <tr>
                <td class="pt20" colspan=2>
                    <input vr-control id="input-save-changes" type="submit" class="btn w280" value="'.lang("Save changes").'" /><br/><br/>
                    <input vr-control id="input-delete-account" type="button" class="btn w280" value="'.lang("Delete account").'" onClick=\'alertify.confirm("'.lang("Are you sure you want to delete your account").'?", function(){ window.location = "/account/settings/delete"; }, function(){ alertify.confirm().destroy();} );\' />
                </td>
            </tr>
            </table>
            </form><br/>
            </div>
            ';
    }
    return $fout;
}