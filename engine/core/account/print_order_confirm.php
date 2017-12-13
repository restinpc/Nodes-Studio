<?php
/**
* Print order confirmation page.
* @path /engine/core/account/print_order_confirm.php
* 
* @name    Nodes Studio    @version 2.0.3
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
* @usage <code> engine::print_order_confirm($site); </code>
*/
function print_order_confirm($site){
    $query = 'SELECT * FROM `nodes_product_order` WHERE `id` = "'.intval($_GET[2]).'"';
    $res = engine::mysql($query);
    $data = mysql_fetch_array($res);
    if(!empty($data)){
        $query = 'SELECT * FROM `nodes_product` WHERE `id` = "'.$data["product_id"].'"';
        $r = engine::mysql($query);
        $product = mysql_fetch_array($r);
        $query = 'SELECT * FROM `nodes_order` WHERE `id` = "'.$data["order_id"].'"';
        $r = engine::mysql($query);
        $order = mysql_fetch_array($r);
        if($order["user_id"] != $_SESSION["user"]["id"]){
            $site->title = lang("Access denied").' - '.$site->title;
            $site->onload .= ' parent.window.location = "'.$_SERVER["DIR"].'/account"; ';
            return;
        }else if(!empty($_POST["rating"])){
            $comment = htmlspecialchars($_POST["comment"]);
            $rating = intval($_POST["rating"]);
            $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$product["user_id"].'"';
            $r = engine::mysql($query);
            $seller = mysql_fetch_array($r);
            if(!empty($_POST["comment"])){
                $url = '/product/'.$product["id"];
                $url = trim(str_replace('"', "'", urldecode($url)));
                $text = str_replace('"', "'", htmlspecialchars(strip_tags($_POST["comment"])));
                $text = str_replace("\n", "<br/>", $text);
                $query = 'SELECT * FROM `nodes_comment` WHERE `text` LIKE "'.$text.'" AND `url` LIKE "'.$url.'" AND `user_id` = "'.$_SESSION["user"]["id"].'"';
                $res = engine::mysql($query);
                $d = mysql_fetch_array($res);
                if(empty($d) && intval($_SESSION["user"]["id"]>0)){
                    $query = 'INSERT INTO `nodes_comment` (`url`, `reply`, `user_id`, `text`, `date`) '
                    . 'VALUES("'.$url.'", "'.intval($_POST["reply"]).'", "'.$_SESSION["user"]["id"].'", "'.$text.'", "'.date("U").'")';
                    engine::mysql($query); 
                }
            }
            $query = 'UPDATE `nodes_product` SET `rating` = "'.($product["rating"]+$_POST["rating"]).'", '
                    . '`votes` = "'.($product["votes"]+1).'" WHERE `id` = "'.$product["id"].'"';
            engine::mysql($query);
            email::delivery_confirmation($data["id"]);
            $query = 'UPDATE `nodes_product_order` SET `status` = "2" WHERE `id` = "'.$data["id"].'"';
            engine::mysql($query);
            $query = 'UPDATE `nodes_user` SET `balance` = "'.($seller["balance"]+doubleval($data["price"])).'" WHERE `id` = "'.$seller["id"].'"';
            engine::mysql($query);
            die('<script>window.location = "'.$_SERVER["DIR"].'/account/purchases"; </script>');
        }
        $images = explode(';', $product["img"]);
        $fout = '
        <div class="document delivery">
            <h1>'.lang("Delivery confirmation").'</h1><br/><br/>
            <div class="delivery_confirm">
                <div class="delivery_image" style="background-image: url('.$_SERVER["DIR"].'/img/data/thumb/'.$images[0].');">&nbsp;</div>
                <div>
                <form method="POST">
                    <input type="hidden" name="rating" id="nodes_rating" value="5" />    
                    <div class="delivery_quality">'.lang("Quality").':</div>
                    <div class="rating_star">
                        <div class="rating_stars" >
                            <div class="rating_blank"></div>
                            <div class="rating_hover"></div>
                            <div class="rating_votes"></div>
                        </div>
                    </div><br/>
                    <textarea name="comment" class="input delivery_textarea" placeHolder="'.lang("Your comment here").'"></textarea><br/><br/>
                    <input type="submit" class="btn w280" value="'.lang("Submit").'" /><br/>
                </form>
                </div>
            </div>
        </div>';
        $site->onload .= ' star_rating(5); ';
    }
    return $fout;
}