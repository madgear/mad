<?php
namespace php_sys\system;
define(__NAMESPACE__ . '\PROJECT_NAME', __NAMESPACE__ . '\\');
define(PROJECT_NAME . "DEBUG_ENABLED", FALSE); 
if (DEBUG_ENABLED) {@ini_set("display_errors", "1");error_reporting(E_ALL);}
//$CONNECTIONS["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "root", "pass" => "", "db" => "sabong_db", "qs" => "`", "qe" => "`");
$CONNECTIONS["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "madgear", "pass" => "qwertyuiop", "db" => "sabong_db", "qs" => "`", "qe" => "`");
$CONNECTIONS[0] = &$CONNECTIONS["DB"];
$ERROR_FUNC = PROJECT_NAME . 'ErrorFunc';
define(PROJECT_NAME . "PROJECT_ENCODING", "UTF-8");
define(PROJECT_NAME . "IS_MYSQL", TRUE); 
define(PROJECT_NAME . "DB_TIME_ZONE", "");
define(PROJECT_NAME . "MYSQL_CHARSET", "utf8");
define(PROJECT_NAME . "SESSION_FAILURE_MESSAGE", PROJECT_NAME . "");
?>

