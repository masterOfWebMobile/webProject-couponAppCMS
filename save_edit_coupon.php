<?php 
include("config.php");
include("session_check.php");
include("webservices/class/GCM.php");
include("webservices/class/config.php");
require_once 'excel_reader2.php';
$excel = new Spreadsheet_Excel_Reader(); 



function makeThumbnails($updir, $img)
{
    
    $thumb_beforeword = "thumb_";
    $arr_image_details = getimagesize($updir . $img); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];

    $thumbnail_width = $original_width / 3;
    $thumbnail_height = $original_height / 3;


    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == 1) {
        $imgt = "imagegif";
        $imgcreatefrom = "imagecreatefromgif";
    }
    if ($arr_image_details[2] == 2) {
        $imgt = "imagejpeg";
        $imgcreatefrom = "imagecreatefromjpeg";
    }
    if ($arr_image_details[2] == 3) {
        $imgt = "imagepng";
        $imgcreatefrom = "imagecreatefrompng";
    }
    if ($imgt) {

        $old_image = $imgcreatefrom($updir . $img);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresized($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $updir .  "$thumb_beforeword" . "$img");
    }
}



  if(isset($_POST['submit'])){
  	  	$name   = $_POST['c_name'];

  	  	// Added newly in new mechanism.
	   	$prefix = $_POST['c_prefix'];
	   	$category = $_POST['c_category'];
	   	$type = $_POST['c_type'];
	   	$nums = $_POST['c_nums'];   // number of coupon numbers
  	  	
  	  	$dateInput = explode('-',$_POST['date_from']);
      	$from_Date = $dateInput[2].'-'.$dateInput[0].'-'.$dateInput[1];
      	$dateInput2 = explode('-',$_POST['date_to']);
    	$to_Date = $dateInput2[2].'-'.$dateInput2[0].'-'.$dateInput2[1];
    
      

		$description = htmlspecialchars($_POST['description'],ENT_QUOTES);	
  		$photo     = $_POST['photo'];
  		$photo_share     = $_POST['photo_share'];
  	    $coup_id   = $_POST['coup_id'];

  	    //delete coupon numbers if prefix or type is changed
		$query = "SELECT * FROM add_coupon WHERE id = '$coup_id'";
		$result = mysql_query($query) or die(mysql_error());
	    $fetch_coupan = mysql_fetch_assoc($result);

		$c_old_category = $fetch_coupan['c_category'];
		$c_old_prefix = $fetch_coupan['c_prefix'];
		$c_old_type = $fetch_coupan['c_type'];
		$c_old_nums = $fetch_coupan['c_nums'];
		$c_path = dirname($fetch_coupan['c_image']);

		$c_image_name = basename($fetch_coupan['c_image']);
		$c_share_image_name = basename($fetch_coupan['c_share_image']);


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
						//$img_name = rand().'.'.$ext;
						$path = "{$c_path}/{$i_name}";

						$upload = copy($i_tmp_name, $path);
						if($upload){										
							$msg .="Your file has been updated";
							makeThumbnails($c_path."/",$i_name);
							//unlink('coup_image/'.$photo);
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


		if(!empty($_FILES['img_share_upload']['name'])) {
			$i_name = $_FILES['img_share_upload']['name'];
			$i_size = $_FILES['img_share_upload']['size'];
			$i_error = $_FILES['img_share_upload']['error'];
			$i_tmp_name = $_FILES['img_share_upload']['tmp_name'];
			$i_type = $_FILES['img_share_upload']['type'];
			$ext = strtolower(end(explode('.',$i_name)));
			$msg = '';
			if($i_error == 0){
				if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
					if($i_size > 0){

						$path_share = "{$c_path}/{$i_name}";
						
						$upload = copy($i_tmp_name, $path_share);

						if($upload){										
							$msg .="Your file has been updated";
							//unlink('coup_image/'.$photo_share);
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
		 $path_share = $photo_share; 
        $msg = "Records has been updated";		 
		 } 

	if ($c_old_category != $category) {
		$time = time();
		$order = $category . '-' . $time .'-' . '1';
		$sql = "update add_coupon set c_name = '".$name."', c_category = ".$category." ,c_prefix = '".$prefix."',c_type = '".$type."',c_nums = ".$nums." ,c_date = '".$from_Date."',to_date = '".$to_Date."',c_image='".$path."',c_share_image='".$path_share."',c_text = '".$description."' ,c_order = '".$order."' where id='".$coup_id."'";			
	}else
    	$sql = "update add_coupon set c_name = '".$name."',c_prefix = '".$prefix."',c_type = '".$type."',c_nums = ".$nums." ,c_date = '".$from_Date."',to_date = '".$to_Date."',c_image='".$path."',c_share_image='".$path_share."',c_text = '".$description."' where id='".$coup_id."'";
   	$run_sql = mysql_query($sql) or die(mysql_error());

	
	if ($c_old_category != $category || $c_old_prefix != $prefix || $c_old_type != $type || $c_old_nums != $nums) 
	{
		$query = " DELETE from cupan_number WHERE coupan_id = '" . $coup_id . "'";
		
		$result = mysql_query($query) or die(mysql_error());
		// create txt file that contains running coupon number
		$handle = fopen($c_path . "/" . $coup_id . ".txt","r+") or die("Unable to open file for editing coupon numbers!");
		//Lock File, error if unable to lock
		if(flock($handle, LOCK_EX)) {
			
		    ftruncate($handle, 0);    //Truncate the file to 0
		    rewind($handle);           //Set write pointer to beginning of file
		    fwrite($handle, 0);    //Write the new Hit Count
		    flock($handle, LOCK_UN);    //Unlock File
		    fclose($handle);
		}
		else
		{
			echo "Can't Lock file";
		}
	}
   	
  	// if(!empty($_FILES["excel"]["name"])) { 
   // 	 	$file_name = "excel_files/".$_FILES["excel"]["name"];

   //      $file = move_uploaded_file($_FILES["excel"]["tmp_name"],"excel_files/".$_FILES["excel"]["name"]);

	 	// if($file) {


	 	// 	// Not sure about we need to delete old coupon number or not. because the devices are linked already
	 	// 	/*
	 	// 	$delete_query = "DELETE FROM cupan_number WHERE cupan_number.coupan_id = '".$coup_id."'";
	 	// 	$result = mysql_query($delete_query);
			// */

	 	// 	$query = " DELETE from cupan_number WHERE coupan_id = '" . $coup_id . "'";
	 	// 	$result = mysql_query($query) or die(mysql_error());

			// $data = new Spreadsheet_Excel_Reader($file_name);								
			// foreach($data->sheets[0]['cells'] as $readdata){	
			
			//    /*$query = "select * from cupan_number where coupan_number = '".$readdata[1]."' and  coupan_id = '".$coup_id."' ";
			//    $result = mysql_query($query) or die(mysql_error());
			//    $fetch = mysql_fetch_assoc($result);	 
			//    $num = mysql_num_rows($result); 	    
			//    if($num == 0 ) { */
			// 	$sql_insert="INSERT INTO cupan_number (cn_id,coupan_number,coupan_id) VALUES('','$readdata[1]','$coup_id')";
			//  	$result_insert = mysql_query($sql_insert) or die(mysql_error()); 
			//  	//}
			// }
	  //   }
   //  }
	    header("Location:view_coupon.php?msg=".$msg);
	   
	  /* echo "Your File Has Been Updated";
echo "<script>setTimeout(\"location.href = 'edit_daily.php';\",1500);</script>";*/
}
 
	?>