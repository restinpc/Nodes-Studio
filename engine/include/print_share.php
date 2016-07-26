<?php

// TODO - Your code here
//----------------------------

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
function print_share($url, $caption){
    return 
        share_twitter($url, $caption).
        share_facebook($url, $caption).
        share_vkontakte($url, $caption).
        share_gplus($url, $caption);
}