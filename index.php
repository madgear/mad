<?php
namespace php_sys\system;
include_once "_load.php";
date_default_timezone_set('Asia/Manila');
session_start();

$route = get_route();


echo $route;



?>