<?php
namespace php_sys\system;
include_once "_load.php";
session_start();

$jquery_js = '';
$bootstrap_js = '';
$bootstrap_css = '';

if(!array_key_exists('CURRENTPAGE',$_SESSION)){$_SESSION['CURRENTPAGE'] = "";}

$route = get_route();
if($route == '/'){
  
}elseif($route=='/page' || preg_match('/page\/[a-z]/i', $route)){
  $arr = explode('/', $route);
  $arr_size = count($arr);
  include 'file.php';  
}else{
  echo '404 ';
  exit;
}

?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="CSRF-TOKEN" content="<?php echo _token(40); ?>">
  <title></title>
</head>
<body>
  
</body>
</html>

