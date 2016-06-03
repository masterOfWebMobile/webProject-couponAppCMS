<?php 
include("config.php");
include("session_check.php");

 if(isset($_POST['submit'])){
  	   $name   = $_POST['p_name'];
  	  
			$query = "select * from add_policy";
			$result = mysql_query($query) or die(mysql_error());
			$num = mysql_num_rows($result);
			if($num > 0 ) { 							
           $sql = "update add_policy set p_name = '".$name."'";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
   	 $msg  = "Policy updated successfully";			
	   header("Location:add_policy.php?msg=".$msg); 
	   } else 
	   {
	   $sql = "INSERT INTO add_policy(p_name)VALUES('".$name."')";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
   	 $msg  = "Policy added successfully";			
	   header("Location:add_policy.php?msg=".$msg);
	   
	   }
	    }
		
	?>