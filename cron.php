#!/usr/bin/php
<?php
/**
* Executable crontab file.
* Should be configured on autoexec every 1 minute.
*
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
if(isset($argv[1])) $_SERVER["HTTP_HOST"] = $argv[1];
else $_SERVER["HTTP_HOST"] = "nodes-tech.ru";
$_SERVER["DOCUMENT_ROOT"] = "C:/Server/home/nodes-tech.ru/www";
$_SERVER["REQUEST_URI"] = "/cron.php";
$_SERVER["CRON"] = "TRUE";
ini_set('include_path', $_SERVER["DOCUMENT_ROOT"]);
require_once("engine/nodes/autoload.php");