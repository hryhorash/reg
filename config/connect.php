<?php
$server = "mysql:host=localhost;dbname=reg";
$user = "mysql";
$pass = "mysql";

try{
	$pdo = new PDO($server, $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
?>