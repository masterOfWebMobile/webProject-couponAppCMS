<?php 
include("config.php");
include("session_check.php");

 if(isset($_POST['submit'])){
  	   $name   = $_POST['c_name'];
  	   $c_lat = $_POST['c_lat'];
  	   $c_long = $_POST['c_long'];
  	/*   
  	   echo $name."<br>";
  	   echo $c_lat."<br>";
  	   echo $c_long."<br>";
  	   exit; */
											
       $sql = "INSERT INTO add_city(c_name,c_lat,c_long)VALUES('".$name."','".$c_lat."','".$c_long."')";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
   	 $msg .="City Added Successfully";
			
	   header("Location:view_city.php?msg=".$msg);
	    }
		
	?>