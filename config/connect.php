<?php
$server = "mysql:host=localhost;dbname=xxx";
$user = "yyy";
$pass = "zzz";

try{
	$pdo = new PDO($server, $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex){
	include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');
}
?>