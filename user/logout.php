<?php 
$access = 0;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

session_destroy();  
 header("location: /index.php");  
?>


