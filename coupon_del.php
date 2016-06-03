<?php 
include("config.php");
include("session_check.php");
if(!empty($_GET['id'])) {
$sql = "DELETE FROM add_coupon WHERE id = '".$_GET['id']."'";
$fire = mysql_query($sql) or die(mysql_error());

$sql = "DELETE FROM cupan_number WHERE coupan_id = '".$_GET['id']."'";
$fire = mysql_query($sql) or die(mysql_error());

$msg = "Records Deleted Successfully";
 header("Location:view_coupon.php?msg=".$msg);
}
?>