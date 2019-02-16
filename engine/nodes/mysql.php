<?php
/**
* Framework database loader.
* @path /engine/nodes/mysql.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
require_once ("engine/nodes/config.php");
$_SERVER["sql_connection"] = mysqli_connect($_SERVER["config"]["sql_server"], $_SERVER["config"]["sql_login"], $_SERVER["config"]["sql_pass"]);
if(function_exists(internal_encoding)) internal_encoding("utf8");
mysqli_select_db($_SERVER["sql_connection"], $_SERVER["config"]["sql_db"]);
mysqli_query($_SERVER["sql_connection"], "SET SESSION wait_timeout = 4000");