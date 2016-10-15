<?php
require_once("engine/nodes/headers.php");
require_once("engine/nodes/session.php");
require_once("engine/nodes/mysql.php");
require_once("engine/nodes/language.php");

header('Content-Type: application/rss+xml; charset=utf-8');

$query = 'SELECT * FROM `nodes_config` WHERE `name` = "name"';
$res = engine::mysql($query);
$title = mysql_fetch_array($res);

$query = 'SELECT * FROM `nodes_config` WHERE `name` = "description"';
$res = engine::mysql($query);
$description = mysql_fetch_array($res);

echo '<?xml version="1.0" encoding="utf-8"?>
<rss xmlns:media="http://search.yahoo.com/mrss/" version="2.0">
<channel>
<title>'.$title["value"].'</title>
<link>http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'</link>
<description>'.$description["value"].'</description>';
$query = 'SELECT * FROM `nodes_content` ORDER BY `date` DESC LIMIT 0, 50';
$res = engine::mysql($query);
while($data = mysql_fetch_array($res)){
    echo '<item>
    <title>'.$data["caption"].'</title>
    <link>http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/'.$data["url"].'</link>
    <description>'.substr(strip_tags($data["text"]),0,100).'</description>
    <guid>http://'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/'.$data["url"].'</guid>
    <pubDate>'.engine::rssDate($data["date"]).'</pubDate>
</item>
';
}echo '</channel>
</rss>';