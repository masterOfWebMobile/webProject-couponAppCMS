<?php 
include("config.php");
include("session_check.php");

if(isset($_POST['submit'])) {
  $curr_pass = $_POST['cur_pass'];
  $new_pass = $_POST['new_pass'];
  $confirm_pass = $_POST['confirm_pass'];
  
  $sql = "select * from login_admin where  id = '".$_SESSION['id']."' ";
  $run = mysql_query($sql) or die(mysql_error());
  $num = mysql_num_rows($run);
  $data = mysql_fetch_assoc($run);
   if($data['user_pass']==$curr_pass)
    {
       if($new_pass==$confirm_pass)
       {
          $upd = "update login_admin set user_pass = '".$new_pass."'";
          $fire = mysql_query($upd) or die(mysql_error());
          $msg = "Your Password Has Been Changed";       
       } 
       else 
       {
          $msg = "New Password Not Match With Confirm Password";       	
       	}       	
   	
   	}
   else 
    {
      $msg = "Current Password is Wrong";  	
   	
   	}
   }
   header("location:chng_pass.php?msg=$msg");



?>