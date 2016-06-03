<?php
include("config.php");
include("session_check.php");
require_once 'excel_reader2.php';

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

if(isset($_POST['submit']) && $_POST['submit']=='Upload')
{
     $i_name = $_FILES['coupon_pack']['name'];
     $i_size = $_FILES['coupon_pack']['size'];
     $i_error = $_FILES['coupon_pack']['error'];
     $i_tmp_name = $_FILES['coupon_pack']['tmp_name'];
     $i_type = $_FILES['coupon_pack']['type'];
 
     $ext = strtolower(end(explode('.',$i_name)));
        $msg = '';

        if($i_error == 0)
        {
            if($ext == 'zip')
            {
                $rndname = rand();
                $img_name = $rndname.'.'.$ext;
                $path = 'coupons/'.$img_name;
                $upload = copy($i_tmp_name, $path);
                if($upload)
                {
                    $path_share = 'coupons/'.$rndname . '/';
                    $zip = new ZipArchive;
                    if ($zip->open($path) === TRUE) 
                    {
                
                        $zip->extractTo($path_share);
                        
                        $path_share .= "package/";

                        $zip->close();
                        $coupon_info_file = $path_share . 'info.xls';

                        if (file_exists($coupon_info_file)) {
                            
                            $data = new Spreadsheet_Excel_Reader($coupon_info_file);
                            $i=0;
                            
                            $time = time();
                            //createThumbs($path_share,$path_share."thumb",150);
                            foreach($data->sheets[0]['cells'] as $readdata)
                            {   
                                if ($i > 0) 
                                {
                                    //print_r($readdata);
                                    //echo "<br/>";
                                    if (!isset($readdata[1]) || !isset($readdata[2]) || !isset($readdata[8]) || !isset($readdata[9]) || !isset($readdata[10]))
                                        continue;
                                    $coupon_name = htmlspecialchars($readdata[1],ENT_QUOTES);
                                    $coupon_category = $readdata[2]; 
                                    $coupon_prefix = isset($readdata[3]) ? isset($readdata[3]) : "";
                                    $coupon_type = $readdata[4];
                                    $coupon_counts = isset($readdata[5]) ? isset($readdata[5]) : 1;
                                    $coupon_date_from = date("Y-m-d", strtotime($readdata[6]));
                                    $coupon_date_to = date("Y-m-d", strtotime($readdata[7]));
                                    $coupon_image = $path_share . $readdata[8];
                                    $coupon_share_image = $path_share . $readdata[9];
                                    $coupon_description = htmlspecialchars($readdata[10],ENT_QUOTES);
                                    $coupon_order = $coupon_category . '-' . $time .'-' . $i;

                                    makeThumbnails($path_share,$readdata[8]);
                                    //generate_image_thumbnail($coupon_image,$path_share."thumb_".$readdata[8]);

                                    $sql = "INSERT INTO add_coupon(c_name,c_category,c_prefix,c_type,c_nums,c_date,to_date,c_image,c_share_image,c_text,c_order)" . 
                                            "VALUES('$coupon_name' ,'$coupon_category','$coupon_prefix','$coupon_type',$coupon_counts,'$coupon_date_from','$coupon_date_to','$coupon_image','$coupon_share_image','$coupon_description','$coupon_order')";

                                    //        exit;

                                    $run_sql = mysql_query($sql) or die(mysql_error());
                                    $coupon_id = mysql_insert_id();

                                    if( $coupon_counts > 1)
                                    {
                                    // create txt file that contains running coupon number
                                        $myfile = fopen($path_share . $coupon_id . ".txt", "w") or die("Unable to open file for saving coupon numbers!");
                                        fwrite($myfile, "0");   
                                        fclose($myfile);
                                    }

                                }
                                $i++;
                            }
                            $msg .= ($i - 1). "Coupons have been uploaded";
                            
                        }
                        else
                        {
                            $msg = "Package does not contain info.xls file.<br>";
                        }
                    }
                    else
                    {
                        $msg = "Unable to open Zip file.<br>";

                    }
                    

                }else{
                    
                    $msg .= "Please try again<br>";
                }
                 
            }else{
                
                $msg .= "The file type is not supported <br>";
            }
        }else{
            $msg .= "There is some error to upload please try again <br>";
        }
     header("location:import_coupon.php?msg=".$msg);
}
?>