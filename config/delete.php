<?php 
$access=1;
include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

/* Переменные
 $table (text);
 $state (archive/restore) (INT)
 $goto (URL)
*/
$table = $_GET['table'];
switch($table)
{
	case 'expences':
		$access = 10;
		break;
	
	case 'locations_vacations':
		$access = 10;
		break;
		
	default:
		$_SESSION['error'] = lang::ERR_NO_WAY;
		session_write_close();
		header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
		exit;
		break;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/config/config.php');

if($_GET['id'] == '')
{
	$_SESSION['error'] = lang::ERR_NO_ID;
	session_write_close();
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
}

if ($access <= $_SESSION['pwr'])
{
	$sql = "DELETE FROM $table WHERE id=:id";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt ->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		if ($stmt->execute() === TRUE) $_SESSION['success'] = lang::SUCCESS_DELETE;
		else $_SESSION['error'] = lang::ERR_GENERAL;
	} catch (PDOException $ex){include($_SERVER['DOCUMENT_ROOT'].'/config/PDO-exceptions.php');}
	$pdo=NULL;
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
} else 
{
	$_SESSION['error'] = lang::ERR_NO_RIGHTS;
	session_write_close();
	header( 'Location: '. $_GET['URL'] . '?tab='.$_GET['tab']);
	exit;
}
 ?>