<?php 
include("config.php");
include("session_check.php");


echo $id = $_POST['id'];
echo "<br/>";
echo $change_id = $_POST['change_id'];



$query = "SELECT c_order FROM add_coupon WHERE id = '$id'";
$result = mysql_query($query) or die(mysql_error());
$first_c_order = mysql_fetch_row($result)[0];

$query = "SELECT c_order FROM add_coupon WHERE id = '$change_id'";
$result = mysql_query($query) or die(mysql_error());
$second_c_order = mysql_fetch_row($result)[0];

$sql = "UPDATE add_coupon SET c_order = '$second_c_order' where id='$id'";
$run_sql = mysql_query($sql) or die(mysql_error());

$sql = "UPDATE add_coupon SET c_order = '$first_c_order' where id='$change_id'";
$run_sql = mysql_query($sql) or die(mysql_error());

header("Location:view_coupon.php"); 
?>