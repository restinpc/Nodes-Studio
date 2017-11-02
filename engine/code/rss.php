<?php
/**
* RSS-feed generator.
* @path /engine/code/rss.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
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
<link>'.$_SERVER["PUBLIC_URL"].'/</link>
<description>'.$description["value"].'</description>';
$query = 'SELECT * FROM `nodes_content` ORDER BY `date` DESC LIMIT 0, 50';
$res = engine::mysql($query);
while($data = mysql_fetch_array($res)){
    echo '<item>
    <title>'.$data["caption"].'</title>
    <link>'.$_SERVER["PUBLIC_URL"].'/'.$data["url"].'</link>
    <description>'.mb_substr(strip_tags($data["text"]),0,100).'</description>
    <guid>'.$_SERVER["PUBLIC_URL"].'/'.$data["url"].'</guid>
    <pubDate>'.date(DATE_RSS, $data["date"]).'</pubDate>
</item>
';
}echo '</channel>
</rss>';