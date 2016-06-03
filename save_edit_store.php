<?php 
include("config.php");
include("session_check.php");

  if(isset($_POST['submit'])){
  	  	$name   = htmlspecialchars($_POST['s_name'],ENT_QUOTES);
  	  	$s_city = htmlspecialchars($_POST['city'],ENT_QUOTES);
                $s_address   = htmlspecialchars($_POST['s_address'],ENT_QUOTES);
                $s_lat   = $_POST['s_lat'];
                $s_long   = $_POST['s_long'];
  	  $description = htmlspecialchars($_POST['description'],ENT_QUOTES);	
  	   $photo     = $_POST['photo'];
  	   $st_id   = $_POST['st_id'];
  		if(!empty($_FILES['img_upload']['name'])) {
		$i_name = $_FILES['img_upload']['name'];
		$i_size = $_FILES['img_upload']['size'];
		$i_error = $_FILES['img_upload']['error'];
		$i_tmp_name = $_FILES['img_upload']['tmp_name'];
		$i_type = $_FILES['img_upload']['type'];
		$ext = strtolower(end(explode('.',$i_name)));
		$msg = '';
		if($i_error == 0){
			if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
				if($i_size > 0){
					$img_name = rand().'.'.$ext;
					$path = 'coup_image/'.$img_name;
					$upload = copy($i_tmp_name, $path);
					if($upload){										
						$msg .="Your file has been updated";
					}else{
						$img_name = 'def.jpg';
						$msg .= "Please try again<br>";
					}
				}else{
					$img_name = 'def.jpg';
					$msg .= "The image size should be greater then 10 kb <br>";
				}	
			}else{
				$img_name = 'def.jpg';	
				$msg .= "The file type is not supported <br>";
			}
		}else{
			$img_name = 'def.jpg';	
			$msg .= "There is some error to upload please try again <br>";
		}	
	
	
		}
		else { 
		 $path = $photo; 
        $msg = "Records has been updated";		 
		 } 


       $sql = "update add_store set s_name = '".$name."',s_city = '".$s_city."',s_address = '".$s_address."',s_lat = '".$s_lat."',s_long = '".$s_long."',s_image='".$path."',s_text = '".$description."' where id='".$st_id."'";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
	    header("Location:view_store.php?msg=".$msg);
	   
	  /* echo "Your File Has Been Updated";
echo "<script>setTimeout(\"location.href = 'edit_daily.php';\",1500);</script>";*/
}
 
	?>