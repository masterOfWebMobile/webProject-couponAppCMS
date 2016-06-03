<?php
include("config.php");
session_start();

if(isset($_POST["submit"])) 
 { 
   $user = $_POST["user"];
   $pw = $_POST["pw"];	

  $sql = "Select * from  login_admin where 	user_name = '".$user."' and user_pass = '".$pw."'" ;
   $run = mysql_query($sql) or die(mysql_error());
   $data = mysql_fetch_assoc($run);
   $num = mysql_num_rows($run);
   
  if($num >= 1) 
   {
   	$_SESSION['id'] = $data["id"];
   	$_SESSION['user'] = $data["user_name"];
   	$_SESSION["pw"] = $data["user_pass"]; 
   	header("location:dashboard.php");

	}
 else
  { 
  header("location:index.php?msg=Invalid Username or Password");
  
  }
}
?>