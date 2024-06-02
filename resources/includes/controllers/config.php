<?php
namespace php_sys\system;
define(__NAMESPACE__ . '\SAIKURU', __NAMESPACE__ . '\\');
define(SAIKURU . "DEBUG_ENABLED", FALSE); 
if (DEBUG_ENABLED) {@ini_set("display_errors", "1");error_reporting(E_ALL);}
//$CONNECTIONS["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "root", "pass" => "", "db" => "sabong_db", "qs" => "`", "qe" => "`");
$CONNECTIONS["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => "localhost", "port" => 3306, "user" => "sieteam", "pass" => "vpDsJCUfwgM4bNqdsMb4DC7WtEFykt", "db" => "sie_management", "qs" => "`", "qe" => "`");
$CONNECTIONS[0] = &$CONNECTIONS["DB"];
$ERROR_FUNC = SAIKURU . 'ErrorFunc';
define(SAIKURU . "PROJECT_ENCODING", "UTF-8");
define(SAIKURU . "IS_MYSQL", TRUE); 
define(SAIKURU . "DB_TIME_ZONE", "");
define(SAIKURU . "MYSQL_CHARSET", "utf8");
define(SAIKURU . "SESSION_FAILURE_MESSAGE", SAIKURU . "");
?>

