<?php
/**
* Prints share friends block.
* @path /engine/core/function/print_share.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
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
* @param string $url Page URL.
* @param string $caption Page caption.
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_share($site, "/", "Hello world"); </code>
*/
require_once("engine/nodes/language.php");
function share_twitter($url, $caption){
 return '
<a title="'.lang("Share friends in").' Twitter" onClick="window.open(\'http://twitter.com/share?text='.$caption.'&url='.urlencode($url).'\', \'Twitter\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/tweeter.jpg" /></a>
  ';
}
function share_facebook($url, $caption){
 return '
<a  title="'.lang("Share friends in").' Facebook" onClick="window.open(\'http://www.facebook.com/sharer.php?u='.urlencode($url).'&t='.$caption.'\', \'Facebook\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/fb.jpg" /></a>
  ';
}
function share_gplus($url, $caption){
 return '
<a title="'.lang("Share friends in").' Google+" onClick="window.open(\'https://plus.google.com/share?url='. urlencode($url).'\', \'Google+\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/gp.jpg" /></a>
  ';
}
function share_vkontakte($url, $caption){
 return '
<a title="'.lang("Share friends in").' VK" onClick="window.open(\'http://vk.com/share.php?url='.urlencode($url).'&title='.$caption.'\', \'Vkontakte\', \'toolbar=0,status=0,width=320,height=250\');" target="_parent" href="javascript: void(0);"><img src="'.$_SERVER["DIR"].'/img/social/vk.jpg" /></a>
  ';
}
function print_share($site, $url, $caption){
    return 
        share_twitter($url, $caption).
        share_facebook($url, $caption).
        share_vkontakte($url, $caption).
        share_gplus($url, $caption);
}