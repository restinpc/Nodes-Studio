<?php
/**
* Prints share friends block.
* @path /engine/core/function/print_share.php
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
* @param string $url Page URL.
* @param string $caption Page caption.
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_share($site, "/", "Hello world"); </code>
*/
require_once("engine/nodes/session.php");
function share_twitter($url, $caption){
 return '
<a title="'.lang("Share friends in").' Twitter" onClick="window.open(\'http://twitter.com/share?text='.$caption.'&url='.urlencode($url).'\', \'Twitter\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/tweeter.jpg" /></a>&nbsp;';
}
function share_facebook($url, $caption){
 return '
<a  title="'.lang("Share friends in").' Facebook" onClick="window.open(\'http://www.facebook.com/sharer.php?u='.urlencode($url).'&t='.$caption.'\', \'Facebook\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/fb.jpg" /></a>&nbsp;';
}
function share_vkontakte($url, $caption){
 return '
<a title="'.lang("Share friends in").' VK" onClick="window.open(\'http://vk.com/share.php?url='.urlencode($url).'&title='.$caption.'\', \'Vkontakte\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/vk.jpg" /></a>&nbsp;';
}
function print_share($site, $url, $caption){
    return 
        share_twitter($url, $caption).
        share_facebook($url, $caption).
        share_vkontakte($url, $caption);
}