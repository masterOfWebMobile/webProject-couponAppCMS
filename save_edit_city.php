<?php 
include("config.php");
include("session_check.php");
  if(isset($_POST['submit'])){
  	$name   = $_POST['c_name'];
        $c_lat   = $_POST['c_lat'];
  	$c_long  = $_POST['c_long'];	
        $ct_id   = $_POST['ct_id'];
  		 
       $sql = "update add_city set c_name = '".$name."',c_lat = '".$c_lat."',c_long='".$c_long."' where id='".$ct_id."'";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
   	 $msg = "Record Has Been Updated";
	    header("Location:view_city.php?msg=".$msg);
	   
	  /* echo "Your File Has Been Updated";
echo "<script>setTimeout(\"location.href = 'edit_daily.php';\",1500);</script>";*/
}
 
	?>