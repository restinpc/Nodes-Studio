<?php
/**
* Dump MySQL database.
* @path /engine/core/dump.php
* 
* @name    MySQLDump    @version 2.20 - 02/11/2007
* @author  Daniele Viganň - CreativeFactory.it <daniele.vigano@creativefactory.it>
* @license http://opensource.org/licenses/gpl-license.php
* 
* @example <code>
*  $dumper = new dump($dbname,'filename.sql',false,false);
*  $dumper->doDump();
* </code>
*/
class dump {
protected $database = null;
var $compress = false;
var $hexValue = false;
var $filename = null;
var $file = null;
protected  $isWritten = false;
//------------------------------------------------------------------------------
/**
* Class constructor
* 
* @param string $db The database name
* @param string $filepath The file where the dump will be written
* @param boolean $compress It defines if the output file is compress (gzip) or not
* @param boolean $hexValue It defines if the outup values are base-16 or not
*/
function dump($db = null, $filepath = 'dump.sql', $compress = false, $hexValue = false){
    $this->compress = $compress;
    if ( !$this->setOutputFile($filepath) )
        return false;
    return $this->setDatabase($db);
}
//------------------------------------------------------------------------------
/**
* Sets the database to work on
* @param string $db The database name
*/
function setDatabase($db){
    $this->database = $db;
    if ( !@mysqli_select_db($_SERVER["sql_connection"], $this->database) )
        return false;
    return true;
 }
//------------------------------------------------------------------------------
/**
* Returns the database where the class is working on
* @return string
*/
function getDatabase(){
    return $this->database;
}
//------------------------------------------------------------------------------
/**
* Sets the output file type (It can be made only if the file hasn't been already written)
* @param boolean $compress If it's true, the output file will be compressed
*/
function setCompress($compress){
    if ( $this->isWritten )
        return false;
    $this->compress = $compress;
    $this->openFile($this->filename);
    return true;
}
//------------------------------------------------------------------------------
/**
* Returns if the output file is or not compressed
* @return boolean
*/
function getCompress(){
    return $this->compress;
}
//------------------------------------------------------------------------------
/**
* Sets the output file
* @param string $filepath The file where the dump will be written
*/
function setOutputFile($filepath){
    if ( $this->isWritten )
        return false;
    $this->filename = $filepath;
    $this->file = $this->openFile($this->filename);
    return $this->file;
}
//------------------------------------------------------------------------------
/**
* Returns the output filename
* @return string
*/
function getOutputFile(){
        return $this->filename;
}
//------------------------------------------------------------------------------
/**
* Writes to file the $table's structure
* @param string $table The table name
*/
function getTableStructure($table){
    if ( !$this->setDatabase($this->database) )
        return false;
    // Structure Header
    $structure = "-- \n";
    $structure .= "-- Table structure for table `{$table}` \n";
    $structure .= "-- \n\n";
    // Dump Structure
    $structure .= 'DROP TABLE IF EXISTS `'.$table.'`;'."\n";
    $structure .= "CREATE TABLE `".$table."` (\n";
    $records = @mysqli_query($_SERVER["sql_connection"], 'SHOW FIELDS FROM `'.$table.'`');
    if ( @mysqli_num_rows($records) == 0 )
            return false;
    while ( $record = mysqli_fetch_assoc($records) ) {
            $structure .= '`'.$record['Field'].'` '.$record['Type'];
            if ( @strcmp($record['Null'],'YES') != 0 )
                    $structure .= ' NOT NULL';
            if ( !empty($record['Default']) || @strcmp($record['Null'],'YES') == 0) 
                    $structure .= ' DEFAULT '.(is_null($record['Default']) ? 'NULL' : "'{$record['Default']}'");
            if ( !empty($record['Extra']) )
                    $structure .= ' '.$record['Extra'];
            $structure .= ",\n";
    }
    $structure = @ereg_replace(",\n$", null, $structure);
    // Save all Column Indexes
    $structure .= $this->getSqlKeysTable($table);
    $structure .= "\n)";
    //Save table engine
    $records = @mysqli_query($_SERVER["sql_connection"], "SHOW TABLE STATUS LIKE '".$table."'");
    // echo $query; - ???
    if ( $record = @mysqli_fetch_assoc($records) ) {
            if ( !empty($record['Engine']) )
                    $structure .= ' ENGINE='.$record['Engine'];
            if ( !empty($record['Auto_increment']) )
                    $structure .= ' AUTO_INCREMENT='.$record['Auto_increment'];
    }
    $structure .= ";\n\n-- --------------------------------------------------------\n\n";
    $this->saveToFile($this->file,$structure);
}
//------------------------------------------------------------------------------
/**
* Writes to file the $table's data
* @param string $table The table name
* @param boolean $hexValue It defines if the output is base 16 or not
*/
function getTableData($table,$hexValue = true) {
    if ( !$this->setDatabase($this->database) )
            return false;
    // Header
    $data = "-- \n";
    $data .= "-- Dumping data for table `$table` \n";
    $data .= "-- \n\n";
    $records = mysqli_query($_SERVER["sql_connection"], 'SHOW FIELDS FROM `'.$table.'`');
    $num_fields = @mysqli_num_rows($records);
    if ( $num_fields == 0 )
            return false;
    // Field names
    $selectStatement = "SELECT ";
    $insertStatement = "INSERT INTO `$table` (";
    $hexField = array();
    for ($x = 0; $x < $num_fields; $x++) {
        $record = @mysqli_fetch_assoc($records);
        if ( ($hexValue) && ($this->isTextValue($record['Type'])) ) {
                $selectStatement .= 'HEX(`'.$record['Field'].'`)';
                $hexField [$x] = true;
        }
        else
                $selectStatement .= '`'.$record['Field'].'`';
        $insertStatement .= '`'.$record['Field'].'`';
        $insertStatement .= ", ";
        $selectStatement .= ", ";
    }
    $insertStatement = @mb_substr($insertStatement,0,-2).') VALUES';
    $selectStatement = @mb_substr($selectStatement,0,-2).' FROM `'.$table.'`';
    $records = @mysqli_query($_SERVER["sql_connection"], $selectStatement);
    $num_rows = @mysqli_num_rows($records);
    $num_fields = @mysqli_num_fields($records);
    // Dump data
    if ( $num_rows > 0 ) {
        $data .= $insertStatement;
        for ($i = 0; $i < $num_rows; $i++) {
            $record = @mysqli_fetch_assoc($records);
            $data .= ' (';
            for ($j = 0; $j < $num_fields; $j++) {
                $field_name = mysqli_fetch_field_direct($records, $j)->name;
                            //@mysql_field_name($records, $j);
                if ( isset($hexField[$j]) && $hexField[$j] && (@strlen($record[$field_name]) > 0) )
                        $data .= "0x".$record[$field_name];
                else if ( is_null($record[$field_name]) )
                        $data .= "NULL";
                else
                        $data .= "'".@str_replace('\"','"',@mysqli_escape_string($_SERVER["sql_connection"], $record[$field_name]))."'";
                $data .= ',';
            }
            $data = @mb_substr($data,0,-1).")";
            $data .= ( $i < ($num_rows-1) ) ? ',' : ';';
            $data .= "\n";
            //if data in greater than 1MB save
            if (strlen($data) > 1048576) {
                    $this->saveToFile($this->file,$data);
                    $data = '';
            }
        }
        $data .= "\n-- --------------------------------------------------------\n\n";
        $this->saveToFile($this->file,$data);
    }
}
//------------------------------------------------------------------------------
/**
* Writes to file all the selected database tables structure
* @return boolean
*/
function getDatabaseStructure(){
    $records = @mysqli_query($_SERVER["sql_connection"], 'SHOW TABLES');
    if ( @mysqli_num_rows($records) == 0 )
            return false;
    $structure = '';
    while ( $record = @mysqli_fetch_row($records) ) {
            $structure .= $this->getTableStructure($record[0]);
    }
    return true;
}
//------------------------------------------------------------------------------
/**
* Writes to file all the selected database tables data
* @param boolean $hexValue It defines if the output is base-16 or not
*/
function getDatabaseData($hexValue = true){
    $records = @mysqli_query($_SERVER["sql_connection"], 'SHOW TABLES');
    if ( @mysqli_num_rows($records) == 0 )
            return false;
    while ( $record = @mysqli_fetch_row($records) ) {
            $this->getTableData($record[0],$hexValue);
    }
}
//------------------------------------------------------------------------------
/**
* Writes to file the selected database dump
*/
function doDump($params = array(), $close_file = true) {
    $this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 0;\n\n");
    if (!isset($params['skip_structure']))
        $this->getDatabaseStructure();
    if (!isset($params['skip_data']))
        $this->getDatabaseData($this->hexValue);
    $this->saveToFile($this->file,"SET FOREIGN_KEY_CHECKS = 1;\n\n");
    if ($close_file)
        $this->closeFile($this->file);
    return true;
}
//------------------------------------------------------------------------------
/**
* @deprecated Look at the doDump() method
*/
function writeDump($filename) {
    if ( !$this->setOutputFile($filename) )
        return false;
    $this->doDump();
    $this->closeFile($this->file);
    return true;
}
//------------------------------------------------------------------------------
function getSqlKeysTable ($table) {
    $primary = "";
    unset($unique);
    unset($index);
    unset($fulltext);
    $results = mysqli_query($_SERVER["sql_connection"], "SHOW KEYS FROM `{$table}`");
    if ( @mysqli_num_rows($results) == 0 )
            return false;
    while($row = mysqli_fetch_object($results)) {
        if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
            if ( $primary == "" )
                $primary = "  PRIMARY KEY  (`{$row->Column_name}`";
            else
                $primary .= ", `{$row->Column_name}`";
        }
        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
            if ( (!is_array($unique)) OR ($unique[$row->Key_name]=="") )
                $unique[$row->Key_name] = "  UNIQUE KEY `{$row->Key_name}` (`{$row->Column_name}`";
            else
                $unique[$row->Key_name] .= ", `{$row->Column_name}`";
        }
        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
            if ( (!is_array($index)) OR ($index[$row->Key_name]=="") )
                $index[$row->Key_name] = "  KEY `{$row->Key_name}` (`{$row->Column_name}`";
            else
                $index[$row->Key_name] .= ", `{$row->Column_name}`";
        }
        if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
            if ( (!is_array($fulltext)) OR ($fulltext[$row->Key_name]=="") )
                $fulltext[$row->Key_name] = "  FULLTEXT `{$row->Key_name}` (`{$row->Column_name}`";
            else
                $fulltext[$row->Key_name] .= ", `{$row->Column_name}`";
        }
    }
    $sqlKeyStatement = '';
    // generate primary, unique, key and fulltext
    if ( $primary != "" ) {
        $sqlKeyStatement .= ",\n";
        $primary .= ")";
        $sqlKeyStatement .= $primary;
    }
    if (isset($unique) && is_array($unique)) {
        foreach ($unique as $keyName => $keyDef) {
            $sqlKeyStatement .= ",\n";
            $keyDef .= ")";
            $sqlKeyStatement .= $keyDef;
        }
    }
    if (isset($index) && is_array($index)) {
        foreach ($index as $keyName => $keyDef) {
            $sqlKeyStatement .= ",\n";
            $keyDef .= ")";
            $sqlKeyStatement .= $keyDef;
        }
    }
    if (isset($fulltext) && is_array($fulltext)) {
        foreach ($fulltext as $keyName => $keyDef) {
            $sqlKeyStatement .= ",\n";
            $keyDef .= ")";
            $sqlKeyStatement .= $keyDef;
        }
    }
    return $sqlKeyStatement;
}
//------------------------------------------------------------------------------
function isTextValue($field_type) {
    switch ($field_type) {
        case "tinytext":
        case "text":
        case "mediumtext":
        case "longtext":
        case "binary":
        case "varbinary":
        case "tinyblob":
        case "blob":
        case "mediumblob":
        case "longblob":
            return True;
            break;
        default:
            return False;
    }
}
//------------------------------------------------------------------------------
function openFile($filename) {
    $file = false;
    if ( $this->compress )
        $file = @gzopen($filename, "w9");
    else
        $file = @fopen($filename, "w");
    return $file;
}
//------------------------------------------------------------------------------
function saveToFile($file, $data) {
    if ( $this->compress )
        @gzwrite($file, $data);
    else
        @fwrite($file, $data);
    $this->isWritten = true;
}
//------------------------------------------------------------------------------
function closeFile($file) {
    if ( $this->compress )
        @gzclose($file);
    else
        @fclose($file);
}
}