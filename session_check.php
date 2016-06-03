<?php
session_start();
include('config.php');
if($_SESSION['id']=='' || $_SESSION['user']=='') { header('location:index.php');}
?>
