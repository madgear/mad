<?php
namespace php_sys\system;
$server_name = "localhost";
$db_username = "dbusername";
$db_password = "dbpassword";
$db_name = "dbname";
define(__NAMESPACE__ . '\MAD_PROJECT', __NAMESPACE__ . '\\');
define(MAD_PROJECT . "error_handling", FALSE); 
$db_connection["DB"] = array("conn" => NULL, "id" => "DB", "type" => "MYSQL", "host" => $server_name, "port" => 3306, "user" => $db_username, "pass" => $db_password, "db" => $db_name, "qs" => "`", "qe" => "`");
$db_connection[0] = &$db_connection["DB"];
$ERROR_FUNC = MAD_PROJECT . 'error_function';
define(MAD_PROJECT . "PROJECT_ENCODING", "UTF-8");
define(MAD_PROJECT . "IS_MYSQL", TRUE); 
define(MAD_PROJECT . "DB_TIME_ZONE", "");
define(MAD_PROJECT . "MYSQL_CHARSET", "utf8");
define(MAD_PROJECT . "db_error_msg", MAD_PROJECT . "");
?>
