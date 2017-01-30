<?php
/**
* Framework database loader.
* @path /engine/nodes/mysql.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
require_once ("engine/nodes/config.php");
mysql_connect($_SERVER["config"]["sql_server"], $_SERVER["config"]["sql_login"], $_SERVER["config"]["sql_pass"]);
if(function_exists(mb_internal_encoding)) mb_internal_encoding("utf8");
mysql_select_db($_SERVER["config"]["sql_db"]);
mysql_query("SET SESSION wait_timeout = 4000");