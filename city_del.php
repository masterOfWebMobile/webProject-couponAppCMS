<?php 
include("config.php");
include("session_check.php");
if(!empty($_GET['id'])) {
$sql = "DELETE FROM add_city WHERE id = '".$_GET['id']."'";
$fire = mysql_query($sql) or die(mysql_error());

$msg = "Records Deleted Successfully";
 header("Location:view_city.php?msg=".$msg);
}
?>