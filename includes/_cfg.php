<?php
namespace php_sys\system;
define(__NAMESPACE__ . '\SABONG_PROJECT', __NAMESPACE__ . '\\');
define(SABONG_PROJECT . "DEBUG_ENABLED", FALSE); 
if (DEBUG_ENABLED) {@ini_set("display_errors", "1");error_reporting(E_ALL);}
$CONNECTIONS["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "root", "pass" => "", "db" => "sabong_db", "qs" => "`", "qe" => "`");
$CONNECTIONS[0] = &$CONNECTIONS["DB"];
$ERROR_FUNC = SABONG_PROJECT . 'ErrorFunc';
define(SABONG_PROJECT . "PROJECT_ENCODING", "UTF-8");
define(SABONG_PROJECT . "IS_MYSQL", TRUE); 
define(SABONG_PROJECT . "DB_TIME_ZONE", "");
define(SABONG_PROJECT . "MYSQL_CHARSET", "utf8");
define(SABONG_PROJECT . "SESSION_FAILURE_MESSAGE", SABONG_PROJECT . "");
?>

