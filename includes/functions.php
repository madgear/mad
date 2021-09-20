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

function get_path(){
	$x_path = explode("/", get_route());
	$path_cnt = count($x_path);	
	$dir_path = "";
	if($path_cnt > 2){
	if($path_cnt==3){$dir_path = "../";}	
	if($path_cnt==4){$dir_path = "../../";}
	if($path_cnt==5){$dir_path = "../../../";}
	if($path_cnt==6){$dir_path = "../../../../";}
	if($path_cnt==6){$dir_path = "../../../../../";}		
	return $dir_path;
	}
}	

function csrf_token($length = 30) {
      $characters = '01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }

function check_error(){	
	if($_SESSION['db_error_msg'] <> "") {
		$err = $_SESSION['db_error_msg'];		
		$_SESSION['db_error_msg'] = "";
		return $err;
		exit;
	}else{
		return "";
	}
}

function return_json($query){
    $get_rows = return_rows($query);
    if (is_array($get_rows)) {                    
        foreach ($get_rows as $get_row) {
            $array[] = $get_row;
        }
        $dataset = array(
            "echo" => 1,
            "totalrecords" => count($array),
            "totaldisplayrecords" => count($array),
            "data" => $array);	
            echo json_encode($dataset);
    }else{
        $dataset = array(
            "echo" => 1,
            "totalrecords" => 0,
            "totaldisplayrecords" => 0,
            "data" => "");	
            echo json_encode($dataset);
    }	
}

?>
