<?php 
include("config.php");
include("session_check.php");
if(!empty($_GET['id'])) {
$sql = "DELETE FROM registration WHERE id = '".$_GET['id']."'";
$fire = mysql_query($sql) or die(mysql_error());

$msg = "Records Deleted Successfully";
 header("Location:view_users.php?msg=".$msg);
}
?>