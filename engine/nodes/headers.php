<?php
/**
* Framework headers file.
* @path /engine/nodes/headers.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: max-age=0, pre-check=0, no-cache, no-store, must-revalidate");
header("Expires: Wed, 11 Jan 1984 05:00:00 GMT");
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Pragma: no-cache');
ini_set('error_reporting', 0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);   
ini_set("upload_max_filesize", "20M");
ini_set("post_max_size", "20M");
ini_set("max_input_time", "60");
ini_set("max_execution_time", "60");
ini_set("mbstring.func_overload", "2");
ini_set("mbstring.internal_encoding", "UTF-8");