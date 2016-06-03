<?php 
/*
$hostname = "localhost";
$username = "root";
$password = "";
$db_name  = "parkhya_couponapp";
*/

ini_set('memory_limit', '1024M');

$hostname = "localhost";
$username = "hcenter_db1";
$password = "lUcJs65Pw";
$db_name  = "hcenter_db1";

$con = mysql_connect($hostname,$username,$password);
mysql_select_db($db_name); 

?>