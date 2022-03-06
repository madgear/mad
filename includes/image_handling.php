BACK END
 $page = $_POST['page'];
    if($page=='uploadcompanylogo'){
      $cid = $_POST['cid'];
      $updated_by = $_POST['updated_by'];
      $name = $_FILES['imgfile']['name'];
      $image_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));      
      $image_base64 = base64_encode(file_get_contents($_FILES['imgfile']['tmp_name']));
      $image_save = 'data:image/'.$image_ext.';base64,'.$image_base64; 
      $update_query = "UPDATE `pay_sys`.`company` SET `company_logo`='".$image_save."', `updated_by`='".$updated_by."' WHERE  `id`=".$cid.";";
      Execute($update_query);
    }  
	
Other Sample	
	
    if($_FILES['load_img']['name']<>'')
	{
        $name = $_FILES['load_img']['name'];
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["load_img"]["name"]);        
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $extensions_arr = array("jpg","jpeg","png","gif");        

        if( in_array($imageFileType, $extensions_arr) ){
            $image_base64 = base64_encode(file_get_contents($_FILES['load_img']['tmp_name']) );
            $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;            
            move_uploaded_file($_FILES['load_img']['tmp_name'],'upload/'.$name);            
        }   
		
		$update_image = ",`image`='".$image."'";
	}		
	
	
	
	DISPLAY
	
<div>
  <img src="data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAAUA
    AAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO
        9TXL0Y4OHwAAAABJRU5ErkJggg==" alt="Red dot" />
</div>

$image = 'http://images.itracki.com/2011/06/favicon.png';
// Read image path, convert to base64 encoding
$imageData = base64_encode(file_get_contents($image));

// Format the image SRC:  data:{mime};base64,{data};
$src = 'data: ' . mime_content_type($image) . ';base64,' . $imageData;

// Echo out a sample image
echo '<img src="' . $src . '">';
	
	
	
	
