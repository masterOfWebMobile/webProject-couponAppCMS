<?php 
include("config.php");
include("session_check.php");
  
  if(isset($_POST['submit'])){
  	  $name   = htmlspecialchars($_POST['p_name'],ENT_QUOTES );
  	  $p_id   = $_POST['p_id']; 	   
	  
	  
	  //echo $name;
	  
	  //exit;
	  
	  

       $sql = "update add_policy set p_name = '".$name."' where id='".$p_id."'";
   	 $run_sql = mysql_query($sql) or die(mysql_error());
   	 $msg  = "Policy updated successfully";		
	    header("Location:add_policy.php?msg=".$msg);
	   
	  /* echo "Your File Has Been Updated";
echo "<script>setTimeout(\"location.href = 'edit_daily.php';\",1500);</script>";*/
}
 
	?>