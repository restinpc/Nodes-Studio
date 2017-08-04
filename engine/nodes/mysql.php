<?php
/**
* Framework database loader.
* @path /engine/nodes/mysql.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
require_once ("engine/nodes/config.php");
mysql_connect($_SERVER["config"]["sql_server"], $_SERVER["config"]["sql_login"], $_SERVER["config"]["sql_pass"]);
if(function_exists(internal_encoding)) internal_encoding("utf8");
mysql_select_db($_SERVER["config"]["sql_db"]);
mysql_query("SET SESSION wait_timeout = 4000");