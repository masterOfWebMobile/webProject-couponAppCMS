<?php
include("config.php");
include("session_check.php");
include("webservices/class/GCM.php");
include("webservices/class/config.php");
include("webservices/class/ToysRUsApns.php");

if(isset($_POST['submit'])) {


		$i_name = $_FILES['img_upload']['name'];
		$i_size = $_FILES['img_upload']['size'];
		$i_error = $_FILES['img_upload']['error'];
		$i_tmp_name = $_FILES['img_upload']['tmp_name'];
		$i_type = $_FILES['img_upload']['type'];
		$ext = strtolower(end(explode('.',$i_name)));
		$msg = '';

        print_r($_FILES['img_upload']);


		if($i_error == 0){
			if($ext == 'jpg' || $ext == 'png'){
				if($i_size > 100){

					unlink("./banner/banner.jpg");
                    unlink("./banner/banner.png");
					$path = "./banner/banner." . $ext;
					$upload = copy($i_tmp_name, $path);

					if($upload){										
						$msg .="Your file has been uploaded";

					}else{
						
						$msg .= "Please try again<br>";
					}
				}else{
					
					$msg .= "The image size should be greater then 10 kb <br>";
				}	
			}else{
				
				$msg .= "The file type is not supported <br>";
			}
		}else{
			$msg .= "There is some error to upload please try again <br>";
		}

	header("Location:dashboard.php");
}else if(isset($_REQUEST['action'])) {
    if($_REQUEST['action'] == 'delete')
    {

        unlink("./banner/banner.jpg");
        unlink("./banner/banner.png");
    }
    header("Location:dashboard.php");
}
		
?>