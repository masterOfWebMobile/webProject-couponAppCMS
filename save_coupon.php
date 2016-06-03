<?php
include("config.php");
include("session_check.php");
include("webservices/class/GCM.php");
include("webservices/class/config.php");
include("webservices/class/ToysRUsApns.php");

require_once 'excel_reader2.php';
$excel = new Spreadsheet_Excel_Reader(); 

$GCM_BOJ = new GCM();
$IOS_GCM_OBJ = new IOSNotify();


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


if(isset($_POST['submit'])) {


	   	$name   = htmlspecialchars($_POST['c_name'],ENT_QUOTES);
	   	// Added newly in new mechanism.
	   	$prefix = $_POST['c_prefix'];
	   	$type = $_POST['c_type'];
	   	$nums = $_POST['c_nums'];   // number of coupon numbers
	   	$category = $_POST['c_category'];

	   	$dateInput = explode('-',$_POST['date_from']);
		$from_Date = $dateInput[2].'-'.$dateInput[0].'-'.$dateInput[1];
		$dateInput2 = explode('-',$_POST['date_to']);
		$to_Date = $dateInput2[2].'-'.$dateInput2[0].'-'.$dateInput2[1];
 		$description = htmlspecialchars($_POST['description'],ENT_QUOTES);	

		$i_name = $_FILES['img_upload']['name'];
		$i_size = $_FILES['img_upload']['size'];
		$i_error = $_FILES['img_upload']['error'];
		$i_tmp_name = $_FILES['img_upload']['tmp_name'];
		$i_type = $_FILES['img_upload']['type'];
		$ext = strtolower(end(explode('.',$i_name)));
		$msg = '';
		
		$rndname = rand();
		$coupon_path = 'coupons/'.$rndname . "/";
		mkdir($coupon_path,0777,true);


		if($i_error == 0){
			if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
				if($i_size > 100){

					
					$path = $coupon_path . $i_name;
					$upload = copy($i_tmp_name, $path);

					if($upload){										
						$msg .="Your file has been uploaded";

						makeThumbnails($coupon_path,$i_name);


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

		$i_name2 = $_FILES['img_share_upload']['name'];
		$i_size2 = $_FILES['img_share_upload']['size'];
		$i_error2 = $_FILES['img_share_upload']['error'];
		$i_tmp_name2 = $_FILES['img_share_upload']['tmp_name'];
		$i_type2 = $_FILES['img_share_upload']['type'];
		$ext2 = strtolower(end(explode('.',$i_name2)));
		$msg = '';
		if($i_error2 == 0){
			if($ext2 == 'jpg' || $ext2 == 'jpeg' || $ext2 == 'png' || $ext2 == 'gif'){
				if($i_size2 >400){

					$path_share = $coupon_path . $i_name2;
					$upload2 = copy($i_tmp_name2, $path_share);

					if($upload2){										
						$msg .="Your file has been uploaded";
					}else{
						$img_name2 = 'def.jpg';
						$msg .= "Please try again<br>";
					}
				}else{
					$img_name2 = 'def.jpg';
					$msg .= "The image size should be greater then 10 kb <br>";
				}	
			}else{
				$img_name2 = 'def.jpg';	
				$msg .= "The file type is not supported <br>";
			}
		}else{
			$img_name2 = 'def.jpg';	
			$msg .= "There is some error to upload please try again <br>";
		}

		$time = time();
		$order = $category . '-' . $time .'-' . '1';

		$sql = "INSERT INTO add_coupon(c_name,c_category,c_prefix,c_type,c_nums,c_date,to_date,c_image,c_share_image,c_text,c_order)VALUES('".$name."',".$category.",'".$prefix."','".$type."','".$nums."','".$from_Date."','".$to_Date."','".$path."','".$path_share."','".$description."','".$order."')";
		$run_sql = mysql_query($sql) or die(mysql_error());
		$coupon_id = mysql_insert_id();

		// create txt file that contains running coupon number
		$myfile = fopen($coupon_path . $coupon_id . ".txt", "w") or die("Unable to open file for saving coupon numbers!");
		//$number = sprintf('%05d', 0);  
		fwrite($myfile, "0");
		fclose($myfile);
		 
	header("Location:view_coupon.php?msg=".$msg);
}
		
?>