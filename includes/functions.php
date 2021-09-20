<?php namespace php_sys\system;?>
<?php
if(!array_key_exists('db_error_msg',$_SESSION)){$_SESSION['db_error_msg'] = "";}

function get_route(){
$get_uri = $_SERVER['REQUEST_URI'];
if($get_uri=="/"){return $route_uri;exit;}
$x_path = explode("/", $get_uri);
$path_cnt = count($x_path);
if($path_cnt > 1){
  $route_uri = str_replace('/'.$x_path[1],'',$get_uri);	
}else{
  $route_uri = '/'; 
}
  return $route_uri;
}

?>
